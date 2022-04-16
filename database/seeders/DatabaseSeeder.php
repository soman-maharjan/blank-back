<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'seller']);
        $role3 = Role::create(['name' => 'user']);

        $permission1 = Permission::create(['name' => 'access admin dashboard']);
        $permission2 = Permission::create(['name' => 'access seller dashboard']);

        $role1->givePermissionTo($permission1);
        $role2->givePermissionTo($permission2);

        $user1 = User::where('email','admin@admin.com')->first();
        $user1->assignRole('admin');

        $user2 = User::where('email','seller@seller.com')->first();
        $user2->assignRole('seller');
    }
}
