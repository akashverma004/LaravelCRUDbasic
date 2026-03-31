<div class="space-y-8 pb-12">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-white px-8 py-8 shadow-sm border border-slate-200 dark:bg-slate-900/50 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-48 w-48 rounded-full bg-indigo-500/10 blur-[60px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-6 lg:flex-row lg:items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">Security Grid</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Access Vectors</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                    Privilege <span class="text-indigo-500">Architecture</span>
                </h1>
                <p class="mt-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest leading-loose">
                    Governance of administrative roles, modular permissions, and user access levels across the organizational construct.
                </p>
            </div>

            <div class="flex gap-4">
                <button wire:click="$set('activeTab', 'roles')" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'roles' ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:text-slate-600' }}">Roles</button>
                <button wire:click="$set('activeTab', 'users')" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab === 'users' ? 'bg-slate-900 text-white shadow-xl' : 'text-slate-400 hover:text-slate-600' }}">Identity Mapping</button>
            </div>
        </div>
    </div>

    @if($activeTab === 'roles')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
                <div class="group relative flex flex-col rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:shadow-md">
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center dark:bg-white/5 shadow-inner">
                            <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 1.5.346 2.919.969 4.183a11.997 11.997 0 007.031 6.471l.032.012.032-.012a11.998 11.998 0 007.031-6.471c.623-1.264.969-2.683.969-4.183 0-1.29-.204-2.532-.581-3.688A11.959 11.959 0 0112 2.714z" /></svg>
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="openRoleModal({{ $role->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-indigo-500 hover:text-white dark:bg-white/5">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                            <button wire:click="deleteRole({{ $role->id }})" class="p-2 rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white dark:bg-white/5">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <h4 class="text-[13px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $role->display_name }}</h4>
                    <p class="text-[8px] font-black text-indigo-500 uppercase tracking-[0.2em] mt-1">{{ $role->name }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose mt-3 line-clamp-2 h-10">{{ $role->description ?: 'Operational access level with specific institutional permissions.' }}</p>
                    
                    <div class="mt-6 pt-6 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
                        <span class="text-[9px] font-black uppercase text-slate-400 tracking-widest">{{ $role->permissions->count() }} Vectors</span>
                        <div class="flex -space-x-2">
                             @php $users = $role->users()->take(4)->get(); @endphp
                             @foreach($users as $u)
                                <div class="h-6 w-6 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[7px] font-black dark:border-slate-900 dark:bg-white/5 uppercase">{{ substr($u->name, 0, 1) }}</div>
                             @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            <button wire:click="openRoleModal()" class="group relative flex flex-col items-center justify-center rounded-[1.75rem] border-2 border-dashed border-slate-200 p-8 text-center transition-all hover:bg-slate-50 hover:border-indigo-400 dark:border-white/10 dark:hover:bg-white/5">
                <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 dark:bg-white/5 group-hover:bg-indigo-500 group-hover:text-white transition-all shadow-inner">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </div>
                <h4 class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] group-hover:text-slate-600">Provision Role</h4>
            </button>
        </div>
    @else
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Identity Grid</h4>
                <div class="relative w-72">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Identifier..." class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-2.5 text-[10px] font-black text-slate-900 dark:border-white/5 dark:bg-slate-900/50 dark:text-white uppercase tracking-widest">
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm dark:border-white/5 dark:bg-slate-900">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/5">
                            <th class="px-8 py-5 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Subject</th>
                            <th class="px-8 py-5 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Assigned Vectors</th>
                            <th class="px-8 py-5 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                        @foreach($users as $user)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-white/2 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-9 w-9 rounded-xl bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-400 dark:bg-white/5 uppercase shadow-inner">{{ substr($user->name, 0, 1) }}</div>
                                        <div>
                                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $user->name }}</p>
                                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-0.5">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->roles as $r)
                                            <span class="px-2.5 py-1 rounded bg-indigo-50 text-[7px] font-black uppercase tracking-widest text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">{{ $r->display_name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <button wire:click="openUserRoleModal({{ $user->id }})" class="px-4 py-2 rounded-xl bg-slate-900 text-[8px] font-black uppercase tracking-widest text-white shadow-lg hover:bg-indigo-600 transition-all opacity-0 group-hover:opacity-100">Recalibrate</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    @endif

    {{-- Role Modal --}}
    @if($showRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="closeRoleModal" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-4xl rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="border-b border-slate-100 p-8 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight">{{ $editingRoleId ? 'Recalibrate' : 'Provision' }} <span class="text-indigo-500">Access Role</span></h2>
                    <button wire:click="$set('showRoleModal', false)" class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 dark:bg-white/5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="saveRole" class="flex flex-col h-full overflow-hidden max-h-[80vh]">
                    <div class="p-10 overflow-y-auto grid grid-cols-1 lg:grid-cols-3 gap-12 custom-scrollbar">
                        {{-- Identity Info --}}
                        <div class="lg:col-span-1 space-y-8">
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Unique Identifier (Slug)</label>
                                <input wire:model="roleName" type="text" placeholder="hr_admin..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest disabled:opacity-50" {{ $editingRoleId ? 'disabled' : '' }}>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Display Label</label>
                                <input wire:model="roleDisplayName" type="text" placeholder="Security Auditor..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs font-black text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase tracking-widest">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-black uppercase text-slate-500 ml-1 tracking-[0.2em]">Role Narrative</label>
                                <textarea wire:model="roleDescription" placeholder="Description of access scope..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-900 dark:border-white/5 dark:bg-white/5 dark:text-white uppercase h-32 leading-relaxed"></textarea>
                            </div>
                        </div>

                        {{-- Permission Grid --}}
                        <div class="lg:col-span-2 space-y-8">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 px-2">Modular Permission Vectors</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                @foreach($permissionsByModule as $module => $perms)
                                    <div class="space-y-4">
                                        <h5 class="text-[9px] font-black uppercase tracking-widest text-indigo-500 border-b border-indigo-100 pb-2 dark:border-white/5">{{ $module }}</h5>
                                        <div class="space-y-3">
                                            @foreach($perms as $p)
                                                <label class="group flex items-center gap-3 p-3 rounded-xl border border-slate-50 transition-all hover:bg-indigo-50/50 cursor-pointer dark:border-white/5 dark:hover:bg-white/5">
                                                    <div class="relative flex items-center justify-center">
                                                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $p->id }}" class="h-4 w-4 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-0 peer appearance-none checked:bg-indigo-600 checked:border-indigo-600 transition-all">
                                                        <svg class="absolute h-3 w-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase text-slate-600 dark:text-slate-300 tracking-widest transition-colors group-hover:text-indigo-600">{{ $p->display_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-8 border-t border-slate-100 bg-slate-50 dark:border-white/5 dark:bg-white/2 flex justify-end gap-4">
                        <button type="button" wire:click="$set('showRoleModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-6">Abort</button>
                        <button type="submit" class="rounded-2xl bg-slate-900 px-12 py-4 text-[11px] font-black uppercase text-white shadow-2xl hover:bg-indigo-600 transition-all">Synchronize security grid</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- User Role Assignment Modal --}}
    @if($showUserRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="$set('showUserRoleModal', false)" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-8 dark:border-white/5 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Identity <span class="text-indigo-500">Reprovisioning</span></h2>
                    <button wire:click="$set('showUserRoleModal', false)" class="h-8 w-8 text-slate-400 hover:text-rose-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="saveUserRoles" class="p-8 space-y-8">
                    <div class="space-y-4">
                        <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 px-2">Designated Privilege Levels</h4>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($roles as $role)
                                <label class="group flex items-center justify-between p-4 rounded-2xl border border-slate-100 transition-all hover:bg-indigo-50/50 cursor-pointer dark:border-white/5 dark:hover:bg-white/5">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-black uppercase text-slate-900 dark:text-white tracking-tight">{{ $role->display_name }}</span>
                                        <span class="text-[8px] font-black uppercase text-indigo-500 mt-0.5">{{ $role->name }}</span>
                                    </div>
                                    <div class="relative flex items-center justify-center">
                                        <input type="checkbox" wire:model="userRoles" value="{{ $role->id }}" class="h-6 w-6 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-0 peer appearance-none checked:bg-indigo-600 checked:border-indigo-600 transition-all">
                                        <svg class="absolute h-4 w-4 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-4">
                        <button type="button" wire:click="$set('showUserRoleModal', false)" class="text-[10px] font-black uppercase text-slate-500 px-6">Abort</button>
                        <button type="submit" class="rounded-2xl bg-slate-900 px-10 py-3.5 text-[10px] font-black uppercase text-white shadow-xl hover:bg-indigo-600 transition-all">Update Identity Mapping</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
