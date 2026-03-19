{{-- Public top navbar --}}
<div class="fixed top-0 left-0 right-0 z-50 flex justify-center py-0 transition-all duration-500 ease-in-out"
     x-data="{ scrolled: false }"
     @scroll.window="scrolled = (window.pageYOffset > 50)"
     :class="scrolled ? 'pt-4' : 'pt-0'">
    
    <nav :class="scrolled 
            ? 'w-[95%] max-w-7xl rounded-full border border-white/40 bg-white/70 shadow-[0_20px_50px_rgba(0,0,0,0.05)] backdrop-blur-2xl px-10 h-16' 
            : 'w-full border-b border-slate-100 bg-white/70 backdrop-blur-xl px-12 h-24'"
         class="mx-auto flex flex-none items-center justify-between transition-all duration-500 ease-in-out">

        {{-- Brand --}}
        <a href="/" class="flex items-center gap-3 transition-transform duration-500"
           :class="scrolled ? 'scale-90' : 'scale-100'">
            <div class="flex h-11 w-11 items-center justify-center rounded-[16px] bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/20">
                <span class="text-xl font-black tracking-tight text-white">PF</span>
            </div>
            <span class="hidden text-[1.9rem] font-black tracking-tight text-slate-900 leading-none sm:block">PeopleFlow</span>
        </a>

        {{-- Center Navigation --}}
        <nav class="hidden md:flex items-center gap-10">
            <a href="{{ route('public.features') }}" 
               class="text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-violet-600"
               :class="scrolled ? 'text-slate-500' : ({{ request()->routeIs('public.features') ? 'true' : 'false' }} ? 'text-violet-600' : 'text-slate-400')">
               Features
            </a>
            <a href="{{ route('public.solutions') }}" 
               class="text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-violet-600"
               :class="scrolled ? 'text-slate-500' : ({{ request()->routeIs('public.solutions') ? 'true' : 'false' }} ? 'text-violet-600' : 'text-slate-400')">
               Solutions
            </a>
            <a href="{{ route('public.pricing') }}" 
               class="text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-violet-600"
               :class="scrolled ? 'text-slate-500' : ({{ request()->routeIs('public.pricing') ? 'true' : 'false' }} ? 'text-violet-600' : 'text-slate-400')">
               Pricing
            </a>
            <a href="{{ route('public.docs') }}" 
               class="text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-violet-600"
               :class="scrolled ? 'text-slate-500' : ({{ request()->routeIs('public.docs') ? 'true' : 'false' }} ? 'text-violet-600' : 'text-slate-400')">
               Documentation
            </a>
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-6">
            @if(!request()->routeIs('login'))
                <a href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 hover:text-violet-600 transition-transform duration-500"
                   :class="scrolled ? 'scale-90' : 'scale-100'">
                    Log In
                </a>
            @endif

            @if(!request()->routeIs('company-signup.create'))
                <a href="{{ route('company-signup.create') }}" 
                    class="rounded-full bg-slate-900 px-6 py-3 text-[9px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-slate-900/10 transition-all hover:bg-violet-600 hover:shadow-violet-600/20 active:scale-[0.98]"
                    :class="scrolled ? 'scale-90' : 'scale-100'">
                    Register Company
                </a>
            @endif
        </div>
    </nav>
</div>
{{-- Spacer to prevent layout shift --}}
<div class="h-24"></div>
