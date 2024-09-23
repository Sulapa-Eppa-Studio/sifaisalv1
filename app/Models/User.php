<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\HasAvatar;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasAvatar
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar_url',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url("$this->avatar_url") : null;
    }


    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // relationships

    public function services_provider()
    {
        return $this->hasOne(\App\Models\ServiceProvider::class);
    }

    public function kpa()
    {
        return $this->hasOne(\App\Models\KPA::class);
    }

    public function ppk()
    {
        return $this->hasOne(\App\Models\PPK::class);
    }

    public function spm()
    {
        return $this->hasOne(SPM::class);
    }

    public function treasurer()
    {
        return $this->hasOne(Treasurer::class);
    }
}
