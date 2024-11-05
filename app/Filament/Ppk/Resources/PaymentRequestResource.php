<?php

namespace App\Filament\Ppk\Resources;

use App\Filament\Ppk\Resources\PaymentRequestResource\Pages;
use App\Models\Contract;
use App\Models\Document;
use App\Models\PaymentRequest;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class PaymentRequestResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $label = 'Verifikasi Pengajuan';

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
            ->columns(2)
            ->schema([

                TextInput::make('contract_number')
                    ->required(),

                TextInput::make('request_number')
                    ->required()
                    ->label('Nomor Permintaan')
                    ->maxLength(255),

                TextInput::make('payment_value')
                    ->string()
                    ->required()
                    ->prefix('Rp. ')
                    ->stripCharacters(',')
                    ->columnSpanFull()
                    ->label('Nilai Pembayaran')
                    ->mask(RawJs::make('$money($input)')),

                Textarea::make('payment_description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Deskripsi Pembayaran'),

                self::getPDFs(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user   =   get_auth_user();

        $query = static::getModel()::query()->where('ppk_id', $user->ppk->id)->orderBy('created_at', 'DESC');

        if (
            static::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            static::scopeEloquentQueryToTenant($query, $tenant);
        }

        return $query;
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')
                    ->label('No. Kontrak')
                    ->searchable(),

                TextColumn::make('request_number')
                    ->label('No. Pengajuan')
                    ->prefix('#')
                    ->searchable(),

                TextColumn::make('payment_stage')
                    ->label('Tahap Pembayaran')
                    ->prefix('Tahap ')
                    ->sortable(),

                TextColumn::make('payment_value')
                    ->label('Nilai Pembayaran')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('id')
                    ->label('Sisa Kontrak')
                    ->formatStateUsing(function ($record) {
                        $contract = $record->contract;
                        return 'Rp. ' . number_format($contract->payment_value - $contract->paid_value, 0, ',', '.');
                    })
                    ->sortable(),

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                // Menggunakan badge pada 'ppk_verification_status'
                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
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

                TextColumn::make('ppk_rejection_reason')
                    ->label('Alasan Penolakan')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_btn')
                    ->label('Setujui')
                    ->requiresConfirmation()
                    ->disabled(function (PaymentRequest $record) {
                        return $record->ppk_verification_status !== 'in_progress';
                    })
                    ->action(function (PaymentRequest $record, array $data) {
                        $record->update([
                            'ppk_verification_status'   =>  'approved',
                            'ppk_id'                    =>  get_auth_user()->ppk->id,
                            // 'verification_progress'     =>  'ppspm',
                            // 'ppspm_verification_status' =>  'in_progress',
                        ]);

                        Notification::make('x_not')
                            ->title('Permohonan Pembayaran Diterima')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' Diterima')
                            ->success()
                            ->send();

                        Notification::make()
                            ->title('Permohonan Pembayaran Disetujui')
                            ->body('Pengajuan Pembayaran #' . $record->contract_number . ' telah disetujui.')
                            ->sendToDatabase($record->service_provider->user);
                    })
                    ->icon('heroicon-o-check-circle'),

                Tables\Actions\Action::make('reject_btn')
                    ->label('Tolak')
                    ->requiresConfirmation()
                    ->disabled(function (PaymentRequest $record) {
                        return $record->ppk_verification_status !== 'in_progress';
                    })
                    ->modalWidth('xl')
                    ->form([
                        RichEditor::make('reject_reason')
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
                    ->action(function (PaymentRequest $record, array $data) {
                        $record->update([
                            'ppk_verification_status' => 'rejected',
                            'verification_progress'   => 'rejected',
                            'ppk_rejection_reason'    => $data['reject_reason'],
                            'ppk_id'                  => get_auth_user()->ppk->id,
                        ]);

                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Anda telah menolak permohonan dengan alasan: ' . $record->ppk_rejection_reason)
                            ->danger()
                            ->send();

                        Notification::make()
                            ->title('Permohonan Pembayaran Ditolak')
                            ->body('Permohonan Anda ditolak oleh PPK dengan alasan: ' . $record->ppk_rejection_reason)
                            ->sendToDatabase($record->service_provider->user);
                    })
                    ->color('danger')
                    ->icon('heroicon-o-x-mark'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPaymentRequests::route('/'),
            'create' => Pages\CreatePaymentRequest::route('/create'),
            'view' => Pages\PaymentRequestPayment::route('/{record}/view'),
            'edit' => Pages\EditPaymentRequest::route('/{record}/edit'),
        ];
    }

    public static function getPDFs()
    {
        return Repeater::make('documents')
            ->relationship()
            ->label('Daftar Dokumen Pendukung')
            ->columnSpanFull()
            ->grid(2)
            ->schema([

                TextInput::make('name')->label(''),

                Actions::make([

                    Action::make('View')
                        ->icon('heroicon-o-eye')
                        ->label('Tampilkan')
                        ->url(function (Document $record) {

                            return asset('/storage/' . $record->path);
                        }, true),

                ])->inlineLabel(),

            ]);
    }
}
