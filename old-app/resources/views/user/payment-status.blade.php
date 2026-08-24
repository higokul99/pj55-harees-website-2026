@extends('layouts.app')

@section('content')
<div class="w-full bg-gray-50 min-h-screen pt-24 pb-8 flex items-center justify-center">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        @if($status === 'success')
        {{-- Payment Success --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-green-600 mb-2">Payment Successful!</h1>
            <p class="text-gray-600 mb-6">Your payment has been processed successfully.</p>

            @if($order)
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="font-semibold text-lg mb-4">Order Details</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-semibold">#{{ $order->id }}</span>
                    </div>
                    @if(isset($transaction_id))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="font-mono text-sm">{{ $transaction_id }}</span>
                    </div>
                    @endif
                    @if(isset($amount))
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount Paid:</span>
                        <span class="font-semibold text-green-600">₹{{ number_format($amount / 100, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <a href="{{ route('orders') }}" class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-dark-blue transition-all duration-300">
                    View My Orders
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-golden text-navy font-bold py-3 rounded-xl hover:bg-amber-400 transition-all duration-300">
                    Continue Shopping
                </a>
            </div>
        </div>

        @elseif($status === 'failed')
        {{-- Payment Failed --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-red-600 mb-2">Payment Failed</h1>
            <p class="text-gray-600 mb-6">Unfortunately, your payment could not be processed.</p>

            @if($order)
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <p class="text-sm text-gray-600 mb-2">Order ID: <span class="font-semibold">#{{ $order->id }}</span></p>
                <p class="text-sm text-gray-500">Your cart items have been saved. Please try again.</p>
            </div>
            @endif

            <div class="flex gap-4">
                <a href="{{ route('checkout') }}" class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-dark-blue transition-all duration-300">
                    Try Again
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-300 transition-all duration-300">
                    Back to Home
                </a>
            </div>
        </div>

        @elseif($status === 'pending')
        {{-- Payment Pending --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-yellow-600 mb-2">Payment Pending</h1>
            <p class="text-gray-600 mb-6">Your payment is being processed. This may take a few moments.</p>

            @if($order)
            <div class="bg-yellow-50 rounded-lg p-6 mb-6">
                <p class="text-sm text-gray-700 mb-2">Order ID: <span class="font-semibold">#{{ $order->id }}</span></p>
                <p class="text-sm text-gray-600">We'll notify you once your payment is confirmed.</p>
            </div>
            @endif

            <div class="mb-6">
                <button onclick="checkStatus()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    Check Status Now
                </button>
            </div>

            <div class="flex gap-4">
                <a href="{{ route('orders') }}" class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-dark-blue transition-all duration-300">
                    View Orders
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-300 transition-all duration-300">
                    Back to Home
                </a>
            </div>
        </div>

        @else
        {{-- Error / Unknown Status --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8 text-center">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-700 mb-2">Payment Status Unknown</h1>
            <p class="text-gray-600 mb-6">{{ $message ?? 'We are unable to determine your payment status at this time.' }}</p>

            <div class="flex gap-4">
                <a href="{{ route('orders') }}" class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-dark-blue transition-all duration-300">
                    Check My Orders
                </a>
                <a href="{{ route('home') }}" class="flex-1 bg-gray-200 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-300 transition-all duration-300">
                    Back to Home
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function checkStatus() {
        @if(isset($order) && $order - > merchant_order_id)
        window.location.href = '{{ route("phonepe.status", $order->merchant_order_id) }}';
        @else
        alert('Unable to check status. Please check your orders page.');
        windowlocation.href = '{{ route("orders") }}';
        @endif
    }
</script>
@endpush
@endsection