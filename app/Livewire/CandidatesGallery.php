<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\Vote;
use App\Models\VoteLimit;
use App\Events\CandidateRegisteredEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class CandidatesGallery extends Component
{
    use WithPagination;

    public $candidates = [];
    public $userVotesToday = [];
    public $loadingVotes = [];
    public $perPage = 12;
    public $page = 1;
    public $totalCandidates = 0;
    public $showAuthModal = false;

    public function mount()
    {
        $this->loadCandidates();
        $this->checkUserVotesToday();
    }

    public function loadCandidates()
    {
        // Mise en cache des candidats avec pagination
        $cacheKey = "candidates_approved_page_{$this->page}_per_{$this->perPage}";
        
        $result = Cache::remember($cacheKey, 300, function () { // 5 minutes
            $candidates = Candidate::approved()
                ->orderByVotes()
                ->select(['id', 'prenom', 'nom', 'votes_count', 'photo_url', 'photo_filename', 'description'])
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->get();

            $total = Candidate::approved()->count();

            return [
                'candidates' => $candidates->map(function ($candidate) {
                    // Utiliser les URLs optimisées du modèle
                    $photoUrl = $candidate->getPhotoUrl() ?: '/images/placeholder-avatar.svg';
                    $thumbUrl = $candidate->getThumbPhotoUrl();
                    
                    return [
                        'id' => $candidate->id,
                        'prenom' => $candidate->prenom,
                        'nom' => $candidate->nom,
                        'votes_count' => $candidate->votes_count,
                        'photo_url' => $photoUrl,
                        'thumb_url' => $thumbUrl,
                        'description' => $candidate->description,
                    ];
                })->toArray(),
                'total' => $total
            ];
        });

        $this->candidates = $result['candidates'];
        $this->totalCandidates = $result['total'];
    }

    public function checkUserVotesToday()
    {
        if (Auth::check()) {
            // Mise en cache des votes utilisateur d'aujourd'hui
            $cacheKey = 'user_votes_today_' . Auth::id() . '_' . now()->toDateString();
            $this->userVotesToday = Cache::remember($cacheKey, 600, function () {
                return Vote::where('user_id', Auth::id())
                    ->whereDate('created_at', today())
                    ->pluck('candidate_id')
                    ->toArray();
            });
        }
    }

    public function vote($candidateId)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            $this->showAuthModal = true;
            $this->dispatch('show-auth-modal');
            return;
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
                ->whereDate('created_at', today())
                ->first();

            if ($existingVote) {
                session()->flash('error', 'Vous avez déjà voté pour ce candidat aujourd\'hui !');
                return;
            }

            // Transaction pour éviter les votes multiples
            \DB::transaction(function () use ($candidateId, $user, $ipAddress) {
                // Créer le vote
                Vote::create([
                    'candidate_id' => $candidateId,
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                ]);

                // Incrémenter le compteur du candidat
                $candidate = Candidate::find($candidateId);
                $candidate->incrementVoteCount();
            });

            // Mettre à jour l'état local immédiatement
            $this->userVotesToday[] = $candidateId;
            
            // Mettre à jour le compteur de votes localement pour un feedback immédiat
            foreach ($this->candidates as &$candidate) {
                if ($candidate['id'] == $candidateId) {
                    $candidate['votes_count'] = $candidate['votes_count'] + 1;
                    break;
                }
            }

            // Invalider les caches liés
            Cache::forget('user_votes_today_' . $user->id . '_' . now()->toDateString());
            Cache::forget("candidates_approved_page_{$this->page}_per_{$this->perPage}");
            Cache::forget('contest_stats');
            Cache::forget('top_candidates_10');
            
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

    public function loadMore()
    {
        $this->page++;
        $this->loadCandidates();
    }

    public function hasVotedToday($candidateId)
    {
        return in_array($candidateId, $this->userVotesToday);
    }

    public function isLoading($candidateId)
    {
        return isset($this->loadingVotes[$candidateId]);
    }

    public function hasMoreCandidates()
    {
        return count($this->candidates) < $this->totalCandidates;
    }

    public function closeAuthModal()
    {
        $this->showAuthModal = false;
    }

    public function render()
    {
        return view('livewire.candidates-gallery');
    }
}