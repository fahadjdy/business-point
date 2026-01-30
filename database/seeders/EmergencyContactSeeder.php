<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmergencyContact;

class EmergencyContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Ambulance',
                'contact_number' => '108',
                'icon' => 'fa-solid fa-truck-medical',
                'badge' => '24/7',
                'color' => 'danger',
                'sort_order' => 1,
            ],
            [
                'name' => 'Police Station',
                'contact_number' => '100',
                'icon' => 'fa-solid fa-shield-halved',
                'badge' => '24/7',
                'color' => 'danger',
                'sort_order' => 2,
            ],
            [
                'name' => 'Fire Brigade',
                'contact_number' => '101',
                'icon' => 'fa-solid fa-fire-extinguisher',
                'badge' => '24/7',
                'color' => 'danger',
                'sort_order' => 3,
            ],
            [
                'name' => 'Sarpanch Office',
                'contact_number' => 'Local Official Hub',
                'icon' => 'fa-solid fa-building-columns',
                'badge' => 'OFFICIAL',
                'color' => 'primary',
                'sort_order' => 4,
            ],
        ];

        foreach ($contacts as $contact) {
            EmergencyContact::updateOrCreate(['name' => $contact['name']], $contact);
        }
    }
}
