<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Buat Kategori Dummy
        $categories = ['Elektronik', 'Pakaian', 'Makanan & Minuman', 'Perabotan', 'Alat Tulis', 'Otomotif', 'Kesehatan', 'Olahraga', 'Mainan', 'Buku'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }

        // Buat Gudang Dummy
        $warehouses = [
            ['name' => 'Gudang Pusat Jakarta', 'location' => 'Jakarta Pusat'],
            ['name' => 'Gudang Cabang Bandung', 'location' => 'Bandung'],
            ['name' => 'Gudang Transit Surabaya', 'location' => 'Surabaya'],
        ];
        foreach ($warehouses as $wh) {
            Warehouse::create($wh);
        }

        $allCategories = Category::pluck('id')->toArray();
        $allWarehouses = Warehouse::pluck('id')->toArray();
        $adminUser = User::where('email', 'admin@inventory.test')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        // Buat 100 Produk Dummy
        for ($i = 0; $i < 100; $i++) {
            $purchasePrice = $faker->numberBetween(10, 500) * 1000;
            $sellingPrice = $purchasePrice + ($faker->numberBetween(10, 50) * 1000);

            $product = Product::create([
                'name' => $faker->words(3, true) . ' ' . strtoupper($faker->lexify('???')),
                'sku' => 'SKU-' . $faker->unique()->numerify('#####') . strtoupper($faker->lexify('??')),
                'category_id' => $faker->randomElement($allCategories),
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'min_stock_threshold' => $faker->numberBetween(5, 20),
            ]);

            // Simulasi Stok Masuk awal
            $stockIn = $faker->numberBetween(20, 100);
            $warehouseId = $faker->randomElement($allWarehouses);
            
            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $adminId,
                'type' => 'in',
                'quantity' => $stockIn,
                'reason' => 'Stok awal (dummy data)',
            ]);

            // Simulasi beberapa barang sudah ada penjualan/keluar agar widget Fast Moving bekerja
            if ($faker->boolean(70)) { // 70% peluang ada stok keluar
                $stockOut = $faker->numberBetween(1, 15);
                
                $stockOutModel = StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouseId,
                    'user_id' => $adminId,
                    'type' => 'out',
                    'quantity' => $stockOut,
                    'reason' => 'Penjualan dummy',
                ]);
                
                \Illuminate\Support\Facades\DB::table('stock_movements')
                    ->where('id', $stockOutModel->id)
                    ->update([
                        'created_at' => now()->subDays($faker->numberBetween(1, 28))
                    ]);
            }
        }
    }
}
