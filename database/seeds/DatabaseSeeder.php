<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* users */
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin',
            'password' => Hash::make('admin')
        ]);

        /* events */
        DB::table('events')->insert([
            'name' => 'Test Event',
            'status' => 'Active',
            'start_date' => '2022-05-10',
            'end_date' => '2022-06-10',
            'user_id' => 1
        ]);

        /* capacities */
        DB::table('capacities')->insert([
            'event_id' => 1,
            'type' => 'Silver',
            'capacity' => 100,
            'price' => 1500,
            'user_id' => 1
        ]);

        DB::table('capacities')->insert([
            'event_id' => 1,
            'type' => 'Gold',
            'capacity' => 50,
            'price' => 3500,
            'user_id' => 1
        ]);

        DB::table('capacities')->insert([
            'event_id' => 1,
            'type' => 'Platinum',
            'capacity' => 25,
            'price' => 5500,
            'user_id' => 1
        ]);

        /* Bookings */
        DB::table('bookings')->insert([
            'customer_name' => 'Rafeeque',
            'event_id' => 1,
            'type' => 1,
            'count' => 2,
            'amount' => 3000,
            'status' => 'Active',
            'user_id' => 1,
            'created_at' => '2022-05-01 13:49:44',
            'updated_at' => '2022-05-01 13:49:44',
        ]);

        DB::table('bookings')->insert([
            'customer_name' => 'Ajmal',
            'event_id' => 1,
            'type' => 2,
            'count' => 3,
            'amount' => 10500,
            'status' => 'Active',
            'user_id' => 1,
            'created_at' => '2022-05-02 13:49:44',
            'updated_at' => '2022-05-02 13:49:44',
        ]);

        DB::table('bookings')->insert([
            'customer_name' => 'Manu',
            'event_id' => 1,
            'type' => 3,
            'count' => 1,
            'amount' => 5500,
            'status' => 'Active',
            'user_id' => 1,
            'created_at' => '2022-05-03 13:49:44',
            'updated_at' => '2022-05-03 13:49:44',
        ]);
    }
}
