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
                    <i class="fas fa-cube text-3xl text-cyan-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold neon-text">ECOM-BEST</h1>
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
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-400 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span class="text-sm">Administrateur</span>
                </div>
                <a href="/login-multi-role" class="px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8">
        <!-- Administrator Overview -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-crown mr-2 text-purple-400"></i>Vue d'ensemble Administrateur
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm">85%</span>
                    </div>
                    <div class="text-2xl font-bold text-white">Aujourd'hui</div>
                    <div class="text-sm text-gray-300">Taux de réussite</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="text-blue-400 text-sm">5%</span>
                    </div>
                    <div class="text-2xl font-bold text-white">Nouveaux</div>
                    <div class="text-sm text-gray-300">Nouveaux colis</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                        <span class="text-orange-400 text-sm">Alerte</span>
                    </div>
                    <div class="text-2xl font-bold text-white">Colis en retard</div>
                    <div class="text-sm text-gray-300">Traitement requis</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-purple-700 flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <span class="text-purple-400 text-sm">Actifs</span>
                    </div>
                    <div class="text-2xl font-bold text-white">Utilisateurs</div>
                    <div class="text-sm text-gray-300">Connectés aujourd'hui</div>
                </div>
            </div>

            <!-- Overdue Packages -->
            <div class="glass-panel-dark p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-clock mr-2 text-orange-400"></i>Colis en retard
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 glass-panel rounded">
                        <div>
                            <div class="text-sm font-mono text-cyan-400">ECM12345678</div>
                            <div class="text-xs text-gray-300">En retard de 2 jours</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-orange-400">25,000 FCFA</div>
                            <button class="px-3 py-1 bg-orange-500 rounded text-xs hover:bg-orange-600">
                                <i class="fas fa-check mr-1"></i>Résoudre
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 glass-panel rounded">
                        <div>
                            <div class="text-sm font-mono text-cyan-400">ECM87654321</div>
                            <div class="text-xs text-gray-300">En retard de 1 jour</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-orange-400">15,000 FCFA</div>
                            <button class="px-3 py-1 bg-orange-500 rounded text-xs hover:bg-orange-600">
                                <i class="fas fa-check mr-1"></i>Résoudre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vendor Portal -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-store mr-2 text-green-400"></i>Portail Vendeur
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <span class="text-blue-400 text-sm">Nouveau</span>
                    </div>
                    <div class="text-2xl font-bold text-white">12</div>
                    <div class="text-sm text-gray-300">Nouveaux envois</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-yellow-500 to-yellow-700 flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <span class="text-yellow-400 text-sm">En attente</span>
                    </div>
                    <div class="text-2xl font-bold text-white">2</div>
                    <div class="text-sm text-gray-300">En attente</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-700 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-white"></i>
                        </div>
                        <span class="text-cyan-400 text-sm">Point relais</span>
                    </div>
                    <div class="text-2xl font-bold text-white">8</div>
                    <div class="text-sm text-gray-300">Au point relais</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <span class="text-green-400 text-sm">Terminé</span>
                    </div>
                    <div class="text-2xl font-bold text-white">8</div>
                    <div class="text-sm text-gray-300">Vendus</div>
                </div>

                <div class="glass-panel-dark p-6 hover-scale">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center">
                            <i class="fas fa-undo text-white"></i>
                        </div>
                        <span class="text-orange-400 text-sm">Retour</span>
                    </div>
                    <div class="text-2xl font-bold text-white">1</div>
                    <div class="text-sm text-gray-300">Retournés</div>
                </div>
            </div>

            <!-- Wallet Balance -->
            <div class="glass-panel-dark p-6 mb-8">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-wallet mr-2 text-green-400"></i>Solde du Portefeuille
                </h3>
                <div class="text-center mb-6">
                    <div class="text-4xl font-bold text-green-400">150,000 FCFA</div>
                    <div class="text-sm text-gray-300">Solde disponible</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button class="p-3 glass-panel hover-scale text-center">
                        <i class="fas fa-mobile-alt text-2xl text-blue-400 mb-2"></i>
                        <div class="text-sm">Wave</div>
                    </button>
                    <button class="p-3 glass-panel hover-scale text-center">
                        <i class="fas fa-credit-card text-2xl text-purple-400 mb-2"></i>
                        <div class="text-sm">Wizall</div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Client Validation -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-user-check mr-2 text-blue-400"></i>Validation Client
            </h2>
            
            <div class="glass-panel-dark p-6">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-key mr-2 text-cyan-400"></i>Entrez le code unique (CRU)
                </h3>
                <div class="max-w-md mx-auto">
                    <input type="text" placeholder="Entrez le code..." class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400 mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <button class="p-3 bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-money-bill mr-2"></i>Payer en espèces
                        </button>
                        <button class="p-3 bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-mobile-alt mr-2"></i>Mobile Money
                        </button>
                    </div>
                </div>

                <!-- Client Info Display -->
                <div class="mt-6 p-4 glass-panel rounded">
                    <div class="text-sm text-gray-300 mb-2">VOTRE PLACE:</div>
                    <div class="text-lg font-bold text-white">Warm Brute X</div>
                    <div class="text-sm text-gray-300">Article: Robe Bleue</div>
                    <div class="text-sm font-mono text-cyan-400">ECM2024001</div>
                </div>
            </div>
        </section>

        <!-- Payment & Disbursement -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-money-check-alt mr-2 text-purple-400"></i>Paiements & Reversements
            </h2>
            
            <div class="glass-panel-dark p-6">
                <div class="mb-4 p-3 bg-orange-500 bg-opacity-20 border border-orange-400 rounded-lg text-orange-300">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        ACTION REQUISE: 1 paiement en retard > 18h
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-300 border-b border-gray-600">
                                <th class="pb-3">Vendeur</th>
                                <th class="pb-3">Montant vente</th>
                                <th class="pb-3">Commission</th>
                                <th class="pb-3">Net à payer</th>
                                <th class="pb-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-700">
                                <td class="py-3">Besique X</td>
                                <td class="py-3">45,000 FCFA</td>
                                <td class="py-3">2,250 FCFA</td>
                                <td class="py-3">42,750 FCFA</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 bg-green-500 rounded text-xs">Envoyé</span>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-700">
                                <td class="py-3">Mode Ablique X</td>
                                <td class="py-3">32,000 FCFA</td>
                                <td class="py-3">1,600 FCFA</td>
                                <td class="py-3">30,400 FCFA</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 bg-yellow-500 rounded text-xs">En attente</span>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-700">
                                <td class="py-3">Mode Abidjan</td>
                                <td class="py-3">28,000 FCFA</td>
                                <td class="py-3">1,400 FCFA</td>
                                <td class="py-3">26,600 FCFA</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 bg-green-500 rounded text-xs">Envoyé</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Fitting Room Management -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-door-open mr-2 text-cyan-400"></i>Gestion des Cabines d'Essai
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Cabin Status -->
                <div class="glass-panel-dark p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-door-closed mr-2 text-green-400"></i>CABINE [LIBRE]
                    </h3>
                    <div class="text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-500 bg-opacity-20 border-2 border-green-400 flex items-center justify-center">
                            <i class="fas fa-check text-3xl text-green-400"></i>
                        </div>
                        <div class="text-lg font-bold text-green-400">Disponible</div>
                    </div>
                </div>

                <!-- Check In Log -->
                <div class="glass-panel-dark p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-clipboard-check mr-2 text-blue-400"></i>Journal d'Entrée
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 glass-panel rounded">
                            <div>
                                <div class="text-sm text-white">Client: Anmoa X</div>
                                <div class="text-xs text-gray-300">Garantie: Carte d'identité</div>
                            </div>
                            <div class="text-xs text-gray-400">14:30</div>
                        </div>
                        <div class="flex items-center justify-between p-3 glass-panel rounded">
                            <div>
                                <div class="text-sm text-white">Client: Marie S</div>
                                <div class="text-xs text-gray-300">Garantie: Téléphone</div>
                            </div>
                            <div class="text-xs text-gray-400">13:45</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Trials -->
            <div class="glass-panel-dark p-6 mt-6">
                <h3 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-calendar-day mr-2 text-purple-400"></i>Essais d'Aujourd'hui
                </h3>
                <div class="text-center">
                    <div class="text-4xl font-bold text-purple-400">15</div>
                    <div class="text-sm text-gray-300">Essais aujourd'hui</div>
                </div>
            </div>
        </section>

        <!-- Studio & Creative Hub -->
        <section class="mb-8">
            <h2 class="text-xl font-bold text-white mb-6">
                <i class="fas fa-camera mr-2 text-pink-400"></i>Studio & Hub Créatif
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Shooting Slots -->
                <div class="glass-panel-dark p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-camera-retro mr-2 text-yellow-400"></i>Créneaux de Shooting
                    </h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-3 glass-panel rounded">
                            <div class="text-2xl font-bold text-yellow-400">0</div>
                            <div class="text-xs text-gray-300">Lissage</div>
                        </div>
                        <div class="p-3 glass-panel rounded">
                            <div class="text-2xl font-bold text-orange-400">0</div>
                            <div class="text-xs text-gray-300">Maquillage</div>
                        </div>
                        <div class="p-3 glass-panel rounded">
                            <div class="text-2xl font-bold text-green-400">1</div>
                            <div class="text-xs text-gray-300">Prêt</div>
                        </div>
                    </div>
                </div>

                <!-- Production Tracker -->
                <div class="glass-panel-dark p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-tasks mr-2 text-blue-400"></i>Suivi de Production
                    </h3>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2 glass-panel rounded">
                            <span class="text-sm">Photos traitées</span>
                            <span class="text-sm font-bold text-green-400">12/15</span>
                        </div>
                        <div class="flex items-center justify-between p-2 glass-panel rounded">
                            <span class="text-sm">Vidéos en montage</span>
                            <span class="text-sm font-bold text-yellow-400">3/5</span>
                        </div>
                        <div class="flex items-center justify-between p-2 glass-panel rounded">
                            <span class="text-sm">Contenu prêt</span>
                            <span class="text-sm font-bold text-blue-400">8/10</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        // Add interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects and animations
            const hoverElements = document.querySelectorAll('.hover-scale');
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                element.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
