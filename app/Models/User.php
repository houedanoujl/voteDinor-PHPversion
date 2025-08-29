<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'role',
        'type',
        'prenom',
        'nom',
        'whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function hasVotedToday()
    {
        return $this->votes()
            ->whereDate('created_at', today())
            ->exists();
    }

    public function canVoteForCandidate($candidateId)
    {
        return !$this->votes()
            ->where('candidate_id', $candidateId)
            ->whereDate('created_at', today())
            ->exists();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    public function isCandidate()
    {
        return $this->type === 'candidate';
    }
    
    public function isVoter()
    {
        return $this->type === 'voter';
    }
    
    public function candidate()
    {
        return $this->hasOne(Candidate::class);
    }
    
    public function getTypeLabel()
    {
        return match($this->type) {
            'admin' => 'Administrateur',
            'candidate' => 'Candidat',
            'voter' => 'Votant',
            default => $this->type
        };
    }
}
