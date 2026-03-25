<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mutuo', 'Affitto', 'Spese bancarie', 'Bolletta elettricità',
            'Bolletta acqua', 'Gas', 'Spese alimentari', 'Trasporti',
            'Voli', 'Viaggi', 'Farmacia', 'Assicurazione',
            'Abbonamenti (Netflix, Spotify, ecc.)', 'Ristoranti',
            'Tempo libero', 'Altro'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat, 'created_by' => null]);
        }
    }
}
