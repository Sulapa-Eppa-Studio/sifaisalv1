<?php

namespace App\Filament\PenyediaJasa\Resources;

use App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource\Pages;
use App\Filament\PenyediaJasa\Resources\MonitoringKontrakResource\RelationManagers;
use App\Models\Contract;
use App\Models\MonitoringKontrak;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitoringKontrakResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $label =   'Monitoring Kontrak';

    protected static ?string $navigationLabel = 'Monitoring Kontrak';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Menu Utama';


    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user   =   get_auth_user();

        $query = static::getModel()::query();

        if ($user->role == 'penyedia_jasa') {
            $query  =   static::getModel()::query()->where('service_provider_id', $user->services_provider->id);
        }

        return $query->orderBy('created_at', 'DESC');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('Nomor Kontrak')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contract_date')
                    ->label('Tanggal Kontrak')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('can_number')
                    ->label('Nomor CAN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('work_package')
                    ->label('Paket Pekerjaan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('execution_time')
                    ->label('Durasi Pekerjaan (Hari)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('advance_payment')
                    ->label('Pembayaran Uang Muka')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payment_stages')
                    ->label('Tahapan Pembayaran')
                    ->numeric()
                    ->suffix(' Tahap')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_value')
                    ->label('Nilai Kontrak')
                    ->searchable()
                    ->money('idr', true),

                Tables\Columns\TextColumn::make('paid_value')
                    ->label('Sudah Terbayar')
                    ->searchable()
                    ->money('idr', true),

                Tables\Columns\TextColumn::make('id')
                    ->label('Sisa Kontrak')
                    ->money('IDR', true)
                    ->formatStateUsing(function ($record) {
                        return 'Rp. ' . number_format($record->payment_value - $record->paid_value, 0, ',', '.');
                    }),

                Tables\Columns\TextColumn::make('service_provider')
                    ->label('Penyedia Jasa')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('npwp')
                    ->label('NPWP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('bank_account_number')
                    ->label('Nomor Rekening Bank')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('ppk_officer.full_name')
                    ->label('Pejabat PPK')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('working_unit')
                    ->label('Satuan Kerja')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([])
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
            'index' => Pages\ListMonitoringKontraks::route('/'),
            'create' => Pages\CreateMonitoringKontrak::route('/create'),
            'edit' => Pages\EditMonitoringKontrak::route('/{record}/edit'),
        ];
    }
}
