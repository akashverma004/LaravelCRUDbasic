<div class="space-y-8">
    {{-- Welcome Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:border-slate-800 dark:bg-slate-900/50">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-[80px]"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white lg:text-3xl">
                    Welcome back, <span class="text-cyan-600 dark:text-cyan-400">{{ explode(' ', $employee->full_name)[0] }}!</span> 👋
                </h1>
                <p class="mt-1 text-[11px] font-medium text-slate-500">
                    {{ $employee->job_title }} · {{ $employee->department->name ?? 'No Department' }}
                </p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-5 py-3 border border-slate-100 shadow-sm dark:bg-slate-900/50 dark:border-slate-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-100 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ now()->format('l, j M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profile Summary & Quick Stats --}}
        <div class="space-y-6">
            {{-- Profile Card --}}
            <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:bg-slate-900/80">
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-5">
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-2xl font-bold text-slate-700 shadow-sm transition-transform group-hover:scale-105 dark:bg-slate-800 dark:text-slate-300">
                            {{ substr($employee->full_name, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 shadow-sm"></div>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $employee->full_name }}</h2>
                    <div class="mt-3 flex flex-wrap justify-center gap-2">
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold capitalize text-slate-600 dark:bg-slate-800 dark:text-slate-400">{{ str_replace('-', ' ', $employee->employment_type) }}</span>
                        <span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-semibold capitalize text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400">{{ mb_strtolower($employee->status) }}</span>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Manager</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->manager ? $employee->manager->full_name : 'None' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Joined</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $employee->joined_on->format('M j, Y') }}</span>
                    </div>
                </div>
                
                <a href="{{ route('employees.show', $employee->id) }}" class="mt-5 flex items-center justify-center gap-2 rounded-lg bg-slate-900 border border-white/10 px-4 py-2 text-[11px] font-black uppercase tracking-widest text-white shadow-lg shadow-indigo-500/10 transition-all hover:bg-cyan-600 active:scale-[0.98] dark:bg-white/5 dark:hover:bg-cyan-500/20 dark:hover:text-cyan-400">
                    View Profile
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            {{-- Policy Quick Access --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Key Policies</h3>
                <div class="mt-4 space-y-3">
                    @foreach([
                        ['Notice Period', $noticePolicy->notice_days ?? '30', 'Days'],
                        ['Work Mode', 'Hybrid', ''],
                        ['Working Hours', 'Flexible', ''],
                    ] as [$name, $val, $tag])
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3.5 dark:bg-slate-950/40">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">{{ $name }}</p>
                            <p class="mt-0.5 text-sm font-bold text-slate-900 dark:text-white">{{ $val }} <span class="text-xs font-medium text-slate-400">{{ $tag }}</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main Dashboard Modules --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Attendance Tracking --}}
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="relative flex flex-col items-center justify-between gap-6 lg:flex-row">
                    <div class="text-center lg:text-left">
                        <h3 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Attendance status</h3>
                        <p class="mt-1 text-sm font-medium text-slate-500">Track your working hours</p>
                    </div>

                    <div class="flex flex-col items-center gap-6 py-4">
                         <div class="text-4xl font-black text-slate-900 dark:text-white tabular-nums drop-shadow-sm select-none">
                            {{ now()->format('H:i') }}<span class="text-cyan-500 opacity-50 animate-pulse">:</span>{{ now()->format('s') }}
                        </div>
                        <div class="flex gap-3">
                            @if(!$todayAttendance)
                                <button class="rounded-xl bg-slate-900 dark:bg-cyan-500 px-8 py-3 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-cyan-500/20 transition-all hover:scale-105 active:scale-95">Punch In</button>
                            @else
                                <button class="rounded-xl bg-rose-500 px-8 py-3 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-rose-500/20 transition-all hover:scale-105 active:scale-95">Punch Out</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity & Leaves --}}
            <div class="grid gap-6 sm:grid-cols-2">
                 <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-6">Leave Balance</h3>
                    <div class="flex flex-col items-center py-4">
                        <div class="relative flex h-32 w-32 items-center justify-center rounded-full border-[10px] border-slate-50 dark:border-slate-800 shadow-inner">
                             <div class="absolute inset-0 rounded-full border-[10px] border-cyan-500 border-t-transparent -rotate-45"></div>
                             <div class="text-center">
                                 <p class="text-3xl font-black text-slate-900 dark:text-white">12</p>
                                 <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Days Left</p>
                             </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Leave Distribution</h3>
                    </div>
                    <div class="relative h-32">
                        <canvas id="employeeLeaveChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
            const leaveCtx = document.getElementById('employeeLeaveChart')?.getContext('2d');
            const leaveData = @js($leaveChartData);
            
            if (leaveCtx) {
                new Chart(leaveCtx, {
                    type: 'doughnut',
                    data: {
                        labels: leaveData.labels,
                        datasets: [{
                            data: leaveData.values,
                            backgroundColor: ['#06b6d4', '#6366f1', '#f43f5e', '#10b981'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</div>
