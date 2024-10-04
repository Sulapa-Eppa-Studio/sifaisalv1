<?php

namespace App\Filament\Spm\Resources;

use Filament\Forms;
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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Support\RawJs;

class SPMRequestResource extends Resource
{
    protected static ?string $model = SPMRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $label = 'Surat Perintah Membayar (SPM)';

    protected static ?string $navigationLabel = 'SPM';

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
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
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('spm_value')
                    ->label('Nilai SPM')
                    ->required()
                    ->stripCharacters(',')
                    ->maxLength(255)
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
                            ->minSize(12)
                            ->required()
                            ->columnSpanFull()
                            ->maxSize(1024 * 12),

                        Select::make('ppk_request_id')
                            ->label('Pilih SPP-PPK')
                            ->placeholder('Pilih SPP-PPK')
                            ->required()
                            ->searchable()
                            ->options(get_list_ppk_request()),

                        Select::make('payment_request_id')
                            ->label('Nomor Pengajuan Pembayaran')
                            ->placeholder('Pilih Nomor Pengajuan')
                            ->required()
                            ->searchable()
                            ->options(get_list_request_payment('ppspm')),
                    ])
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
                Tables\Actions\EditAction::make(),
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
