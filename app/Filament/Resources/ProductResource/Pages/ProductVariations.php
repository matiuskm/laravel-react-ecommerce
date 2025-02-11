<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\ProductVariationTypeEnum;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
  protected static string $resource = ProductResource::class;
  protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
  protected static ?string $title = 'Variations';

  public function form(Form $form): Form
  {
    $types = $this->record->variationTypes;
    $fields = [];

    foreach($types as $type) {
      $fields[] = Forms\Components\TextInput::make('variation_type_'. ($type->id) . '.id')
        ->hidden();
      $fields[] = Forms\Components\TextInput::make('variation_type_'. ($type->id) . '.name')
        ->label($type->name);
    }

    return $form
      ->schema([
        Forms\Components\Repeater::make('variations')
          ->label(false)
          ->collapsible()
          ->addable(false)
          ->defaultItems(1)
          ->schema([
            Forms\Components\Section::make()
              ->schema($fields)
              ->columns(3),
            Forms\Components\TextInput::make('stock')
              ->numeric(),
            Forms\Components\TextInput::make('price')
              ->numeric(),
          ])
          ->columns(2)
          ->columnSpan(2)
      ]);
  }

  protected function getHeaderActions(): array
  {
    return [
      Actions\DeleteAction::make(),
    ];
  }

  protected function mutateFormDataBeforeFill(array $data): array {
    $variations = $this->record->variations->toArray();
    $data['variations'] = $this->mergeCartesianWithExisting($this->record->variationTypes, $variations);

    return $data;
  }

  private function mergeCartesianWithExisting($variationTypes, $existingData): array {
    $defaultQuantity = $this->record->stock;
    $defaultPrice = $this->record->price;
    $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
    $mergedResult = [];

    foreach ($cartesianProduct as $product) {
      $optionIds = collect($product)
        ->filter(fn($value, $key) => str_starts_with($key, 'variation_type_'))
        ->map(fn($option) => $option['id'])
        ->values()
        ->toArray();

      $match = array_filter($existingData, function ($existingOption) use ($optionIds) {
          return $existingOption['variation_type_option_ids'] === $optionIds;
      });

      if (!empty($match)) {
        $existingEntry = reset($match);
        $product['id'] = $existingEntry['id'];
        $product['stock'] = $existingEntry['stock'];
        $product['price'] = $existingEntry['price'];
      } else {
        $product['stock'] = $defaultQuantity;
        $product['price'] = $defaultPrice;
      }

      $mergedResult[] = $product;
    }

    return $mergedResult;
  }

  private function cartesianProduct($variationTypes, $defaultQuantity = null, $defaultPrice = null): array {
    $result = [[]];

    foreach ($variationTypes as $index => $variationType) {
      $tmp = [];

      foreach ($variationType->options as $option) {
        foreach ($result as $combination) {
          $newCombination = $combination + [
            'variation_type_' . ($variationType->id) => [
              'id' => $option->id,
              'name' => $option->name,
              'label' => $variationType->name,
            ],
          ];

          $tmp[] = $newCombination;
        }
      }

      $result = $tmp;
    }

    foreach ($result as &$combination) {
      if (count($combination) === count($variationTypes)) {
        $combination['stock'] = $defaultQuantity;
        $combination['price'] = $defaultPrice;
      }
    }

    return $result;
  }

  protected function mutateFormDataBeforeSave(array $data): array {
    // Initialize an array to store the formatted data
    $formattedData = [];

    // Loop through the variations to restructuring the data
    foreach($data['variations'] as $option) {
      $variationTypeOptionIds = [];
      foreach($this->record->variationTypes as $variationType) {
        $variationTypeOptionIds[] = $option['variation_type_'. ($variationType->id)]['id'];
      }

      $stock = $option['stock'];
      $price = $option['price'];

      $formattedData[] = [
        'id' => $option['id'],
        'variation_type_option_ids' => $variationTypeOptionIds,
        'stock' => $stock,
        'price' => $price,
      ];
    }
    $data['variations'] = $formattedData;
    return $data;
  }

  protected function handleRecordUpdate(Model $record, array $data): Model {
    $variations = $data['variations'];
    unset($data['variations']);

    $variations = collect($variations)->map(function($variation) {
      return [
        'id' => $variation['id'],
        'variation_type_option_ids' => json_encode($variation['variation_type_option_ids']),
        'stock' => $variation['stock'],
        'price' => $variation['price'],
      ];
    })->toArray();

    $record->variations()->delete();
    $record->variations()->upsert($variations, ['id'], ['variation_type_option_ids', 'stock', 'price']);

    return $record;
  }
}
