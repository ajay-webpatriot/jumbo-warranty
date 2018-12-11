<?php

use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
            ['id' => 1, 'role_id' => 1, 'company_id' => null, 'service_center_id' => null, 'name' => 'Admin', 'phone' => null, 'address_1' => null, 'address_2' => null, 'location_address' => null, 'location_latitude' => null, 'location_longitude' => null, 'email' => 'admin@admin.com', 'password' => '$2y$10$a/ZNrp2/n8qyExiUbfzzAeZpqD.jw69J1GEtV86AAG8KqKOqiqav.', 'remember_token' => ''],

        ];

        foreach ($items as $item) {
            \App\User::create($item);
        }
    }
}
