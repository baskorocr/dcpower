<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Retail - Klaim Garansi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Login Retail</h2>
                    <p class="text-gray-600">Masukkan PIN retail Anda untuk mengakses klaim garansi</p>
                </div>

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="{{ route('warranty.replacement.verify') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
                        @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PIN Retail (6 digit)</label>
                        <input type="password" name="pin" maxlength="6" pattern="[0-9]{6}" required
                            class="w-full px-4 py-3 text-center text-2xl font-mono border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••">
                        @error('pin')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-indigo-700 transition transform hover:scale-105">
                        Masuk
                    </button>
                </form>

                <div class="text-center mt-6">
                    <a href="{{ route('warranty.menu') }}" class="text-gray-600 hover:text-blue-600 transition">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
