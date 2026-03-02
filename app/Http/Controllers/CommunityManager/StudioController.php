<?php

namespace App\Http\Controllers\CommunityManager;

use App\Http\Controllers\Controller;
use App\Models\FittingRoom;
use App\Models\StudioSession;
use App\Models\Package;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudioController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_fitting_rooms' => FittingRoom::count(),
            'available_rooms' => FittingRoom::where('status', 'available')->count(),
            'occupied_rooms' => FittingRoom::where('status', 'occupied')->count(),
            'maintenance_rooms' => FittingRoom::where('status', 'maintenance')->count(),
            'today_trials' => FittingRoom::whereDate('check_in_time', today())->count(),
            'total_sessions' => StudioSession::count(),
            'scheduled_sessions' => StudioSession::where('status', 'scheduled')->count(),
            'completed_sessions' => StudioSession::where('status', 'completed')->count(),
        ];

        $recentCheckIns = FittingRoom::with('package.vendor')
            ->where('status', 'occupied')
            ->orderBy('check_in_time', 'desc')
            ->take(5)
            ->get();

        $upcomingSessions = StudioSession::with('vendor.user', 'package')
            ->where('status', 'scheduled')
            ->where('scheduled_time', '>', now())
            ->orderBy('scheduled_time', 'asc')
            ->take(5)
            ->get();

        return view('studio.dashboard', compact('stats', 'recentCheckIns', 'upcomingSessions'));
    }

    public function fittingRooms(Request $request): JsonResponse
    {
        $rooms = FittingRoom::when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('current_client', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'rooms' => $rooms,
            'stats' => [
                'total' => $rooms->count(),
                'available' => $rooms->where('status', 'available')->count(),
                'occupied' => $rooms->where('status', 'occupied')->count(),
                'maintenance' => $rooms->where('status', 'maintenance')->count(),
            ]
        ]);
    }

    public function checkInClient(Request $request, $roomId): JsonResponse
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'guarantee_type' => 'required|in:id_card,phone,cash',
            'guarantee_details' => 'required|string',
            'package_code' => 'nullable|string|exists:packages,tracking_code',
        ]);

        $room = FittingRoom::findOrFail($roomId);

        if (!$room->isAvailable()) {
            return response()->json([
                'error' => 'La cabine n\'est pas disponible',
                'message' => 'Veuillez choisir une autre cabine'
            ], 400);
        }

        $result = $room->checkIn(
            $validated['client_name'],
            $validated['guarantee_type'],
            $validated['guarantee_details']
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'room' => $room->fresh(),
                'message' => 'Client enregistré avec succès dans la cabine',
                'check_in_time' => $room->check_in_time
            ]);
        }

        return response()->json([
            'error' => 'Impossible d\'enregistrer le client',
            'message' => 'Veuillez réessayer'
        ], 500);
    }

    public function checkOutClient($roomId): JsonResponse
    {
        $room = FittingRoom::findOrFail($roomId);

        if (!$room->isOccupied()) {
            return response()->json([
                'error' => 'La cabine n\'est pas occupée',
                'message' => 'Aucun client à faire sortir'
            ], 400);
        }

        $result = $room->checkOut();

        if ($result) {
            return response()->json([
                'success' => true,
                'room' => $room->fresh(),
                'message' => 'Client sorti avec succès',
                'duration' => $room->getOccupancyDuration()
            ]);
        }

        return response()->json([
            'error' => 'Impossible de sortir le client',
            'message' => 'Veuillez réessayer'
        ], 500);
    }

    public function shootingSlots(Request $request): JsonResponse
    {
        $sessions = StudioSession::with('vendor.user', 'package')
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->session_type, function ($query, $type) {
                $query->where('session_type', $type);
            })
            ->when($request->date, function ($query, $date) {
                $query->whereDate('scheduled_time', $date);
            })
            ->orderBy('scheduled_time', 'desc')
            ->paginate(15);

        $stats = [
            'total' => StudioSession::count(),
            'scheduled' => StudioSession::where('status', 'scheduled')->count(),
            'in_progress' => StudioSession::where('status', 'in_progress')->count(),
            'completed' => StudioSession::where('status', 'completed')->count(),
            'cancelled' => StudioSession::where('status', 'cancelled')->count(),
        ];

        $typeStats = [
            'photoshoot' => StudioSession::where('session_type', 'photoshoot')->count(),
            'video' => StudioSession::where('session_type', 'video')->count(),
            'smoothing' => StudioSession::where('session_type', 'smoothing')->count(),
            'makeup' => StudioSession::where('session_type', 'makeup')->count(),
        ];

        return response()->json([
            'sessions' => $sessions,
            'stats' => $stats,
            'type_stats' => $typeStats
        ]);
    }

    public function createShooting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'package_id' => 'required|exists:packages,id',
            'session_type' => 'required|in:photoshoot,video,smoothing,makeup',
            'scheduled_time' => 'required|date|after:now',
            'duration' => 'sometimes|integer|min:15|max:180',
        ]);

        $session = StudioSession::create([
            'vendor_id' => $validated['vendor_id'],
            'package_id' => $validated['package_id'],
            'session_type' => $validated['session_type'],
            'scheduled_time' => $validated['scheduled_time'],
            'duration' => $validated['duration'] ?? 60,
            'status' => 'scheduled',
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->load('vendor.user', 'package'),
            'message' => 'Session de shooting créée avec succès'
        ]);
    }

    public function startSession($sessionId): JsonResponse
    {
        $session = StudioSession::findOrFail($sessionId);

        if (!$session->isScheduled()) {
            return response()->json([
                'error' => 'La session n\'est pas programmée',
                'message' => 'Seules les sessions programmées peuvent être démarrées'
            ], 400);
        }

        $result = $session->startSession();

        if ($result) {
            return response()->json([
                'success' => true,
                'session' => $session->fresh(),
                'message' => 'Session démarrée avec succès',
                'start_time' => now()
            ]);
        }

        return response()->json([
            'error' => 'Impossible de démarrer la session',
            'message' => 'Veuillez réessayer'
        ], 500);
    }

    public function completeSession(Request $request, $sessionId): JsonResponse
    {
        $validated = $request->validate([
            'photos_count' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $session = StudioSession::findOrFail($sessionId);

        if (!$session->isInProgress()) {
            return response()->json([
                'error' => 'La session n\'est pas en cours',
                'message' => 'Seules les sessions en cours peuvent être complétées'
            ], 400);
        }

        $result = $session->completeSession($validated['photos_count']);

        if ($result) {
            return response()->json([
                'success' => true,
                'session' => $session->fresh(),
                'message' => 'Session complétée avec succès',
                'photos_count' => $validated['photos_count']
            ]);
        }

        return response()->json([
            'error' => 'Impossible de compléter la session',
            'message' => 'Veuillez réessayer'
        ], 500);
    }

    public function cancelSession($sessionId): JsonResponse
    {
        $session = StudioSession::findOrFail($sessionId);

        if (!$session->isScheduled() && !$session->isInProgress()) {
            return response()->json([
                'error' => 'La session ne peut pas être annulée',
                'message' => 'Seules les sessions programmées ou en cours peuvent être annulées'
            ], 400);
        }

        $result = $session->cancelSession();

        if ($result) {
            return response()->json([
                'success' => true,
                'session' => $session->fresh(),
                'message' => 'Session annulée avec succès'
            ]);
        }

        return response()->json([
            'error' => 'Impossible d\'annuler la session',
            'message' => 'Veuillez réessayer'
        ], 500);
    }

    public function getPerformanceMetrics(Request $request): JsonResponse
    {
        $dateRange = $request->date_range ?? now()->subDays(30)->format('Y-m-d') . ',' . now()->format('Y-m-d');
        $dates = explode(',', $dateRange);

        $fittingRoomMetrics = [
            'total_check_ins' => FittingRoom::whereBetween('check_in_time', [$dates[0], $dates[1]])->count(),
            'average_duration' => $this->calculateAverageDuration($dates),
            'peak_hours' => $this->getPeakHours($dates),
            'guarantee_types' => $this->getGuaranteeTypeStats($dates),
        ];

        $shootingMetrics = [
            'total_sessions' => StudioSession::whereBetween('scheduled_time', [$dates[0], $dates[1]])->count(),
            'completion_rate' => $this->calculateCompletionRate($dates),
            'type_distribution' => $this->getSessionTypeDistribution($dates),
            'average_duration' => StudioSession::whereBetween('scheduled_time', [$dates[0], $dates[1]])->avg('duration'),
        ];

        return response()->json([
            'fitting_rooms' => $fittingRoomMetrics,
            'shooting_sessions' => $shootingMetrics,
            'period' => $dateRange,
            'generated_at' => now()
        ]);
    }

    private function calculateAverageDuration(array $dates): float
    {
        $checkIns = FittingRoom::whereBetween('check_in_time', [$dates[0], $dates[1]])
            ->whereNotNull('check_out_time')
            ->get();

        if ($checkIns->isEmpty()) {
            return 0;
        }

        $totalDuration = $checkIns->sum(function ($room) {
            return $room->check_in_time->diffInMinutes($room->check_out_time);
        });

        return round($totalDuration / $checkIns->count(), 2);
    }

    private function getPeakHours(array $dates): array
    {
        $checkIns = FittingRoom::whereBetween('check_in_time', [$dates[0], $dates[1]])
            ->get()
            ->groupBy(function ($room) {
                return $room->check_in_time->format('H');
            });

        return $checkIns->map->count()->sortDesc()->take(3)->toArray();
    }

    private function getGuaranteeTypeStats(array $dates): array
    {
        return FittingRoom::whereBetween('check_in_time', [$dates[0], $dates[1]])
            ->whereNotNull('guarantee_type')
            ->groupBy('guarantee_type')
            ->map->count()
            ->toArray();
    }

    private function calculateCompletionRate(array $dates): float
    {
        $total = StudioSession::whereBetween('scheduled_time', [$dates[0], $dates[1]])->count();
        $completed = StudioSession::whereBetween('scheduled_time', [$dates[0], $dates[1]])
            ->where('status', 'completed')
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    private function getSessionTypeDistribution(array $dates): array
    {
        return StudioSession::whereBetween('scheduled_time', [$dates[0], $dates[1]])
            ->groupBy('session_type')
            ->map->count()
            ->toArray();
    }
}
