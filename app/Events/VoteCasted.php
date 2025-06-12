<?php

namespace App\Events;

use App\Models\Survey;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteCasted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Survey $survey
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('survey.' . $this->survey->id),
        ];
    }

    public function broadcastWith(): array
    {
        return ['survey' => $this->survey->toArray()];
    }
}