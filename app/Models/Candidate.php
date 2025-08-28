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