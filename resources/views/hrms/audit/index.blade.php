@extends('hrms.layouts.app')

@section('title', 'Audit Log - PeopleFlow HRMS')

@section('content')
<div x-data="auditManager()" x-init="init()">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Audit Log / Activity Trail</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Comprehensive history of all system changes and user actions.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
                <tr>
                    <th class="px-6 py-4">Timestamp</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Details</th>
                    <th class="px-6 py-4">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                <template x-for="log in logs" :key="log.id">
                    <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap" x-text="formatDate(log.created_at)"></td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-slate-900 dark:text-white" x-text="log.user ? log.user.name : 'System'"></span>
                                <span class="text-[10px] text-slate-400" x-text="log.user ? log.user.email : ''"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-green-100 text-green-700 dark:bg-green-500/20': log.action === 'created',
                                    'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20': log.action === 'updated',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-500/20': log.action === 'deleted',
                                    'bg-slate-100 text-slate-700 dark:bg-slate-700': !['created', 'updated', 'deleted'].includes(log.action)
                                }" x-text="log.action"></span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 truncate max-w-xs" x-text="log.subject_type"></td>
                        <td class="px-6 py-4">
                            <button @click="showDetails(log)" class="text-xs font-semibold text-cyan-600 hover:text-cyan-500 dark:text-cyan-400">View Changes</button>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400" x-text="log.ip_address"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- Details Modal --}}
    <div x-show="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" style="display: none;">
        <div @click.away="detailsModal = false" class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-2xl p-8 shadow-2xl">
            <h3 class="text-xl font-bold mb-6">Activity Details</h3>
            <div class="max-h-96 overflow-y-auto rounded-xl bg-slate-50 p-4 dark:bg-slate-900 font-mono text-xs">
                <template x-if="selectedLog">
                    <pre x-text="JSON.stringify(selectedLog.properties, null, 2)" class="text-slate-700 dark:text-slate-300"></pre>
                </template>
            </div>
            <div class="mt-8 flex justify-end">
                <button @click="detailsModal = false" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white hover:bg-slate-800 dark:bg-slate-700">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
