<?php

namespace App\Filament\Ppk\Resources\TermintSppPpkResource\Pages;

use App\Filament\Ppk\Resources\TermintSppPpkResource;
use App\Models\Contract;
use App\Models\PaymentRequest;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTermintSppPpk extends CreateRecord
{
    protected static string $resource = TermintSppPpkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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

        // Tutup modal
    }
}
