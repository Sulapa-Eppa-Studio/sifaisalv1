<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\WorkPackage;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\ActionSize;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $label =   'Daftar Pengguna';

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Menu Utama';




    public static function canAccess(): bool
    {
        return get_auth_user()->role == 'admin';
    }


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at', 'DESC');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('avatar_url')
                    ->label('Gambar Profile')
                    ->directory('avatars')
                    ->image()
                    ->imageEditor()
                    ->columnSpanFull()
                    ->maxSize(1024 * 4),

                TextInput::make('name')
                    ->label('Nama Pengguna')
                    ->required()
                    ->minLength(3)
                    ->maxLength(199),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->alphaDash()
                    ->unique(User::class, 'username', fn($record) => $record)
                    ->minLength(3)
                    ->maxLength(90),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->required()
                    ->email()
                    ->columnSpanFull()
                    ->unique(User::class, 'email', fn($record) => $record)
                    ->minLength(3)
                    ->maxLength(199),

                Select::make('role')
                    ->label('Peran Pengguna')
                    ->required()
                    ->columnSpanFull()
                    ->live()
                    ->options(
                        [
                            'admin' => 'Admin',
                            'kpa' => 'KPA',
                            'penyedia_jasa' => 'Penyedia Jasa',
                            'ppk' => 'PPK',
                            'spm' => 'PP-SPM',
                            'bendahara' => 'Bendahara'
                        ]
                    ),

                Fieldset::make('Katasandi')->schema([

                    TextInput::make('password')
                        ->label('Katasandi')
                        ->required(function ($record) {
                            return !$record;
                        })
                        ->password()
                        ->revealable()
                        ->minLength(3)
                        ->maxLength(199),

                    TextInput::make('confirm_password')
                        ->label('Konfirmasi')
                        ->password()
                        ->revealable()
                        ->same('password')

                ]),


                Section::make('Data KPA')
                    ->disabled(function (Get $get) {
                        return $get('role') !== 'kpa';
                    })
                    ->visible(function (Get $get) {
                        return $get('role') == 'kpa';
                    })->relationship('kpa')
                    ->columns(2)
                    ->schema([

                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->minLength(3)
                            ->columnSpanFull()
                            ->maxLength(199)
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(18)
                            ->required(),

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->required(),
                    ]),


                Section::make('Data PP-SPM')
                    ->disabled(function (Get $get) {
                        return $get('role') !== 'spm';
                    })
                    ->visible(function (Get $get) {
                        return $get('role') == 'spm';
                    })->relationship('spm')
                    ->columns(2)
                    ->schema([

                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->minLength(3)
                            ->maxLength(199)
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(18)
                            ->required(),

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->required(),

                        TextInput::make('working_unit')
                            ->label('Unit Kerja')
                            ->string()
                            ->required()
                    ]),


                Section::make('Data PPK')
                    ->disabled(function (Get $get) {
                        return $get('role') !== 'ppk';
                    })
                    ->visible(function (Get $get) {
                        return $get('role') == 'ppk';
                    })->relationship('ppk')
                    ->columns(2)
                    ->schema([

                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->minLength(3)
                            ->maxLength(199)
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(18)
                            ->required(),

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->required(),

                        Select::make('working_package')
                            ->label('Paket Pekerjaan')
                            ->options(WorkPackage::all()->pluck('name', 'name')->toArray()),
                    ]),



                Section::make('Data Bendahara')
                    ->disabled(function (Get $get) {
                        return $get('role') !== 'bendahara';
                    })
                    ->visible(function (Get $get) {
                        return $get('role') == 'bendahara';
                    })->relationship('treasurer')
                    ->columns(2)
                    ->schema([

                        TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->minLength(3)
                            ->maxLength(199)
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP')
                            ->maxLength(18)
                            ->required(),

                        TextInput::make('position')
                            ->label('Jabatan')
                            ->required(),

                        TextInput::make('working_unit')
                            ->label('Unit Pekerjaan')
                            ->required()
                            ->maxLength(199),
                    ]),

                Section::make('Data Penyedia Jasa')
                    ->disabled(function (Get $get) {
                        return $get('role') !== 'penyedia_jasa';
                    })
                    ->visible(function (Get $get) {
                        return $get('role') == 'penyedia_jasa';
                    })->relationship('services_provider')
                    ->schema([
                        Fieldset::make('Identitas')->schema([
                            TextInput::make('registration_number')
                                ->label('Nomor Registrasi')
                                ->minLength(3)
                                ->maxLength(199)
                                ->required(),

                            TextInput::make('full_name')
                                ->label('Nama Penyedia')
                                ->minLength(3)
                                ->maxLength(199)
                                ->required(),

                            TextInput::make('npwp')
                                ->label('NPWP')
                                ->length(16)
                                ->maxLength(16)
                                ->required(),

                            TextInput::make('account_number')
                                ->label('Nomor Rekening')
                                ->maxLength(199)
                                ->required(),

                            TextInput::make('address')
                                ->label('Alamat')
                                ->maxLength(199)
                                ->required(),

                            Select::make('job_package')
                                ->required()
                                ->label('Paket Pekerjaan')
                                ->options(WorkPackage::all()->pluck('name', 'name')->toArray()),

                        ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Profile')
                    ->circular()
                    ->defaultImageUrl('/images/default_avatar.png'),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->formatStateUsing(fn(User $record): string => ucwords($record->name))
                    ->sortable(),

                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->placeholder('Tidak Tersedia')
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Peran')
                    ->badge()
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Admin')
                    ->options([
                        'admin' => 'admin',
                        'kpa' => 'kpa',
                        'penyedia_jasa' => 'penyedia_jasa',
                        'ppk' => 'ppk',
                        'spm' => 'spm',
                        'bendahara' => 'bendahara'
                    ]),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        "active"  =>  "Aktif",
                        "inactive" =>  "Tidak Aktif",
                        "banned" =>  "Banned",
                    ]),
            ])
            ->filtersFormWidth('md')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
