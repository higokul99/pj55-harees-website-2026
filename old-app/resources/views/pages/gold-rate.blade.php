@extends('layouts.app')

@push('styles')
<style>
    .gold-gradient-text {
        background: linear-gradient(135deg, #fbbf24, #fcd34d, #fbbf24);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .gold-accent {
        height: 2px;
        background: linear-gradient(90deg, transparent, #fbbf24, #fcd34d, #fbbf24, transparent);
    }
    .rate-card {
        transition: all 0.3s ease;
    }
    .rate-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.15);
    }
    .price-badge {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .price-badge::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -60%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to right,
            rgba(255, 255, 255, 0) 20%,
            rgba(255, 255, 255, 0.8) 50%,
            rgba(255, 255, 255, 0) 80%
        );
        transform: rotate(15deg);
        transition: all 0.7s ease;
    }
    .price-badge:hover::after {
        left: 100%;
    }
    .market-card {
        transition: all 0.3s ease;
    }
    .market-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    }
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
        <!-- Hero Section -->
        <div class="text-center mb-6 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-bold gold-gradient-text mb-4">
                Today's Gold Rates
            </h1>
            <div class="gold-accent w-48 mx-auto my-4 rounded-full"></div>
            <div class="inline-flex items-center bg-gradient-to-r from-yellow-400/20 to-amber-200/30 border-2 border-yellow-400/30 text-blue-900 px-6 py-3 rounded-full shadow-lg">
                <i class="fas fa-sync-alt mr-2 text-yellow-400"></i>
                <strong>{{ \Carbon\Carbon::parse($date)->format('F j, Y \a\t h:i A') }}</strong>
            </div>
        </div>

        <!-- Price Cards (Premium Compact) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- 18KT Gold (Blue Gradient) -->
            <div class="bg-gradient-to-br from-blue-900 via-slate-900 to-slate-900 rounded-2xl shadow-lg p-5 text-white animate-slide-up border-2 border-yellow-400/30 hover:shadow-xl transition-all duration-300">
                <div class="text-center mb-2">
                    <h3 class="text-2xl font-bold text-white">18 KT Gold</h3>
                    <p class="text-white/90 text-sm italic">(750 Pure)</p>
                </div>
                
                <div class="space-y-4 mt-4">
                    <div class="bg-white/15 backdrop-blur-md p-4 rounded-xl border border-yellow-400/30 text-center transform hover:scale-[1.02] transition-transform">
                        <div class="text-xs font-semibold text-white/90 uppercase tracking-widest mb-1">Per Gram</div>
                        <div class="text-2xl font-bold text-white">
                            <span class="text-yellow-400">₹</span>{{ $rate_18k }}<span class="text-white/80 text-sm ml-1">/g</span>
                        </div>
                    </div>
                    
                    <div class="bg-white/15 backdrop-blur-md p-4 rounded-xl border border-yellow-400/30 text-center transform hover:scale-[1.02] transition-transform">
                        <div class="text-xs font-semibold text-white/90 uppercase tracking-widest mb-1">Per Pavan (8g)</div>
                        <div class="text-2xl font-bold text-white">
                            <span class="text-yellow-400">₹</span>{{ $rate_18k * 8 }}
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center mt-5">
                    <span class="px-4 py-2 bg-white/90 {{ $change_18k >= 0 ? 'text-green-800' : 'text-red-800' }} font-bold rounded-full text-sm flex items-center shadow-md hover:shadow-lg transition-shadow">
                        <i class="fas {{ $change_18k >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2 animate-pulse"></i>
                        {{ ($change_18k >= 0 ? '+' : '') . $change_18k }} 
                        <span class="text-xs ml-1">({{ ($change_percent_18k >= 0 ? '+' : '') . $change_percent_18k }}%)</span>
                    </span>
                </div>

            </div> 

            <!-- 22KT Gold (Golden Gradient) -->
            <div class="bg-gradient-to-br from-yellow-400 via-amber-500 to-yellow-600 rounded-2xl shadow-lg p-5 text-blue-900 animate-slide-up border-2 border-yellow-400/30 hover:shadow-xl transition-all duration-300">
                <div class="text-center mb-2">
                    <h3 class="text-2xl font-bold text-blue-900">22 KT Gold</h3>
                    <p class="text-blue-900/90 text-sm italic">(916 Pure)</p>
                </div>
                
                <div class="space-y-4 mt-4">
                    <div class="bg-white/25 backdrop-blur-md p-4 rounded-xl border border-yellow-400/40 text-center transform hover:scale-[1.02] transition-transform">
                        <div class="text-xs font-semibold text-blue-900/90 uppercase tracking-widest mb-1">Per Gram</div>
                        <div class="text-2xl font-bold text-blue-900">
                            <span>₹</span>{{ $rate_22k }}<span class="text-blue-900/80 text-sm ml-1">/g</span>
                        </div>
                    </div>
                    
                    <div class="bg-white/25 backdrop-blur-md p-4 rounded-xl border border-yellow-400/40 text-center transform hover:scale-[1.02] transition-transform">
                        <div class="text-xs font-semibold text-blue-900/90 uppercase tracking-widest mb-1">Per Pavan (8g)</div>
                        <div class="text-2xl font-bold text-blue-900">
                            <span>₹</span>{{ $rate_22k * 8 }}
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center mt-5">
                    <span class="px-4 py-2 bg-white/90 {{ $change_22k >= 0 ? 'text-green-800' : 'text-red-800' }} font-bold rounded-full text-sm flex items-center shadow-md hover:shadow-lg transition-shadow">
                        <i class="fas {{ $change_22k >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2 animate-pulse"></i>
                        {{ ($change_22k >= 0 ? '+' : '') . $change_22k }} 
                        <span class="text-xs ml-1">({{ ($change_percent_22k >= 0 ? '+' : '') . $change_percent_22k }}%)</span>
                    </span>
                </div>
            </div>
            
        </div>

         <!-- Market Insights Section -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-8 mb-12 border border-yellow-400/20 animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-900 to-slate-900 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-chart-line text-yellow-400 text-lg"></i>
                        </div>
                        Market Insights
                    </h2>
                    <p class="text-gray-600 mt-2">Current trends and analysis for gold</p>
                </div>
                <div class="flex space-x-3 mt-4 md:mt-0">
                    <button class="px-4 py-2 bg-gradient-to-r from-blue-900 to-slate-900 text-white rounded-lg font-medium transition-all duration-300 ">
                        View Historical Data
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- 24h Change Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200 market-card">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center mr-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800">24 hour Change</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="text-gray-700">
                            18 KT: 
                            <span class="{{ $change_18k >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ ($change_18k >= 0 ? '+' : '') . $change_18k }} 
                                <span class="text-xs">({{ ($change_percent_18k >= 0 ? '+' : '') . $change_percent_18k }}%)</span>
                            </span>
                            @if(!$prev) <span class="text-xs text-gray-500 ml-1">(No prev. data)</span> @endif
                        </div>
                        <div class="text-gray-700">
                            22 KT: 
                            <span class="{{ $change_22k >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ ($change_22k >= 0 ? '+' : '') . $change_22k }} 
                                <span class="text-xs">({{ ($change_percent_22k >= 0 ? '+' : '') . $change_percent_22k }}%)</span>
                            </span>
                            @if(!$prev) <span class="text-xs text-gray-500 ml-1">(No prev. data)</span> @endif
                        </div>
                        @if($prev)
                        <div class="text-xs text-gray-500 mt-2">
                            Previous day rates: 
                            ₹{{ $prev->{'18k_1gm'} }} (18K), 
                            ₹{{ $prev->{'22k_1gm'} }} (22K)
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Current Trend Card -->
                <div class="bg-gradient-to-br from-amber-50 to-yellow-50 p-6 rounded-xl border border-amber-200 market-card">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center mr-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5z" />
                                <path d="M9 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1h-2a1 1 0 01-1-1V7z" />
                                <path d="M16 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                                <path fill-rule="evenodd" d="M12 7a1 1 0 01-1.707-.707l3-3a1 1 0 011.414 1.414l-3 3A1 1 0 0112 7z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M12 13a1 1 0 01-.707.293l-3-3a1 1 0 011.414-1.414l3 3A1 1 0 0112 13z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800">Current Trend</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="text-gray-700">
                            18 KT: 
                            <span class="{{ $trend_18k == 'Bullish' ? 'text-green-600' : ($trend_18k == 'Bearish' ? 'text-red-600' : 'text-gray-600') }} font-medium">
                                {{ $trend_18k }}
                                @if($prev)
                                <span class="text-xs ml-1">
                                    ({{ ($change_18k >= 0 ? '+' : '') . $change_18k }})
                                </span>
                                @endif
                            </span>
                            @if(!$prev) <span class="text-xs text-gray-500 ml-1">(No prev. data)</span> @endif
                        </div>
                        <div class="text-gray-700">
                            22 KT: 
                            <span class="{{ $trend_22k == 'Bullish' ? 'text-green-600' : ($trend_22k == 'Bearish' ? 'text-red-600' : 'text-gray-600') }} font-medium">
                                {{ $trend_22k }}
                                @if($prev)
                                <span class="text-xs ml-1">
                                    ({{ ($change_22k >= 0 ? '+' : '') . $change_22k }})
                                </span>
                                @endif
                            </span>
                            @if(!$prev) <span class="text-xs text-gray-500 ml-1">(No prev. data)</span> @endif
                        </div>
                        <div class="text-xs text-gray-500 mt-2">
                            Current rates: 
                            ₹{{ $rate_18k }} (18K), 
                            ₹{{ $rate_22k }} (22K)
                        </div>
                    </div>
                </div>
                
                <!-- 30d Performance Card -->
                <div class="bg-gradient-to-br from-gray-50 to-slate-50 p-6 rounded-xl border border-gray-200 market-card">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mr-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                <text x="14" y="16" font-size="5" font-weight="bold" fill="currentColor">30</text>
                                <path d="M5 8h2v1H5z" />
                                <path d="M8 8h2v1H8z" />
                                <path d="M11 8h2v1h-2z" />
                                <path d="M5 10h2v1H5z" />
                                <path d="M8 10h2v1H8z" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-gray-800">30 days Performance</h4>
                    </div>
                    <div class="space-y-2">
                        <div class="text-gray-700">
                            18 KT: 
                            <span class="{{ $month_change_18k >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ ($month_change_18k >= 0 ? '+' : '') . $month_change_18k }} 
                                <span class="text-xs">({{ ($month_percent_18k >= 0 ? '+' : '') . $month_percent_18k }}%)</span>
                            </span>
                            @if(!$month_ago) <span class="text-xs text-gray-500 ml-1">(No 30d data)</span> @endif
                        </div>
                        <div class="text-gray-700">
                            22 KT: 
                            <span class="{{ $month_change_22k >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                {{ ($month_change_22k >= 0 ? '+' : '') . $month_change_22k }} 
                                <span class="text-xs">({{ ($month_percent_22k >= 0 ? '+' : '') . $month_percent_22k }}%)</span>
                            </span>
                            @if(!$month_ago) <span class="text-xs text-gray-500 ml-1">(No 30d data)</span> @endif
                        </div>
                        @if($month_ago)
                        <div class="text-xs text-gray-500 mt-2">
                            30 days ago rates: 
                            ₹{{ $month_ago->{'18k_1gm'} }} (18K), 
                            ₹{{ $month_ago->{'22k_1gm'} }} (22K)
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
