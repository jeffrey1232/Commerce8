<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Connexion Multi-Rôles</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .role-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
        }

        .role-card.selected {
            border: 2px solid var(--primary-blue);
            box-shadow: 0 0 20px var(--primary-blue);
        }

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
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-6xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="floating-icon inline-block mb-4">
                    <i class="fas fa-cube text-6xl text-cyan-400"></i>
                </div>
                <h1 class="text-4xl font-bold neon-text mb-2">ECOM-BEST</h1>
                <p class="text-gray-300">Plateforme E-Commerce Sécurisée</p>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Login Form -->
                <div class="glass-panel-dark p-8">
                    <h2 class="text-2xl font-bold mb-6 text-cyan-400">
                        <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                    </h2>

                    <form id="loginForm" class="space-y-4">
                        <div>
                            <label class="block text-sm mb-2">Adresse Email</label>
                            <input type="email" id="email" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="email@exemple.com" required>
                        </div>

                        <div>
                            <label class="block text-sm mb-2">Mot de passe</label>
                            <input type="password" id="password" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="••••••••" required>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" class="mr-2">
                                <span class="text-sm">Se souvenir de moi</span>
                            </label>
                            <a href="#" class="text-sm text-cyan-400 hover:text-cyan-300">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg font-bold hover:from-cyan-600 hover:to-blue-700 transition-all transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                        </button>
                    </form>

                    <!-- Demo Accounts -->
                    <div class="mt-6 p-4 glass-panel rounded-lg">
                        <h3 class="text-sm font-semibold mb-3 text-yellow-400">
                            <i class="fas fa-info-circle mr-1"></i>Comptes de démonstration
                        </h3>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span>Admin:</span>
                                <code class="text-cyan-400">admin@ecom-best.sn / password</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Vendeur:</span>
                                <code class="text-cyan-400">vendor@ecom-best.sn / password</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Staff:</span>
                                <code class="text-cyan-400">staff@ecom-best.sn / password</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Client:</span>
                                <code class="text-cyan-400">client@ecom-best.sn / password</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="glass-panel-dark p-8">
                    <h2 class="text-2xl font-bold mb-6 text-cyan-400">
                        <i class="fas fa-user-tag mr-2"></i>Sélectionner votre rôle
                    </h2>

                    <div class="space-y-4">
                        <!-- Administrateur -->
                        <div class="role-card glass-panel p-4" data-role="admin" onclick="selectRole('admin')">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-500 to-purple-700 flex items-center justify-center mr-4">
                                    <i class="fas fa-crown text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">Administrateur ECOM-BEST</h3>
                                    <p class="text-sm text-gray-300">Gestion complète de la plateforme</p>
                                </div>
                                <i class="fas fa-chevron-right text-cyan-400"></i>
                            </div>
                        </div>

                        <!-- Vendeur -->
                        <div class="role-card glass-panel p-4" data-role="vendor" onclick="selectRole('vendor')">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center mr-4">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">Vendeur</h3>
                                    <p class="text-sm text-gray-300">Déposer et suivre vos colis</p>
                                </div>
                                <i class="fas fa-chevron-right text-cyan-400"></i>
                            </div>
                        </div>

                        <!-- Community Manager -->
                        <div class="role-card glass-panel p-4" data-role="staff" onclick="selectRole('staff')">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center mr-4">
                                    <i class="fas fa-camera text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">Community Manager</h3>
                                    <p class="text-sm text-gray-300">Gestion des services digitaux</p>
                                </div>
                                <i class="fas fa-chevron-right text-cyan-400"></i>
                            </div>
                        </div>

                        <!-- Client -->
                        <div class="role-card glass-panel p-4" data-role="client" onclick="selectRole('client')">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-lg">Client Final</h3>
                                    <p class="text-sm text-gray-300">Retrait et validation des colis</p>
                                </div>
                                <i class="fas fa-chevron-right text-cyan-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Role Display -->
                    <div class="mt-6 p-4 glass-panel rounded-lg text-center">
                        <p class="text-sm text-gray-300">Rôle sélectionné:</p>
                        <p id="selectedRole" class="text-lg font-bold text-cyan-400">Aucun</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedRole = null;

        function selectRole(role) {
            // Remove previous selection
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selection to clicked card
            document.querySelector(`[data-role="${role}"]`).classList.add('selected');

            // Update selected role display
            selectedRole = role;
            const roleNames = {
                'admin': 'Administrateur ECOM-BEST',
                'vendor': 'Vendeur',
                'staff': 'Community Manager',
                'client': 'Client Final'
            };
            document.getElementById('selectedRole').textContent = roleNames[role];
        }

        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!selectedRole) {
                alert('Veuillez sélectionner un rôle avant de vous connecter');
                return;
            }

            // Simulate login and redirect based on role
            const roleRoutes = {
                'admin': '/admin-dashboard',
                'vendor': '/vendor-portal',
                'staff': '/community-manager',
                'client': '/client-validation'
            };

            // Simulate authentication delay
            const button = e.target.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion en cours...';
            button.disabled = true;

            setTimeout(() => {
                // Redirect to appropriate interface
                window.location.href = roleRoutes[selectedRole];
            }, 1500);
        });

        // Auto-select role based on email (demo purposes)
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.toLowerCase();
            if (email.includes('admin')) selectRole('admin');
            else if (email.includes('vendor')) selectRole('vendor');
            else if (email.includes('staff')) selectRole('staff');
            else if (email.includes('client')) selectRole('client');
        });
    </script>
</body>
</html>
