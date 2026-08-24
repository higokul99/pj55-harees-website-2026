@extends('layouts.app')

@push('styles')
<style>
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    0% { transform: translateY(10px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}
.animate-slide-up {
    animation: slideUp 0.3s ease-out;
}
</style>
@endpush

@section('content')
<main class="pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-12 animate-fade-in">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-900 to-slate-900 bg-clip-text text-transparent mb-4">
                Frequently Asked Questions
            </h1>
            <p class="text-gray-600 text-lg">Find answers to common questions about our jewellery and services</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- FAQ Items -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-yellow-400/20 animate-fade-in p-8">
                    <div class="space-y-6">
                        <!-- Question 1 -->
                        <div class="group">
                            <div class="bg-gradient-to-r from-yellow-400/5 to-amber-100/10 p-6 rounded-xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
                                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-blue-900 text-sm font-bold">
                                        1
                                    </div>
                                    What types of jewelry do you offer?
                                </h2>
                                <p class="mt-3 ml-11 text-gray-700">We offer a wide range of jewelry including 18K gold, 22K gold, diamonds, silver, and gemstone collections. Our designs blend tradition with modern elegance.</p>
                            </div>
                        </div>

                        <!-- Question 2 -->
                        <div class="group">
                            <div class="bg-gradient-to-r from-yellow-400/5 to-amber-100/10 p-6 rounded-xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
                                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-blue-900 text-sm font-bold">
                                        2
                                    </div>
                                    Do you offer custom jewelry design?
                                </h2>
                                <p class="mt-3 ml-11 text-gray-700">Yes! Our in-house designers can work with you to create custom pieces that reflect your vision and style. Visit our Chinnakada showroom to begin your custom journey.</p>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="group">
                            <div class="bg-gradient-to-r from-yellow-400/5 to-amber-100/10 p-6 rounded-xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
                                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-blue-900 text-sm font-bold">
                                        3
                                    </div>
                                    Is your jewelry certified?
                                </h2>
                                <p class="mt-3 ml-11 text-gray-700">Absolutely. All our gold and diamond products are hallmarked and certified to ensure authenticity and purity.</p>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="group">
                            <div class="bg-gradient-to-r from-yellow-400/5 to-amber-100/10 p-6 rounded-xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
                                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-blue-900 text-sm font-bold">
                                        4
                                    </div>
                                    Do you offer exchange or buy-back?
                                </h2>
                                <p class="mt-3 ml-11 text-gray-700">Yes, we provide both exchange and buy-back options for gold and diamond jewelry, subject to our terms and conditions. Please contact our support team for more details.</p>
                            </div>
                        </div>

                        <!-- Question 5 -->
                        <div class="group">
                            <div class="bg-gradient-to-r from-yellow-400/5 to-amber-100/10 p-6 rounded-xl border border-yellow-400/20 hover:border-yellow-400/40 transition-all duration-300">
                                <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-full flex items-center justify-center text-blue-900 text-sm font-bold">
                                        5
                                    </div>
                                    How can I reach customer support?
                                </h2>
                                <p class="mt-3 ml-11 text-gray-700">You can email us at <strong class="text-blue-900">{{ config('shop.contact.email') }}</strong> or WhatsApp us at <strong class="text-blue-900">{{ config('shop.contact.phone') }}</strong>. We're happy to assist you {{ config('shop.hours.weekday') }}.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Contact Card -->
                    <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-slate-900 rounded-2xl shadow-2xl p-6 text-white animate-slide-up border border-yellow-400/20">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-yellow-400 flex items-center gap-2">
                                <i class="fas fa-headset"></i>
                                NEED HELP?
                            </h3>
                        </div>

                        <div class="space-y-4 mb-6">
                            <p class="text-white/90">
                                Didn't find your answer? Contact our support team for personalized assistance.
                            </p>

                            <a href="https://wa.me/{{ str_replace(['+', ' '], '', config('shop.contact.phone')) }}" target="_blank" class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-4 px-6 rounded-xl hover:from-green-600 hover:to-green-500 transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <i class="fab fa-whatsapp text-white text-xl group-hover:scale-110 transition-transform"></i>
                                    <span>WHATSAPP US</span>
                                </div>
                                <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                            </a>
                            
                            <a href="tel:{{ str_replace(['+', ' '], '', config('shop.contact.phone')) }}" class="block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-4 px-6 rounded-xl hover:from-blue-600 hover:to-blue-500 transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-phone-alt text-white text-xl group-hover:scale-110 transition-transform"></i>
                                    <span>CALL US</span>
                                </div>
                                <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links Card -->
                    <div class="bg-gradient-to-br from-yellow-400 via-amber-500 to-yellow-600 rounded-2xl shadow-2xl p-6 text-blue-900 animate-slide-up border border-yellow-400/20">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-blue-900 flex items-center gap-2">
                                <i class="fas fa-link"></i>
                                QUICK LINKS
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-shipping-fast text-blue-900 text-sm"></i>
                                </div>
                                <a href="{{ route('shipping-policy') }}" class="font-medium hover:underline">Shipping Information</a>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exchange-alt text-blue-900 text-sm"></i>
                                </div>
                                <a href="{{ route('return-exchange') }}" class="font-medium hover:underline">Returns & Exchanges</a>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-file-invoice-dollar text-blue-900 text-sm"></i>
                                </div>
                                <a href="{{ route('refund-policy') }}" class="font-medium hover:underline">Refund Policy</a>
                            </div>
                        </div>
                    </div>

                    <!-- Visit Us Card -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-yellow-400/20 animate-slide-up">
                        <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-store text-yellow-400"></i>
                            VISIT OUR STORE
                        </h3>
                        
                        <div class="space-y-3 text-gray-700">
                            <p class="flex items-start gap-2">
                                <i class="fas fa-clock text-yellow-400 mt-1"></i>
                                <span>Open: {{ config('shop.hours.weekday') }}</span>
                            </p>
                            <p class="flex items-start gap-2">
                                <i class="fas fa-phone text-yellow-400 mt-1"></i>
                                <span>{{ config('shop.contact.phone') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
