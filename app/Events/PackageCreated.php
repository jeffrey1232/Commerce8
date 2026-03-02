<?php

namespace App\Events;

use App\Models\Package;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Package $package
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendor.' . $this->package->vendor_id),
            new Channel('packages'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'package.created';
    }

    public function broadcastWith(): array
    {
        return [
            'package_id' => $this->package->id,
            'tracking_code' => $this->package->tracking_code,
            'vendor_id' => $this->package->vendor_id,
            'client_name' => $this->package->client_name,
            'product_name' => $this->package->product_name,
            'total_amount' => $this->package->total_amount,
            'status' => $this->package->status,
            'created_at' => $this->package->created_at,
        ];
    }
}
