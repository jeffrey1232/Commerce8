<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Portail Vendeur</title>
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

        .package-card {
            transition: all 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px 0 rgba(31, 38, 135, 0.4);
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <header class="glass-panel-dark p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="floating-icon">
                    <i class="fas fa-store text-3xl text-green-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold neon-text">Portail Vendeur</h1>
                    <p class="text-sm text-gray-300">Gérez vos colis et vos ventes</p>
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
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <span class="text-sm">Fashion Store</span>
                </div>
                <a href="/login-multi-role" class="px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500 bg-opacity-20 border border-green-400 rounded-lg text-green-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500 bg-opacity-20 border border-red-400 rounded-lg text-red-300">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500 bg-opacity-20 border border-red-400 rounded-lg text-red-300">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Erreurs de validation:</strong>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- New Shipment Button -->
        <div class="mb-8">
            <button onclick="showNewShipmentModal()" class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-green-500 to-green-700 rounded-lg font-bold hover:from-green-600 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fas fa-plus-circle mr-2"></i>Nouvel Envoi
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <span class="text-blue-400 text-sm">En attente</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['pending_colis'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Colis en attente</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-700 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <span class="text-cyan-400 text-sm">Au point relais</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['deposited_colis'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Colis déposés</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-green-700 flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <span class="text-green-400 text-sm">Vendus</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['sold_colis'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Colis vendus</div>
            </div>

            <div class="glass-panel-dark p-6 hover-scale">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-orange-700 flex items-center justify-center">
                        <i class="fas fa-undo text-white"></i>
                    </div>
                    <span class="text-orange-400 text-sm">Retournés</span>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['returned_colis'] ?? 0 }}</div>
                <div class="text-sm text-gray-300">Colis retournés</div>
            </div>
        </div>

        <!-- Wallet Balance -->
        <div class="glass-panel-dark p-6 mb-8">
            <h3 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-wallet mr-2 text-green-400"></i>Solde du Portefeuille
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-400 mb-2">{{ number_format($walletBalance['available'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    <div class="text-sm text-gray-300">Solde disponible</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-cyan-400 mb-2">{{ number_format($walletBalance['pending'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    <div class="text-sm text-gray-300">En attente de reversement</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-2">{{ number_format($walletBalance['total_sales'] ?? 0, 0, ',', ' ') }} FCFA</div>
                    <div class="text-sm text-gray-300">Total des ventes</div>
                </div>
            </div>
        </div>

        <!-- Recent Packages -->
        <div class="glass-panel-dark p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-white">
                    <i class="fas fa-boxes mr-2 text-cyan-400"></i>Mes Colis Récents
                </h3>
                <button class="text-cyan-400 hover:text-cyan-300">
                    Voir tout <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($recentPackages ?? [] as $package)
                <div class="package-card glass-panel p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="text-sm font-mono text-cyan-400">{{ $package['tracking_code'] }}</div>
                            <div class="text-xs text-gray-300">{{ $package['product_name'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-green-400">{{ number_format($package['total_amount'], 0, ',', ' ') }} FCFA</div>
                            <div class="text-xs text-gray-300">{{ $package['created_at'] }}</div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="status-indicator status-{{ $package['status'] === 'paid' ? 'online' : ($package['status'] === 'deposited' ? 'pending' : 'offline') }}"></div>
                            <span class="text-xs">{{ strtoupper($package['status']) }}</span>
                        </div>
                        <button class="px-2 py-1 bg-blue-500 rounded text-xs hover:bg-blue-600">
                            <i class="fas fa-eye mr-1"></i>Détails
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-400">
                    Aucun colis trouvé
                </div>
                @endforelse
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="glass-panel-dark p-6">
            <h3 class="text-xl font-semibold text-white mb-4">
                <i class="fas fa-chart-line mr-2 text-cyan-400"></i>Évolution des Ventes
            </h3>
            <canvas id="salesChart" width="400" height="200"></canvas>
        </div>
    </main>

    <!-- New Shipment Modal -->
    <div id="newShipmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="glass-panel-dark p-8 w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">
                    <i class="fas fa-plus-circle mr-2"></i>Nouvel Envoi
                </h3>
                <button onclick="hideNewShipmentModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form action="/vendor/create-shipment" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Nom du produit</label>
                        <input type="text" name="product_name" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="Ex: Robe d'été" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Prix (FCFA)</label>
                        <input type="number" name="price" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="25000" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">Description</label>
                    <textarea name="description" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" rows="3" placeholder="Description détaillée du produit..." required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Téléphone client</label>
                        <input type="tel" name="client_phone" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="+221 77 123 45 67" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Email client</label>
                        <input type="email" name="client_email" class="w-full p-3 glass-panel rounded-lg text-white placeholder-gray-400" placeholder="client@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">Photo du produit</label>
                    <div id="uploadArea" class="w-full p-8 glass-panel rounded-lg border-2 border-dashed border-gray-400 text-center cursor-pointer hover:border-cyan-400 transition-colors">
                        <input type="file" id="productPhoto" name="product_photo" accept="image/*" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-300">Cliquez pour uploader ou glissez-déposez</p>
                        <div id="previewContainer" class="mt-4 hidden">
                            <img id="imagePreview" class="max-h-32 mx-auto rounded-lg shadow-lg" alt="Aperçu">
                            <p id="fileName" class="text-xs text-cyan-400 mt-2"></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="fitting" name="fitting_fee" value="500" class="mr-2">
                    <label for="fitting" class="text-sm">Option d'essayage (+500 FCFA)</label>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-green-500 to-green-700 rounded-lg font-bold hover:from-green-600 hover:to-green-800">
                        <i class="fas fa-check mr-2"></i>Créer l'envoi
                    </button>
                    <button type="button" onclick="hideNewShipmentModal()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Ventes',
                    data: [3, 5, 4, 7, 6, 9, 8],
                    backgroundColor: 'rgba(0, 255, 136, 0.5)',
                    borderColor: 'rgba(0, 255, 136, 1)',
                    borderWidth: 1
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
                        beginAtZero: true,
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

        // Modal functions
        function showNewShipmentModal() {
            document.getElementById('newShipmentModal').classList.remove('hidden');
            // Reset form when opening
            resetForm();
        }

        function hideNewShipmentModal() {
            document.getElementById('newShipmentModal').classList.add('hidden');
        }

        function resetForm() {
            // Reset form fields
            const form = document.querySelector('#newShipmentModal form');
            if (form) {
                form.reset();
            }

            // Reset file preview
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const fileName = document.getElementById('fileName');
            const uploadArea = document.getElementById('uploadArea');

            previewContainer.classList.add('hidden');
            imagePreview.src = '';
            fileName.textContent = '';

            // Reset upload area appearance
            uploadArea.classList.remove('border-green-400', 'bg-green-400', 'bg-opacity-10');
            uploadArea.classList.add('border-gray-400');
        }

        // Check for success message and close modal
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('.bg-green-500.bg-opacity-20');
            if (successMessage) {
                // Close modal if there's a success message
                hideNewShipmentModal();

                // Scroll to top to see the success message
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Auto-hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.style.transition = 'opacity 0.5s';
                    successMessage.style.opacity = '0';
                    setTimeout(() => {
                        successMessage.remove();
                    }, 500);
                }, 5000);
            }
        });

        // File upload functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('productPhoto');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const fileName = document.getElementById('fileName');

        // Click to upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        // File selection
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                handleFile(file);
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('border-cyan-400', 'bg-cyan-400', 'bg-opacity-10');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-cyan-400', 'bg-cyan-400', 'bg-opacity-10');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('border-cyan-400', 'bg-cyan-400', 'bg-opacity-10');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    fileInput.files = files;
                    handleFile(file);
                } else {
                    showNotification('Veuillez sélectionner une image valide', 'error');
                }
            }
        });

        function handleFile(file) {
            // Check file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('L\'image ne doit pas dépasser 5MB', 'error');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                fileName.textContent = file.name;
                previewContainer.classList.remove('hidden');

                // Update upload area appearance
                uploadArea.classList.add('border-green-400', 'bg-green-400', 'bg-opacity-10');
                uploadArea.classList.remove('border-gray-400');
            };
            reader.readAsDataURL(file);
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
                type === 'error' ? 'bg-red-500' : 'bg-green-500'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 100);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>
