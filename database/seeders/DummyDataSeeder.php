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

        // 1. Buat 10 Kategori Dummy
        $categories = ['Elektronik', 'Pakaian', 'Makanan & Minuman', 'Perabotan', 'Alat Tulis', 'Otomotif', 'Kesehatan', 'Olahraga', 'Mainan', 'Buku'];
        foreach (array_slice($categories, 0, 10) as $cat) {
            Category::create(['name' => $cat]);
        }

        // 2. Buat 10 Gudang Dummy
        for ($i = 1; $i <= 10; $i++) {
            Warehouse::create([
                'name' => 'Gudang ' . $faker->city(),
                'location' => $faker->address()
            ]);
        }

        // 3. Buat 10 Satuan (Unit) Dummy
        $units = [
            ['name' => 'Pieces', 'symbol' => 'Pcs'],
            ['name' => 'Kotak', 'symbol' => 'Box'],
            ['name' => 'Kilogram', 'symbol' => 'Kg'],
            ['name' => 'Gram', 'symbol' => 'Gr'],
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Mililiter', 'symbol' => 'Ml'],
            ['name' => 'Paket', 'symbol' => 'Pkt'],
            ['name' => 'Lusin', 'symbol' => 'Lsn'],
            ['name' => 'Karton', 'symbol' => 'Krt'],
            ['name' => 'Karung', 'symbol' => 'Krg'],
        ];
        foreach (array_slice($units, 0, 10) as $unit) {
            \App\Models\Unit::create($unit);
        }

        // 4. Buat 10 Pemasok (Supplier) Dummy
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Supplier::create([
                'name' => $faker->company(),
                'contact_person' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
            ]);
        }

        $allCategories = Category::pluck('id')->toArray();
        $allWarehouses = Warehouse::pluck('id')->toArray();
        $allUnits = \App\Models\Unit::pluck('id')->toArray();
        $allSuppliers = \App\Models\Supplier::pluck('id')->toArray();
        
        $adminUser = User::where('email', 'admin@inventory.test')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        $indonesianProducts = [
            ['name' => 'Indomie Goreng Spesial', 'price' => 3000, 'unit' => 'Pcs'],
            ['name' => 'Air Mineral Aqua 600ml', 'price' => 3500, 'unit' => 'Botol'],
            ['name' => 'Kopi Kapal Api Sachet', 'price' => 1500, 'unit' => 'Sachet'],
            ['name' => 'Minyak Goreng Bimoli 2L', 'price' => 38000, 'unit' => 'Pouch'],
            ['name' => 'Tolak Angin Cair Sido Muncul', 'price' => 4500, 'unit' => 'Sachet'],
            ['name' => 'Sabun Cair Lifebuoy 450ml', 'price' => 25000, 'unit' => 'Pouch'],
            ['name' => 'Pasta Gigi Pepsodent 190g', 'price' => 15000, 'unit' => 'Tube'],
            ['name' => 'Beras Maknyuss 5Kg', 'price' => 75000, 'unit' => 'Karung'],
            ['name' => 'Buku Tulis Sinar Dunia 38 Lembar', 'price' => 4000, 'unit' => 'Pcs'],
            ['name' => 'Deterjen Rinso Anti Noda 700g', 'price' => 22000, 'unit' => 'Bungkus'],
        ];

        // 5. Buat 10 Produk Dummy
        foreach ($indonesianProducts as $index => $prodData) {
            $purchasePrice = $prodData['price'];
            $sellingPrice = $purchasePrice + ($purchasePrice * 0.2); // margin 20%

            $product = Product::create([
                'name' => $prodData['name'],
                'sku' => 'SKU-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'category_id' => $faker->randomElement($allCategories),
                'unit_id' => $faker->randomElement($allUnits),
                'supplier_id' => $faker->randomElement($allSuppliers),
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'min_stock_threshold' => 10,
            ]);

            // Simulasi Stok Masuk awal
            $warehouseId = $faker->randomElement($allWarehouses);
            
            StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $adminId,
                'type' => 'in',
                'quantity' => 100, // 100 stok
                'reason' => 'Stok awal',
            ]);

            // Simulasi stok keluar untuk memicu analitik
            $stockOutModel = StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'user_id' => $adminId,
                'type' => 'out',
                'quantity' => $faker->numberBetween(5, 25),
                'reason' => 'Penjualan ke toko cabang',
            ]);
            
            \Illuminate\Support\Facades\DB::table('stock_movements')
                ->where('id', $stockOutModel->id)
                ->update([
                    'created_at' => now()->subDays($faker->numberBetween(1, 28))
                ]);
        }
    }
}
