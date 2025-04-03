<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThaiGeoSeeder extends Seeder
{
    public function run(): void
    {
        // Provinces
        $provinces = json_decode(Storage::get('thai-geo/api_province.json'), true);
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
        $districts = json_decode(Storage::get('thai-geo/api_amphure.json'), true);
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
        $subdistricts = json_decode(Storage::get('thai-geo/api_tambon.json'), true);
        foreach ($subdistricts as $subdistrict) {
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
