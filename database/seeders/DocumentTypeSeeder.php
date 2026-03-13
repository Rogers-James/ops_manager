<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // $companyId = 1; // single tenant now

        $types = [
            ['name' => 'CNIC Front', 'required' => 1],
            ['name' => 'CNIC Back', 'required' => 1],
            ['name' => 'Employment Contract', 'required' => 1],

            ['name' => 'Resume / CV', 'required' => 0],
            ['name' => 'Academic Degree', 'required' => 0],
            ['name' => 'Experience Letter', 'required' => 0],
            ['name' => 'Passport', 'required' => 0],
            ['name' => 'Employee Photo', 'required' => 0],
            ['name' => 'Medical Certificate', 'required' => 0],
        ];

        foreach ($types as $t) {
            DocumentType::updateOrCreate(
                ['name' => $t['name']],
                ['required' => $t['required']]
            );
        }
    }
}
