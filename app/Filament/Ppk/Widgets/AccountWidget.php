<?php

namespace App\Filament\Ppk\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AccountWidget extends Widget
{
    protected static string $view = 'filament.ppk.widgets.account-widget';

    protected int | string | array $columnSpan = 'full';


    protected function getHeading(): string
    {
        return 'Selamat Datang, ' . Auth::user()->name . '!';
    }



    protected function getSubheading(): string
    {
        return 'siFaisal: (Sistem Informasi veriFikasi Anggaran yang dIlaksanakan Secara kontraktuAL)';
    }

    public $notifications;

    public function mount()
    {
        // Contoh: Mengambil data notifikasi pengguna
        $this->notifications = Auth::user()->unreadNotifications;
    }
}
