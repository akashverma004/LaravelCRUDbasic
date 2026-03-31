<div class="space-y-5 pb-8 relative">
    {{-- High-Impact Glass Header --}}
    <div class="relative overflow-hidden rounded-xl bg-white/80 px-6 py-5 shadow-sm border border-slate-200 backdrop-blur-xl dark:bg-slate-900/60 dark:border-white/5">
        <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-violet-500/5 blur-[80px]"></div>
        
        <div class="relative flex flex-col items-start justify-between gap-4 lg:flex-row lg:items-center text-center lg:text-left">
            <div>
                <div class="flex items-center justify-center lg:justify-start gap-2 mb-0.5">
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-violet-600 dark:text-violet-400">Institutional</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400">Access Lattice</span>
                </div>
                <h1 class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase transition-all">
                    System <span class="text-violet-500">Nodes</span>
                </h1>
                <p class="mt-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-widest opacity-80 leading-none">
                    Manage identity provisioning and administrative privilege tiers.
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-2.5">
                <button wire:click="$set('showInviteModal', true)" class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 text-[9px] font-black uppercase tracking-widest text-slate-600 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800">
                    <span>Deploy Invite</span>
                </button>
                <button wire:click="$set('showUserModal', true)" class="inline-flex h-10 items-center gap-2 rounded-lg bg-slate-900 px-5 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:bg-violet-600 transition-all active:scale-95 dark:bg-white/10 dark:hover:bg-violet-500/20 dark:hover:text-violet-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="3.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    <span>Provision User</span>
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Active Nodes --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="flex items-center justify-between px-1">
                <h4 class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400">Active Identity Matrix</h4>
                <div class="relative w-48">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Filter nodes..." class="w-full rounded-lg border border-slate-100 bg-white pl-8 pr-3 py-1.5 text-[8px] font-black text-slate-900 dark:border-white/5 dark:bg-slate-900/50 dark:text-white uppercase tracking-widest transition-all focus:ring-1 focus:ring-violet-500/10">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @foreach($users as $user)
                    <div wire:key="node-{{ $user->id }}" class="group relative flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm dark:border-white/5 dark:bg-slate-900 transition-all hover:border-violet-500/30">
                        <div class="h-8 w-8 rounded-lg bg-white flex items-center justify-center font-black dark:bg-white/5 uppercase text-slate-400 text-[10px] shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" class="h-full w-full object-cover">
                            @else
                                {{ substr($user->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight truncate leading-none mb-1">{{ $user->name }}</h4>
                            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="px-1.5 py-0.5 rounded-md bg-violet-50 text-[6px] font-black uppercase tracking-widest text-violet-600 dark:bg-violet-500/10 dark:text-violet-400 border border-violet-100 dark:border-violet-500/20 shadow-sm">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="openRoleModal({{ $user->id }})" class="p-1.5 rounded-lg text-slate-300 hover:text-violet-500 transition-all">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($users->hasPages())
                <div class="p-1">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- Pending Signals --}}
        <div class="lg:col-span-1 space-y-5">
            <h4 class="text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 px-1">Pending Acquisition Signals</h4>
            
            <div class="space-y-3">
                @forelse($invitations as $inv)
                    <div wire:key="inv-{{ $inv->id }}" class="group rounded-xl border border-dashed border-slate-200 bg-white p-4 shadow-sm dark:border-white/5 dark:bg-slate-900/50 hover:border-cyan-500/30 transition-all">
                        <div class="flex items-start justify-between mb-2">
                            <div class="min-w-0">
                                <h4 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight truncate">{{ $inv->name ?: 'Target Identity' }}</h4>
                                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ $inv->email }}</p>
                            </div>
                            <span class="px-1.5 py-0.5 rounded-md bg-cyan-50 text-[6px] font-black uppercase tracking-widest text-cyan-600 dark:bg-cyan-500/10 border border-cyan-100">{{ $inv->role_name }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between text-[7px] font-black uppercase tracking-widest text-slate-400">
                            <div class="flex items-center gap-1">
                                <span class="h-1 w-1 rounded-full bg-cyan-500 animate-pulse"></span>
                                <span>Expires {{ $inv->expires_at->diffForHumans() }}</span>
                            </div>
                            <button wire:click="deleteInvitation({{ $inv->id }})" wire:confirm="Revoke token?" class="text-rose-400 hover:text-rose-600 transition-colors">Revoke</button>
                        </div>

                        <div class="mt-3 pt-2.5 border-t border-slate-50 dark:border-white/5 opacity-40 group-hover:opacity-100 transition-opacity">
                            <div class="relative">
                                <input type="text" readonly value="{{ route('tenant-invitations.accept', $inv->token) }}" class="w-full rounded-lg bg-slate-50 border-none px-2.5 py-1.5 text-[6px] font-black text-slate-400 dark:bg-white/5 uppercase select-all focus:ring-0 shadow-inner">
                                <div class="absolute right-2 top-1.5 text-[5px] font-black text-slate-300">LINK_VECTOR</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border-2 border-dashed border-slate-100 p-8 flex flex-col items-center justify-center text-center dark:border-white/5 opacity-50">
                        <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] leading-loose">No active recruitment nodes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Provisioning Modal (Standardized) --}}
    @if($showUserModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div wire:click="$set('showUserModal', false)" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-5 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                    <h2 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">Provision <span class="text-violet-500">Identity</span></h2>
                    <button wire:click="$set('showUserModal', false)" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="createUser" class="p-5 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Full Identity</label>
                        <input wire:model="name" type="text" placeholder="Identity Label..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-violet-500 focus:ring-4 focus:ring-violet-500/10 dark:border-white/5 dark:bg-white/10 dark:text-white uppercase transition-all">
                        @error('name') <span class="text-[7px] font-black text-rose-500 uppercase ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Contact Vector (Email)</label>
                        <input wire:model="email" type="email" placeholder="Identifier..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-violet-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                        @error('email') <span class="text-[7px] font-black text-rose-500 uppercase ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Access Level</label>
                            <select wire:model="roleName" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-violet-500 dark:border-white/5 dark:bg-white/10 dark:text-white transition-all">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Cipher</label>
                            <input wire:model="password" type="password" placeholder="Cipher..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-violet-500 dark:border-white/5 dark:bg-white/10 dark:text-white">
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 flex justify-end gap-3 dark:border-white/5 bg-slate-50/50 -mx-5 -mb-5 p-5 dark:bg-white/5 mt-6">
                        <button type="button" wire:click="$set('showUserModal', false)" class="text-[9px] font-black uppercase text-slate-400 px-4">Abort</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-6 py-2 text-[9px] font-black uppercase text-white shadow-xl hover:bg-violet-600 transition-all active:scale-95">Deploy Node</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Invitation Deployment Modal --}}
    @if($showInviteModal)
        {{-- Standardized compact structure ... --}}
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div wire:click="$set('showInviteModal', false)" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-5 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                    <h2 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">Deploy <span class="text-cyan-500">Recruitment Signal</span></h2>
                    <button wire:click="$set('showInviteModal', false)" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <form wire:submit="inviteUser" class="p-5 space-y-4">
                    <div class="space-y-1">
                        <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Target Identifier (Email)</label>
                        <input wire:model="inviteEmail" type="email" placeholder="Recipient Identity..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white">
                        @error('inviteEmail') <span class="text-[7px] font-black text-rose-500 uppercase ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Identity Label</label>
                            <input wire:model="inviteName" type="text" placeholder="Known As..." class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white uppercase">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[8px] font-black uppercase text-slate-400 tracking-widest ml-1">Tier Assignment</label>
                            <select wire:model="inviteRoleName" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-[11px] font-bold text-slate-900 focus:border-cyan-500 dark:border-white/5 dark:bg-white/10 dark:text-white uppercase">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-500/10 rounded-lg p-3 border border-indigo-100 dark:border-indigo-500/20">
                        <p class="text-[8px] font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-widest leading-relaxed">System will emit a unique recruitment token valid for 7 atmospheric cycles across all sectors.</p>
                    </div>

                    <div class="border-t border-slate-100 pt-5 flex justify-end gap-3 dark:border-white/5 bg-slate-50/50 -mx-5 -mb-5 p-5 dark:bg-white/5 mt-6">
                        <button type="button" wire:click="$set('showInviteModal', false)" class="text-[9px] font-black uppercase text-slate-400 px-4">Abort</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-6 py-2 text-[9px] font-black uppercase text-white shadow-xl hover:bg-cyan-600 transition-all active:scale-95">Send Signal</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Privilege Recalibration (Role) Modal --}}
    @if($showRoleModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div wire:click="$set('showRoleModal', false)" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl dark:bg-slate-950 border border-slate-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="border-b border-slate-100 p-5 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 flex justify-between items-center">
                    <h2 class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-[0.2em]">Recalibrate <span class="text-violet-500">Privileges</span></h2>
                    <button wire:click="$set('showRoleModal', false)" class="text-slate-400 hover:text-slate-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                <div class="p-5 space-y-5">
                    <div class="space-y-3">
                        <p class="px-1 text-[8px] font-black text-slate-400 uppercase tracking-widest opacity-80">Select Access Vectors:</p>
                        <div class="grid gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer dark:border-white/5 dark:hover:bg-white/5 transition-all group">
                                    <input type="checkbox" wire:model="userRoles" value="{{ $role->id }}" class="rounded text-violet-600 focus:ring-violet-500/20 border-slate-200 dark:bg-slate-900">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none mb-1 group-hover:text-violet-600 transition-colors">{{ $role->name }}</p>
                                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ $role->display_name }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 flex justify-end gap-3 dark:border-white/5 bg-slate-50/50 -mx-5 -mb-5 p-5 dark:bg-white/5 mt-6">
                        <button type="button" wire:click="$set('showRoleModal', false)" class="text-[9px] font-black uppercase text-slate-400 px-4">Abort</button>
                        <button wire:click="saveUserRoles" class="rounded-lg bg-slate-900 px-6 py-2 text-[9px] font-black uppercase text-white shadow-xl hover:bg-violet-600 transition-all active:scale-95">Archiving Changes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
