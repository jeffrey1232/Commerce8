<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECOM-BEST - Validation Client</title>
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

        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.5);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .floating-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .qr-scanner {
            position: relative;
            overflow: hidden;
        }

        .qr-scanner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary-blue), transparent);
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .step-indicator {
            transition: all 0.3s ease;
        }

        .step-indicator.active {
            background: var(--primary-green);
            box-shadow: 0 0 20px var(--primary-green);
        }

        .step-indicator.completed {
            background: var(--primary-blue);
            box-shadow: 0 0 20px var(--primary-blue);
        }
    </style>
</head>
<body class="text-white">
    <!-- Header -->
    <header class="glass-panel-dark p-4 mb-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="floating-icon">
                    <i class="fas fa-user-check text-3xl text-orange-400"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold neon-text">Espace Client</h1>
                    <p class="text-sm text-gray-300">Retrait et validation de vos colis</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <button class="p-2 rounded-lg glass-panel hover-scale">
                    <i class="fas fa-question-circle text-cyan-400"></i>
                </button>
                <a href="/login-multi-role" class="px-4 py-2 bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 pb-8">
        <!-- Progress Steps -->
        <div class="glass-panel-dark p-6 mb-8">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
                <div class="flex flex-col items-center">
                    <div class="step-indicator active w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center mb-2">
                        <i class="fas fa-qrcode text-white"></i>
                    </div>
                    <span class="text-xs text-center">Scanner QR</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center mb-2">
                        <i class="fas fa-box text-white"></i>
                    </div>
                    <span class="text-xs text-center">Vérifier Colis</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center mb-2">
                        <i class="fas fa-tshirt text-white"></i>
                    </div>
                    <span class="text-xs text-center">Essayer (Option)</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center mb-2">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                    <span class="text-xs text-center">Payer</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex flex-col items-center">
                    <div class="step-indicator w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center mb-2">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <span class="text-xs text-center">Valider</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- QR Code Scanner -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-qrcode mr-2 text-cyan-400"></i>Scanner votre Code CRU
                </h3>
                
                <div class="glass-panel qr-scanner p-8 mb-4 text-center">
                    <div class="w-48 h-48 mx-auto bg-white rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-qrcode text-8xl text-gray-800"></i>
                    </div>
                    <p class="text-sm text-gray-300 mb-4">Positionnez le code QR dans le cadre</p>
                    <button onclick="simulateScan()" class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg font-bold hover:from-cyan-600 hover:to-blue-700 transition-all">
                        <i class="fas fa-camera mr-2"></i>Scanner
                    </button>
                </div>

                <!-- Manual Entry -->
                <div class="glass-panel p-4">
                    <h4 class="font-semibold mb-3">Ou entrez manuellement le code</h4>
                    <div class="flex space-x-2">
                        <input type="text" id="manualCode" class="flex-1 p-3 glass-panel-dark rounded-lg text-white placeholder-gray-400" placeholder="ECM2024001">
                        <button onclick="validateCode()" class="px-6 py-3 bg-blue-500 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Package Information -->
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-info-circle mr-2 text-cyan-400"></i>Informations du Colis
                </h3>
                
                <div id="packageInfo" class="space-y-4">
                    <!-- Default state -->
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-300">Scannez un code QR pour voir les détails</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods (Hidden by default) -->
        <div id="paymentSection" class="hidden mt-8">
            <div class="glass-panel-dark p-6">
                <h3 class="text-xl font-semibold text-white mb-4">
                    <i class="fas fa-credit-card mr-2 text-cyan-400"></i>Choisissez votre méthode de paiement
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <button onclick="selectPayment('cash')" class="payment-option glass-panel p-6 hover-scale text-center">
                        <i class="fas fa-money-bill-wave text-4xl text-green-400 mb-3"></i>
                        <div class="font-semibold">Paiement en espèces</div>
                        <div class="text-sm text-gray-300">Payer sur place</div>
                    </button>
                    
                    <button onclick="selectPayment('mobile')" class="payment-option glass-panel p-6 hover-scale text-center">
                        <i class="fas fa-mobile-alt text-4xl text-blue-400 mb-3"></i>
                        <div class="font-semibold">Mobile Money</div>
                        <div class="text-sm text-gray-300">Wave, Orange, MTN</div>
                    </button>
                </div>

                <!-- Mobile Money Options (Hidden by default) -->
                <div id="mobileOptions" class="hidden space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button class="glass-panel p-4 hover-scale">
                            <div class="text-center">
                                <i class="fas fa-wallet text-2xl text-blue-400 mb-2"></i>
                                <div>Wave</div>
                            </div>
                        </button>
                        <button class="glass-panel p-4 hover-scale">
                            <div class="text-center">
                                <i class="fas fa-wallet text-2xl text-orange-400 mb-2"></i>
                                <div>Orange Money</div>
                            </div>
                        </button>
                        <button class="glass-panel p-4 hover-scale">
                            <div class="text-center">
                                <i class="fas fa-wallet text-2xl text-purple-400 mb-2"></i>
                                <div>MTN Mobile</div>
                            </div>
                        </button>
                    </div>
                    
                    <div class="glass-panel p-4">
                        <label class="block text-sm mb-2">Numéro de téléphone</label>
                        <input type="tel" class="w-full p-3 glass-panel-dark rounded-lg text-white placeholder-gray-400" placeholder="+221 77 123 45 67">
                    </div>
                </div>

                <!-- Fitting Option -->
                <div class="glass-panel p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold">Option d'essayage</div>
                            <div class="text-sm text-gray-300">Essayez avant d'acheter (+500 FCFA)</div>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" id="fittingOption" class="mr-2">
                            <span class="text-sm">Oui, je veux essayer</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                    <button onclick="confirmPayment()" class="flex-1 py-3 bg-gradient-to-r from-green-500 to-green-700 rounded-lg font-bold hover:from-green-600 hover:to-green-800">
                        <i class="fas fa-check mr-2"></i>Confirmer le paiement
                    </button>
                    <button onclick="cancelProcess()" class="flex-1 py-3 glass-panel rounded-lg font-bold hover:bg-white hover:bg-opacity-20">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message (Hidden by default) -->
        <div id="successMessage" class="hidden mt-8">
            <div class="glass-panel-dark p-8 text-center">
                <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
                <h3 class="text-2xl font-bold text-white mb-2">Paiement réussi !</h3>
                <p class="text-gray-300 mb-6">Votre colis a été validé avec succès</p>
                
                <div class="glass-panel p-4 mb-6">
                    <div class="text-sm text-gray-300 mb-2">Numéro de reçu:</div>
                    <div class="text-lg font-mono text-cyan-400">RCT-2024-001234</div>
                </div>
                
                <button onclick="downloadReceipt()" class="px-6 py-3 bg-blue-500 rounded-lg hover:bg-blue-600 mr-2">
                    <i class="fas fa-download mr-2"></i>Télécharger le reçu
                </button>
                <button onclick="resetProcess()" class="px-6 py-3 glass-panel rounded-lg hover:bg-white hover:bg-opacity-20">
                    <i class="fas fa-redo mr-2"></i>Nouveau colis
                </button>
            </div>
        </div>
    </main>

    <script>
        let currentStep = 1;
        let selectedPayment = null;

        function simulateScan() {
            // Simulate QR code scanning
            const codes = ['ECM2024001', 'ECM2024002', 'ECM2024003'];
            const randomCode = codes[Math.floor(Math.random() * codes.length)];
            document.getElementById('manualCode').value = randomCode;
            validateCode();
        }

        function validateCode() {
            const code = document.getElementById('manualCode').value;
            if (!code) {
                alert('Veuillez entrer un code valide');
                return;
            }

            // Simulate package validation
            showPackageInfo(code);
            updateStep(2);
            
            // Show payment section after 2 seconds
            setTimeout(() => {
                document.getElementById('paymentSection').classList.remove('hidden');
                updateStep(3);
            }, 2000);
        }

        function showPackageInfo(code) {
            const packageData = {
                'ECM2024001': {
                    name: 'Robe bleue élégante',
                    price: '25,000 FCFA',
                    vendor: 'Fashion Store',
                    description: 'Robe en coton, taille M, parfaite pour occasions spéciales',
                    image: 'https://picsum.photos/seed/dress1/200/200.jpg'
                },
                'ECM2024002': {
                    name: 'Chaussures noires classiques',
                    price: '35,000 FCFA',
                    vendor: 'Mode Abidjan',
                    description: 'Chaussures en cuir, pointure 42, élégantes et confortables',
                    image: 'https://picsum.photos/seed/shoes1/200/200.jpg'
                },
                'ECM2024003': {
                    name: 'Sac à main en cuir',
                    price: '18,000 FCFA',
                    vendor: 'Boutique Premium',
                    description: 'Sac en cuir véritable, couleur marron, spacieux et élégant',
                    image: 'https://picsum.photos/seed/bag1/200/200.jpg'
                }
            };

            const package = packageData[code] || packageData['ECM2024001'];
            
            document.getElementById('packageInfo').innerHTML = `
                <div class="glass-panel p-4">
                    <div class="flex items-start space-x-4">
                        <img src="${package.image}" alt="Produit" class="w-24 h-24 object-cover rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-semibold text-lg mb-2">${package.name}</h4>
                            <p class="text-sm text-gray-300 mb-2">${package.description}</p>
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm text-gray-300">Vendeur</div>
                                    <div class="font-semibold">${package.vendor}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-300">Prix</div>
                                    <div class="text-xl font-bold text-green-400">${package.price}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="glass-panel p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-300">Code du colis</div>
                            <div class="font-mono text-cyan-400">${code}</div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-400 mr-2 pulse"></div>
                            <span class="text-sm text-green-400">Colis vérifié</span>
                        </div>
                    </div>
                </div>
            `;
        }

        function updateStep(step) {
            currentStep = step;
            const indicators = document.querySelectorAll('.step-indicator');
            indicators.forEach((indicator, index) => {
                if (index < step) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } else if (index === step - 1) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            });
        }

        function selectPayment(type) {
            selectedPayment = type;
            document.querySelectorAll('.payment-option').forEach(btn => {
                btn.classList.remove('border-2', 'border-cyan-400');
            });
            event.target.closest('.payment-option').classList.add('border-2', 'border-cyan-400');

            if (type === 'mobile') {
                document.getElementById('mobileOptions').classList.remove('hidden');
            } else {
                document.getElementById('mobileOptions').classList.add('hidden');
            }
        }

        function confirmPayment() {
            if (!selectedPayment) {
                alert('Veuillez choisir une méthode de paiement');
                return;
            }

            // Simulate payment processing
            updateStep(4);
            
            setTimeout(() => {
                updateStep(5);
                document.getElementById('paymentSection').classList.add('hidden');
                document.getElementById('successMessage').classList.remove('hidden');
            }, 2000);
        }

        function cancelProcess() {
            if (confirm('Êtes-vous sûr de vouloir annuler ?')) {
                resetProcess();
            }
        }

        function resetProcess() {
            currentStep = 1;
            selectedPayment = null;
            document.getElementById('manualCode').value = '';
            document.getElementById('packageInfo').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-300">Scannez un code QR pour voir les détails</p>
                </div>
            `;
            document.getElementById('paymentSection').classList.add('hidden');
            document.getElementById('successMessage').classList.add('hidden');
            document.getElementById('mobileOptions').classList.add('hidden');
            updateStep(1);
        }

        function downloadReceipt() {
            // Simulate receipt download
            alert('Téléchargement du reçu en cours...');
        }
    </script>
</body>
</html>
