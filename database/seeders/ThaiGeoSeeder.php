<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThaiGeoSeeder extends Seeder
{
    public function run(): void
    {
        // Provinces
        $provincePath = storage_path('app/thai-geo/api_province.json');
        $provinces = json_decode(file_get_contents($provincePath), true);

        foreach ($provinces as $province) {
            DB::table('provinces')->insert([
                'id' => $province['id'],
                'name_th' => $province['name_th'],
                'name_en' => $province['name_en'],
                'geography_id' => $province['geography_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Districts
        $districtPath = storage_path('app/thai-geo/api_amphure.json');
        $districts = json_decode(file_get_contents($districtPath), true);

        foreach ($districts as $district) {
            DB::table('districts')->insert([
                'id' => $district['id'],
                'province_id' => $district['province_id'],
                'name_th' => $district['name_th'],
                'name_en' => $district['name_en'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Subdistricts
        $subdistrictPath = storage_path('app/thai-geo/api_tambon.json');
        $subdistricts = json_decode(file_get_contents($subdistrictPath), true);

        foreach ($subdistricts as $subdistrict) {
            // ตรวจสอบว่ามี district_id อยู่ในตาราง districts ก่อน
            $exists = DB::table('districts')->where('id', $subdistrict['amphure_id'])->exists();
            if (!$exists) {
                continue; // ข้าม
            }

            DB::table('subdistricts')->insert([
                'id' => $subdistrict['id'],
                'district_id' => $subdistrict['amphure_id'],
                'name_th' => $subdistrict['name_th'],
                'name_en' => $subdistrict['name_en'],
                'zip_code' => $subdistrict['zip_code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
