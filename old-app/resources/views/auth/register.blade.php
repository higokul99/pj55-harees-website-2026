@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8 mt-8">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
            <p class="text-gray-600">Fill in your details to create an account</p>

            {{-- Error message display --}}
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 mt-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" onclick="this.parentElement.parentElement.style.display='none'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </span>
                </div>
            @endif
        </div>

        {{-- Sign-up form --}}
        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf
            
            {{-- Personal Details Section --}}
            <div class="space-y-4">
                {{-- Full Name field (full width) --}}
                <div>
                    <label for="fullname" class="block text-gray-700 text-sm font-medium mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="fullname" 
                        name="fullname" 
                        required
                        pattern="[A-Za-z ]{3,50}"
                        title="Name should be 3-50 characters long and contain only letters"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('fullname') border-red-500 @enderror"
                        placeholder="Enter your full name"
                        value="{{ old('fullname') }}"
                    >
                    @error('fullname')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Other personal details in 2 columns --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Email field --}}
                    <div>
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('email') border-red-500 @enderror"
                            placeholder="Enter your email"
                            value="{{ old('email') }}"
                        >
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone field --}}
                    <div>
                        <label for="phone" class="block text-gray-700 text-sm font-medium mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            required
                            pattern="[0-9]{10}"
                            title="Please enter a 10-digit phone number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('phone') border-red-500 @enderror"
                            placeholder="Enter your phone number"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            value="{{ old('phone') }}"
                        />
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 4-digit PIN field --}}
                    <div>
                        <label for="pin" class="block text-gray-700 text-sm font-medium mb-1">
                            4-digit Number PIN <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="pin" 
                            name="password" 
                            required
                            maxlength="4"
                            pattern="\d{4}"
                            title="PIN must be exactly 4 digits"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                            placeholder="Create a 4-digit PIN"
                        >
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm 4-digit PIN field --}}
                    <div>
                        <label for="confirm_pin" class="block text-gray-700 text-sm font-medium mb-1">
                            Confirm PIN <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            id="confirm_pin" 
                            name="password_confirmation" 
                            required
                            maxlength="4"
                            pattern="\d{4}"
                            title="PIN must be exactly 4 digits"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200"
                            placeholder="Confirm your 4-digit PIN"
                        >
                    </div>
                </div>
            </div>

            {{-- Security Question Section --}}
            <div class="pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Security Question (For Password Recovery)</h3>
                <div class="grid grid-cols-1 gap-6">
                    {{-- Security Question --}}
                    <div>
                        <label for="security_question" class="block text-gray-700 text-sm font-medium mb-1">
                            Security Question <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="security_question" 
                            name="security_question" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('security_question') border-red-500 @enderror"
                        >
                            <option value="" disabled selected>Select a security question</option>
                            <option value="What was your first pet's name?" {{ old('security_question') == "What was your first pet's name?" ? 'selected' : '' }}>What was your first pet's name?</option>
                            <option value="What was the name of your first school?" {{ old('security_question') == "What was the name of your first school?" ? 'selected' : '' }}>What was the name of your first school?</option>
                            <option value="What city were you born in?" {{ old('security_question') == "What city were you born in?" ? 'selected' : '' }}>What city were you born in?</option>
                            <option value="What is your favorite book?" {{ old('security_question') == "What is your favorite book?" ? 'selected' : '' }}>What is your favorite book?</option>
                        </select>
                        @error('security_question')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Security Answer --}}
                    <div>
                        <label for="security_answer" class="block text-gray-700 text-sm font-medium mb-1">
                            Your Answer <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="security_answer" 
                            name="security_answer" 
                            required
                            pattern="[A-Za-z0-9 ]{3,50}"
                            title="Answer should be 3-50 characters long"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('security_answer') border-red-500 @enderror"
                            placeholder="Enter your answer"
                            value="{{ old('security_answer') }}"
                        >
                        @error('security_answer')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Address Section --}}
            <div class="pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Address Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Address Line 1 --}}
                    <div class="md:col-span-2">
                        <label for="address1" class="block text-gray-700 text-sm font-medium mb-1">
                            Address Line 1 <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="address1" 
                            name="address1" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('address1') border-red-500 @enderror"
                            placeholder="House/Flat No, Building"
                            value="{{ old('address1') }}"
                        >
                        @error('address1')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address Line 2 --}}
                    <div class="md:col-span-2">
                        <label for="address2" class="block text-gray-700 text-sm font-medium mb-1">
                            Address Line 2
                        </label>
                        <input 
                            type="text" 
                            id="address2" 
                            name="address2" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200"
                            placeholder="Street, Area"
                            value="{{ old('address2') }}"
                        >
                    </div>

                    {{-- Town/Village/City --}}
                    <div>
                        <label for="city" class="block text-gray-700 text-sm font-medium mb-1">
                            Town/City <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="city" 
                            name="city" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('city') border-red-500 @enderror"
                            placeholder="Enter your city"
                            value="{{ old('city') }}"
                        >
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- State --}}
                    <div>
                        <label for="state" class="block text-gray-700 text-sm font-medium mb-1">
                            State <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="state" 
                            name="state" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('state') border-red-500 @enderror"
                            placeholder="Enter your state"
                            value="{{ old('state') }}"
                        >
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pincode --}}
                    <div>
                        <label for="pincode" class="block text-gray-700 text-sm font-medium mb-1">
                            Pincode <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="pincode" 
                            name="pincode" 
                            required
                            pattern="[0-9]{6}"
                            maxlength="6"
                            title="Pincode must be 6 digits"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200 @error('pincode') border-red-500 @enderror"
                            placeholder="Enter your pincode"
                            value="{{ old('pincode') }}"
                        >
                        @error('pincode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Landmark --}}
                    <div>
                        <label for="landmark" class="block text-gray-700 text-sm font-medium mb-1">
                            Landmark
                        </label>
                        <input 
                            type="text" 
                            id="landmark" 
                            name="landmark" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200"
                            placeholder="Nearby famous location"
                            value="{{ old('landmark') }}"
                        >
                    </div>
                </div>
            </div>

            {{-- Terms and conditions --}}
            <div class="flex items-start pt-4">
                <input 
                    type="checkbox" 
                    id="terms" 
                    name="terms" 
                    required
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1"
                >
                <label for="terms" class="ml-2 text-gray-700 text-sm">
                    I agree to the 
                    <a href="{{ route('terms-conditions') }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">Terms and Conditions</a> 
                    and 
                    <a href="{{ route('privacy-policy') }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">Privacy Policy</a>
                    <span class="text-red-500">*</span>
                </label>
            </div>

            {{-- Sign-up button --}}
            <button 
                type="submit" 
                name="signup"
                class="w-full bg-yellow-400 text-white font-semibold py-3 px-4 rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 mt-6"
            >
                Create Account
            </button>
        </form>

        {{-- Divider and Login Link --}}
        <div class="flex items-center my-6">
            <div class="border-t border-gray-300 flex-grow"></div>
            <span class="flex-shrink-0 px-4 text-gray-500 text-sm">or</span>
            <div class="border-t border-gray-300 flex-grow"></div>
        </div>

        <div class="text-center">
            <p class="text-gray-600">
                Already have an account? 
                <a 
                    href="{{ route('login') }}" 
                    class="text-yellow-500 hover:text-yellow-400 font-semibold transition-colors duration-200"
                >
                    Login Here
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
