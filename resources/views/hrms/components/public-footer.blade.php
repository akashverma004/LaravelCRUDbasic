{{-- Public footer component --}}
<footer class="relative mt-12 pb-12 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-12 pt-12 border-t border-slate-100">
            {{-- Brand Section --}}
            <div class="space-y-6">
                <a href="/" class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 shadow-lg shadow-violet-500/20">
                        <span class="text-lg font-black tracking-tight text-white">PF</span>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-slate-900 leading-none">PeopleFlow</span>
                </a>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose">
                    The next-generation human capital terminal. Built for modern teams that value speed, security, and precision in workforce management.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="h-8 w-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-violet-50 hover:text-violet-600 transition-all">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="h-8 w-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-violet-50 hover:text-violet-600 transition-all">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.6.113.793-.26.793-.577v-2.234c-3.338.726-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Links Sections --}}
            <div>
                <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900 mb-8">Platform</h5>
                <ul class="space-y-4">
                    <li><a href="{{ route('public.features') }}" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Features</a></li>
                    <li><a href="{{ route('public.solutions') }}" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Solutions</a></li>
                    <li><a href="{{ route('public.pricing') }}" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Pricing</a></li>
                </ul>
            </div>

            <div>
                <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900 mb-8">Resources</h5>
                <ul class="space-y-4">
                    <li><a href="{{ route('public.docs') }}" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Documentation</a></li>
                    <li><a href="#" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Security Ops</a></li>
                    <li><a href="#" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">System Status</a></li>
                </ul>
            </div>

            <div>
                <h5 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900 mb-8">Legal</h5>
                <ul class="space-y-4">
                    <li><a href="#" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Privacy Protocol</a></li>
                    <li><a href="#" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-violet-600 transition-colors">Data Processing</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-50 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-[9px] font-black uppercase tracking-[0.5em] text-slate-300">
                &copy; {{ date('Y') }} PeopleFlow Institutional. All Rights Reserved.
            </p>
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">All Nodes Operational</span>
                </span>
            </div>
        </div>
    </div>
</footer>
