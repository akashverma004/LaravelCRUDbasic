@extends('hrms.layouts.app')

@section('title', 'System Logs - PeopleFlow HRMS')

@section('content')
<div x-data="auditManager()" x-init="init()" class="space-y-8">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Audit Logs</h1>
            <p class="mt-2 text-sm text-slate-500">Track and monitor all system activity and changes.</p>
        </div>
        <div class="flex items-center gap-3">
             <button @click="fetchData()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                <svg :class="loading ? 'animate-spin' : ''" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                <span>Refresh Logs</span>
            </button>
        </div>
    </div>

    {{-- Telemetry Grid --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950/50">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500">Timestamp</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500">Action</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500">Resource</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500">IP Address</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <template x-for="log in logs" :key="log.id">
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400" x-text="formatDate(log.created_at)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        <span x-text="log.user ? log.user.name.charAt(0).toUpperCase() : 'S'"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 truncate dark:text-white" x-text="log.user ? log.user.name : 'System'"></p>
                                        <p class="text-[10px] font-medium text-slate-500 truncate" x-text="log.user ? log.user.email : 'Automated Action'"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-md px-2 py-1 text-[10px] font-semibold tracking-wide uppercase"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400': log.action === 'created',
                                        'bg-cyan-50 text-cyan-700 dark:bg-cyan-500/10 dark:text-cyan-400': log.action === 'updated',
                                        'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400': log.action === 'deleted',
                                        'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400': !['created', 'updated', 'deleted'].includes(log.action)
                                    }" x-text="log.action"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                     <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.25 11.25L10.5 15.75m9.75-4.875c0 5.591-4.409 10.125-10 10.125a10.02 10.02 0 01-5.63-1.688l-3.396.947a.75.75 0 01-.947-.947l.947-3.396A10.02 10.02 0 012.25 12c0-5.591 4.409-10 10-10a10.02 10.02 0 015.63 1.688l3.396-.947a.75.75 0 01.947.947l-.947 3.396c1.303 1.258 2.103 3.018 2.103 4.965z" /></svg>
                                     <span class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="log.subject_type"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-500 dark:text-slate-400" x-text="log.ip_address"></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="showDetails(log)" class="rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 shadow-sm">View Payload</button>
                            </td>
                        </tr>
                    </template>
                    
                    {{-- Loading State --}}
                    <template x-if="loading && logs.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex justify-center mb-4">
                                     <svg class="h-8 w-8 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-slate-500">Loading logs...</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Empty State --}}
                    <template x-if="logs.length === 0 && !loading">
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">No Activity Found</h3>
                                    <p class="mt-1 text-xs text-slate-500">There are no audit logs to display at this time.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Details Modal --}}
    <div x-show="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="detailsModal = false" class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Data Payload</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Raw properties tracking changes</p>
                </div>
                <button @click="detailsModal = false" class="rounded-lg p-2 text-slate-400 hover:bg-slate-50 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="px-6 py-6">
                <div class="max-h-96 overflow-y-auto rounded-xl bg-slate-50 border border-slate-200 p-4 font-mono text-xs dark:bg-slate-950 dark:border-slate-800 custom-scrollbar">
                    <template x-if="selectedLog">
                        <pre x-text="JSON.stringify(selectedLog.properties, null, 2)" class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap word-break-all"></pre>
                    </template>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end dark:bg-slate-900/50 dark:border-slate-800">
                <button @click="detailsModal = false" class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
