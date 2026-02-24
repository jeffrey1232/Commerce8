<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Essai;
use App\Models\Cabine;
use App\Models\ServiceDigital;
use Illuminate\Support\Facades\Auth;

class CommunityManagerController extends Controller
{
    public function index()
    {
        // Statistiques du Community Manager
        $stats = [
            'today_shootings' => Essai::whereDate('created_at', today())->count(),
            'followed_clients' => 156, // Simulé pour le moment
            'satisfaction_rate' => 89, // Simulé pour le moment
            'today_reservations' => Cabine::whereDate('created_at', today())->count(),
        ];

        // Planning du jour
        $todaySchedule = collect([
            [
                'time' => '09:00 - 10:00',
                'client' => 'Fashion Store',
                'project' => 'Collection Printemps',
                'studio' => 'Studio A',
                'status' => 'online'
            ],
            [
                'time' => '10:30 - 12:00',
                'client' => 'Mode Abidjan',
                'project' => 'Lookbook',
                'studio' => 'Studio B',
                'status' => 'pending'
            ],
            [
                'time' => '14:00 - 16:00',
                'client' => 'Client VIP',
                'project' => 'Shooting personnel',
                'studio' => 'Studio A',
                'status' => 'offline'
            ]
        ]);

        // Disponibilité des studios
        $studios = collect([
            [
                'name' => 'Studio A',
                'status' => 'Disponible',
                'occupancy' => 30
            ],
            [
                'name' => 'Studio B',
                'status' => 'Partiellement disponible',
                'occupancy' => 60
            ],
            [
                'name' => 'Studio C (Extérieur)',
                'status' => 'Disponible',
                'occupancy' => 10
            ]
        ]);

        // Production tracker
        $productionItems = collect([
            ['status' => 'Prêt', 'image' => 'fashion1'],
            ['status' => 'En cours', 'image' => 'fashion2'],
            ['status' => 'Prêt', 'image' => 'fashion3'],
            ['status' => 'Retouche', 'image' => 'fashion4'],
            ['status' => 'Prêt', 'image' => 'fashion5'],
            ['status' => 'En cours', 'image' => 'fashion6'],
            ['status' => 'Prêt', 'image' => 'fashion7'],
            ['status' => 'Validation', 'image' => 'fashion8'],
        ]);

        // Calendrier de contenu
        $contentCalendar = collect([
            [
                'title' => 'Collection Printemps',
                'platforms' => ['Instagram', 'Facebook'],
                'scheduled_time' => 'Aujourd\'hui, 18:00',
                'platform_colors' => ['purple', 'blue']
            ],
            [
                'title' => 'Témoignage Client',
                'platforms' => ['TikTok', 'Reels'],
                'scheduled_time' => 'Demain, 12:00',
                'platform_colors' => ['black', 'pink']
            ],
            [
                'title' => 'Behind the Scenes',
                'platforms' => ['YouTube Shorts'],
                'scheduled_time' => 'Demain, 15:00',
                'platform_colors' => ['red']
            ]
        ]);

        return view('community.manager', compact(
            'stats',
            'todaySchedule',
            'studios',
            'productionItems',
            'contentCalendar'
        ));
    }

    public function createShooting(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'project_type' => 'required|string',
            'studio_id' => 'required|exists:cabines,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string'
        ]);

        // Créer un nouvel essai/shooting
        $shooting = Essai::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'client_name' => $validated['client_name'],
            'project_type' => $validated['project_type'],
            'cabine_id' => $validated['studio_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'notes' => $validated['notes'] ?? '',
            'status' => 'scheduled',
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shooting créé avec succès',
            'shooting' => $shooting
        ]);
    }

    public function updateProductionStatus(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'status' => 'required|string|in:Prêt,En cours,Retouche,Validation'
        ]);

        // Logique pour mettre à jour le statut de production
        return response()->json([
            'success' => true,
            'message' => 'Statut de production mis à jour'
        ]);
    }

    public function getPerformanceMetrics()
    {
        // Données simulées pour les métriques de performance
        $metrics = [
            'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            'engagement' => [1200, 1900, 1500, 2500, 2200, 3000, 2800],
            'conversions' => [400, 600, 450, 800, 700, 950, 850]
        ];

        return response()->json($metrics);
    }
}
