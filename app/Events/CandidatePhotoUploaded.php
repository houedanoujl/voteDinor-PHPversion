<?php

namespace App\Events;

use App\Models\Candidate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CandidatePhotoUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Candidate $candidate;
    public string $photoPath;

    public function __construct(Candidate $candidate, string $photoPath)
    {
        $this->candidate = $candidate;
        $this->photoPath = $photoPath;
    }
}