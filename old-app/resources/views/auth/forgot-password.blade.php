@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Forgot Password</h1>
            <p class="text-gray-600">Enter your phone number to reset your password</p>

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="phone" class="block text-gray-700 text-sm font-medium mb-2">
                    Phone Number
                </label>
                <input type="text" id="phone" name="phone" required
                    pattern="\d{10}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition-all duration-200 @error('phone') border-red-500 @enderror"
                    placeholder="Enter your phone number"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    value="{{ old('phone') }}">
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" name="verify_phone"
                class="w-full bg-yellow-400 text-white font-semibold py-3 px-4 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105">
                Continue
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-600">
                Remember your password? 
                <a href="{{ route('login') }}" class="text-yellow-500 hover:text-yellow-400 font-semibold transition-colors duration-200">
                    Login Here
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
