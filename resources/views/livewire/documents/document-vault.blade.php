<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Secure Storage</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Digital Vault</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Institutional <span class="text-indigo-500">Vault</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Centralized encrypted repository for personal credentials, contracts, and organizational documentation.
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="$set('showUploadModal', true)" class="group relative flex items-center gap-2 rounded-xl bg-slate-900 px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-indigo-600 transition-all">
                    <span>Archive Document</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/5 dark:bg-slate-900/50 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[240px]">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Title or filename..." class="w-full rounded-xl border-slate-100 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 placeholder-slate-300 focus:ring-0 focus:border-indigo-400 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
        </div>
        
        <select wire:model.live="category" class="rounded-xl border-slate-100 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
            <option value="all">Any Category</option>
            @foreach($this->categories as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        @if(Auth::user()->hasAnyRole(['admin', 'hr_manager']))
            <select wire:model.live="employeeId" class="rounded-xl border-slate-100 bg-slate-50 px-4 py-2.5 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase max-w-[200px]">
                <option value="">Any Employee</option>
                @foreach($this->employees as $e)
                    <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Items Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($documents as $doc)
            <div class="group relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-lg">
                <div class="flex items-start justify-between mb-4">
                    <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center dark:bg-white/5">
                        {{-- Icon based on mime-type --}}
                        @if(Str::contains($doc->mime_type, 'pdf'))
                            <svg class="h-6 w-6 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12h4.5m-4.5 3H12" /></svg>
                        @elseif(Str::contains($doc->mime_type, 'image'))
                            <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                        @else
                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25" /></svg>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if($doc->is_private)
                            <span class="px-2 py-0.5 rounded bg-slate-900 border border-white/10 text-[7px] font-black uppercase text-white">Private</span>
                        @endif
                        <span class="px-2 py-0.5 rounded bg-slate-50 text-[7px] font-black uppercase text-slate-400 dark:bg-white/5">{{ $doc->category }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="text-[12px] font-black text-slate-900 dark:text-white uppercase tracking-tight line-clamp-1">{{ $doc->title }}</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1 line-clamp-1">{{ $doc->file_name }}</p>
                </div>

                <div class="space-y-4 pt-4 border-t border-slate-50 dark:border-white/5">
                    @if($doc->employee)
                        <div class="flex items-center gap-2">
                            <div class="h-5 w-5 rounded-md bg-slate-50 flex items-center justify-center text-[8px] font-black dark:bg-white/5 uppercase">{{ substr($doc->employee->full_name, 0, 1) }}</div>
                            <span class="text-[9px] font-black uppercase text-slate-500">{{ $doc->employee->full_name }}</span>
                        </div>
                    @endif
                    
                    <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-widest text-slate-400">
                        <span>{{ $doc->readable_size }}</span>
                        <span>{{ $doc->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2 lg:opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="downloadDocument({{ $doc->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-600 hover:bg-indigo-500 hover:text-white transition-all dark:bg-white/5 dark:text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </button>
                    @if(Auth::user()->hasAnyRole(['admin', 'hr_manager']) || $doc->uploaded_by === Auth::id())
                        <button wire:confirm="Are you sure you want to purge this record?" wire:click="deleteDocument({{ $doc->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all dark:bg-white/5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 xl:col-span-4 flex flex-col items-center justify-center py-20 text-center rounded-[3rem] border-2 border-dashed border-slate-200 dark:border-white/10">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Empty Repository</h3>
                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">No institutional records found matching your current filter set.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $documents->links() }}
    </div>

    {{-- Upload Modal --}}
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showUploadModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-6 dark:border-white/5">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Archive <span class="text-indigo-500">Record</span></h2>
                </div>
                
                <form wire:submit="uploadDocument" class="p-6 space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Document Title</label>
                        <input wire:model="title" type="text" placeholder="Institutional Name..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                        @error('title') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Category</label>
                            <select wire:model="uploadCategory" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                @foreach($this->categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('uploadCategory') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Expiry Date</label>
                            <input wire:model="expiryDate" type="date" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white">
                            @error('expiryDate') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if(Auth::user()->hasAnyRole(['admin', 'hr_manager']))
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Subject Employee</label>
                            <select wire:model="targetEmployeeId" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase">
                                <option value="">Company Generic</option>
                                @foreach($this->employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">File Attachment (10MB MAX)</label>
                        <div 
                            x-data="{ isUploading: false, progress: 0 }" 
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="relative h-24 w-full rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50 flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors dark:bg-white/5 dark:border-white/5"
                        >
                            <input wire:model="file" type="file" class="absolute inset-0 opacity-0 cursor-pointer">
                            <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            <p class="mt-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $file ? $file->getClientOriginalName() : 'Drop Mission Payload' }}</p>
                            
                            <div x-show="isUploading" class="absolute inset-0 bg-white/90 dark:bg-slate-900/90 flex flex-col items-center justify-center rounded-2xl">
                                <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-2">Transmitting... <span x-text="progress + '%'"></span></span>
                                <div class="w-32 h-1 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                </div>
                            </div>
                        </div>
                        @error('file') <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 px-1">
                        <input wire:model="isPrivate" type="checkbox" id="is_private" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_private" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Mark as Restricted / Confidential</label>
                    </div>

                    <div class="border-t border-slate-100 pt-6 flex justify-end gap-3 dark:border-white/5">
                        <button type="button" wire:click="$set('showUploadModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-4">Abort</button>
                        <button type="submit" class="rounded-xl bg-slate-900 px-8 py-2.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-indigo-600 transition-all">Archiving Sequence</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
