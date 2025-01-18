<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Example seeding data
        VariantValue::create([
            'variant_id' => 1,  // Replace with actual variant ID
            'value' => 'Small',
        ]);
    }
}
