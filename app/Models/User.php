<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel as FilamentPanel;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasAvatar, FilamentUser
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

    public function canAccessPanel(FilamentPanel $panel): bool
    {
        $role   =   get_auth_user()->role;

        return match ($panel->getId()) {
            'admin' => $role == 'admin',
            'kpa' => $role == 'kpa',
            'ppk' => $role == 'ppk',
            'treasurer' => $role == 'bendahara',
            'spm' => $role == 'spm',
            'penyediaJasa' => $role == 'penyedia_jasa',
        };
    }

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

    public function termintSppPpks()
    {
        return $this->hasMany(TermintSppPpk::class);
    }
}
