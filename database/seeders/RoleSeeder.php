<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['name' => 'Admin']);
        $userRole = Role::create(['name' => 'User']);

        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo(['general']);

        User::firstWhere('email', 'admin@gmail.com')->assignRole('Admin');
        User::where('email', '<>', 'admin@gmail.com')->get()->each(function ($user) {
            $user->assignRole('User');
        });
    }
}
