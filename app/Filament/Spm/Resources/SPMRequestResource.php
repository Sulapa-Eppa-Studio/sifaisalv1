<?php

namespace App\Filament\Spm\Resources;

use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SPMRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Spm\Resources\SPMRequestResource\Pages;
use App\Filament\Spm\Resources\SPMRequestResource\RelationManagers;
use App\Models\PPK;
use App\Models\TermintSppPpk;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class SPMRequestResource extends Resource
{
    protected static ?string $model = SPMRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $label = 'Surat Perintah Membayar (SPM)';

    protected static ?string $navigationLabel = 'SPM';

    protected static ?string $navigationGroup = 'Menu Utama';

    /**
     * Whether the user can create a new SPM request.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->treasurer_verification_status == 'rejected' || $record->kpa_verification_status == 'rejected';
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('spm_number')
                    ->label('No SPM')
                    ->required(),

                Forms\Components\TextInput::make('spm_value')
                    ->label('Nilai SPM')
                    ->required()
                    ->stripCharacters(',')
                    ->mask(RawJs::make('$money($input)')),

                Forms\Components\TextInput::make('spm_description')
                    ->label('Uraian Pembayaran SPM')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),

                Fieldset::make('Dokumen Pendukung')
                    ->schema([

                        FileUpload::make('spm_document')
                            ->directory('documents')
                            ->label('Surat Perintah Membayar')
                            ->uploadingMessage('Upload dokumen...')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required()
                            ->columnSpanFull()
                            ->maxSize(1024 * 25),

                        Select::make('ppk_request_id')
                            ->label('Pilih SPP-PPK')
                            ->placeholder('Pilih SPP-PPK')
                            ->required()
                            ->live()
                            ->searchable()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $ppk = TermintSppPpk::find($state);

                                if ($ppk?->payment_request?->ppspm_verification_status !== 'approved') {
                                    Notification::make()
                                        ->title('Peringatan!')
                                        ->body('Verifikasi Dokumen Pendukung Belum Disetujui')
                                        ->color('#c44d47')
                                        ->send();

                                    return;
                                }

                                $set('payment_request_id', $ppk?->payment_request_id);
                                $set('payment_request_name', $ppk?->payment_request?->request_number);
                            })
                            ->options(get_list_ppk_request()),

                        TextInput::make('payment_request_name')
                            ->disabled()
                            ->hiddenOn('view')
                            ->label('Nomor Pengajuan Pembayaran'),

                        TextInput::make('payment_request_name')
                            ->visibleOn('view')
                            ->disabled()
                            ->formatStateUsing(function ($record) {
                                return $record->payment_request?->request_number ?? 'Belum Tersedia';
                            })
                            ->label('Nomor Pengajuan Pembayaran'),

                        Hidden::make('payment_request_id'),
                    ]),


                Fieldset::make('Progres Verifikasi Petugas Bendahara')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->treasurer_verification_status === 'not_available';
                    })
                    ->schema([

                        TextInput::make('treasurer.full_name')
                            ->label('Petugas Bendahara')
                            ->formatStateUsing(function ($record) {
                                return $record?->treasurer?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        TextInput::make('treasurer_verification_status')
                            ->label('Status Verifikasi Petugas Bendahara')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        RichEditor::make('treasurer_rejection_reason')
                            ->label('Alasan Penolakan Bendahara')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),

                Fieldset::make('Progres Verifikasi KPA')
                    ->visibleOn(['view', 'edit'])
                    ->hidden(function ($record) {
                        return $record?->kpa_verification_status === 'not_available';
                    })
                    ->schema([

                        TextInput::make('kpa.full_name')
                            ->label('Petugas KPA')
                            ->formatStateUsing(function ($record) {
                                return $record?->kpa?->full_name ?? 'Belum Tersedia';
                            })
                            ->disabled(),

                        TextInput::make('kpa_verification_status')
                            ->label('Status Verifikasi KPA')
                            ->formatStateUsing(function ($state) {
                                return match ($state) {
                                    'not_available' => 'Belum Tersedia',
                                    'in_progress' => 'Dalam Proses',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    default => $state,
                                };
                            })
                            ->disabled(),

                        RichEditor::make('kpa_rejection_reason')
                            ->label('Alasan Penolakan KPA')
                            ->columnSpanFull()
                            ->hidden(function ($state) {
                                return !$state ? true : false;
                            })
                            ->disabled(),
                    ]),
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

                Tables\Columns\TextColumn::make('spm_value')
                    ->label('Nilai SPM')
                    ->numeric()
                    ->money('IDR', true)
                    ->sortable(),

                // Menggunakan badge pada 'treasurer_verification_status'
                TextColumn::make('treasurer_verification_status')
                    ->label('Status Verifikasi Bendahara')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Diproses',
                            'in_progress' => 'Sedang Diproses',
                            'approved'    => 'Disetujui',
                            'rejected'    => 'Ditolak',
                        ];
                        return $labels[$state] ?? ucfirst($state);
                    })
                    ->sortable(),

                // Menggunakan badge pada 'kpa_verification_status'
                TextColumn::make('kpa_verification_status')
                    ->label('Status Verifikasi KPA')
                    ->badge()
                    ->colors([
                        'warning' => 'not_available',
                        'primary' => 'in_progress',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? ucfirst($state);
                    })
                    ->sortable(),

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
                Action::make('next_kpa')
                    ->label('Ajukan Ke KPA')
                    ->icon('heroicon-o-arrow-right')
                    ->action(function ($record) {

                        $record->update([
                            'kpa_verification_status' => 'in_progress',
                        ]);

                        Notification::make()
                            ->title('Pengajuan Berhasil')
                            ->body('SPM berhasil diajukan ke KPA.')
                            ->success()
                            ->send();
                    })
                    ->disabled(function ($record) {
                        return !($record->treasurer_verification_status === 'approved' && $record->kpa_verification_status === 'not_available');
                    }),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListSPMRequests::route('/'),
            'create' => Pages\CreateSPMRequest::route('/create'),
            'edit' => Pages\EditSPMRequest::route('/{record}/edit'),
        ];
    }
}
