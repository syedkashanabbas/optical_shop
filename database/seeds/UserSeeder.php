<?php

use App\Models\User;
use App\Models\Warehouse;
use App\Models\UserWarehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert some stuff
        DB::table('users')->insert(
            array(
                'id' => 1,
                'username' => 'William Castillo',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'avatar' => 'no_avatar.png',
                'role_users_id' => 1,
                'is_all_warehouses' => 1,
                'status' => 1,
            )
        );

        $user = User::findOrFail(1);
        $user->assignRole(1);

        $warehouse = Warehouse::create([
            'name' => 'optical shop',
        ]);

        UserWarehouse::insert([
            'user_id' => $user->id,
            'warehouse_id' => $warehouse->id,
        ]);
    }
}
