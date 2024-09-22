<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Filament\Ppk\Resources\TermintSppPpkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTermintSppPpk extends CreateRecord
{
    protected static string $resource = TermintSppPpkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hapus 'files' dari data untuk mencegah masalah mass assignment
        unset($data['files']);


        // Tambahkan 'user_id' dari pengguna yang sedang login
        $data['user_id'] = Auth::user()->id;

        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        // Simpan file-file
        if (!empty($data['files'])) {
            foreach ($data['files'] as $fileType => $filePath) {
                $this->record->files()->create([
                    'file_type' => $fileType,
                    'file_path' => $filePath,
                ]);
            }
        }
    }
}
