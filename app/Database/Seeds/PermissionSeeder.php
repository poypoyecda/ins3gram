<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'administrateur',
                'slug' => 'administrateur',
            ],
            [
                'name' => 'utilisateur',
                'slug' => 'utilisateur',
            ],
        ];
        $this->db->table('user_permission')->insertBatch($data);
    }
}
