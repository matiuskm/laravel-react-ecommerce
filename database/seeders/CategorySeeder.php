<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $categories = [
        [
          'name' => 'Electronics',
          'department_id' => 1,
          'parent_id' => null,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Fashion',
          'department_id' => 2,
          'parent_id' => null,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],

        [
          'name' => 'Computers',
          'department_id' => 1,
          'parent_id' => 1,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Smartphones',
          'department_id' => 1,
          'parent_id' => 1,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],

        [
          'name' => 'Laptops',
          'department_id' => 1,
          'parent_id' => 3,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'Desktops',
          'department_id' => 1,
          'parent_id' => 3,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],

        [
          'name' => 'Android',
          'department_id' => 1,
          'parent_id' => 4,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],
        [
          'name' => 'iPhones',
          'department_id' => 1,
          'parent_id' => 4,
          'active' => 1,
          'created_at' => now(),
          'updated_at' => now(),
        ],

      ];

      DB::table('categories')->insert($categories);
    }
}
