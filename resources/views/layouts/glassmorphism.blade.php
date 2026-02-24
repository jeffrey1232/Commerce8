<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Plateforme E-Commerce Sécurisée</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            --primary-blue: #00d4ff;
            --primary-green: #00ff88;
            --accent-purple: #7c3aed;
            --text-primary: #ffffff;
            --text-secondary: #b8c5d6;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
        }

        .glass-panel-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .neon-text {
            text-shadow: 0 0 10px var(--primary-blue), 0 0 20px var(--primary-blue);
        }

        .neon-border {
            box-shadow: 0 0 20px var(--primary-blue), inset 0 0 20px rgba(0, 212, 255, 0.1);
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-online { background: var(--primary-green); box-shadow: 0 0 10px var(--primary-green); }
        .status-pending { background: #fbbf24; box-shadow: 0 0 10px #fbbf24; }
        .status-offline { background: #ef4444; box-shadow: 0 0 10px #ef4444; }

        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
        }

        .data-grid {
            display: grid;
            gap: 1rem;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .chart-container {
            position: relative;
            height: 200px;
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="text-white">
    <div class="min-h-screen p-4">
        <!-- Header -->
        <header class="glass-panel-dark p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="floating-icon">
                        <i class="fas fa-cube text-4xl text-cyan-400"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold neon-text">ECOM-BEST</h1>
                        <p class="text-sm text-gray-300">Plateforme E-Commerce Sécurisée</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="p-2 rounded-lg glass-panel hover-scale">
                        <i class="fas fa-bell text-cyan-400"></i>
                    </button>
                    <button class="p-2 rounded-lg glass-panel hover-scale">
                        <i class="fas fa-cog text-cyan-400"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-purple-500 flex items-center justify-center">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <span class="text-sm">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="data-grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Administrator Overview -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-chart-line mr-2"></i>ADMINISTRATOR OVERVIEW
                    </h2>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="glass-panel-dark p-4 text-center">
                            <div class="text-2xl font-bold text-green-400">85%</div>
                            <div class="text-xs text-gray-300">Today</div>
                            <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                                <div class="bg-green-400 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="glass-panel-dark p-4 text-center">
                            <div class="text-2xl font-bold text-red-400">5%</div>
                            <div class="text-xs text-gray-300">New</div>
                            <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                                <div class="bg-red-400 h-2 rounded-full" style="width: 5%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="chart-container mb-6">
                        <canvas id="deliveriesChart"></canvas>
                    </div>

                    <!-- Overdue Packages -->
                    <div>
                        <h3 class="text-sm font-semibold mb-3 text-yellow-400">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Overdue Packages
                        </h3>
                        <div class="space-y-2">
                            <div class="glass-panel-dark p-3 flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-mono">ECM12345678</div>
                                    <div class="text-xs text-gray-400">25,000 FCFA</div>
                                </div>
                                <button class="px-3 py-1 bg-red-500 rounded text-xs hover:bg-red-600">
                                    Resolve
                                </button>
                            </div>
                            <div class="glass-panel-dark p-3 flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-mono">ECM87654321</div>
                                    <div class="text-xs text-gray-400">15,000 FCFA</div>
                                </div>
                                <button class="px-3 py-1 bg-red-500 rounded text-xs hover:bg-red-600">
                                    Resolve
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vendor Portal -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-store mr-2"></i>VENDOR PORTAL
                    </h2>

                    <!-- New Shipment Button -->
                    <button class="w-full py-3 bg-gradient-to-r from-green-400 to-green-600 rounded-lg font-bold mb-6 hover:from-green-500 hover:to-green-700 transition-all transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2"></i>NEW SHIPMENT
                    </button>

                    <!-- Status Flow -->
                    <div class="grid grid-cols-4 gap-2 mb-6">
                        <div class="text-center">
                            <div class="glass-panel-dark p-3 rounded-full">
                                <div class="text-lg font-bold">12</div>
                            </div>
                            <div class="text-xs mt-1">PENDING</div>
                        </div>
                        <div class="text-center">
                            <div class="glass-panel-dark p-3 rounded-full">
                                <div class="text-lg font-bold">2</div>
                            </div>
                            <div class="text-xs mt-1">AT RELAY</div>
                        </div>
                        <div class="text-center">
                            <div class="glass-panel-dark p-3 rounded-full">
                                <div class="text-lg font-bold">8</div>
                            </div>
                            <div class="text-xs mt-1">SOLD</div>
                        </div>
                        <div class="text-center">
                            <div class="glass-panel-dark p-3 rounded-full">
                                <div class="text-lg font-bold">1</div>
                            </div>
                            <div class="text-xs mt-1">RETURN</div>
                        </div>
                    </div>

                    <!-- Wallet Balance -->
                    <div class="glass-panel-dark p-4">
                        <h3 class="text-sm font-semibold mb-3">WALLET BALANCE</h3>
                        <div class="text-2xl font-bold text-green-400 mb-3">150,000 FCFA</div>
                        <div class="flex space-x-2">
                            <div class="flex-1 glass-panel p-2 text-center">
                                <i class="fas fa-mobile-alt text-blue-400"></i>
                                <div class="text-xs">Wave</div>
                            </div>
                            <div class="flex-1 glass-panel p-2 text-center">
                                <i class="fas fa-wallet text-purple-400"></i>
                                <div class="text-xs">Wizall</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Validation -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-user-check mr-2"></i>CLIENT VALIDATION
                    </h2>

                    <!-- QR Code -->
                    <div class="glass-panel-dark p-8 mb-4 text-center">
                        <div class="w-32 h-32 mx-auto bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-6xl text-gray-800"></i>
                        </div>
                    </div>

                    <!-- Code Input -->
                    <div class="mb-4">
                        <label class="block text-sm mb-2">ENTER UNIQUE CODE (CRU)</label>
                        <input type="text" class="w-full p-3 glass-panel-dark rounded-lg text-white placeholder-gray-400" placeholder="Enter code...">
                    </div>

                    <!-- Payment Options -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <button class="py-2 glass-panel-dark rounded-lg hover:bg-white hover:bg-opacity-20">
                            <i class="fas fa-money-bill-wave mr-1"></i>PAY CASH
                        </button>
                        <button class="py-2 glass-panel-dark rounded-lg hover:bg-white hover:bg-opacity-20">
                            <i class="fas fa-mobile-alt mr-1"></i>MOBILE MONEY
                        </button>
                    </div>

                    <!-- Item Details -->
                    <div class="glass-panel-dark p-3">
                        <div class="text-sm font-semibold mb-1">YOUR PALACE: Warm Brute X</div>
                        <div class="text-xs text-gray-300">Item: Blue Dress</div>
                        <div class="text-xs text-cyan-400 mt-1">ECM2024001</div>
                    </div>
                </div>
            </div>

            <!-- Payment & Disbursement -->
            <div class="lg:col-span-2">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-money-bill-transfer mr-2"></i>PAYMENT & DISBURSEMENT
                    </h2>

                    <!-- Alert -->
                    <div class="glass-panel-dark p-3 mb-4 border border-red-500">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-2"></i>
                            <span class="text-red-400">ACTION REQUIRED: 1 payment overdue > 18h</span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-300">
                                    <th class="pb-3">Vendor</th>
                                    <th class="pb-3">Sale Amt.</th>
                                    <th class="pb-3">Commission</th>
                                    <th class="pb-3">Net Payout</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-b border-gray-600">
                                    <td class="py-3">Besique X</td>
                                    <td class="py-3">45,000 FCFA</td>
                                    <td class="py-3">2,250 FCFA</td>
                                    <td class="py-3">42,750 FCFA</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 bg-green-500 rounded text-xs">Sent</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-600">
                                    <td class="py-3">Mode Ablique X</td>
                                    <td class="py-3">32,000 FCFA</td>
                                    <td class="py-3">1,600 FCFA</td>
                                    <td class="py-3">30,400 FCFA</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 bg-yellow-500 rounded text-xs">Pending</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-600">
                                    <td class="py-3">Mode Abidjan</td>
                                    <td class="py-3">28,000 FCFA</td>
                                    <td class="py-3">1,400 FCFA</td>
                                    <td class="py-3">26,600 FCFA</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 bg-green-500 rounded text-xs">Sent</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fitting Room Management -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-door-open mr-2"></i>FITTING ROOM MANAGEMENT
                    </h2>

                    <!-- Cabin Status -->
                    <div class="glass-panel-dark p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-lg font-bold">CABIN [FREE]</div>
                                <div class="text-sm text-gray-300">Available</div>
                            </div>
                            <div class="status-indicator status-online"></div>
                        </div>
                    </div>

                    <!-- Check-in Log -->
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-3">CHECK IN LOG</h3>
                        <div class="space-y-2">
                            <div class="glass-panel-dark p-3">
                                <div class="text-sm">Client: Anmoa X</div>
                                <div class="text-xs text-gray-300">Guarantie: ID Card</div>
                                <div class="text-xs text-cyan-400">14:30</div>
                            </div>
                            <div class="glass-panel-dark p-3">
                                <div class="text-sm">Client: Marie S</div>
                                <div class="text-xs text-gray-300">Guarantie: Phone</div>
                                <div class="text-xs text-cyan-400">13:45</div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Trials -->
                    <div class="glass-panel-dark p-4 text-center">
                        <div class="text-2xl font-bold text-cyan-400">15</div>
                        <div class="text-sm text-gray-300">TODAY'S TRIALS</div>
                    </div>
                </div>
            </div>

            <!-- Studio & Creative Hub -->
            <div class="lg:col-span-1">
                <div class="glass-panel p-6 hover-scale">
                    <h2 class="text-xl font-bold mb-4 text-cyan-400">
                        <i class="fas fa-camera mr-2"></i>STUDIO & CREATIVE HUB
                    </h2>

                    <!-- Shooting Slots -->
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-3">SHOOTING SLOTS</h3>
                        <div class="space-y-2">
                            <div>
                                <div class="text-xs mb-1">SMOOTHING (0)</div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-400 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs mb-1">MCHITING</div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: 60%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs mb-1">READY (1)</div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-400 h-2 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Production Tracker -->
                    <div>
                        <h3 class="text-sm font-semibold mb-3">PRODUCTION TRACKER</h3>
                        <div class="grid grid-cols-4 gap-2">
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress1/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress2/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress3/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress4/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress5/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress6/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress7/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                            <div class="glass-panel-dark p-2 aspect-square flex items-center justify-center">
                                <img src="https://picsum.photos/seed/dress8/50/50.jpg" alt="Product" class="w-full h-full object-cover rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Chart.js Configuration
        const ctx = document.getElementById('deliveriesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Deliveries',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    backgroundColor: 'rgba(0, 212, 255, 0.5)',
                    borderColor: 'rgba(0, 212, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#b8c5d6'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#b8c5d6'
                        }
                    }
                }
            }
        });

        // Add interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats on load
            const stats = document.querySelectorAll('.text-2xl');
            stats.forEach(stat => {
                stat.style.opacity = '0';
                stat.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    stat.style.transition = 'all 0.5s ease';
                    stat.style.opacity = '1';
                    stat.style.transform = 'translateY(0)';
                }, 100);
            });

            // Add click handlers for buttons
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    this.appendChild(ripple);
                    
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });
    </script>
</body>
</html>
