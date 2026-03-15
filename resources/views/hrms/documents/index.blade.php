@extends('hrms.layouts.app')

@section('title', 'Documents - PeopleFlow HRMS')

@section('content')
<div x-data="documentManager()" x-init="init()" class="space-y-6">

    {{-- Notification Toast --}}
    <div
        x-show="toast.show"
        x-transition
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl border bg-white dark:bg-slate-900 border-emerald-200 text-emerald-800"
        style="display: none;"
    >
        <span x-text="toast.message" class="text-sm font-bold"></span>
    </div>

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-white/5">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Documents</h1>
            <p class="mt-2 text-sm text-slate-500">Securely store and manage your files and records.</p>
        </div>
        <button @click="showUploadForm = !showUploadForm" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-5 py-2 text-sm font-bold text-white shadow-sm transition-colors hover:bg-cyan-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            <span>Upload File</span>
        </button>
    </div>

    {{-- Upload Form --}}
    <div x-show="showUploadForm" x-transition class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-6">Upload New Document</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Document Title</label>
                <input type="text" x-model="uploadForm.title" placeholder="Enter title" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</label>
                <select x-model="uploadForm.category" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                    <template x-for="[key, label] in Object.entries(categories)" :key="key">
                        <option :value="key" x-text="label"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Select File</label>
                <input type="file" @change="uploadForm.file = $event.target.files[0]" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 dark:file:bg-slate-800 dark:file:text-slate-400">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Expiry Date (Optional)</label>
                <input type="date" x-model="uploadForm.expiry_date" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
            </div>
            <template x-if="isAdmin">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Assign to Employee</label>
                    <select x-model="uploadForm.employee_id" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
                        <option value="">Global (Company-wide)</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
            </template>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Notes</label>
                <input type="text" x-model="uploadForm.notes" placeholder="Additional info" class="w-full rounded-lg border border-slate-200 bg-transparent px-3 py-2 text-sm focus:border-cyan-500 dark:border-slate-700 dark:text-white">
            </div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button @click="upload()" :disabled="uploading" class="rounded-lg bg-slate-900 px-5 py-2 text-xs font-bold text-white transition-colors hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                <span x-text="uploading ? 'Uploading...' : 'Upload Now'"></span>
            </button>
            <button @click="showUploadForm = false" class="text-xs font-semibold text-slate-500 hover:text-slate-900">Cancel</button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 [scrollbar-width:none]">
            <button @click="filter = 'all'; fetchDocs()" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors whitespace-nowrap" :class="filter === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'">All</button>
            <template x-for="[key, label] in Object.entries(categories)" :key="key">
                <button @click="filter = key; fetchDocs()" class="px-4 py-1.5 text-xs font-bold rounded-lg transition-colors whitespace-nowrap" :class="filter === key ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800'" x-text="label"></button>
            </template>
        </div>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196 7.5 7.5 0 0010.607 10.607z" /></svg>
            <input type="text" x-model="search" @input.debounce.400ms="fetchDocs()" placeholder="Search documents..." class="w-full sm:w-64 rounded-lg border border-slate-200 bg-white py-1.5 pl-9 pr-4 text-xs focus:border-cyan-500 dark:border-slate-800 dark:bg-slate-900/50 dark:text-white">
        </div>
    </div>

    {{-- Document List --}}
    <div class="space-y-3">
        <template x-if="loading">
            <div class="py-20 text-center">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-slate-100 border-t-cyan-500 mx-auto"></div>
            </div>
        </template>

        <template x-if="!loading && documents.length === 0">
            <div class="py-20 text-center rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                <p class="text-sm font-medium text-slate-500">No documents found matching your search.</p>
            </div>
        </template>

        <template x-for="doc in documents" :key="doc.id">
            <div class="group flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 transition-all hover:shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 dark:bg-slate-800"
                    :class="{
                        'text-indigo-500': doc.category === 'identity',
                        'text-purple-500': doc.category === 'contract',
                        'text-amber-500': doc.category === 'certificate',
                        'text-emerald-500': doc.category === 'payslip',
                        'text-rose-500': doc.category === 'letter',
                        'text-cyan-500': doc.category === 'policy',
                    }">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="doc.title"></h4>
                        <span class="rounded px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider bg-slate-100 text-slate-500 dark:bg-slate-800" x-text="doc.category_label"></span>
                    </div>
                    <div class="mt-1 flex items-center gap-3 text-[10px] font-medium text-slate-400">
                        <span x-text="doc.file_size"></span>
                        <template x-if="doc.employee_name">
                            <span class="flex items-center gap-1">· <span x-text="doc.employee_name"></span></span>
                        </template>
                        <template x-if="doc.is_expired">
                            <span class="text-rose-500 font-bold uppercase tracking-tighter">Expired</span>
                        </template>
                    </div>
                </div>

                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a :href="`/documents/${doc.id}/download`" class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-50 hover:text-cyan-600 dark:hover:bg-white/5" title="Download">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </a>
                    <button @click="deleteDoc(doc.id)" class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-50 hover:text-rose-600 dark:hover:bg-white/5" title="Delete">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- Pagination --}}
    <template x-if="pagination.lastPage > 1">
        <div class="flex items-center justify-center gap-2 pt-6">
            <button @click="page = page - 1; fetchDocs()" :disabled="page <= 1" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900 disabled:opacity-30">Previous</button>
            <div class="text-[10px] font-bold text-slate-400 uppercase">Page <span x-text="page"></span> of <span x-text="pagination.lastPage"></span></div>
            <button @click="page = page + 1; fetchDocs()" :disabled="page >= pagination.lastPage" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-900 disabled:opacity-30">Next</button>
        </div>
    </template>
</div>
@endsection
