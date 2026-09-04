<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DemoInventorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik untuk kebutuhan operasional.'],
            ['name' => 'Souvenir', 'description' => 'Cendera mata dan merchandise perusahaan.'],
            ['name' => 'ATK', 'description' => 'Alat tulis kantor untuk kebutuhan administrasi.'],
        ])->mapWithKeys(fn (array $category) => [
            $category['name'] => Kategori::updateOrCreate(
                ['nama_kategori' => $category['name']],
                ['deskripsi_kategori' => $category['description']]
            ),
        ]);

        $unit = Unit::firstOrCreate(['nama_unit' => 'Satuan'], ['singkatan_unit' => 'Pcs']);
        $location = Lokasi::firstOrCreate(['nama_lokasi' => 'Ruko U-Town'], ['kode_lokasi' => 'RUKO-01']);

        $items = [
            ['Elektronik', 'Mouse Wireless', 'DMY-ELK-001', 'aset', 12, 2, 185000],
            ['Elektronik', 'Keyboard Wireless', 'DMY-ELK-002', 'aset', 8, 2, 325000],
            ['Elektronik', 'Proyektor Portable', 'DMY-ELK-003', 'aset', 3, 1, 4500000],
            ['Souvenir', 'Tote Bag BASS', 'DMY-SVR-001', 'habis_pakai', 30, 5, 45000],
            ['Souvenir', 'Mug Keramik BASS', 'DMY-SVR-002', 'habis_pakai', 18, 3, 65000],
            ['Souvenir', 'Notebook Eksklusif', 'DMY-SVR-003', 'habis_pakai', 25, 5, 35000],
            ['ATK', 'Pulpen Gel Hitam', 'DMY-ATK-001', 'habis_pakai', 100, 20, 5000],
            ['ATK', 'Kertas HVS A4', 'DMY-ATK-002', 'habis_pakai', 15, 3, 65000],
            ['ATK', 'Stapler', 'DMY-ATK-003', 'aset', 6, 1, 45000],
        ];

        foreach ($items as [$category, $name, $code, $type, $stock, $minimumStock, $price]) {
            Barang::updateOrCreate(
                ['kode_barang' => $code],
                [
                    'nama_barang' => $name,
                    'deskripsi' => "Data dummy {$category} untuk pengujian lokal.",
                    'kategori_id' => $categories[$category]->id,
                    'unit_id' => $unit->id,
                    'lokasi_id' => $location->id,
                    'tipe_item' => $type,
                    'stok' => $stock,
                    'stok_minimum' => $minimumStock,
                    'harga_beli' => $price,
                    'status' => 'aktif',
                ]
            );
        }
    }
}
