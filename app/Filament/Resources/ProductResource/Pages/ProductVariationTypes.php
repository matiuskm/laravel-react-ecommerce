<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class ProductVariationTypes extends EditRecord
{
  protected static string $resource = ProductResource::class;
  protected static ?string $navigationIcon = 'heroicon-m-numbered-list';
  protected static ?string $title = 'Variation Types';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Repeater::make('variationTypes')
          ->label(false)
          ->relationship()
          ->collapsible()
          ->defaultItems(1)
          ->addActionLabel('Add new variation type')
          ->columns(2)
          ->schema([
            Forms\Components\TextInput::make('name')
              ->required(),
            Forms\Components\Select::make('type')
              ->options(ProductVariationTypeEnum::labels())
              ->required(),
            Forms\Components\Repeater::make('options')
              ->relationship()
              ->collapsible()
              ->schema([
                Forms\Components\TextInput::make('name')
                  ->columnSpan(2)
                  ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                  ->image()
                  ->multiple()
                  ->openable()
                  ->panelLayout('grid')
                  ->collection('images')
                  ->reorderable()
                  ->appendFiles()
                  ->preserveFilenames()
              ])
              ->columnSpan(2),
          ])
          ->columnSpan(2),
      ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }
}
