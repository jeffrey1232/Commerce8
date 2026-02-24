<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Administrateur</title>
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

        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
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

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <header class="glass-panel-dark p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="floating-icon">
                    <i class="fas fa-crown text-3xl text-purple-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold neon-text">Administrateur ECOM-BEST</h1>
                    <p class="text-sm text-gray-300">Panneau de contrôle global</p>
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
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span class="text-sm">Admin</span>
                </div>
                <a href="/login-multi-role" class="px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-700 flex items-center justify-center">
                        <i class="fas fa-box text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+12%</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['total_colis'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Total Colis</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                        <i class="fas fa-store text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+8%</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['total_vendors'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Vendeurs Actifs</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-purple-700 flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+23%</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="text-sm text-gray-300">Revenus Mensuels</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <span class="text-red-400 text-sm">-5%</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['pending_payments'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Paiements En Attente</div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-chart-line mr-2 text-cyan-400"></i>Tendance des Revenus
                </h3>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>

            <!-- Package Status -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-boxes mr-2 text-cyan-400"></i>Statut des Colis
                </h3>
                <canvas id="statusChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Management Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Pending Disbursements -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-money-check-alt mr-2 text-cyan-400"></i>Reversements En Attente
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-300 border-b border-gray-600">
                                <th class="pb-3">Vendeur</th>
                                <th class="pb-3">Montant</th>
                                <th class="pb-3">Commission</th>
                                <th class="pb-3">Net</th>
                                <th class="pb-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingDisbursements ?? [] as $disbursement)
                            <tr class="border-b border-gray-700">
                                <td class="py-3">{{ $disbursement['vendor_name'] }}</td>
                                <td class="py-3">{{ number_format($disbursement['total_sales'], 0, ',', ' ') }} FCFA</td>
                                <td class="py-3">{{ number_format($disbursement['commission'], 0, ',', ' ') }} FCFA</td>
                                <td class="py-3">{{ number_format($disbursement['net_payout'], 0, ',', ' ') }} FCFA</td>
                                <td class="py-3">
                                    <button onclick="approveDisbursement({{ $disbursement['vendor_id'] }})" class="px-3 py-1 bg-green-500 rounded text-xs hover:bg-green-600">
                                        <i class="fas fa-check mr-1"></i>Valider
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">
                                    Aucun reversement en attente
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-history mr-2 text-cyan-400"></i>Activités Récentes
                </h3>

                <div class="space-y-3">
                @forelse ($recentActivities ?? [] as $activity)
                <div class="flex items-center p-3 glass-panel rounded">
                    <div class="status-indicator status-{{ $activity['status'] ?? 'online' }}"></div>
                    <div class="flex-1">
                        <div class="text-sm">{{ $activity['message'] }}</div>
                        <div class="text-xs text-gray-300">{{ $activity['details'] }}</div>
                    </div>
                    <div class="text-xs text-gray-400">{{ $activity['time'] }}</div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-400">
                    Aucune activité récente
                </div>
                @endforelse
            </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-panel-dark p-6">
            <h3 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-bolt mr-2 text-cyan-400"></i>Actions Rapides
            </h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="showAddVendorModal()" class="p-4 glass-panel hover-scale text-center">
                    <i class="fas fa-user-plus text-2xl text-green-400 mb-2"></i>
                    <div class="text-sm">Ajouter Vendeur</div>
                </button>

                <button onclick="showValidateDepositModal()" class="p-4 glass-panel hover-scale text-center">
                    <i class="fas fa-box-open text-2xl text-blue-400 mb-2"></i>
                    <div class="text-sm">Valider Dépôt</div>
                </button>

                <button onclick="showGenerateReportModal()" class="p-4 glass-panel hover-scale text-center">
                    <i class="fas fa-file-invoice-dollar text-2xl text-purple-400 mb-2"></i>
                    <div class="text-sm">Générer Rapport</div>
                </button>

                <button onclick="showSettingsModal()" class="p-4 glass-panel hover-scale text-center">
                    <i class="fas fa-cogs text-2xl text-orange-400 mb-2"></i>
                    <div class="text-sm">Paramètres</div>
                </button>
            </div>
        </div>
    </main>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Revenus',
                    data: [1200000, 1900000, 1500000, 2500000, 2200000, 3000000, 2800000],
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

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Déposé', 'En Transit', 'Livré', 'En Attente'],
                datasets: [{
                    data: [347, 156, 423, 321],
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

    <!-- Modal Ajouter Vendeur -->
    <div id="addVendorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-user-plus mr-2"></i>Ajouter un Vendeur
                </h3>
                <button onclick="hideAddVendorModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/admin/add-vendor" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-2">Nom de l'entreprise</label>
                    <input type="text" name="business_name" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="Nom de l'entreprise" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Email</label>
                    <input type="email" name="email" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="email@exemple.com" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Téléphone</label>
                    <input type="tel" name="phone" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="+221 77 123 45 67" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Adresse</label>
                    <textarea name="address" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" rows="3" placeholder="Adresse complète" required></textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-green-500 to-green-700 rounded-lg font-bold hover:from-green-600 hover:to-green-800">
                        <i class="fas fa-check mr-2"></i>Ajouter
                    </button>
                    <button type="button" onclick="hideAddVendorModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Valider Dépôt -->
    <div id="validateDepositModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-box-open mr-2"></i>Valider un Dépôt
                </h3>
                <button onclick="hideValidateDepositModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/admin/validate-deposit" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-2">Code de suivi</label>
                    <input type="text" name="tracking_code" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="ECM123456" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Action</label>
                    <select name="action" class="w-full p-3 glass-panel rounded-lg text-white">
                        <option value="approve">Approuver le dépôt</option>
                        <option value="reject">Rejeter le dépôt</option>
                        <option value="request_info">Demander plus d'informations</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">Notes</label>
                    <textarea name="notes" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" rows="3" placeholder="Notes supplémentaires..."></textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg font-bold hover:from-blue-600 hover:to-blue-800">
                        <i class="fas fa-check mr-2"></i>Valider
                    </button>
                    <button type="button" onclick="hideValidateDepositModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Générer Rapport -->
    <div id="generateReportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Générer un Rapport
                </h3>
                <button onclick="hideGenerateReportModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/admin/generate-report" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-2">Type de rapport</label>
                    <select name="report_type" class="w-full p-3 glass-panel rounded-lg text-white">
                        <option value="sales">Rapport des ventes</option>
                        <option value="vendors">Rapport des vendeurs</option>
                        <option value="payments">Rapport des paiements</option>
                        <option value="deposits">Rapport des dépôts</option>
                        <option value="financial">Rapport financier</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Date de début</label>
                        <input type="date" name="start_date" class="w-full p-3 glass-panel rounded-lg text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Date de fin</label>
                        <input type="date" name="end_date" class="w-full p-3 glass-panel rounded-lg text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">Format</label>
                    <select name="format" class="w-full p-3 glass-panel rounded-lg text-white">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-purple-500 to-purple-700 rounded-lg font-bold hover:from-purple-600 hover:to-purple-800">
                        <i class="fas fa-download mr-2"></i>Générer
                    </button>
                    <button type="button" onclick="hideGenerateReportModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Paramètres -->
    <div id="settingsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-cogs mr-2"></i>Paramètres
                </h3>
                <button onclick="hideSettingsModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/admin/settings" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-2">Commission par défaut (%)</label>
                    <input type="number" name="default_commission" class="w-full p-3 glass-panel rounded-lg text-white" placeholder="5" step="0.1" min="0" max="100">
                </div>

                <div>
                    <label class="block text-sm mb-2">Frais de livraison par défaut (FCFA)</label>
                    <input type="number" name="default_shipping_fee" class="w-full p-3 glass-panel rounded-lg text-white" placeholder="1000" min="0">
                </div>

                <div>
                    <label class="block text-sm mb-2">Frais d'essayage par défaut (FCFA)</label>
                    <input type="number" name="default_fitting_fee" class="w-full p-3 glass-panel rounded-lg text-white" placeholder="500" min="0">
                </div>

                <div>
                    <label class="block text-sm mb-2">Email de notification</label>
                    <input type="email" name="notification_email" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="admin@ecom-best.sn">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="auto_approve_vendors" name="auto_approve_vendors" class="mr-2">
                    <label for="auto_approve_vendors" class="text-sm">Approuver automatiquement les nouveaux vendeurs</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="enable_notifications" name="enable_notifications" class="mr-2">
                    <label for="enable_notifications" class="text-sm">Activer les notifications par email</label>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-orange-500 to-orange-700 rounded-lg font-bold hover:from-orange-600 hover:to-orange-800">
                        <i class="fas fa-save mr-2"></i>Sauvegarder
                    </button>
                    <button type="button" onclick="hideSettingsModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function showAddVendorModal() {
            document.getElementById('addVendorModal').classList.remove('hidden');
        }

        function hideAddVendorModal() {
            document.getElementById('addVendorModal').classList.add('hidden');
        }

        function showValidateDepositModal() {
            document.getElementById('validateDepositModal').classList.remove('hidden');
        }

        function hideValidateDepositModal() {
            document.getElementById('validateDepositModal').classList.add('hidden');
        }

        function showGenerateReportModal() {
            document.getElementById('generateReportModal').classList.remove('hidden');
        }

        function hideGenerateReportModal() {
            document.getElementById('generateReportModal').classList.add('hidden');
        }

        function showSettingsModal() {
            document.getElementById('settingsModal').classList.remove('hidden');
        }

        function hideSettingsModal() {
            document.getElementById('settingsModal').classList.add('hidden');
        }
    </script>
</body>
</html>
