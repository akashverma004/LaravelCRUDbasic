<?php

namespace App\Livewire\Payroll;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\PayStructure;
use App\Support\TenantContext;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('hrms.layouts.app')]
#[Title('Payroll Hub - PeopleFlow HRMS')]
class PayrollHub extends Component
{
    use WithPagination;

    public string $activeTab = 'generator'; // 'generator' or 'structures'

    // Generator state
    public string $selectedMonth = '';
    public string $searchGenerator = '';
    public bool $isGenerating = false;
    public int $totalEmployeesCount = 0;
    public float $totalPayrollSum = 0;
    public int $draftCount = 0;
    public int $paidCount = 0;

    // Structure state
    public string $searchStructures = '';
    public bool $showEditModal = false;
    public ?int $selectedEmployeeId = null;
    public string $selectedEmployeeName = '';
    public float $baseSalary = 0;
    public array $allowances = [];
    public array $deductions = [];

    // Edit Payslip state
    public bool $showEditPayslipModal = false;
    public ?int $editingPayslipId = null;
    public string $editingPayslipEmployeeName = '';
    public float $editPayslipBaseSalary = 0;
    public array $editPayslipAllowances = [];
    public array $editPayslipDeductions = [];

    protected $queryString = [
        'activeTab' => ['except' => 'generator'],
        'selectedMonth' => ['except' => ''],
        'searchGenerator' => ['except' => '', 'as' => 'q_gen'],
        'searchStructures' => ['except' => '', 'as' => 'q_struct'],
    ];

    public function mount()
    {
        $this->selectedMonth = $this->selectedMonth ?: now()->format('Y-m');
        $this->loadStats();
    }

    public function loadStats()
    {
        $tenantId = auth()->user()->tenant_id;
        $this->totalEmployeesCount = Employee::where('tenant_id', $tenantId)
            ->whereHas('payStructure')
            ->count();
        
        $this->totalPayrollSum = Payslip::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->sum('net_pay');
            
        $this->draftCount = Payslip::where('tenant_id', $tenantId)
            ->where('status', 'draft')
            ->count();
            
        $this->paidCount = Payslip::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->count();
    }

