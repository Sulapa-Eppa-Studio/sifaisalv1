<?php

namespace App\Filament\Treasurer\Resources;

use App\Filament\Treasurer\Resources\MonitoringPengajuanResource\Pages;
use App\Filament\Treasurer\Resources\MonitoringPengajuanResource\RelationManagers;
use App\Models\MonitoringPengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

                // Menggunakan badge dan menyesuaikan label serta warna
                TextColumn::make('verification_progress')
                    ->label('Progres Verifikasi')
                    ->badge()
                    ->colors([
                        'primary'   => 'ppk',
                        'success'   => 'done',
                        'danger'    => 'rejected',
                        'warning'   => 'ppspm',
                        'secondary' => 'treasurer',
                    ])
                    ->formatStateUsing(function ($state) {
                        $labels = [
                            'ppk'       => 'PPK',
                            'ppspm'     => 'PP-SPM',
                            'treasurer' => 'Bendahara',
                            'done'      => 'Selesai',
                            'rejected'  => 'Ditolak',
                        ];
                        return $labels[$state] ?? strtoupper($state);
                    })
                    ->sortable(),

                // Menyesuaikan kolom status dengan badge dan label bahasa Indonesia
                TextColumn::make('ppk_verification_status')
                    ->label('Status Verifikasi PPK')
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
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('ppspm_verification_status')
                    ->label('Status Verifikasi PP-SPM')
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
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

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
                            'not_available' => 'Belum Tersedia',
                            'in_progress'   => 'Sedang Diproses',
                            'approved'      => 'Disetujui',
                            'rejected'      => 'Ditolak',
                        ];
                        return $labels[$state] ?? $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

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
                        return $labels[$state] ?? $state;
                    })
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
                    ->label('Pembayaran Disetujui'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'in_progress'))
                    ->label('Pembayaran Sedang Diproses'),

                Filter::make('verification_status')
                    ->query(fn(Builder $query): Builder => $query->where('verification_status', 'rejected'))
                    ->label('Pembayaran Ditolak'),
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
