<?php

namespace App\Filament\Kpa\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SPMRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Kpa\Resources\SPMRequestAprovalResource\Pages;
use App\Models\Contract;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\RawJs;

use Filament\Tables\Columns\TextColumn;

class SPMRequestAprovalResource extends Resource
{
    protected static ?string $model = SPMRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $label = 'Verifikasi Pengajuan SPM';

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function getEloquentQuery(): Builder
    {
        return parent::getModel()::query()->where('kpa_verification_status', '!=', 'not_available');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ...

                TextInput::make('spm_number')
                    ->label('No SPM')
                    ->required(),

                TextInput::make('spm_value')
                    ->label('Nilai SPM')
                    ->required()
                    ->stripCharacters(',')
                    ->maxLength(255)
                    ->mask(RawJs::make('$money($input)')),

                TextInput::make('spm_description')
                    ->label('Uraian Pembayaran SPM')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),

                Repeater::make('docs')
                    ->label('Daftar Dokumen Pendukung')
                    ->columnSpanFull()
                    ->grid(2)
                    ->schema([

                        TextInput::make('name')->label(''),

                        Actions::make([

                            Action::make('path')
                                ->icon('heroicon-o-eye')
                                ->label('Tampilkan')
                                ->url(function ($state) {
                                    return asset('/storage/' . $state['path']);
                                }, true),

                        ])->inlineLabel(),

                    ])

                // ...
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('spm_number')
                    ->label('No. SPM')
                    ->numeric()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ppk_request.no_termint')
                    ->label('No. PPK')
                    ->prefix('#')
                    ->sortable(),

                TextColumn::make('payment_request.contract.work_package')
                    ->label('Paket Pekerjaan')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),

                Tables\Columns\TextColumn::make('spm_value')
                    ->label('Nilai SPM')
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        return format_number_new($state);
                    })
                    ->prefix('Rp. ')
                    ->sortable(),

                // Menggunakan badge pada 'kpa_verification_status'
                TextColumn::make('kpa_verification_status')
                    ->label('Status Verifikasi KPA')
                    ->badge()
                    ->colors([
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'in_progress' => 'Sedang Diproses',
                            'approved'    => 'Disetujui',
                            'rejected'    => 'Ditolak',
                        ];
                        return $labels[$state] ?? ucfirst($state);
                    })
                    ->sortable(),

                TextColumn::make('kpa_rejection_reason')
                    ->label('Alasan Penolakan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_btn')
                    ->label('Setujui')
                    ->requiresConfirmation()
                    ->disabled(function (SPMRequest $record) {
                        return $record->kpa_verification_status !== 'in_progress';
                    })
                    ->action(function (SPMRequest $record, array $data) {

                        $contract = $record->payment_request->contract;

                        if ($contract instanceof Contract) {

                            if ($contract->paid_value >= $contract->payment_value) {

                                Notification::make()
                                    ->title('Gagal Menyetujui')
                                    ->body('Kontrak #' . $contract->contract_number . ' sudah terbayarkan!')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $contract->update([
                                'paid_value' => $record->spm_value + $contract->paid_value,
                            ]);
                        } else {

                            Notification::make()
                                ->title('Gagal Menyetujui')
                                ->body('Kontrak tidak ditemukan!')
                                ->danger()
                                ->send();

                            return;
                        }

                        $payment_request = $record->payment_request;

                        $payment_request->update([
                            'kpa_id'                  => get_auth_user()->kpa->id,
                            'verification_progress'   => 'done',
                            'kpa_verification_status' => 'approved',
                        ]);

                        $record->update([
                            'kpa_verification_status' => 'approved',
                            'kpa_id'                  => get_auth_user()->kpa->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan SPM Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->spm_number . ' telah disetujui.')
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (SPMRequest $record) {
                        return $record->kpa_verification_status !== 'in_progress';
                    })
                    ->modalWidth('xl')
                    ->form([
                        \Filament\Forms\Components\RichEditor::make('reject_reason')
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->label('Alasan Penolakan')
                            ->placeholder('Kenapa Anda menolaknya?')
                            ->required(),
                    ])
                    ->color('danger')
                    ->action(function (SPMRequest $record, array $data) {

                        $record->update([
                            'kpa_verification_status' => 'rejected',
                            'kpa_rejection_reason'    => $data['reject_reason'],
                            'kpa_id'                  => get_auth_user()->kpa->id,
                        ]);

                        $payment_request = $record->payment_request;

                        $payment_request->update([
                            'kpa_id'                  => get_auth_user()->kpa->id,
                            'kpa_rejection_reason'    => $data['reject_reason'],
                            'verification_progress'   => 'rejected',
                            'kpa_verification_status' => 'rejected',
                        ]);

                        Notification::make()
                            ->title('Permohonan SPM Ditolak')
                            ->body('Anda telah menolak permohonan dengan alasan: ' . $record->kpa_rejection_reason)
                            ->danger()
                            ->send();
                    })
                    ->icon('heroicon-o-x-mark'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => Pages\ViewSPMRequest::route('/{record}/view'),
            'index' => Pages\ListSPMRequestAprovals::route('/'),
            'create' => Pages\CreateSPMRequestAproval::route('/create'),
            'edit' => Pages\EditSPMRequestAproval::route('/{record}/edit'),
        ];
    }
}
