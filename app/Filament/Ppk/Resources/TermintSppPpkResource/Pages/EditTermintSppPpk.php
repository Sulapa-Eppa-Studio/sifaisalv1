<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Filament\Ppk\Resources\TermintSppPpkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditTermintSppPpk extends EditRecord
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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['ppspm_verification_status']      = 'in_progress';

        return $data;
    }


    // protected function afterSave(): void
    // {
    //     $data = $this->form->getState();

    //     // Hapus file lama jika ada dan simpan file baru
    //     if (!empty($data['files'])) {
    //         foreach ($data['files'] as $fileType => $file) {
    //             // Cari file lama
    //             $existingFile = $this->record->files()->where('file_type', $fileType)->first();

    //             if ($existingFile) {
    //                 // Hapus file lama dari storage
    //                 Storage::delete($existingFile->file_path);

    //                 // Hapus record file lama
    //                 $existingFile->delete();
    //             }

    //             // Simpan file baru dan dapatkan path
    //             $path = $file->store('termint_files');

    //             $this->record->files()->create([
    //                 'file_type' => $fileType,
    //                 'file_path' => $path,
    //             ]);
    //         }
    //     }
    // }
}
