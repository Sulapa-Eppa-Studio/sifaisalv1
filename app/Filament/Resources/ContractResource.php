<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use App\Models\PPK;
use App\Models\RoleHasWorkPackage;
use App\Models\ServiceProvider;
use App\Models\WorkPackage;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $label = 'Kontrak';

    protected static ?string $navigationLabel = 'Kontrak';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Menu Utama';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = get_auth_user();

        $query = static::getModel()::query();

        if ($user->role == 'penyedia_jasa') {
            $query = static::getModel()::query()->where('service_provider_id', $user->services_provider->id);
        }

        return $query->orderBy('created_at', 'DESC');
    }

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
                    ->required(),

                Forms\Components\TextInput::make('can_number')
                    ->label('Nomor CAN')
                    ->maxLength(255),

                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->reactive(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state, Get $get) {
                        $startDate = $get('start_date');
                        if ($startDate && $state) {
                            $duration = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($state));
                            $set('execution_time', $duration);
                        }
                    }),

                Forms\Components\TextInput::make('execution_time')
                    ->label('Durasi Pekerjaan (Hari)')
                    ->numeric()
                    ->suffix('Hari')
                    ->readOnly() // Tidak bisa diedit secara manual
                    ->required()
                    ->disabled(fn($record) => $record != null) // Disable saat form edit
                    ->dehydrated(), // Tetap simpan data

                Forms\Components\TextInput::make('payment_stages')
                    ->label('Tahapan Pembayaran')
                    ->suffix('Tahap')
                    ->required()
                    ->numeric(),


                Forms\Components\Select::make('ppk_id')
                    ->label('Petugas PPK (Pejabat Pembuat Komitmen)')
                    ->searchable()
                    ->options(function () {
                        $ppks = PPK::get();

                        $options = [];

                        foreach ($ppks as $ppk) {
                            $options[$ppk->id] = $ppk->full_name . " ( " . $ppk->nip . " ) ";
                        }

                        return $options;
                    })
                    ->placeholder('Pilih NIP Petugas PPK')
                    ->required(),

                Forms\Components\Select::make('work_package')
                    ->label('Paket Pekerjaan')
                    ->searchable()
                    ->placeholder('Pilih Paket Pekerjaan')
                    ->options(function (Get $get) {

                        $ppk_id = $get('ppk_id') ?? null;

                        $ppk = PPK::find($ppk_id);

                        if (!$ppk) return [];

                        $wp =  $ppk->workPackages;

                        $op = [];

                        foreach ($wp as $pkg) {
                            $op[$pkg->name] = $pkg->name;
                        }

                        return $op;
                    })
                    ->required()
                    ->preload(),

                Forms\Components\TextInput::make('working_unit')
                    ->label('Unit Kerja')
                    ->required()
                    ->maxLength(255),

                Fieldset::make('Informasi Pembayaran')
                    ->schema([

                        Forms\Components\TextInput::make('payment_value')
                            ->label('Nilai Kontrak')
                            ->required()
                            ->prefix('Rp. ')
                            ->inlineLabel()
                            ->stripCharacters(',')
                            ->mask(RawJs::make('$money($input)'))
                            ->numeric(),

                        Forms\Components\Toggle::make('advance_payment')
                            ->label('Pembayaran Uang Muka')
                            ->live()
                            ->required(),

                    ]),

                Fieldset::make('Penyedia Jasa')
                    ->schema([

                        Forms\Components\Select::make('service_provider_id')
                            ->label('Penyedia Jasa')
                            ->required()
                            ->live()
                            ->columnSpanFull()
                            ->options(ServiceProvider::pluck('full_name', 'id')->toArray())
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $serviceProvider = ServiceProvider::find($state);
                                    if ($serviceProvider) {
                                        $set('npwp', $serviceProvider->npwp);
                                        $set('bank_account_number', $serviceProvider->account_number);
                                        $set('bank_name', $serviceProvider->bank_name);
                                    } else {
                                        $set('npwp', null);
                                        $set('bank_account_number', null);
                                        $set('bank_name', null);
                                    }
                                } else {
                                    $set('npwp', null);
                                    $set('bank_account_number', null);
                                    $set('bank_name', null);
                                }
                            }),

                        Forms\Components\TextInput::make('npwp')
                            ->label('NPWP')
                            ->required()
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('bank_account_number')
                            ->label('Nomor Rekening Bank')
                            ->required()
                            ->maxLength(199)
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('bank_name')
                            ->label('Nama Bank')
                            ->required()
                            ->maxLength(199)
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
                    ->searchable()->wrap(),

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
                    ->toggleable(isToggledHiddenByDefault: true),

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
            ->actions([
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
