<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatusEnum;
use App\Enums\RolesEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Filament\Resources\ProductResource\Pages\ProductImages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;

  protected static ?string $navigationIcon = 'heroicon-s-queue-list';

  protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Grid::make()
          ->schema([
            Forms\Components\TextInput::make('title')
              ->label('Title')
              ->live(onBlur: true)
              ->afterStateUpdated(function (string $operation, $state, callable $set) {
                $set('slug', Str::slug($state));
              })
              ->required()
              ->autofocus(),
            Forms\Components\TextInput::make('slug')
              ->required(),
            Forms\Components\Select::make('department_id')
              ->label(__('Department'))
              ->relationship('department', 'name')
              ->required()
              ->preload()
              ->searchable()
              ->reactive()
              ->afterStateUpdated(function (callable $set) {
                $set('category_id', null);
              }),
            Forms\Components\Select::make('category_id')
              ->label(__('Category'))
              ->relationship(
                name: 'category',
                titleAttribute: 'name',
                modifyQueryUsing: function (Builder $query, callable $get) {
                  $departmentId = $get('department_id');
                  if ($departmentId) {
                    $query->where('department_id', $departmentId);
                  }
                }
              )
              ->preload()
              ->searchable()
              ->required(),
          ]),
        Forms\Components\RichEditor::make('description')
          ->required()
          ->toolbarButtons([
            'blockquote',
            'bold',
            'bulletList',
            'h2',
            'h3',
            'italic',
            'link',
            'orderedList',
            'redo',
            'strike',
            'underline',
            'undo',
            'table'
          ])
          ->columnSpan(2),
        Forms\Components\TextInput::make('price')
          ->numeric()
          ->required(),
        Forms\Components\TextInput::make('stock')
          ->numeric(),
        Forms\Components\Select::make('status')
          ->required()
          ->options(ProductStatusEnum::labels())
          ->default(ProductStatusEnum::Draft->value),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        SpatieMediaLibraryImageColumn::make('images')
          ->collection('images')
          ->limit(1)
          ->conversion('thumb'),
        Tables\Columns\TextColumn::make('title')
          ->words(5)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->badge()
          ->colors(ProductStatusEnum::colors()),
        Tables\Columns\TextColumn::make('department.name'),
        Tables\Columns\TextColumn::make('category.name'),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime(),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->options(ProductStatusEnum::labels()),
        Tables\Filters\SelectFilter::make('department_id')
          ->label(__('Department'))
          ->relationship('department', 'name'),
      ])
      ->actions([
        Tables\Actions\ActionGroup::make([
          Tables\Actions\EditAction::make(),
          Tables\Actions\DeleteAction::make(),
        ]),
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
      'images' => Pages\ProductImages::route('/{record}/images'),
      'variation-types' => Pages\ProductVariationTypes::route('/{record}/variation-types'),
    ];
  }

  public static function getRecordSubNavigation(Page $page): array {
    return $page->generateNavigationItems([
        EditProduct::class,
        Pages\ProductImages::class,
        Pages\ProductVariationTypes::class,
      ]);
  }

  public static function canViewAny(): bool {
    $user = Filament::auth()->user();
    return $user && $user->hasRole(RolesEnum::Vendor);
  }
}
