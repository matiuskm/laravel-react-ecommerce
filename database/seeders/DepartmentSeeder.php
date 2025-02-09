<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $departments = [
      [
        'name' => 'Electronics',
        'slug' => Str::slug('Electronics'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Fashion',
        'slug' => Str::slug('Fashion'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Home, Garden & Tools',
        'slug' => Str::slug('Home, Garden & Tools'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Health & Beauty',
        'slug' => Str::slug('Health & Beauty'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Books & Audible',
        'slug' => Str::slug('Books & Audible'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Toys & Hobbies',
        'slug' => Str::slug('Toys & Hobbies'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Sporting Goods',
        'slug' => Str::slug('Sporting Goods'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Automotive',
        'slug' => Str::slug('Automotive'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'Collectibles',
        'slug' => Str::slug('Collectibles'),
        'active' => 1,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ];

    DB::table('departments')->insert($departments);
  }
}
