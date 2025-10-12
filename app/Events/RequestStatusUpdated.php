<?php

namespace App\Events;

use App\Models\HelpRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $helpRequest;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(HelpRequest $helpRequest, $oldStatus, $newStatus)
    {
        $this->helpRequest = $helpRequest;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('emergency-requests'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'request.status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'helpRequest' => [
                'id' => $this->helpRequest->id,
                'emergency_type' => $this->helpRequest->emergency_type,
                'description' => $this->helpRequest->description,
                'urgency_level' => $this->helpRequest->urgency_level,
                'location' => $this->helpRequest->location,
                'people_count' => $this->helpRequest->people_count,
                'status' => $this->helpRequest->status,
                'name' => $this->helpRequest->name,
                'phone' => $this->helpRequest->phone,
                'created_at' => $this->helpRequest->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->helpRequest->updated_at->format('Y-m-d H:i:s'),
            ],
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
