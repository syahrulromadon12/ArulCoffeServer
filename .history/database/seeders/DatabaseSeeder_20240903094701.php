<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Avatar;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Membuat data statuses
        Status::factory()->create();
        
        // Membuat data roles
        Role::factory()->create();

        // Membuat data avatars
        Avatar::factory()->create();

        // Membuat data categories
        Category::factory(5)->create();

        // Membuat data products
        Product::factory(5)->create();

        // Membuat data users
        User::factory()->create();

        // Membuat data orders
        Order::factory()->create();

        // Membuat data order items
        OrderItem::factory(30)->create();

        // Membuat data payments
        Payment::factory(10)->create();
    }
}
