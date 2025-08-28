<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\VoteLimit;
use App\Events\CandidateRegisteredEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CandidatesGallery extends Component
{
    public $candidates = [];
    public $userVotesToday = [];
    public $loadingVotes = [];

    public function mount()
    {
        $this->loadCandidates();
        $this->checkUserVotesToday();
    }

    public function loadCandidates()
    {
        $this->candidates = Candidate::approved()
            ->orderByVotes()
            ->get()
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'prenom' => $candidate->prenom,
                    'nom' => $candidate->nom,
                    'votes_count' => $candidate->votes_count,
                    'photo_url' => $candidate->getPhotoUrl() ?: '/images/placeholder-avatar.svg',
                    'description' => $candidate->description,
                ];
            })
            ->toArray();
    }

    public function checkUserVotesToday()
    {
        if (Auth::check()) {
            $today = now()->toDateString();
            $this->userVotesToday = Vote::where('user_id', Auth::id())
                ->where('vote_date', $today)
                ->pluck('candidate_id')
                ->toArray();
        }
    }

    public function vote($candidateId)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            session()->flash('error', 'Vous devez être connecté pour voter');
            return redirect()->route('auth.redirect', 'google');
        }

        $user = Auth::user();
        $today = now()->toDateString();
        $ipAddress = request()->ip();

        // Vérifier si déjà voté aujourd'hui
        if (in_array($candidateId, $this->userVotesToday)) {
            session()->flash('error', 'Vous avez déjà voté pour ce candidat aujourd\'hui !');
            return;
        }

        try {
            // Ajouter à l'état de chargement
            $this->loadingVotes[$candidateId] = true;

            // Vérifier les limites de vote
            $existingVote = Vote::where('candidate_id', $candidateId)
                ->where('user_id', $user->id)
                ->where('vote_date', $today)
                ->first();

            if ($existingVote) {
                session()->flash('error', 'Vous avez déjà voté pour ce candidat aujourd\'hui !');
                return;
            }

            // Vérifier aussi par IP (double sécurité)
            $existingIpVote = Vote::where('candidate_id', $candidateId)
                ->where('ip_address', $ipAddress)
                ->where('vote_date', $today)
                ->first();

            if ($existingIpVote) {
                session()->flash('error', 'Un vote a déjà été enregistré depuis cette adresse IP aujourd\'hui !');
                return;
            }

            // Transaction pour éviter les votes multiples
            \DB::transaction(function () use ($candidateId, $user, $today, $ipAddress) {
                // Créer le vote
                Vote::create([
                    'candidate_id' => $candidateId,
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                    'user_agent' => request()->userAgent(),
                    'vote_date' => $today,
                ]);

                // Créer la limite de vote
                VoteLimit::create([
                    'candidate_id' => $candidateId,
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                    'vote_date' => $today,
                    'vote_count' => 1,
                ]);

                // Incrémenter le compteur du candidat
                $candidate = Candidate::find($candidateId);
                $candidate->incrementVoteCount();
            });

            // Mettre à jour l'état local
            $this->userVotesToday[] = $candidateId;
            
            // Recharger les candidats pour voir le nouveau compteur
            $this->loadCandidates();

            session()->flash('success', 'Vote enregistré avec succès !');
            
            Log::info("Vote enregistré", [
                'candidate_id' => $candidateId,
                'user_id' => $user->id,
                'ip' => $ipAddress
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors du vote: " . $e->getMessage(), [
                'candidate_id' => $candidateId,
                'user_id' => $user->id ?? null
            ]);
            
            session()->flash('error', 'Erreur lors du vote: ' . $e->getMessage());
        } finally {
            // Retirer du chargement
            unset($this->loadingVotes[$candidateId]);
        }
    }

    public function hasVotedToday($candidateId)
    {
        return in_array($candidateId, $this->userVotesToday);
    }

    public function isLoading($candidateId)
    {
        return isset($this->loadingVotes[$candidateId]);
    }

    public function render()
    {
        return view('livewire.candidates-gallery');
    }
}