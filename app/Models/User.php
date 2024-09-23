<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
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

    // /**
    //  * Implementasi metode dari FilamentUser.
    //  *
    //  * @param \Filament\Panel $panel
    //  * @return bool
    //  */
    // public function canAccessPanel(Panel $panel): bool
    // {
    //     // if ($panel->getId() === 'admin') {
    //     //     // Hanya pengguna dengan email @yourdomain.com dan sudah verifikasi email yang bisa mengakses panel admin
    //     //     return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    //     // }

    //     if ($panel->getId() === 'ppk') {
    //         // Hanya pengguna dengan peran 'user' yang bisa mengakses panel user
    //         return $this->hasRole('ppk');
    //     }

    //     if ($panel->getId() === 'kpa') {
    //         // Hanya pengguna dengan peran 'user' yang bisa mengakses panel user
    //         return $this->hasRole('kpa');
    //     }

    //     if ($panel->getId() === 'treasurer') {
    //         // Hanya pengguna dengan peran 'user' yang bisa mengakses panel user
    //         return $this->hasRole('treasurer');
    //     }

    //     if ($panel->getId() === 'spm') {
    //         // Hanya pengguna dengan peran 'user' yang bisa mengakses panel user
    //         return $this->hasRole('spm');
    //     }

    //     if ($panel->getId() === 'service-provider') {
    //         // Hanya pengguna dengan peran 'user' yang bisa mengakses panel user
    //         return $this->hasRole('service_provider');
    //     }

    //     // Untuk panel lain, izinkan akses berdasarkan kondisi yang diinginkan
    //     return false;
    // }



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

    public function termintSppPpks()
    {
        return $this->hasMany(TermintSppPpk::class);
    }
}
