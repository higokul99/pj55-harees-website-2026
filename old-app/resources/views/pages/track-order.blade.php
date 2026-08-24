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
.accordion-content.hidden {
    display: none;
}
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endpush

@section('content')
<main class="pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-12 animate-fade-in">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-900 to-slate-900 bg-clip-text text-transparent mb-4">
                Track Your Order
            </h1>
            <p class="text-gray-600 text-lg">Follow your precious jewellery's journey to you</p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Tracking Information Card -->
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-yellow-400/20 animate-fade-in">
                    <div class="p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-amber-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search text-blue-900 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Order Tracking Information</h2>
                        </div>

                        <div class="space-y-6 text-gray-700">
                            <div class="space-y-4">
                                <h3 class="text-xl font-semibold text-slate-900">How Our Order Tracking Works</h3>
                                <p>At Harees Jewellery, we provide comprehensive tracking for all your orders. Once your order is placed and processed, you'll be able to follow every step of its journey until it reaches your hands.</p>
                                
                                <div class="bg-gradient-to-r from-yellow-400/10 to-amber-100/20 p-6 rounded-xl border border-yellow-400/20">
                                    <h4 class="text-lg font-semibold text-slate-900 mb-3">Tracking Features:</h4>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check-circle text-yellow-400 mt-1"></i>
                                            <span><strong>Real-time updates:</strong> Get live updates on your order status from processing to delivery</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check-circle text-yellow-400 mt-1"></i>
                                            <span><strong>Detailed tracking:</strong> See exactly where your order is in our fulfillment process</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check-circle text-yellow-400 mt-1"></i>
                                            <span><strong>Delivery notifications:</strong> Receive alerts when your order is out for delivery and when it's delivered</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-check-circle text-yellow-400 mt-1"></i>
                                            <span><strong>Complete history:</strong> Access your full order history with tracking information</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-xl font-semibold text-slate-900">Delivery Timeframes</h3>
                                <p>Our standard delivery takes 3-5 business days after dispatch. For custom jewelry items, please allow 7-10 business days for crafting before dispatch. You'll receive notifications at each stage of the process.</p>
                                
                                <div class="bg-gradient-to-r from-blue-100/20 to-indigo-100/20 p-6 rounded-xl border border-blue-200">
                                    <h4 class="text-lg font-semibold text-slate-900 mb-3">Delivery Options:</h4>
                                    <ul class="space-y-3">
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-shipping-fast text-blue-900 mt-1"></i>
                                            <span><strong>Standard Delivery:</strong> 3-5 business days</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-bolt text-blue-900 mt-1"></i>
                                            <span><strong>Express Delivery:</strong> 1-2 business days (Additional charges apply)</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-store text-blue-900 mt-1"></i>
                                            <span><strong>Store Pickup:</strong> Available at our showrooms (Ready in 1-2 days)</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            @auth
                                <a href="/umyorders" class="block w-full bg-gradient-to-r from-blue-900 to-slate-900 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-800 hover:to-slate-800 transition-all duration-300 flex items-center justify-center gap-2">
                                    <i class="fas fa-box-open"></i>
                                    View My Orders
                                </a>
                            @else
                                <div class="space-y-4">
                                    <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-blue-900 to-slate-900 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-800 hover:to-slate-800 transition-all duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Login to Track Your Orders
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <!-- FAQ Accordion -->
                        <div class="space-y-4 mt-8">
                            <h3 class="text-xl font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-question-circle text-yellow-400"></i>
                                Frequently Asked Questions
                            </h3>
                            
                            <div class="accordion-item border border-gray-200 rounded-lg overflow-hidden">
                                <button class="accordion-button w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                    <span class="font-medium text-gray-800">How long does delivery usually take?</span>
                                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                                </button>
                                <div class="accordion-content hidden px-4 pb-4 pt-2 bg-white">
                                    <p class="text-gray-700">Standard delivery takes 3-5 business days after dispatch. For custom jewelry items, please allow 7-10 business days for crafting before dispatch.</p>
                                </div>
                            </div>
                            
                            <div class="accordion-item border border-gray-200 rounded-lg overflow-hidden">
                                <button class="accordion-button w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                    <span class="font-medium text-gray-800">Can I change my delivery address after ordering?</span>
                                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                                </button>
                                <div class="accordion-content hidden px-4 pb-4 pt-2 bg-white">
                                    <p class="text-gray-700">Address changes are possible only before your order is dispatched. Please contact our customer support immediately if you need to change your delivery address.</p>
                                </div>
                            </div>
                            
                            <div class="accordion-item border border-gray-200 rounded-lg overflow-hidden">
                                <button class="accordion-button w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                                    <span class="font-medium text-gray-800">What should I do if my order is delayed?</span>
                                    <i class="fas fa-chevron-down transition-transform duration-300"></i>
                                </button>
                                <div class="accordion-content hidden px-4 pb-4 pt-2 bg-white">
                                    <p class="text-gray-700">If your order is delayed beyond the estimated delivery date, please check the tracking information first. If there are no updates, contact our support team with your order number for assistance.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Help Card -->
                    <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-slate-900 rounded-2xl shadow-2xl p-6 text-white animate-slide-up border border-yellow-400/20">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-yellow-400 flex items-center gap-2">
                                <i class="fas fa-question-circle"></i>
                                NEED HELP?
                            </h3>
                        </div>

                        <div class="space-y-4 mb-6">
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

                    <!-- Order Tips Card -->
                    <div class="bg-gradient-to-br from-yellow-400 via-amber-500 to-yellow-600 rounded-2xl shadow-2xl p-6 text-blue-900 animate-slide-up border border-yellow-400/20">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-blue-900 flex items-center gap-2">
                                <i class="fas fa-lightbulb"></i>
                                ORDER TIPS
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-box-open text-blue-900 text-sm"></i>
                                </div>
                                <p class="font-medium">Always inspect your package upon delivery</p>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-video text-blue-900 text-sm"></i>
                                </div>
                                <p class="font-medium">Record unboxing video for valuable items</p>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-white/30 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-id-card text-blue-900 text-sm"></i>
                                </div>
                                <p class="font-medium">Keep ID proof ready for verification</p>
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

@push('scripts')
<script>
    // Accordion functionality
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', () => {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i');
            
            // Toggle content
            content.classList.toggle('hidden');
            
            // Rotate icon
            icon.classList.toggle('rotate-180');
            
            // Change background
            button.classList.toggle('bg-gray-50');
            button.classList.toggle('bg-gray-100');
        });
    });
</script>
@endpush
@endsection
