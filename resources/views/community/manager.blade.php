<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Community Manager</title>
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

        .slot-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .slot-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(31, 38, 135, 0.4);
        }

        .production-item {
            transition: all 0.3s ease;
        }

        .production-item:hover {
            transform: scale(1.05);
            z-index: 10;
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <header class="glass-panel-dark p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="floating-icon">
                    <i class="fas fa-camera text-3xl text-blue-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold neon-text">Studio & Creative Hub</h1>
                    <p class="text-sm text-gray-300">Community Management & Production</p>
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
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span class="text-sm">CM Manager</span>
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
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                        <i class="fas fa-camera-retro text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+15%</span>
                </div>
                <div class="text-2xl font-bold text-white">24</div>
                <div class="text-sm text-gray-300">Shootings ce mois</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-purple-700 flex items-center justify-center">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+8%</span>
                </div>
                <div class="text-2xl font-bold text-white">156</div>
                <div class="text-sm text-gray-300">Clients suivis</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">+23%</span>
                </div>
                <div class="text-2xl font-bold text-white">89%</div>
                <div class="text-sm text-gray-300">Taux de satisfaction</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <span class="text-yellow-400 text-sm">Aujourd'hui</span>
                </div>
                <div class="text-2xl font-bold text-white">6</div>
                <div class="text-sm text-gray-300">Réservations</div>
            </div>
        </div>

        <!-- Shooting Slots Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Today's Schedule -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-calendar-day mr-2 text-cyan-400"></i>Planning du Jour
                </h3>

                <div class="space-y-3">
                    <div class="slot-card glass-panel p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold">09:00 - 10:00</div>
                                <div class="text-sm text-gray-300">Fashion Store - Collection Printemps</div>
                                <div class="text-xs text-cyan-400">Studio A</div>
                            </div>
                            <div class="flex items-center">
                                <div class="status-indicator status-online"></div>
                                <span class="text-xs">En cours</span>
                            </div>
                        </div>
                    </div>

                    <div class="slot-card glass-panel p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold">10:30 - 12:00</div>
                                <div class="text-sm text-gray-300">Mode Abidjan - Lookbook</div>
                                <div class="text-xs text-cyan-400">Studio B</div>
                            </div>
                            <div class="flex items-center">
                                <div class="status-indicator status-pending"></div>
                                <span class="text-xs">En attente</span>
                            </div>
                        </div>
                    </div>

                    <div class="slot-card glass-panel p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold">14:00 - 16:00</div>
                                <div class="text-sm text-gray-300">Client VIP - Shooting personnel</div>
                                <div class="text-xs text-cyan-400">Studio A</div>
                            </div>
                            <div class="flex items-center">
                                <div class="status-indicator status-offline"></div>
                                <span class="text-xs">Confirmé</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button onclick="showAddSlotModal()" class="w-full mt-4 py-2 glass-panel hover:bg-white hover:bg-opacity-20 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>Ajouter un créneau
                </button>
            </div>

            <!-- Studio Availability -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-door-open mr-2 text-cyan-400"></i>Disponibilité des Studios
                </h3>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold">Studio A</span>
                            <span class="text-sm text-green-400">Disponible</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="bg-green-400 h-3 rounded-full" style="width: 30%"></div>
                        </div>
                        <div class="text-xs text-gray-300 mt-1">30% occupé aujourd'hui</div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold">Studio B</span>
                            <span class="text-sm text-yellow-400">Partiellement disponible</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="bg-yellow-400 h-3 rounded-full" style="width: 60%"></div>
                        </div>
                        <div class="text-xs text-gray-300 mt-1">60% occupé aujourd'hui</div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold">Studio C (Extérieur)</span>
                            <span class="text-sm text-green-400">Disponible</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="bg-green-400 h-3 rounded-full" style="width: 10%"></div>
                        </div>
                        <div class="text-xs text-gray-300 mt-1">10% occupé aujourd'hui</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Production Tracker -->
        <div class="glass-panel-dark p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-white">
                    <i class="fas fa-tasks mr-2 text-cyan-400"></i>Suivi de Production
                </h3>
                <button onclick="showNewProjectModal()" class="px-4 py-2 bg-blue-500 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-plus mr-2"></i>Nouveau projet
                </button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion1/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Prêt</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion2/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">En cours</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion3/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Prêt</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion4/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Retouche</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion5/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Prêt</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion6/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">En cours</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion7/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Prêt</div>
                </div>
                <div class="production-item glass-panel-dark p-2 aspect-square">
                    <img src="https://picsum.photos/seed/fashion8/100/100.jpg" alt="Production" class="w-full h-full object-cover rounded">
                    <div class="text-xs text-center mt-1">Validation</div>
                </div>
            </div>
        </div>

        <!-- Social Media Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Content Calendar -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-calendar-alt mr-2 text-cyan-400"></i>Calendrier de Contenu
                </h3>

                <div class="space-y-3">
                    <div class="glass-panel p-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold">Collection Printemps</div>
                                <div class="text-sm text-gray-300">Instagram & Facebook</div>
                                <div class="text-xs text-gray-400">Aujourd'hui, 18:00</div>
                            </div>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 bg-purple-500 rounded text-xs">IG</span>
                                <span class="px-2 py-1 bg-blue-500 rounded text-xs">FB</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold">Témoignage Client</div>
                                <div class="text-sm text-gray-300">TikTok & Reels</div>
                                <div class="text-xs text-gray-400">Demain, 12:00</div>
                            </div>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 bg-black rounded text-xs">TT</span>
                                <span class="px-2 py-1 bg-pink-500 rounded text-xs">RE</span>
                            </div>
                        </div>
                    </div>

                    <div class="glass-panel p-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-semibold">Behind the Scenes</div>
                                <div class="text-sm text-gray-300">YouTube Shorts</div>
                                <div class="text-xs text-gray-400">Demain, 15:00</div>
                            </div>
                            <div class="flex space-x-2">
                                <span class="px-2 py-1 bg-red-500 rounded text-xs">YT</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-chart-bar mr-2 text-cyan-400"></i>Métriques de Performance
                </h3>
                <canvas id="performanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </main>

    <script>
        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [
                    {
                        label: 'Engagement',
                        data: [1200, 1900, 1500, 2500, 2200, 3000, 2800],
                        borderColor: 'rgb(0, 212, 255)',
                        backgroundColor: 'rgba(0, 212, 255, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Conversions',
                        data: [400, 600, 450, 800, 700, 950, 850],
                        borderColor: 'rgb(0, 255, 136)',
                        backgroundColor: 'rgba(0, 255, 136, 0.1)',
                        tension: 0.4
                    }
                ]
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
    </script>

    <!-- Modal pour Ajouter un Créneau -->
    <div id="addSlotModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-plus-circle mr-2"></i>Ajouter un Créneau
                </h3>
                <button onclick="hideAddSlotModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/community/create-shooting" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Studio</label>
                        <select name="studio" class="w-full p-3 glass-panel rounded-lg text-white">
                            <option value="A">Studio A</option>
                            <option value="B">Studio B</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Type de shooting</label>
                        <select name="type" class="w-full p-3 glass-panel rounded-lg text-white">
                            <option value="portrait">Portrait</option>
                            <option value="fashion">Fashion</option>
                            <option value="product">Produit</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Date</label>
                        <input type="date" name="date" class="w-full p-3 glass-panel rounded-lg text-white" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Heure</label>
                        <input type="time" name="time" class="w-full p-3 glass-panel rounded-lg text-white" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">Client</label>
                    <input type="text" name="client" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="Nom du client" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Description</label>
                    <textarea name="description" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" rows="3" placeholder="Description du shooting..."></textarea>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-green-500 to-green-700 rounded-lg font-bold hover:from-green-600 hover:to-green-800">
                        <i class="fas fa-check mr-2"></i>Créer le créneau
                    </button>
                    <button type="button" onclick="hideAddSlotModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal pour Nouveau Projet -->
    <div id="newProjectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-plus-circle mr-2"></i>Nouveau Projet
                </h3>
                <button onclick="hideNewProjectModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/community/update-production" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-2">Nom du projet</label>
                    <input type="text" name="project_name" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="Nom du projet" required>
                </div>

                <div>
                    <label class="block text-sm mb-2">Type de production</label>
                    <select name="production_type" class="w-full p-3 glass-panel rounded-lg text-white">
                        <option value="shooting">Shooting photo</option>
                        <option value="video">Vidéo</option>
                        <option value="catalogue">Catalogue</option>
                        <option value="social">Contenu social</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">Statut</label>
                    <select name="status" class="w-full p-3 glass-panel rounded-lg text-white">
                        <option value="planning">Planification</option>
                        <option value="in_progress">En cours</option>
                        <option value="review">Relecture</option>
                        <option value="completed">Terminé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">Description</label>
                    <textarea name="description" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" rows="3" placeholder="Description du projet..."></textarea>
                </div>

                <div>
                    <label class="block text-sm mb-2">Date de livraison</label>
                    <input type="date" name="delivery_date" class="w-full p-3 glass-panel rounded-lg text-white">
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-blue-500 to-blue-700 rounded-lg font-bold hover:from-blue-600 hover:to-blue-800">
                        <i class="fas fa-check mr-2"></i>Créer le projet
                    </button>
                    <button type="button" onclick="hideNewProjectModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonctions pour les modales
        function showAddSlotModal() {
            document.getElementById('addSlotModal').classList.remove('hidden');
        }

        function hideAddSlotModal() {
            document.getElementById('addSlotModal').classList.add('hidden');
        }

        function showNewProjectModal() {
            document.getElementById('newProjectModal').classList.remove('hidden');
        }

        function hideNewProjectModal() {
            document.getElementById('newProjectModal').classList.add('hidden');
        }
    </script>
</body>
</html>
