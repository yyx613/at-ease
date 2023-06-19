<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Product A',
                'price' => 12.50,
                'img_name' => 'product1.jpg',
                'qty' => 22
            ],
            [
                'name' => 'Product B',
                'price' => 7.20,
                'img_name' => 'product2.jpg',
                'qty' => 12
            ],
            [
                'name' => 'Product C',
                'price' => 2.70,
                'img_name' => 'product3.jpg',
                'qty' => 15
            ],
            [
                'name' => 'Product D',
                'price' => 9.00,
                'img_name' => 'product4.jpg',
                'qty' => 7
            ],
            [
                'name' => 'Product E',
                'price' => 11.20,
                'img_name' => 'product5.jpg',
                'qty' => 3
            ],
        ];

        Product::insert($products);
    }
}
