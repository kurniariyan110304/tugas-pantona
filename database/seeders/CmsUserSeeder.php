<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsUser;
use Illuminate\Support\Facades\Hash;

class CmsUserSeeder extends Seeder
{
    public function run(): void
    {
        CmsUser::create([
            'email' => 'admin@gmail.com',
            'nama' => 'Admin',
            'password' => Hash::make('admin123'),
            'image' => null,
        ]);
    }
}