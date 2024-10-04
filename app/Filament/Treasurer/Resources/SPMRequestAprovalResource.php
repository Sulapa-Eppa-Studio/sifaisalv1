<?php

namespace App\Filament\Treasurer\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SPMRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Treasurer\Resources\SPMRequestAprovalResource\Pages;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\RawJs;

class SPMRequestAprovalResource extends Resource
{
    protected static ?string $model = SPMRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $label = 'Verifikasi Pengajuan SPM';

    protected static ?string $navigationGroup = 'Menu Utama';

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
                    ->required()
                    ->numeric(),

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
                    ->label('Nomor SPM')
                    ->numeric()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('spm_value')
                    ->label('Nilai SPM')
                    ->numeric()
                    ->money('IDR', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('treasurer_verification_status')
                    ->label('Status Verifikasi Bendahara')
                    ->colors([
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('treasurer_rejection_reason')
                    ->label('Alasan Penolakan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
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

                        if ($record->treasurer_verification_status == 'in_progress') {
                            return false;
                        }

                        return true;
                    })
                    ->action(function (SPMRequest $record, array $data) {

                        $record->update([
                            'treasurer_verification_status'     =>  'approved',
                            'treasurer_id'                      =>  get_auth_user()->treasurer->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan SPM Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->spm_number . ' Diterima')
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (SPMRequest $record) {

                        if ($record->treasurer_verification_status == 'in_progress') {
                            return false;
                        }

                        return true;
                    })
                    ->form([
                        TextInput::make('reject_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Kenapa anda menolaknya?')
                            ->minLength(3)
                            ->maxLength(199)
                    ])
                    ->action(function (SPMRequest $record, array $data) {

                        $record->update([
                            'treasurer_verification_status'     =>  'rejected',
                            'treasurer_rejection_reason'        =>  $data['reject_reason'],
                            'treasurer_id'                      =>  get_auth_user()->treasurer->id,
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan SPM ditolak')
                            ->body('Berhasil Menolak Permohonan Dengan alasan ' . "' $record->treasurer_rejection_reason '")
                            ->send();
                    })
                    ->color('danger')
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
            'view' => Pages\ViewSPMRequest::route('/{record}/show'),
            'index' => Pages\ListSPMRequestAprovals::route('/'),
            'create' => Pages\CreateSPMRequestAproval::route('/create'),
            'edit' => Pages\EditSPMRequestAproval::route('/{record}/edit'),
        ];
    }
}
