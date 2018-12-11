<?php

use Illuminate\Database\Seeder;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
            ['id' => 1, 'title' => 'Super Admin (can create other users)',],
            ['id' => 3, 'title' => 'Admin',],
            ['id' => 4, 'title' => 'Company Admin',],
            ['id' => 5, 'title' => 'Service Center Admin',],
            ['id' => 6, 'title' => 'Technician',],
            ['id' => 7, 'title' => 'Company Users',],

        ];

        foreach ($items as $item) {
            \App\Role::create($item);
        }
    }
}
