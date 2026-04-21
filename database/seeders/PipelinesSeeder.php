<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use Illuminate\Database\Seeder;

class PipelinesSeeder extends Seeder
{
    public function run(): void
    {
        Pipeline::firstOrCreate(
            ['slug' => 'default'],
            [
                'name' => 'Default sales pipeline',
                'description' => 'The standard pipeline used for incoming leads.',
                'stages' => [
                    ['key' => 'new', 'label' => 'New', 'color' => 'info'],
                    ['key' => 'contacted', 'label' => 'Contacted', 'color' => 'primary'],
                    ['key' => 'qualified', 'label' => 'Qualified', 'color' => 'warning'],
                    ['key' => 'proposal', 'label' => 'Proposal sent', 'color' => 'warning'],
                    ['key' => 'won', 'label' => 'Won', 'color' => 'success', 'is_won' => true],
                    ['key' => 'lost', 'label' => 'Lost', 'color' => 'danger', 'is_lost' => true],
                ],
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );
    }
}
