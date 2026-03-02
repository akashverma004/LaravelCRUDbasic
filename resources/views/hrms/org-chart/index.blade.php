@extends('hrms.layouts.app')

@section('title', 'Organization Chart - PeopleFlow HRMS')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-4xl font-bold">Organization Chart</h1>
        <p class="mt-2 text-slate-400">{{ $stats['totalEmployees'] }} members · {{ $stats['managers'] }} managers</p>
    </div>
    <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium transition hover:bg-slate-600">← Dashboard</a>
</div>

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-8" x-data="orgChart()">
    <!-- Search and Filter -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <input
            type="text"
            placeholder="Search employee..."
            x-model="search"
            @input="filterChart()"
            class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-2 text-white focus:border-cyan-500 focus:outline-none"
        >
        <select x-model="filterDept" @change="filterChart()" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
            <option value="">All Departments</option>
            @foreach($ceo?->subordinates?->pluck('department.name')->unique() ?? [] as $dept)
                <option value="{{ $dept }}">{{ $dept }}</option>
            @endforeach
        </select>
        <select x-model="sortBy" @change="filterChart()" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-2 text-white focus:border-cyan-500 focus:outline-none">
            <option value="name">Sort by Name</option>
            <option value="title">Sort by Title</option>
            <option value="department">Sort by Department</option>
        </select>
    </div>

    <!-- Org Chart Tree -->
    <div class="overflow-x-auto pb-8">
        <div class="inline-block min-w-full">
            @if($ceo)
                <x-hrms.org-chart-interactive :employee="$ceo" />
            @else
                <div class="rounded-lg border border-slate-700 bg-slate-950 p-8 text-center">
                    <p class="text-slate-400">No organization structure found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Employee Details Panel -->
    <div
        x-show="selectedEmployee"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="selectedEmployee = null"
    >
        <div class="w-full max-w-2xl rounded-2xl border border-cyan-500/30 bg-slate-900 p-6 shadow-2xl" @click.stop>
            <div class="flex items-start justify-between mb-6">
                <h2 class="text-2xl font-bold text-white" x-text="selectedEmployee?.name"></h2>
                <button @click="selectedEmployee = null" class="text-slate-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Left Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Job Title</p>
                        <p class="text-lg font-semibold text-cyan-300" x-text="selectedEmployee?.title"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Department</p>
                        <p class="text-lg font-semibold" x-text="selectedEmployee?.department"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Email</p>
                        <p class="text-sm text-cyan-300" x-text="selectedEmployee?.email"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Phone</p>
                        <p class="text-sm" x-text="selectedEmployee?.phone"></p>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Status</p>
                        <p class="text-sm font-medium" :class="selectedEmployee?.status === 'active' ? 'text-emerald-300' : 'text-yellow-300'" x-text="selectedEmployee?.status"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Employment Type</p>
                        <p class="text-sm capitalize" x-text="selectedEmployee?.employment_type"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Joined On</p>
                        <p class="text-sm" x-text="selectedEmployee?.joined_on"></p>
                    </div>
                    <div class="mb-4">
                        <p class="text-sm text-slate-400">Direct Reports</p>
                        <p class="text-2xl font-bold text-cyan-300" x-text="selectedEmployee?.direct_reports || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Manager Info -->
            <div class="mt-6 border-t border-slate-700 pt-6" x-show="selectedEmployee?.manager">
                <p class="mb-2 text-sm text-slate-400">Reports To</p>
                <p class="text-lg font-semibold" x-text="selectedEmployee?.manager"></p>
            </div>

            <!-- Subordinates -->
            <div class="mt-6 border-t border-slate-700 pt-6" x-show="selectedEmployee?.subordinates?.length > 0">
                <p class="mb-4 text-sm font-semibold text-slate-300">Direct Reports</p>
                <div class="space-y-2">
                    <template x-for="sub in selectedEmployee?.subordinates || []" :key="sub.id">
                        <div class="rounded-lg bg-slate-950 p-3 cursor-pointer hover:bg-slate-800" @click="loadEmployeeDetails(sub.id)">
                            <p class="font-medium text-white" x-text="sub.name"></p>
                            <p class="text-xs text-slate-400" x-text="sub.title + ' · ' + sub.department"></p>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <a :href="'/employees/' + selectedEmployee?.id" class="flex-1 rounded-lg bg-cyan-500 px-4 py-2 text-center font-medium text-slate-900 transition hover:bg-cyan-400">
                    View Profile
                </a>
                <button @click="selectedEmployee = null" class="rounded-lg bg-slate-700 px-4 py-2 font-medium transition hover:bg-slate-600">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function orgChart() {
    return {
        search: '',
        filterDept: '',
        sortBy: 'name',
        selectedEmployee: null,

        async loadEmployeeDetails(employeeId) {
            try {
                const response = await fetch(`/org-chart/${employeeId}`);
                const data = await response.json();
                this.selectedEmployee = data;
            } catch (error) {
                console.error('Error loading employee details:', error);
            }
        },

        filterChart() {
            // This would filter the chart display based on search, department, and sort
            // For now, just trigger a re-render
            console.log('Filter:', { search: this.search, dept: this.filterDept, sort: this.sortBy });
        },
    };
}
</script>
@endsection
