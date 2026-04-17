<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg rotate-3">
            <i class="fas fa-coffee text-white text-4xl"></i>
        </div>
        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Kopi Ngaji</h2>
        <p class="text-sm font-bold text-amber-600 uppercase tracking-widest mt-1">Admin Panel Login</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-amber-500 transition-colors">
                    <i class="fas fa-envelope"></i>
                </div>
                <input id="email" 
                       class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 transition-all border shadow-sm placeholder:text-gray-300" 
                       type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@kopingaji.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Password</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-amber-500 transition-colors">
                    <i class="fas fa-lock"></i>
                </div>
                <input id="password" 
                       class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 transition-all border shadow-sm placeholder:text-gray-300"
                       type="password"
                       name="password"
                       required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
                <span class="ms-2 text-sm text-gray-500 font-medium">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-bold text-amber-600 hover:text-amber-700 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-black py-4 rounded-xl shadow-lg shadow-amber-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <span>MASUK KE PANEL</span>
                <i class="fas fa-arrow-right text-xs opacity-50"></i>
            </button>
        </div>
    </form>
</x-guest-layout>
