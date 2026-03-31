{{-- Public top navbar --}}
<div class="fixed top-0 left-0 right-0 z-50 flex justify-center py-0 transition-all duration-700 ease-in-out"
     x-data="{ scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="scrolled ? 'pt-2' : 'pt-0'">
    
    <nav :class="scrolled 
            ? 'w-[98%] max-w-7xl rounded-[2.5rem] border border-white/40 bg-white/60 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.08)] backdrop-blur-3xl px-8 h-14' 
            : 'w-full border-b border-slate-100/50 bg-white/40 backdrop-blur-2xl px-12 h-20'"
         class="mx-auto flex flex-none items-center justify-between transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1)">

        {{-- Brand --}}
        <a href="/" class="flex items-center gap-3 transition-all duration-700 group"
           :class="scrolled ? 'scale-90' : 'scale-100'">
            <div class="relative flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 overflow-hidden shadow-2xl shadow-slate-900/10 transition-transform group-hover:rotate-6">
                <div class="absolute inset-0 bg-gradient-to-br from-violet-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <span class="relative text-lg font-black tracking-tighter text-white">PF</span>
            </div>
            <span class="hidden text-xl font-black tracking-tight text-slate-900 leading-none sm:block lg:text-2xl">People<span class="text-violet-600">Flow.</span></span>
        </a>

        {{-- Center Navigation --}}
        <nav class="hidden md:flex items-center gap-1">
            @foreach([
                ['Features', 'public.features'],
                ['Solutions', 'public.solutions'],
                ['Pricing', 'public.pricing'],
                ['Documentation', 'public.docs']
            ] as $item)
                <a href="{{ route($item[1]) }}" 
                   class="px-5 py-2 rounded-full text-[9px] font-black uppercase tracking-[0.2em] transition-all duration-500 hover:text-slate-900 relative group overflow-hidden"
                   :class="{{ request()->routeIs($item[1]) ? 'true' : 'false' }} ? 'text-slate-950 bg-white/50 shadow-sm' : 'text-slate-400 hover:bg-white/30'">
                    <span class="relative z-10">{{ $item[0] }}</span>
                    @if(request()->routeIs($item[1]))
                        <div class="absolute inset-0 bg-gradient-to-r from-violet-50 to-indigo-50 -z-0"></div>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-4">
            @if(!request()->routeIs('login'))
                <a href="{{ route('login') }}" 
                   class="group relative overflow-hidden rounded-full bg-slate-950 px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 active:scale-[0.98]"
                   :class="scrolled ? 'scale-95' : 'scale-100'">
                    <span class="relative z-10 transition-colors">Log In</span>
                </a>
            @endif

            @if(!request()->routeIs('company-signup.create'))
                <a href="{{ route('company-signup.create') }}" 
                    class="group relative overflow-hidden rounded-full bg-slate-950 px-6 py-2.5 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-slate-900/20 transition-all hover:pr-10 active:scale-[0.98]"
                    :class="scrolled ? 'scale-95' : 'scale-100'">
                    <span class="relative z-10">Sign Up</span>
                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 h-3 w-3 opacity-0 group-hover:opacity-100 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            @endif
        </div>
    </nav>
</div>
{{-- Spacer to prevent layout shift --}}
<div class="h-20"></div>
