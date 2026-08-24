@extends('layouts.app')

@section('content')
<div class="w-full bg-gray-50 min-h-screen pt-24 pb-8 flex items-center justify-center">
    <div class="container mx-auto px-4 py-8 max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <!-- PhonePe Logo Placeholder -->
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 7V6a3 3 0 00-3-3H6a3 3 0 00-3 3v1a3 3 0 003 3h12a3 3 0 003-3z" />
                        <path d="M3 11v7a3 3 0 003 3h12a3 3 0 003-3v-7H3zm15 3a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-navy mb-4">Redirecting to PhonePe</h1>
            <p class="text-gray-600 mb-6">Please wait while we redirect you to complete your payment securely.</p>

            <div class="bg-purple-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-semibold">{{ $order->id }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-semibold text-purple-600">₹{{ number_format($amount / 100, 2) }}</span>
                </div>
            </div>

            <!-- Auto-submit form -->
            <form id="phonePeForm" action="{{ route('phonepe.initiate') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="amount" value="{{ $amount }}">

                <button type="submit" class="w-full bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition-all duration-300 transform hover:scale-105 mb-4">
                    <div class="flex items-center justify-center gap-2">
                        <span>Proceed to Payment</span>
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </button>
            </form>

            <a href="{{ route('cart') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel & Return to Cart</a>

            <!-- Trust Badge -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>TLS Secured Payment</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit after 2 seconds if user doesn't click
    setTimeout(() => {
        document.getElementById('phonePeForm').submit();
    }, 2000);
</script>
@endpush
@endsection