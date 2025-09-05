<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Candidate extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'whatsapp',
        'photo_url',
        'photo_filename',
        'description',
        'votes_count',
        'status',
        'created_by_user',
        'user_id',
        'supabase_user_id',
    ];

    protected $casts = [
        'created_by_user' => 'boolean',
        'votes_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function voteLimits(): HasMany
    {
        return $this->hasMany(VoteLimit::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOrderByVotes($query, $direction = 'desc')
    {
        return $query->orderBy('votes_count', $direction);
    }

    // Accesseurs
    public function getFullNameAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen(10);

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10);
    }

    public function getPhotoUrl(): ?string
    {
        return $this->getFirstMediaUrl('photos') ?: $this->photo_url;
    }

    public function getPhotoThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('photos', 'thumb') ?: $this->photo_url;
    }

    /**
     * Retourne les URLs optimisées pour les différentes tailles d'image
     */
    public function getOptimizedPhotoUrls(): array
    {
        if (!$this->photo_url && !$this->getFirstMediaUrl('photos')) {
            return [
                'main' => '/images/placeholder-avatar.svg',
                'thumb' => '/images/placeholder-avatar.svg',
                'small' => '/images/placeholder-avatar.svg',
            ];
        }

        $imageService = app(\App\Services\ImageOptimizationService::class);
        $originalPath = $this->photo_url ?? $this->getFirstMediaUrl('photos');

        // Extraire le chemin relatif pour le service d'optimisation
        if (str_starts_with($originalPath, 'http')) {
            $relativePath = str_replace(Storage::disk('public')->url(''), '', $originalPath);
        } else {
            $relativePath = $originalPath;
        }

        return $imageService->getOptimizedUrls($relativePath);
    }

    /**
     * Retourne l'URL de la photo principale optimisée
     */
    public function getMainPhotoUrl(): string
    {
        $urls = $this->getOptimizedPhotoUrls();
        return $urls['main'] ?? $urls['original'] ?? '/images/placeholder-avatar.svg';
    }

    /**
     * Retourne l'URL de la photo thumbnail optimisée
     */
    public function getThumbPhotoUrl(): string
    {
        $urls = $this->getOptimizedPhotoUrls();
        return $urls['thumb'] ?? $urls['original'] ?? '/images/placeholder-avatar.svg';
    }

    /**
     * Retourne l'URL de la petite photo optimisée
     */
    public function getSmallPhotoUrl(): string
    {
        $urls = $this->getOptimizedPhotoUrls();
        return $urls['small'] ?? $urls['original'] ?? '/images/placeholder-avatar.svg';
    }

    /**
     * Retourne l'URL de l'image thumbnail sans double suffixe
     */
    public function getCleanThumbUrl(): string
    {
        $photoUrl = $this->getPhotoUrl();

        if (!$photoUrl || $photoUrl === '/images/placeholder-avatar.svg') {
            return '/images/placeholder-avatar.svg';
        }

        // Si l'URL contient déjà _thumb, la retourner telle quelle
        if (strpos($photoUrl, '_thumb') !== false) {
            return $photoUrl;
        }

        // Sinon, générer l'URL thumbnail
        $pathInfo = pathinfo($photoUrl);
        $extension = $pathInfo['extension'] ?? 'jpg';
        $filename = $pathInfo['filename'];

        return str_replace($filename . '.' . $extension, $filename . '_thumb.' . $extension, $photoUrl);
    }

    /**
     * Vérifie si les images optimisées existent pour ce candidat
     */
    public function hasOptimizedImages(): bool
    {
        if (!$this->photo_url) {
            return false;
        }

        $pathInfo = pathinfo($this->photo_url);
        $filename = $pathInfo['filename'];
        $extension = $pathInfo['extension'];

        // Vérifier si au moins le thumbnail existe
        $thumbPath = "candidates/{$filename}_thumb.{$extension}";

        return Storage::disk('public')->exists($thumbPath);
    }

    /**
     * Déclenche l'optimisation des images pour ce candidat
     */
    public function optimizeImages(): bool
    {
        if (!$this->photo_url || !Storage::disk('public')->exists($this->photo_url)) {
            return false;
        }

        $this->update(['photo_optimization_status' => 'processing']);

        \App\Events\CandidatePhotoUploaded::dispatch($this, $this->photo_url);

        return true;
    }

    /**
     * Retourne le statut d'optimisation lisible
     */
    public function getOptimizationStatusAttribute(): string
    {
        return match($this->photo_optimization_status) {
            'pending' => 'En attente',
            'processing' => 'En cours...',
            'completed' => 'Terminé',
            'failed' => 'Échec',
            default => 'Inconnu'
        };
    }

    public function getPhotoUrlAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        // Si c'est déjà une URL complète, la retourner
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Sinon, générer l'URL depuis le storage
        return Storage::disk('public')->url($value);
    }

    // Méthodes utilitaires
    public function hasVotedToday(?User $user = null, ?string $ipAddress = null): bool
    {
        if (!$user && !$ipAddress) {
            return false;
        }

        $today = now()->toDateString();

        return $this->votes()
            ->where('vote_date', $today)
            ->when($user, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($ipAddress && !$user, function ($query) use ($ipAddress) {
                return $query->where('ip_address', $ipAddress);
            })
            ->exists();
    }

    public function incrementVoteCount(): void
    {
        $this->increment('votes_count');
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
