<x-guest-layout>
    <h2 class="text-center text-2xl font-semibold text-gray-700 mb-8">Iniciar Sesión</h2>

    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6" novalidate>
        @csrf

        <div>
            <label for="email" class="block mb-1 text-gray-600 font-medium">Correo electrónico</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    {{-- Icono correo --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16 12H8m8 0v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6m12 0a2 2 0 01-2 2H8a2 2 0 01-2-2v-6a2 2 0 012-2h8a2 2 0 012 2v6z" />
                    </svg>
                </span>
                <input id="email" type="email" name="email" autocomplete="email" required
                    value="{{ old('email') }}"
                    class="pl-10 pr-3 py-2 w-full border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block mb-1 text-gray-600 font-medium">Contraseña</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    {{-- Icono candado --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 11c.552 0 1 .448 1 1v2a1 1 0 01-2 0v-2c0-.552.448-1 1-1z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 11V9a7 7 0 1114 0v2" />
                    </svg>
                </span>
                <input id="password" type="password" name="password" autocomplete="current-password" required
                    class="pl-10 pr-10 py-2 w-full border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-indigo-600 focus:outline-none"
                    tabindex="-1">
                    👁️
                </button>
            </div>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-between items-center text-sm text-gray-600">
            <label class="inline-flex items-center space-x-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="hover:underline text-indigo-600">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-br from-green-800 to-amber-600 text-white py-2 rounded-md hover:from-green-900 hover:to-amber-700 transition font-semibold">
            Iniciar sesión
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🙈';
            });
        });
    </script>
</x-guest-layout>
