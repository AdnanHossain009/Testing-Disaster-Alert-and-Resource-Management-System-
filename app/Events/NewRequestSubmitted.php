<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\HelpRequest;

class NewRequestSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $helpRequest;

    /**
     * Create a new event instance.
     */
    public function __construct(HelpRequest $helpRequest)
    {
        $this->helpRequest = $helpRequest;
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
     */
    public function broadcastAs(): string
    {
        return 'new.request.submitted';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->helpRequest->id,
            'name' => $this->helpRequest->name,
            'phone' => $this->helpRequest->phone,
            'location' => $this->helpRequest->location,
            'request_type' => $this->helpRequest->request_type,
            'urgency' => $this->helpRequest->urgency ?? 'Medium',
            'people_count' => $this->helpRequest->people_count ?? 1,
            'description' => $this->helpRequest->description,
            'latitude' => $this->helpRequest->latitude,
            'longitude' => $this->helpRequest->longitude,
            'created_at' => $this->helpRequest->created_at->toISOString(),
            'timestamp' => now()->toISOString(),
        ];
    }
}
