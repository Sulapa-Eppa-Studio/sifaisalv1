<?php

namespace App\Filament\Spm\Resources\SPMRequestResource\Pages;

use App\Filament\Spm\Resources\SPMRequestResource;
use App\Models\Contract;
use App\Models\TermintSppPpk;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSPMRequest extends CreateRecord
{
    protected static string $resource = SPMRequestResource::class;


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {

            $user   =   get_auth_user();

            $spm    =   $user->spm;

            $data['spm_id']     =   $spm->id;

            $termit_ppk     =   TermintSppPpk::findOrFail($data['ppk_request_id']);

            $contract       =   $termit_ppk->contract;

            if ($contract->payment_value - $contract->paid_value < $data['spm_value']) {
                throw new \Exception('Nilai SPM melebihi sisa kontrak ' . 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.'));
            }

            return $data;

        } catch (\Throwable $th) {

            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body($th->getMessage())
                ->send();

            $this->halt();
        }
    }
}
