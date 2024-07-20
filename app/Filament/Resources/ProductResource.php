<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Product information')->schema([
                            TextInput::make('name')
                                ->label("Product name")
                                ->required()
                                ->maxLength(255)
                                ->live()
                                ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                            TextInput::make('slug')
                                ->label("Product slug")
                                ->maxLength(255)
                                ->disabled()
                                ->required()
                                ->dehydrated()
                                ->unique(Product::class, 'slug', ignoreRecord: true),

                            MarkdownEditor::make("description")
                                ->columnSpanFull()
                                ->fileAttachmentsDirectory('products')

                        ])->columns(2),
                        Section::make('Images')
                            ->schema([
                                FileUpload::make('images')
                                    ->multiple()
                                    ->label('Image')
                                    ->image()
                                    ->maxFiles(5)
                                    ->reorderable()
                                    ->directory('products'),
                            ])

                    ])->columnSpan(2),
                Group::make()->schema([
                    Section::make("Price")
                        ->schema([
                            TextInput::make('price')
                                ->label("Product price")
                                ->required()
                                ->numeric()
                                ->prefix('VND')
                        ]),
                    Section::make("Associations")
                        ->schema([
                            Select::make("category_id")
                                ->label("Product category")
                                ->required()
                                ->searchable()
                                ->preload()
                                ->relationship('category', 'name'),
                            Select::make("brand_id")
                                ->label("Product brand")
                                ->required()
                                ->searchable()
                                ->preload()
                                ->relationship('brand', 'name')
                        ]),
                    Section::make("Status")
                        ->schema([
                            Toggle::make("is_stock")
                                ->label('Stocking')
                                ->required()
                                ->default(true),
                            Toggle::make("is_active")
                                ->label('Active')
                                ->required()
                                ->default(true),
                            Toggle::make("is_featured")
                                ->label('Featuring')
                                ->required()
                                ->default(false),
                            Toggle::make("on_sale")
                                ->label('Sale')
                                ->required()
                                ->default(false)
                        ])

                ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->limit(20),
                TextColumn::make('slug')
                    ->sortable()
                    ->limit(20),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->sortable(),
                TextColumn::make('price')
                    ->sortable()
                    ->money('VND'),
                ToggleColumn::make("is_stock")
                    ->tooltip("Click to change status")
                    ->extraAttributes(['class' => '!justify-center'])
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make("is_active")
                    ->tooltip("Click to change status")
                    ->extraAttributes(['class' => '!justify-center'])
                    ->toggleable(isToggledHiddenByDefault: false),
                ToggleColumn::make("is_featured")
                    ->tooltip("Click to change status")
                    ->extraAttributes(['class' => '!justify-center'])
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make("on_sale")
                    ->tooltip("Click to change status")
                    ->extraAttributes(['class' => '!justify-center'])
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\ViewColumn::make('status')
                //     ->view('filament.status-toggles')
                //     ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    public function confirmStatusChange($rowId)
    {
        $this->confirm('Are you sure you want to change the status?', [
            'onConfirmed' => 'changeStatus',
            'parameters' => [$rowId],
        ]);
    }
}
