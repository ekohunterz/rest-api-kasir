<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

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

        Category::create([
            'name' => 'Cemilan'
        ]);

        Category::create([
            'name' => 'Makanan'
        ]);

        Category::create([
            'name' => 'Minuman'
        ]);

        Product::create([
            'code' => 'C-01',
            'category_id' => 2,
            'name' => 'Sate Ayam',
            'price' => '16000',
            'is_ready' => true,
            'image' => 'sate-ayam.jpg'
        ]);

        Product::create([
            'code' => 'C-02',
            'category_id' => 2,
            'name' => 'Nasi Goreng Telur',
            'price' => '14000',
            'is_ready' => true,
            'image' => 'nasi-goreng-telor.jpg'
        ]);

        Product::create([
            'code' => 'C-03',
            'category_id' => 2,
            'name' => 'Nasi Rames',
            'price' => '12000',
            'is_ready' => true,
            'image' => 'nasi-rames.jpg'
        ]);

        Product::create([
            'code' => 'C-04',
            'category_id' => 2,
            'name' => 'Lontong Opor Ayam',
            'price' => '18000',
            'is_ready' => true,
            'image' => 'lontong-opor-ayam.jpg'
        ]);

        Product::create([
            'code' => 'C-05',
            'category_id' => 2,
            'name' => 'Mie Goreng',
            'price' => '13000',
            'is_ready' => true,
            'image' => 'mie-goreng.jpg'
        ]);

        Product::create([
            'code' => 'C-06',
            'category_id' => 2,
            'name' => 'Bakso',
            'price' => '10000',
            'is_ready' => true,
            'image' => 'bakso.jpg'
        ]);

        Product::create([
            'code' => 'C-07',
            'category_id' => 2,
            'name' => 'Sate Ayam',
            'price' => '16000',
            'is_ready' => true,
            'image' => 'sate-ayam.jpg'
        ]);

        Product::create([
            'code' => 'C-08',
            'category_id' => 2,
            'name' => 'Nasi Ayam Geprek',
            'price' => '17000',
            'is_ready' => true,
            'image' => 'nasi-ayam-geprek.jpg'
        ]);

        Product::create([
            'code' => 'C-09',
            'category_id' => 1,
            'name' => 'Cheese Burger',
            'price' => '30000',
            'is_ready' => true,
            'image' => 'cheese-burger.jpg'
        ]);

        Product::create([
            'code' => 'C-10',
            'category_id' => 1,
            'name' => 'Kentang Goreng',
            'price' => '14000',
            'is_ready' => true,
            'image' => 'kentang-goreng.jpg'
        ]);

        Product::create([
            'code' => 'C-11',
            'category_id' => 1,
            'name' => 'Pangsit',
            'price' => '20000',
            'is_ready' => true,
            'image' => 'pangsit.jpg'
        ]);

        Product::create([
            'code' => 'C-12',
            'category_id' => 3,
            'name' => 'Coffe Late',
            'price' => '15000',
            'is_ready' => true,
            'image' => 'coffe-late.jpg'
        ]);

        Product::create([
            'code' => 'C-13',
            'category_id' => 3,
            'name' => 'Es Jeruk',
            'price' => '8000',
            'is_ready' => true,
            'image' => 'es-jeruk.jpg'
        ]);

        Product::create([
            'code' => 'C-14',
            'category_id' => 3,
            'name' => 'Es Teh',
            'price' => '5000',
            'is_ready' => true,
            'image' => 'es-teh.jpg'
        ]);

        Product::create([
            'code' => 'C-15',
            'category_id' => 3,
            'name' => 'teh-hangat',
            'price' => '5000',
            'is_ready' => true,
            'image' => 'teh-hangat.jpg'
        ]);
    }
}
