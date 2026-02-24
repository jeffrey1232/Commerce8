@props(['colis'])

<div class="glass-panel p-4 hover-scale cursor-pointer" onclick="showColisDetails('{{ $colis->uuid }}')">
    <div class="flex justify-between items-start mb-3">
        <div>
            <div class="text-sm font-mono text-cyan-400">{{ $colis->tracking_code }}</div>
            <div class="text-xs text-gray-300">{{ $colis->product_name }}</div>
        </div>
        <div class="text-right">
            <div class="text-lg font-bold text-green-400">{{ number_format($colis->total_amount, 0, ',', ' ') }} FCFA</div>
            <div class="text-xs text-gray-300">{{ $colis->created_at->format('d/m H:i') }}</div>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <div class="status-indicator status-{{ $colis->status === 'paid' ? 'online' : ($colis->status === 'deposited' ? 'pending' : 'offline') }}"></div>
            <span class="text-xs">{{ strtoupper(__('colis.status_' . $colis->status)) }}</span>
        </div>
        <div class="flex space-x-2">
            @if($colis->canBeWithdrawn())
                <button onclick="event.stopPropagation(); withdrawColis('{{ $colis->uuid }}')" class="px-2 py-1 bg-blue-500 rounded text-xs hover:bg-blue-600">
                    <i class="fas fa-hand-holding-box mr-1"></i>{{ __('colis.withdraw') }}
                </button>
            @endif
            @if($colis->canBePaid())
                <button onclick="event.stopPropagation(); payColis('{{ $colis->uuid }}')" class="px-2 py-1 bg-green-500 rounded text-xs hover:bg-green-600">
                    <i class="fas fa-money-bill-wave mr-1"></i>{{ __('common.pay') }}
                </button>
            @endif
        </div>
    </div>
</div>

<script>
function showColisDetails(uuid) {
    // Ouvrir une modal avec les détails du colis
    window.location.href = `/colis/${uuid}`;
}

function withdrawColis(uuid) {
    if (confirm('Confirmer le retrait de ce colis?')) {
        fetch(`/api/colis/${uuid}/withdraw`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        });
    }
}

function payColis(uuid) {
    window.location.href = `/colis/${uuid}/payment`;
}
</script>
