<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuponResource\Pages;
use App\Filament\Resources\CuponResource\RelationManagers;
use App\Models\Cupon;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CuponResource extends Resource
{
    protected static ?string $model = Cupon::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->Schema([
                        TextInput::make('name')
                            ->label("Cupon name")
                            ->required()
                            ->maxLength(255),
                        TextInput::make('code')
                            ->label("Cupon code")
                            ->required()
                            ->maxLength(255),
                        TextInput::make('cupon')
                            ->label("Discount")
                            ->required()
                            ->numeric()
                            ->prefix('VND')
                    ]),


                // ]),
                Grid::make()
                    ->schema([
                        DateTimePicker::make('start_date')
                            ->label("Start date")
                            ->required()
                            ->default(fn () => Carbon::now('Asia/Ho_Chi_Minh'))
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get) {
                                $isActive = $get('is_active');
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');

                                if ($startDate && $endDate) {
                                    $now = Carbon::now();
                                    $isActive = $now->between(Carbon::parse($startDate), Carbon::parse($endDate));
                                }
                                $get('is_active', $isActive);
                            }),

                        DateTimePicker::make('end_date')
                            ->label("End date")
                            ->required()
                            ->after('start_date')
                            ->reactive()
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Asia/Ho_Chi_Minh')
                            ->afterOrEqual('start_date')
                            ->rules([
                                function (callable $get) {
                                    return Rule::requiredIf(fn () => filled($get('start_date')));
                                },
                            ])
                            ->validationAttribute('End date')
                            ->afterStateUpdated(function ($state, callable $get) {
                                $isActive = $get('is_active');
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');

                                if ($startDate && $endDate) {
                                    $now = Carbon::now();
                                    $isActive = $now->between(Carbon::parse($startDate), Carbon::parse($endDate));
                                }
                                $get('is_active', $isActive);
                            }),
                        MarkdownEditor::make("description")
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('cupons'),
                        // Toggle::make('is_active')
                        //     ->label('Active')
                        //     ->required()
                        //     ->default(false) // Giá trị mặc định  
                        //     ->dehydrated(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('code')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('start_date')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh')
                    ->searchable(),
                TextColumn::make('end_date')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Ho_Chi_Minh')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('is_active')
                    ->label(function ($record) {
                        $now = Carbon::now();
                        $endDate = Carbon::parse($record->end_date);

                        if ($record->is_active && $now->gt($endDate)) {
                            return 'Inactive';
                        } else {
                            return $record->is_active ? 'Active' : 'Inactive';
                        }
                    })
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->tooltip("Click to change status")
                    ->extraAttributes(['class' => '!justify-center'])
                    ->getStateUsing(function ($record) {
                        $now = Carbon::now();
                        $endDate = Carbon::parse($record->end_date);

                        // If is_active is already true but the current date is greater than the end date, set is_active to false
                        if ($record->is_active && $now->gt($endDate)) {
                            return false;
                        } else {
                            return $record->is_active;
                        }
                    }),
            ])
            ->filters([
                Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Tên')
                            ->placeholder('Tìm theo tên'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name): Builder => $query->where('name', 'like', "%{$name}%")
                            );
                    }),
                Filter::make('code')
                    ->form([
                        Forms\Components\TextInput::make('code')
                            ->label('Mã')
                            ->placeholder('Tìm theo mã'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['code'],
                                fn (Builder $query, $code): Builder => $query->where('code', 'like', "%{$code}%")
                            );
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-o-ellipsis-vertical'),
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
            'index' => Pages\ListCupons::route('/'),
            'create' => Pages\CreateCupon::route('/create'),
            'edit' => Pages\EditCupon::route('/{record}/edit'),
        ];
    }
}