    // Generator Methods
    public function generate()
    {
        $this->validate([
            'selectedMonth' => 'required',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $date = Carbon::parse($this->selectedMonth);
        $monthLabel = $date->format('F Y');

        \App\Jobs\GenerateMonthlyPayrollJob::dispatch($tenantId, $monthLabel);
        
        $this->dispatch('notify', message: "Payroll generation for $monthLabel has been queued.", type: 'success');
        $this->isGenerating = true;
    }

    public function markAsPaid(int $id)
    {
        Payslip::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->update(['status' => 'paid']);
            
        $this->loadStats();
        $this->dispatch('notify', message: 'Payslip marked as paid.', type: 'info');
    }

    public function revertToDraft(int $id)
    {
        Payslip::where('id', $id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->update(['status' => 'draft']);
            
        $this->loadStats();
        $this->dispatch('notify', message: 'Payslip reverted to draft for amendment.', type: 'warning');
    }

    public function sendEmail(int $id)
    {
        $payslip = Payslip::with('employee')->find($id);
        if (!$payslip || !$payslip->employee) return;

        $user = \App\Models\User::where('email', $payslip->employee->email)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$user) {
            $this->dispatch('notify', message: 'No user account linked to this employee.', type: 'error');
            return;
        }

        $user->notify(new \App\Notifications\PayslipGenerated($payslip));
        $this->dispatch('notify', message: 'Payslip sent to ' . $payslip->employee->email, type: 'success');
    }

    // Structure Methods
    public function openEditModal(int $employeeId)
    {
        $employee = Employee::with('payStructure')->findOrFail($employeeId);
        $this->selectedEmployeeId = $employee->id;
        $this->selectedEmployeeName = $employee->full_name;
        
        if ($employee->payStructure) {
            $this->baseSalary = (float)$employee->payStructure->base_salary;
            $this->allowances = $employee->payStructure->allowances ?? [];
            $this->deductions = $employee->payStructure->deductions ?? [];
        } else {
            $this->baseSalary = 0;
            $this->allowances = [];
            $this->deductions = [];
        }

        $this->showEditModal = true;
    }

    public function addAllowance()
    {
        $this->allowances[] = ['name' => '', 'amount' => 0];
    }

    public function removeAllowance(int $index)
    {
        unset($this->allowances[$index]);
        $this->allowances = array_values($this->allowances);
    }

    public function addDeduction()
    {
        $this->deductions[] = ['name' => '', 'amount' => 0];
    }

    public function removeDeduction(int $index)
    {
        unset($this->deductions[$index]);
        $this->deductions = array_values($this->deductions);
    }

    public function saveStructure()
    {
        $this->validate([
            'baseSalary' => 'required|numeric|min:0',
            'allowances.*.name' => 'required|string|max:100',
            'allowances.*.amount' => 'required|numeric|min:0',
            'deductions.*.name' => 'required|string|max:100',
            'deductions.*.amount' => 'required|numeric|min:0',
        ]);

        PayStructure::updateOrCreate(
            ['employee_id' => $this->selectedEmployeeId, 'tenant_id' => auth()->user()->tenant_id],
            [
                'base_salary' => $this->baseSalary,
                'allowances' => $this->allowances,
                'deductions' => $this->deductions,
            ]
        );

        $this->showEditModal = false;
        $this->dispatch('notify', message: 'Pay structure updated for ' . $this->selectedEmployeeName, type: 'success');
    }

    // Edit Payslip Methods
    public function openEditPayslipModal(int $id)
    {
        $payslip = Payslip::with('employee')->where('tenant_id', auth()->user()->tenant_id)->findOrFail($id);
        
        $this->editingPayslipId = $payslip->id;
        $this->editingPayslipEmployeeName = $payslip->employee->full_name;
        $this->editPayslipBaseSalary = (float)$payslip->base_salary;
        
        $details = $payslip->details ?? [];
        $this->editPayslipAllowances = $details['allowances'] ?? [];
        $this->editPayslipDeductions = $details['deductions'] ?? [];
        
        $this->showEditPayslipModal = true;
    }

    public function addEditPayslipAllowance()
    {
        $this->editPayslipAllowances[] = ['name' => '', 'amount' => 0];
    }

    public function removeEditPayslipAllowance(int $index)
    {
        unset($this->editPayslipAllowances[$index]);
        $this->editPayslipAllowances = array_values($this->editPayslipAllowances);
    }

    public function addEditPayslipDeduction()
    {
        $this->editPayslipDeductions[] = ['name' => '', 'amount' => 0];
    }

    public function removeEditPayslipDeduction(int $index)
    {
        unset($this->editPayslipDeductions[$index]);
        $this->editPayslipDeductions = array_values($this->editPayslipDeductions);
    }

    public function savePayslipEdit()
    {
        $this->validate([
            'editPayslipBaseSalary' => 'required|numeric|min:0',
            'editPayslipAllowances.*.name' => 'required|string|max:100',
            'editPayslipAllowances.*.amount' => 'required|numeric|min:0',
            'editPayslipDeductions.*.name' => 'required|string|max:100',
            'editPayslipDeductions.*.amount' => 'required|numeric|min:0',
        ]);

        $payslip = Payslip::where('tenant_id', auth()->user()->tenant_id)->findOrFail($this->editingPayslipId);
        
        $totalAllowances = collect($this->editPayslipAllowances)->sum('amount');
        $totalDeductions = collect($this->editPayslipDeductions)->sum('amount');
        $netPay = $this->editPayslipBaseSalary + $totalAllowances - $totalDeductions;

        $details = $payslip->details ?? [];
        $details['allowances'] = $this->editPayslipAllowances;
        $details['deductions'] = $this->editPayslipDeductions;
        $details['manually_edited'] = true;
        $details['edit_timestamp'] = now()->toDateTimeString();

        $payslip->update([
            'base_salary' => $this->editPayslipBaseSalary,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'net_pay' => max(0, $netPay),
            'details' => $details,
        ]);

        $this->showEditPayslipModal = false;
        $this->loadStats();
        $this->dispatch('notify', message: 'Payslip adjusted successfully.', type: 'success');
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        // Generator Query
        $payslips = Payslip::where('tenant_id', $tenantId)
            ->with('employee')
            ->when($this->searchGenerator, function($q) {
                $q->whereHas('employee', fn($e) => $e->where('full_name', 'like', "%{$this->searchGenerator}%"));
            })
            ->when($this->selectedMonth, function($q) {
                $date = Carbon::parse($this->selectedMonth);
                $monthLabel = $date->format('F Y');
                $q->where('month', $monthLabel);
            })
            ->orderByDesc('created_at')
            ->paginate(10, pageName: 'p_gen');

        // Structures Query
        $employees = Employee::where('tenant_id', $tenantId)
            ->with('payStructure')
            ->when($this->searchStructures, function($q) {
                $q->where('full_name', 'like', "%{$this->searchStructures}%")
                  ->orWhere('employee_id', 'like', "%{$this->searchStructures}%");
            })
            ->orderBy('full_name')
            ->paginate(12, pageName: 'p_struct');

        return view('livewire.payroll.payroll-hub', [
            'payslips' => $payslips,
            'employees' => $employees,
        ]);
    }
}
