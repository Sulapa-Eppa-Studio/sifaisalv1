<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use App\Models\PPK;
use App\Models\ServiceProvider;
use App\Models\WorkPackage;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $label =   'Tabel Kontrak';

    protected static ?string $navigationLabel = 'Kontrak';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contract_number')
                    ->label('Nomor Kontrak')
                    ->unique('contracts', 'contract_number', fn($record) => $record)
                    ->required()
                    ->minLength(3)
                    ->maxLength(255),

                Forms\Components\DatePicker::make('contract_date')
                    ->label('Tanggal Kontrak')
                    ->minDate(now())
                    ->required(),

                Forms\Components\TextInput::make('can_number')
                    ->label('Nomor CAN')
                    ->maxLength(255),

                Forms\Components\TextInput::make('execution_time')
                    ->label('Durasi Pekerjaan')
                    ->required()
                    ->numeric()->suffix(' Hari'),

                Forms\Components\TextInput::make('payment_stages')
                    ->label('Tahapan Pembayaran')
                    ->required()
                    ->prefix('Tahap ')
                    ->numeric(),

                Forms\Components\Select::make('work_package')
                    ->label('Paket Pekerjaan')
                    ->options(WorkPackage::pluck('name', 'name')->toArray())
                    ->required(),

                Forms\Components\Toggle::make('advance_payment')
                    ->label('Pembayaran Uang Muka')
                    ->required(),

                Fieldset::make('Petugas PPK')
                    ->schema([
                        Forms\Components\Select::make('ppk_officer')
                            ->label('Pejabat PPK')
                            ->required()
                            ->options(PPK::pluck('full_name', 'id')->toArray()),

                        Forms\Components\TextInput::make('working_unit')
                            ->label('Unit Kerja')
                            ->required()
                            ->maxLength(255),
                    ]),

                Fieldset::make('Penyedia Jasa')
                    ->schema([
                        Forms\Components\Select::make('service_provider_id')
                            ->label('Penyedia Jasa')
                            ->required()
                            ->columnSpanFull()
                            ->options(ServiceProvider::pluck('full_name', 'id')->toArray())
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $serviceProvider = ServiceProvider::find($state);
                                    if ($serviceProvider) {
                                        $set('npwp', $serviceProvider->npwp);
                                        $set('bank_account_number', $serviceProvider->account_number);
                                    } else {
                                        $set('npwp', null);
                                        $set('bank_account_number', null);
                                    }
                                } else {
                                    $set('npwp', null);
                                    $set('bank_account_number', null);
                                }
                            }),

                        Forms\Components\TextInput::make('npwp')
                            ->label('NPWP')
                            ->required()
                            ->length(16)
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('bank_account_number')
                            ->label('Nomor Rekening Bank')
                            ->required()
                            ->maxLength(20)
                            ->readOnly()
                            ->dehydrated(),
                    ]),
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
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah Pada')
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
