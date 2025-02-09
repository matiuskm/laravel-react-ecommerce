<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
  use SoftDeletes;

  public function department(): BelongsTo
  {
    return $this->belongsTo(Department::class);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }
}
