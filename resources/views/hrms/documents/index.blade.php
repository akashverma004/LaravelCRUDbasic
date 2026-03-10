@extends('hrms.layouts.app')

@section('title', 'Documents - PeopleFlow HRMS')

@section('content')
<div x-data="documentManager()" x-init="init()">

    {{-- Toast Notification --}}
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl px-5 py-3 shadow-2xl"
        :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        style="display: none;"
    >
        <template x-if="toast.type === 'success'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <template x-if="toast.type === 'error'">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Documents</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage and organize employee and company documents</p>
        </div>
        <button @click="showUploadForm = !showUploadForm" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-600 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Upload Document
        </button>
    </div>

    {{-- Upload Form --}}
    <div x-show="showUploadForm" x-transition class="mb-6 rounded-2xl border border-cyan-200 bg-cyan-50/50 p-6 dark:border-slate-600 dark:bg-slate-800/50">
        <h3 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Upload New Document</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Title *</label>
                <input type="text" x-model="uploadForm.title" placeholder="e.g. Aadhaar Card" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Category *</label>
                <select x-model="uploadForm.category" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <template x-for="[key, label] in Object.entries(categories)" :key="key">
                        <option :value="key" x-text="label"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">File *</label>
                <input type="file" @change="uploadForm.file = $event.target.files[0]" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm file:mr-2 file:rounded file:border-0 file:bg-cyan-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-cyan-600 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:file:bg-slate-600 dark:file:text-cyan-400" id="docFileInput">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Expiry Date</label>
                <input type="date" x-model="uploadForm.expiry_date" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <template x-if="isAdmin">
                <div>
                    <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Employee</label>
                    <select x-model="uploadForm.employee_id" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">Company-wide</option>
                        <template x-for="emp in employees" :key="emp.id">
                            <option :value="emp.id" x-text="emp.full_name"></option>
                        </template>
                    </select>
                </div>
            </template>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-500 dark:text-slate-400">Notes</label>
                <input type="text" x-model="uploadForm.notes" placeholder="Optional notes..." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <button @click="upload()" :disabled="uploading" class="inline-flex items-center gap-1.5 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-cyan-600 disabled:opacity-50">
                <svg x-show="uploading" class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <svg x-show="!uploading" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload
            </button>
            <button @click="showUploadForm = false" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Cancel</button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div class="flex overflow-x-auto rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800/50 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <button @click="filter = 'all'; fetchDocs()" class="flex-shrink-0 border-b-2 px-4 py-2.5 text-xs font-medium transition-colors" :class="filter === 'all' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'">All</button>
            <template x-for="[key, label] in Object.entries(categories)" :key="key">
                <button @click="filter = key; fetchDocs()" class="flex-shrink-0 border-b-2 px-4 py-2.5 text-xs font-medium transition-colors" :class="filter === key ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400'" x-text="label"></button>
            </template>
        </div>
        <div class="relative ml-auto">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" x-model="search" @input.debounce.400ms="fetchDocs()" placeholder="Search documents..." class="w-48 rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        </div>
    </div>

    {{-- Documents Grid --}}
    <div class="space-y-3">
        <template x-if="loading">
            <div class="flex items-center justify-center py-12">
                <svg class="h-8 w-8 animate-spin text-cyan-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>
        </template>

        <template x-if="!loading && documents.length === 0">
            <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center dark:border-slate-600">
                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <p class="mt-3 text-sm font-medium text-slate-500 dark:text-slate-400">No documents found</p>
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Upload your first document to get started</p>
            </div>
        </template>

        <template x-for="doc in documents" :key="doc.id">
            <div class="group rounded-2xl border border-slate-200 bg-white p-4 transition-shadow hover:shadow-md dark:border-slate-700 dark:bg-slate-800/50">
                <div class="flex items-center gap-4">
                    {{-- File Icon --}}
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl"
                        :class="{
                            'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400': doc.category === 'identity',
                            'bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400': doc.category === 'contract',
                            'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400': doc.category === 'certificate',
                            'bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400': doc.category === 'payslip',
                            'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400': doc.category === 'letter',
                            'bg-cyan-100 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400': doc.category === 'policy',
                            'bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300': doc.category === 'general',
                        }">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="truncate text-sm font-semibold text-slate-900 dark:text-white" x-text="doc.title"></h4>
                            <span class="flex-shrink-0 rounded-full px-2 py-0.5 text-[10px] font-medium"
                                :class="{
                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400': doc.category === 'identity',
                                    'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400': doc.category === 'contract',
                                    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400': doc.category === 'certificate',
                                    'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400': doc.category === 'payslip',
                                    'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400': doc.category === 'letter',
                                    'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/20 dark:text-cyan-400': doc.category === 'policy',
                                    'bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300': doc.category === 'general',
                                }"
                                x-text="doc.category_label"></span>
                            <template x-if="doc.is_expired">
                                <span class="flex-shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-700 dark:bg-red-500/20 dark:text-red-400">Expired</span>
                            </template>
                            <template x-if="doc.expires_soon && !doc.is_expired">
                                <span class="flex-shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">Expiring Soon</span>
                            </template>
                            <template x-if="doc.is_private">
                                <svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </template>
                        </div>
                        <div class="mt-0.5 flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                            <span x-text="doc.file_name"></span>
                            <span>·</span>
                            <span x-text="doc.file_size"></span>
                            <template x-if="doc.employee_name">
                                <span>· <span x-text="doc.employee_name"></span></span>
                            </template>
                            <template x-if="doc.expiry_display">
                                <span>· Expires <span x-text="doc.expiry_display"></span></span>
                            </template>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                        <a :href="`/documents/${doc.id}/download`" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-cyan-600 dark:hover:bg-slate-700 dark:hover:text-cyan-400" title="Download">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                        <button @click="deleteDoc(doc.id)" class="rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400" title="Delete">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Pagination --}}
    <template x-if="pagination.lastPage > 1">
        <div class="mt-6 flex items-center justify-center gap-2">
            <button @click="page = page - 1; fetchDocs()" :disabled="page <= 1" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-40 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Previous</button>
            <span class="text-xs text-slate-500 dark:text-slate-400" x-text="`Page ${page} of ${pagination.lastPage}`"></span>
            <button @click="page = page + 1; fetchDocs()" :disabled="page >= pagination.lastPage" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 disabled:opacity-40 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Next</button>
        </div>
    </template>
</div>
@endsection
