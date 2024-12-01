<?php

namespace Database\Seeders;

use App\Models\Invoicing;
use App\Models\Seller;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'JM Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'is_admin' => true
        ]);

        Seller::create([
            'name' => 'Ryan',
            'role' => 'Pré Venda',
        ]);

        Seller::create([
            'name' => 'Elizeu',
            'role' => 'Pré Venda',
        ]);

        Seller::create([
            'name' => 'Juscelino',
            'role' => 'Pronta Entrega',
        ]);

        Seller::create([
            'name' => 'Neves',
            'role' => 'Pronta Entrega',
        ]);



        for ($i = 1; $i < 5; $i++) {
            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-11-04',
                'final_date' => '2024-11-08',
                'month_sale' => 11
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-11-11',
                'final_date' => '2024-11-15',
                'month_sale' => 11
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-11-18',
                'final_date' => '2024-11-22',
                'month_sale' => 11
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-11-25',
                'final_date' => '2024-11-29',
                'month_sale' => 11
            ]);
        }

        for ($i = 1; $i < 5; $i++) {
            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-09-30',
                'final_date' => '2024-10-04',
                'month_sale' => 10,
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-10-07',
                'final_date' => '2024-10-11',
                'month_sale' => 10,
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-10-14',
                'final_date' => '2024-10-18',
                'month_sale' => 10,
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-10-21',
                'final_date' => '2024-10-25',
                'month_sale' => 10,
            ]);

            Invoicing::create([
                'seller_id' => $i,
                'nfe_value' => fake()->numberBetween(59800.00, 60000.00),
                'bol_value' => fake()->numberBetween(59700.00, 59800.00),
                'initial_date' => '2024-10-28',
                'final_date' => '2024-11-01',
                'month_sale' => 10,
            ]);
        }
    }
}
