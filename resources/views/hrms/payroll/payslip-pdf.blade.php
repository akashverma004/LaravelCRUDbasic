<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $employee->full_name }}</title>
    <style>
        @page { size: a4; margin: 15mm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #334155; margin: 0; padding: 0; font-size: 9pt; }
        .container { width: 100%; border: 0px solid #e2e8f0; }
        
        /* Header Logic */
        .header { margin-bottom: 25px; position: relative; }
        .logo-text { font-size: 22pt; font-weight: 800; color: #0e7490; margin: 0; }
        .logo-sub { font-size: 8pt; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        
        .payslip-badge { background: #0e7490; color: white; padding: 6px 15px; border-radius: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; font-size: 9pt; float: right; margin-top: 5px; }
        .month-label { font-size: 14pt; font-weight: 800; color: #334155; text-align: right; margin-top: 15px; clear: both; }
        
        .divider { border-top: 2px solid #0e7490; margin-top: 10px; margin-bottom: 25px; }

        /* Double Column Info */
        .info-grid { width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 0; }
        .info-card { width: 48%; vertical-align: top; border: 1px solid #f1f5f9; padding: 15px; background: #fff; }
        .card-title { font-size: 7pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 12px; }
        
        .line-item { margin-bottom: 6px; width: 100%; }
        .line-label { font-weight: 700; color: #64748b; font-size: 8pt; display: inline-block; width: 45%; }
        .line-value { font-weight: 800; color: #1e293b; font-size: 8pt; display: inline-block; width: 50%; text-align: right; word-wrap: break-word; }

        /* Prorated Banner */
        .banner { background: #f5f3ff; border-left: 4px solid #8b5cf6; padding: 12px 15px; margin-bottom: 25px; }
        .banner-title { font-size: 8pt; font-weight: 900; color: #5b21b6; text-transform: uppercase; margin-bottom: 4px; }
        .banner-body { font-size: 8pt; color: #7c3aed; font-weight: 600; }

        /* Summary Horizontal Bar */
        .summary-bar { width: 100%; margin-bottom: 30px; border-collapse: collapse; border: 1px solid #f1f5f9; table-layout: fixed; }
        .summary-item { padding: 15px; text-align: center; border-right: 1px solid #f1f5f9; vertical-align: middle; }
        .summary-item:last-child { border-right: none; }
        .summary-label { font-size: 7pt; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .summary-value { font-size: 13pt; font-weight: 900; color: #1e293b; }
        
        /* Tables Logic */
        .breakdown-grid { width: 100%; margin-bottom: 20px; table-layout: fixed; border-collapse: collapse; }
        .breakdown-side { vertical-align: top; }
        
        .matrix-title { font-size: 7pt; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; }
        .table-matrix { width: 100%; border-collapse: collapse; border: 1px solid #f8fafc; }
        .table-matrix th { background: #f8fafc; padding: 8px 12px; font-size: 7pt; font-weight: 800; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .table-matrix td { padding: 10px 12px; font-size: 8pt; font-weight: 700; color: #334155; border-bottom: 1px solid #f1f5f9; }
        .amount-col { text-align: right; }
        
        /* Total Rows */
        .total-row td { background: white; font-weight: 800 !important; color: #0f172a !important; padding-top: 15px !important; border: none !important; }

        /* Signatures Area */
        .footer-visuals { margin-top: 60px; width: 100%; }
        .sig-box { vertical-align: bottom; }
        .seal-box { text-align: right; vertical-align: bottom; }
        
        .sig-image { height: 40px; border-bottom: 1px solid #cbd5e1; margin-bottom: 5px; max-width: 150px; }
        .sig-label { font-size: 7pt; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; }
        
        .draft-badge { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-weight: 900; font-size: 8pt; text-transform: uppercase; display: inline-block; margin-bottom: 15px; }

        /* Footer Metadata */
        .footer-meta { margin-top: 50px; border-top: 1px solid #f1f5f9; padding-top: 15px; width: 100%; border-collapse: collapse; }
        .footer-left { color: #94a3b8; font-size: 7pt; font-weight: 700; }
        .footer-right { text-align: right; color: #cbd5e1; font-size: 7pt; font-weight: 700; }
        .footer-note { font-size: 7pt; color: #cbd5e1; margin-top: 10px; font-weight: 600; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header Vector --}}
        <div class="header">
            <div class="payslip-badge">PAYSLIP</div>
            <div style="clear: both; padding-top: 5px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 42px; width: auto; object-fit: contain;">
                @else
                    <h1 class="logo-text">{{ $tenant->name ?? 'PeoplesAI' }}</h1>
                @endif
                <p class="logo-sub" style="margin-top: 2px;">Employee Payslip</p>
            </div>
            <div class="month-label">{{ $payslip->month }}</div>
        </div>

        <div class="divider"></div>

        {{-- Info Clusters --}}
        <table class="info-grid" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-card">
                    <div class="card-title">Employee Details</div>
                    <div class="line-item">
                        <span class="line-label">Name</span>
                        <span class="line-value">{{ $employee->full_name }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Designation</span>
                        <span class="line-value truncate">{{ $employee->job_title ?? 'Employee' }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Department</span>
                        <span class="line-value uppercase">{{ $employee->department->name ?? 'Personnel' }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Email</span>
                        <span class="line-value" style="font-size: 7pt;">{{ $employee->email }}</span>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td class="info-card">
                    <div class="card-title">Pay Period Details</div>
                    <div class="line-item">
                        <span class="line-label">Pay Month</span>
                        <span class="line-value">{{ $payslip->month }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Period</span>
                        <span class="line-value" style="font-size: 7pt;">{{ \Carbon\Carbon::parse($payslip->period_start)->format('d M Y') }} – {{ \Carbon\Carbon::parse($payslip->period_end)->format('d M Y') }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Annual CTC</span>
                        <span class="line-value">₹{{ number_format($payStructure->base_salary ?? 0, 2) }}</span>
                    </div>
                    <div class="line-item">
                        <span class="line-label">Paid Via</span>
                        <span class="line-value">Direct Deposit</span>
                    </div>
                    <div class="line-item" style="margin-top: 8px;">
                        <span class="line-label text-slate-400">Ref #</span>
                        <span class="line-value">#{{ str_pad($payslip->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Proration Notice Vector --}}
        @if(isset($details['proration']))
        <div class="banner">
            <div class="banner-title">⚠ Prorated Salary Applied</div>
            <div class="banner-body">
                {{ $details['proration']['reason'] }} — {{ $details['proration']['worked_days'] }} of {{ $details['proration']['total_days'] }} days ({{ round($details['proration']['ratio'] * 100) }}%)
            </div>
        </div>
        @endif

        {{-- Summary Snapshot Bar --}}
        <table class="summary-bar">
            <tr>
                <td class="summary-item">
                    <div class="summary-label">Base Salary</div>
                    <div class="summary-value">₹{{ number_format($payslip->base_salary, 2) }}</div>
                </td>
                <td class="summary-item">
                    <div class="summary-label">Total Earnings</div>
                    <div class="summary-value" style="color: #059669;">+₹{{ number_format($payslip->total_allowances, 2) }}</div>
                </td>
                <td class="summary-item">
                    <div class="summary-label">Total Deductions</div>
                    <div class="summary-value" style="color: #dc2626;">-₹{{ number_format($payslip->total_deductions, 2) }}</div>
                </td>
            </tr>
        </table>

        {{-- Breakdown Matrix --}}
        <table class="breakdown-grid">
            <tr>
                <td class="breakdown-side">
                    <div class="matrix-title">Earnings</div>
                    <table class="table-matrix">
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th class="amount-col">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Base Salary</td>
                                <td class="amount-col">{{ number_format($payslip->base_salary, 2) }}</td>
                            </tr>
                            @foreach($details['allowances'] ?? [] as $a)
                            <tr>
                                <td>{{ $a['name'] }}</td>
                                <td class="amount-col">{{ number_format($a['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                            @if(isset($details['adjustments']))
                                @foreach($details['adjustments'] as $adj)
                                    @if($adj['type'] === 'addition')
                                    <tr>
                                        <td>{{ $adj['label'] }}</td>
                                        <td class="amount-col">{{ number_format($adj['amount'], 2) }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                            <tr class="total-row">
                                <td>Total Earnings</td>
                                <td class="amount-col">{{ number_format($payslip->total_allowances + $payslip->base_salary, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 5%;"></td>
                <td class="breakdown-side">
                    <div class="matrix-title">Deductions</div>
                    <table class="table-matrix">
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th class="amount-col">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details['deductions'] ?? [] as $d)
                            <tr>
                                <td>{{ $d['name'] }}</td>
                                <td class="amount-col">{{ number_format($d['amount'], 2) }}</td>
                            </tr>
                            @empty
                                @if(!isset($details['unpaid_leave_deduction']))
                                <tr>
                                    <td colspan="2" style="text-align: center; color: #94a3b8; font-style: italic;">No deductions this period</td>
                                </tr>
                                @endif
                            @endforelse
                            @if(isset($details['unpaid_leave_deduction']))
                            <tr>
                                <td>Unpaid Leave ({{ $details['unpaid_leave_deduction']['days'] }}d)</td>
                                <td class="amount-col">{{ number_format($details['unpaid_leave_deduction']['amount'], 2) }}</td>
                            </tr>
                            @endif
                            @if(isset($details['adjustments']))
                                @foreach($details['adjustments'] as $adj)
                                    @if($adj['type'] === 'deduction')
                                    <tr>
                                        <td>{{ $adj['label'] }}</td>
                                        <td class="amount-col">{{ number_format($adj['amount'], 2) }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td class="amount-col">{{ number_format($payslip->total_deductions, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Signature Control Cluster --}}
        <table class="footer-visuals" cellpadding="0" cellspacing="0">
            <tr>
                <td class="sig-box">
                    @if($signatureBase64)
                        <img src="{{ $signatureBase64 }}" style="max-height: 45px; border-bottom: 1px solid #cbd5e1; margin-bottom: 5px;">
                    @else
                        <div class="sig-image" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/e/e0/Placeholder_Signature.png'); background-size: contain; background-repeat: no-repeat;"></div>
                    @endif
                    <div class="sig-label">Authorized Signatory</div>
                </td>
                <td class="seal-box">
                    <div class="draft-badge" @if($payslip->status === 'paid') style="background:#dcfce7; color:#15803d;" @endif>{{ strtoupper($payslip->status) }}</div>
                    <div style="height: 50px; margin-bottom: 5px; text-align: right;">
                        @if($stampBase64)
                            <img src="{{ $stampBase64 }}" style="max-height: 50px; opacity: 0.85;">
                        @else
                            <img src="https://via.placeholder.com/150x50/f1f5f9/64748b?text=COMPANY+SEAL" alt="Seal" style="opacity: 0.5;">
                        @endif
                    </div>
                    <div class="sig-label" style="text-align: right;">Official Seal</div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            {{ $tenant->name ?? 'PeoplesAI' }} · {{ $tenant->email ?? 'support@peoplesai.com' }}<br>
            This is a system-generated payslip and does not require a signature. For verification, check node reference Ref #{{ str_pad($payslip->id, 6, '0', STR_PAD_LEFT) }}.
        </div>

        <table class="footer-meta">
            <tr>
                <td class="footer-left">Generated: {{ now()->format('d M Y, h:i A') }}</td>
                <td class="footer-right">Disbursement Integrity Vector v2.0</td>
            </tr>
        </table>
    </div>
</body>
</html>
