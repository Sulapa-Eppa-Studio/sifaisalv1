<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringPengajuanResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\PaymentRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class MonitoringPengajuanResource extends Resource
{
    protected static ?string $model = PaymentRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $label = 'Monitoring Pembayaran';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';


    // canCreate, canEdit, canDelete, canDeleteAny
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



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_number')
                    ->label('Nomor Kontrak')
                    ->searchable(),

                TextColumn::make('request_number')
                    ->label('Nomor Permintaan')
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

                TextColumn::make('payment_description')
                    ->label('Deskripsi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                TextColumn::make('verification_progress')
                    ->label('Progres Verifikasi')
                    ->colors([
                        'primary'   => 'ppk',
                        'success'   => 'done',
                        'danger'    => 'rejected',
                        'warning'   => 'ppspm',
                        'secondary' => 'treasurer',
                    ])
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state);
                    })
                    ->sortable(),

                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),


                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi PP-SPM')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('treasurer_verification_status')
                    ->label('Status Verifikasi Bendahara')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('kpa_verification_status')
                    ->label('Status Verifikasi KPA')
                    ->colors([
                        'warning'   => 'not_available',
                        'primary'   => 'in_progress',
                        'success'   => 'approved',
                        'danger'    => 'rejected',
                    ])
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

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
                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'approved'))
                    ->label('Approved Payments'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'in_progress'))
                    ->label('In Progress Payments'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'rejected'))
                    ->label('Rejected Payments'),
            ])
            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListMonitoringPengajuans::route('/'),
            'create' => Pages\CreateMonitoringPengajuan::route('/create'),
            'edit' => Pages\EditMonitoringPengajuan::route('/{record}/edit'),
        ];
    }
}
