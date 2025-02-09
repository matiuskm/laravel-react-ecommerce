<?php

namespace App\Filament\Resources;

use App\Enums\RolesEnum;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use App\Filament\Resources\DepartmentResource\RelationManagers\CategoriesRelationManager;
use App\Models\Department;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
  protected static ?string $model = Department::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->live(onBlur: true)
          ->required()
          ->afterStateUpdated(function (string $operation, $state, callable $set) {
            $set('slug', Str::slug($state));
          }),
        Forms\Components\TextInput::make('slug')
          ->required(),
        Forms\Components\Textarea::make('meta-title')
          ->columnSpanFull(),
        Forms\Components\Textarea::make('meta-description')
          ->columnSpanFull(),
        Forms\Components\Toggle::make('active')
          ->required(),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('slug')
          ->searchable(),
        Tables\Columns\IconColumn::make('active')
          ->boolean(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        //
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
      CategoriesRelationManager::class
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListDepartments::route('/'),
      'create' => Pages\CreateDepartment::route('/create'),
      'edit' => Pages\EditDepartment::route('/{record}/edit'),
    ];
  }

  public static function canViewAny(): bool {
    $user = Filament::auth()->user();
    return $user && $user->hasRole(RolesEnum::Admin->value);
  }
}
