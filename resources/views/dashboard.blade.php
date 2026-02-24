@extends('layouts.glassmorphism')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white neon-text mb-2">
            {{ __('dashboard.dashboard') }}
        </h1>
        <p class="text-gray-300">
            {{ __('dashboard.welcome_back') }} - {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stats-card
            title="dashboard.total_parcels"
            value="{{ $stats['total_colis'] }}"
            icon="box"
            color="cyan"
            :trend="['direction' => 'up', 'value' => '12']"
        />
        <x-stats-card
            title="dashboard.active_vendors"
            value="{{ $stats['total_vendors'] }}"
            icon="store"
            color="green"
            :trend="['direction' => 'up', 'value' => '8']"
        />
        <x-stats-card
            title="dashboard.monthly_revenue"
            value="{{ number_format($deliveryStats['total'] ?? 0, 0, ',', ' ') }} FCFA"
            icon="money-bill-wave"
            color="purple"
            :trend="['direction' => 'up', 'value' => '23']"
        />
        <x-stats-card
            title="dashboard.pending_payments"
            value="{{ $stats['colis_deposited'] }}"
            icon="clock"
            color="orange"
            :trend="['direction' => 'down', 'value' => '5']"
        />
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Graphique des revenus -->
        <div class="glass-panel-dark p-6">
            <h3 class="text-xl font-semibold text-white mb-4">{{ __('dashboard.revenue_trend') }}</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>

        <!-- Statut des colis -->
        <div class="glass-panel-dark p-6">
            <h3 class="text-xl font-semibold text-white mb-4">{{ __('dashboard.parcel_status') }}</h3>
            <canvas id="statusChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Colis récents -->
    <div class="glass-panel-dark p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-white">{{ __('dashboard.parcels') }}</h3>
            <a href="{{ route('colis.index') }}" class="text-cyan-400 hover:text-cyan-300">
                {{ __('common.view_all') }} →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($overdueColis->take(6) as $colis)
                <x-colis-card :colis="$colis" />
            @endforeach
        </div>
    </div>
</div>

<script>
// Graphique des revenus
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: @json($chartData['labels'] ?? []),
        datasets: [{
            label: '{{ __('dashboard.revenue_trend') }}',
            data: @json($chartData['data'] ?? []),
            borderColor: 'rgb(0, 212, 255)',
            backgroundColor: 'rgba(0, 212, 255, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#ffffff' }
            }
        },
        scales: {
            y: {
                ticks: { color: '#ffffff' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            },
            x: {
                ticks: { color: '#ffffff' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' }
            }
        }
    }
});

// Graphique des statuts
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['{{ __('dashboard.deposited') }}', '{{ __('dashboard.in_transit') }}', '{{ __('dashboard.delivered_today') }}', '{{ __('dashboard.pending') }}'],
        datasets: [{
            data: [{{ $stats['colis_deposited'] }}, 15, {{ $stats['colis_paid'] }}, {{ $stats['colis_today'] }}],
            backgroundColor: [
                'rgba(0, 212, 255, 0.8)',
                'rgba(0, 255, 136, 0.8)',
                'rgba(124, 58, 237, 0.8)',
                'rgba(251, 146, 60, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#ffffff' }
            }
        }
    }
});
</script>
@endsection
