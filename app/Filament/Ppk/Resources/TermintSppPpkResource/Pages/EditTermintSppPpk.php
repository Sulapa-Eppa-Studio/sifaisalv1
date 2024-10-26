<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Enums\FileType;
use App\Filament\Ppk\Resources\TermintSppPpkResource;
use App\Models\Contract;
use App\Models\PaymentRequest;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EditTermintSppPpk extends EditRecord
{
    protected static string $resource = TermintSppPpkResource::class;


    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        try {
            // Hapus 'files' dari data untuk mencegah masalah mass assignment
            unset($data['files']);

            // Tambahkan 'user_id' dari pengguna yang sedang login
            $data['user_id'] = Auth::user()->id;

            $contract    =   Contract::find($data['contract_id']);

            if ($contract->payment_value - $contract->paid_value < $data['payment_value']) {
                throw new \Exception('Nilai pembayaran melebihi sisa kontrak ' . 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.'));
            }

            PaymentRequest::find($data['payment_request_id'])->update([
                'verification_progress'     =>  'ppspm',
                'ppspm_verification_status' =>  'in_progress',
            ]);

            return $data;
        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Keslahan')
                ->body($th->getMessage())
                ->danger()
                ->color('#c44d47')
                ->send();

            $this->halt();
        }
    }

    protected function afterUpdate(): void
    {
        try {

            $data = $this->form->getState();

            // Simpan file-file
            if (!empty($data['files'])) {
                foreach ($data['files'] as $fileType => $filePath) {

                    if (empty($filePath)) {
                        continue;
                    }

                    $this->record->files()->create([
                        'file_type' => $fileType,
                        'file_path' => $filePath,
                    ]);
                }
            }
        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Keslahan')
                ->body($th->getMessage())
                ->danger()
                ->color('#c44d47')
                ->send();

            $this->halt();

            //throw $th;
        }
    }


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $files = $record->files()->get()->pluck('file_path', 'file_type')->toArray();

        foreach ($files as $key => $value) {
            $data['files'][$key]  =  $value;
        }

        // dd('files.' . FileType::KARWAS->value, $data);

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

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl('index');
    }



    protected function afterSave(): void
    {
        try {

            $data = $this->form->getState();

            // Simpan file-file
            if (!empty($data['files'])) {

                $this->record->files()->delete();

                foreach ($data['files'] as $fileType => $filePath) {

                    if (empty($filePath)) {
                        continue;
                    }

                    $this->record->files()->create([
                        'file_type' => $fileType,
                        'file_path' => $filePath,
                    ]);
                }
            }

        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Keslahan')
                ->body($th->getMessage())
                ->danger()
                ->color('#c44d47')
                ->send();

            $this->halt();

            //throw $th;
        }
    }
}
