@extends('layouts.app')

@push('head_scripts')
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'dark-blue': '#1e3a8a',
                    'darker-blue': '#1e40af',
                    'navy': '#0f172a',
                    'golden': '#fbbf24',
                    'light-golden': '#fcd34d',
                },
                animation: {
                    'fade-in': 'fadeIn 0.5s ease-in-out',
                    'slide-up': 'slideUp 0.3s ease-out',
                    'pulse-slow': 'pulse 3s infinite',
                },
            }
        }
    }
</script>
@endpush

@section('content')
<div class="w-full bg-gray-50 min-h-screen pt-24 pb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <form id="checkoutForm" class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-golden/20 animate-fade-in" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <!-- Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-dark-blue to-navy bg-clip-text text-transparent mb-2">
                            Your Details
                        </h1>
                        <p class="text-gray-600">Required to Save Cart and Send Order Updates</p>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-8">
                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none"
                                    value="{{ old('email', $user->email) }}" />
                                <label for="email" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    Email Address *
                                </label>
                            </div>

                            <!-- Mobile -->
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-mobile-alt text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="tel"
                                    id="mobile"
                                    name="mobile"
                                    required
                                    pattern="[0-9]{10}"
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none"
                                    value="{{ old('mobile', $user->phone) }}" />
                                <label for="mobile" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    Mobile Number *
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Type -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-navy mb-2">Delivery Type</h2>
                        <p class="text-gray-600 mb-6">Please choose preferred type of delivery.</p>

                        <div class="space-y-4">
                            <!-- Home Delivery -->
                            <label class="block cursor-pointer group">
                                <div class="flex items-center justify-between p-6 bg-white/80 border-2 border-golden/30 rounded-xl hover:border-dark-blue hover:shadow-lg transition-all duration-300 group-hover:bg-white delivery-option">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-golden/20 rounded-lg group-hover:bg-golden/30 transition-colors">
                                            <i class="fas fa-home text-dark-blue text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-navy group-hover:text-dark-blue transition-colors">
                                                Home Delivery
                                            </h3>
                                            <p class="text-sm text-gray-600">Get your order delivered to your doorstep</p>
                                        </div>
                                    </div>
                                    <input
                                        type="radio"
                                        id="delivery-home"
                                        name="delivery"
                                        value="home"
                                        checked
                                        required
                                        class="w-5 h-5 text-dark-blue border-2 border-golden focus:ring-golden focus:ring-2" />
                                </div>
                            </label>

                            <!-- Store Pickup -->
                            <label class="block cursor-pointer group">
                                <div class="flex items-center justify-between p-6 bg-white/80 border-2 border-golden/30 rounded-xl hover:border-dark-blue hover:shadow-lg transition-all duration-300 group-hover:bg-white delivery-option">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-dark-blue/10 rounded-lg group-hover:bg-dark-blue/20 transition-colors">
                                            <i class="fas fa-store text-dark-blue text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-navy group-hover:text-dark-blue transition-colors">
                                                Pick up from store
                                            </h3>
                                            <p class="text-sm text-gray-600">Buy now, pick up from our store at your convenience.</p>
                                        </div>
                                    </div>
                                    <input
                                        type="radio"
                                        id="delivery-pickup"
                                        name="delivery"
                                        value="pickup"
                                        required
                                        class="w-5 h-5 text-dark-blue border-2 border-golden focus:ring-golden focus:ring-2" />
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Address Section (Shows when Home Delivery is selected) -->
                    <div id="address-section" class="mb-8">
                        <h2 class="text-2xl font-bold text-navy mb-2">Delivery Address</h2>
                        <p class="text-gray-600 mb-6">Please provide your delivery address.</p>

                        <div class="grid md:grid-cols-2 gap-6 mb-4">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="text"
                                    id="fullname"
                                    name="fullname"
                                    required
                                    value="{{ old('fullname', $user->fullname) }}"
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none capitalize"
                                    placeholder="Enter your full name" />
                                <label for="fullname" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    Full Name *
                                </label>
                            </div>

                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-map-pin text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="text"
                                    id="pincode"
                                    name="pincode"
                                    required
                                    pattern="[0-9]{6}"
                                    value="{{ old('pincode', $user->pincode) }}"
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-yellow-400 focus:ring-4 focus:ring-yellow-200 transition-all duration-300 hover:shadow-md focus:outline-none"
                                    placeholder="Enter pincode" />

                                <label for="pincode" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    Pincode *
                                </label>
                            </div>
                        </div>

                        <div class="relative group mb-4">
                            <div class="absolute top-4 left-0 pl-4 pointer-events-none">
                                <i class="fas fa-home text-dark-blue group-focus-within:text-navy transition-colors"></i>
                            </div>
                            <textarea
                                id="address"
                                name="address"
                                required
                                rows="3"
                                class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none resize-none capitalize"
                                placeholder="Enter your complete address">{{ old('address', $user->address1) }}</textarea>
                            <label for="address" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                Complete Address *
                            </label>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-city text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    required
                                    value="{{ old('city', $user->city) }}"
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none capitalize"
                                    placeholder="Enter city" />
                                <label for="city" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    City *
                                </label>
                            </div>

                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-map text-dark-blue group-focus-within:text-navy transition-colors"></i>
                                </div>
                                <input
                                    type="text"
                                    id="state"
                                    name="state"
                                    required
                                    value="{{ old('state', $user->state) }}"
                                    class="w-full pl-12 pr-4 py-4 bg-white/70 border-2 border-golden/30 rounded-xl focus:border-dark-blue focus:ring-4 focus:ring-golden/20 transition-all duration-300 hover:shadow-md focus:outline-none capitalize"
                                    placeholder="Enter state" />
                                <label for="state" class="absolute -top-2 left-3 px-2 bg-white text-xs text-dark-blue font-medium">
                                    State *
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-navy mb-2">Payment Method</h2>
                        <p class="text-gray-600 mb-6">Choose your preferred payment method.</p>

                        <div class="space-y-4">
                            <!-- Cash on Delivery -->
                            <label class="block cursor-pointer group">
                                <div class="flex items-center justify-between p-6 bg-white/80 border-2 border-golden/30 rounded-xl hover:border-dark-blue hover:shadow-lg transition-all duration-300 group-hover:bg-white payment-option">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-navy group-hover:text-dark-blue transition-colors">
                                                Cash on Delivery (COD)
                                            </h3>
                                            <p class="text-sm text-gray-600">Pay when you receive your order</p>
                                        </div>
                                    </div>
                                    <input
                                        type="radio"
                                        id="payment-cod"
                                        name="payment_method"
                                        value="cod"
                                        checked
                                        required
                                        class="w-5 h-5 text-dark-blue border-2 border-golden focus:ring-golden focus:ring-2" />
                                </div>
                            </label>

                            <!-- PhonePe Online Payment -->
                            <label class="block cursor-pointer group">
                                <div class="flex items-center justify-between p-6 bg-white/80 border-2 border-golden/30 rounded-xl hover:border-dark-blue hover:shadow-lg transition-all duration-300 group-hover:bg-white payment-option">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                                            <svg class="w-6 h-6 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M21 7V6a3 3 0 00-3-3H6a3 3 0 00-3 3v1a3 3 0 003 3h12a3 3 0 003-3z" />
                                                <path d="M3 11v7a3 3 0 003 3h12a3 3 0 003-3v-7H3zm15 3a1 1 0 11-2 0 1 1 0 012 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-navy group-hover:text-dark-blue transition-colors flex items-center gap-2">
                                                Pay Online with PhonePe
                                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-normal">Secure</span>
                                            </h3>
                                            <p class="text-sm text-gray-600">UPI, Cards, NetBanking & More</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <i class="fab fa-cc-visa text-lg text-blue-600"></i>
                                                <i class="fab fa-cc-mastercard text-lg text-red-600"></i>
                                                <span class="text-xs text-purple-600 font-medium">UPI</span>
                                                <span class="text-xs text-gray-500">All Payment Methods</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input
                                        type="radio"
                                        id="payment-phonepe"
                                        name="payment_method"
                                        value="phonepe"
                                        required
                                        class="w-5 h-5 text-dark-blue border-2 border-golden focus:ring-golden focus:ring-2" />
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-8 border-t border-golden/20">
                        <div class="text-center group">
                            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-golden to-amber-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-gem text-dark-blue text-2xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-navy leading-tight">INDIA'S Customer friendly<br>ONLINE JEWELLERY STORE</p>
                        </div>
                        <div class="text-center group">
                            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-dark-blue to-navy rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-handshake text-golden text-2xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-navy leading-tight">TRUSTED BY REPUTED<br>INVESTORS</p>
                        </div>
                        <div class="text-center group">
                            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-golden to-yellow-500 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-rupee-sign text-dark-blue text-2xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-navy leading-tight">TRANSPARENT<br>PRICING</p>
                        </div>
                        <div class="text-center group">
                            <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-dark-blue to-darker-blue rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <i class="fas fa-certificate text-golden text-2xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-navy leading-tight">100% CERTIFIED<br>JEWELLERY</p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-8">
                    <!-- Order Summary -->
                    <div class="bg-gradient-to-br from-dark-blue via-navy to-slate-900 rounded-2xl shadow-2xl p-6 text-white animate-slide-up border border-golden/20">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-golden">ORDER SUMMARY</h3>
                            <button type="button" id="toggleDetailsBtn" class="text-golden/80 hover:text-golden transition-colors">
                                <span class="text-sm">View Details</span>
                                <i class="fas fa-chevron-down ml-2 transition-transform duration-300" id="toggleIcon"></i>
                            </button>
                        </div>

                        <div id="orderDetailsContent" class="hidden mt-4 transition-all duration-300 origin-top">
                            <ul class="space-y-1 text-sm text-gray-700 text-white !text-white">
                                @foreach($products as $product)
                                <li class="flex justify-between border-b py-1">
                                    <span>{{ $product->name ?? 'Unnamed Product' }} x {{ $product->quantity }}</span>
                                    <span>&#8377; {{ number_format($product->calculated_price * $product->quantity) }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="border-t border-golden/20 pt-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total Payable ({{ $products->count() }} Items)</span>
                                <span class="text-xl font-bold text-golden">₹ {{ number_format($finalTotal) }}</span>
                            </div>
                        </div>

                        <button type="submit" form="checkoutForm" class="w-full bg-gradient-to-r from-golden to-amber-500 text-dark-blue font-bold py-4 rounded-xl hover:from-amber-400 hover:to-golden transition-all duration-300 transform hover:scale-105 hover:shadow-lg mb-6">
                            PROCEED TO PAYMENT
                        </button>

                        <!-- Contact Info -->
                        <div class="text-center text-white/90 text-sm">
                            <p class="mb-2">Any Questions?</p>
                            <p>Please call us at <span class="font-semibold text-white-900">{{ config('shop.stores.paravur.phone1') }}</span> or
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', config('shop.stores.paravur.whatsapp')) }}" class="text-blue-600 hover:text-blue-700 font-medium">Chat with us on WhatsApp</a>
                        </div>

                        <!-- Payment Methods -->
                        <div class="mt-6 pt-6 border-t border-golden/20">
                            <div class="flex justify-center items-center space-x-3 text-golden/80">
                                <i class="fab fa-cc-visa text-2xl"></i>
                                <i class="fab fa-cc-mastercard text-2xl"></i>
                                <i class="fab fa-cc-amex text-2xl"></i>
                                <span class="text-xs bg-golden/20 px-2 py-1 rounded text-golden font-medium">RuPay</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('checkoutForm');
        const addressSection = document.getElementById('address-section');
        const deliveryRadios = document.querySelectorAll('input[name="delivery"]');

        // Toggle address section based on delivery type
        function toggleAddressSection() {
            const homeDelivery = document.getElementById('delivery-home').checked;
            if (homeDelivery) {
                addressSection.style.display = 'block';
                // Make address fields required
                document.querySelectorAll('#address-section input, #address-section textarea').forEach(field => {
                    field.required = true;
                });
            } else {
                addressSection.style.display = 'none';
                // Remove required from address fields
                document.querySelectorAll('#address-section input, #address-section textarea').forEach(field => {
                    field.required = false;
                });
            }
        }

        // Initial toggle
        toggleAddressSection();

        // Listen for delivery type changes
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', toggleAddressSection);
        });

        // Radio button interactions
        deliveryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove active state from all options
                document.querySelectorAll('.delivery-option').forEach(option => {
                    option.classList.remove('border-dark-blue', 'bg-golden/10', 'shadow-lg');
                });

                // Add active state to selected option
                if (this.checked) {
                    const container = this.closest('label').querySelector('.delivery-option');
                    container.classList.add('border-dark-blue', 'bg-golden/10', 'shadow-lg');
                }
            });
        });

        // Set initial active state
        const checkedRadio = document.querySelector('input[name="delivery"]:checked');
        if (checkedRadio) {
            const container = checkedRadio.closest('label').querySelector('.delivery-option');
            container.classList.add('border-dark-blue', 'bg-golden/10', 'shadow-lg');
        }

        // Payment method interactions
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove active state from all payment options
                document.querySelectorAll('.payment-option').forEach(option => {
                    // Only target payment options, not delivery options
                    const isPaymentOption = option.closest('label').querySelector('input[name="payment_method"]');
                    if (isPaymentOption) {
                        option.classList.remove('border-dark-blue', 'bg-purple-50', 'shadow-lg');
                    }
                });

                // Add active state to selected payment option
                if (this.checked) {
                    const container = this.closest('label').querySelector('.payment-option');
                    container.classList.add('border-dark-blue', 'bg-purple-50', 'shadow-lg');
                }
            });
        });

        // Set initial active state for payment method
        const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
        if (checkedPayment) {
            const container = checkedPayment.closest('label').querySelector('.payment-option');
            container.classList.add('border-dark-blue', 'bg-purple-50', 'shadow-lg');
        }

        // Form submission logic can be expanded here if needed
        form.addEventListener('submit', function(e) {
            // e.preventDefault(); 
            // The format is standard HTML5 validation + backend validation.
            // Let it submit normally to route('checkout.process')
        });

        // Input validation on type (Email/Mobile/Pincode same as legacy)
        // ... (Referencing Legacy Code Logic)
        document.getElementById('mobile').addEventListener('input', function() {
            let value = this.value.replace(/[^\d+]/g, '');
            if (!value.startsWith('+')) {
                // value = '+' + value; // Legacy added +? Let's check user inputs mostly without + in India
                // Legacy code: if (!value.startsWith('+')) { value = '+' + value; }
                // I will skip enforcing + for now unless critical, but legacy had it.
            }
            // this.value = value;
        });

        document.getElementById('pincode').addEventListener('input', function() {
            this.value = this.value.replace(/[^\d]/g, '').slice(0, 6);
        });

        // View Details Toggle
        const toggleBtn = document.getElementById('toggleDetailsBtn');
        const content = document.getElementById('orderDetailsContent');
        const icon = document.getElementById('toggleIcon');

        toggleBtn.addEventListener('click', function() {
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });
</script>
@endpush
@endsection