<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteCasted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $surveyId;
    public $results;

    public function __construct($surveyId, $results = [])
    {
        $this->surveyId = $surveyId;
        $this->results = $results;
    }

    public function broadcastOn()
    {
        return new Channel('survey.' . $this->surveyId);
    }

    public function broadcastAs()
    {
        return 'VoteCasted';
    }

    public function broadcastWith()
    {
        return $this->results;
    }
} 