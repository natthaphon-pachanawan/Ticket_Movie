<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\District;
use App\Models\Subdistrict;

class AddressController extends Controller
{
    public function indexProvince()
    {
        $provinces = Province::orderBy('name_th')->get();
        return $this->returnJson($provinces);
    }

    public function indexDistrict(Request $request)
    {
        $q = District::query();
        if ($request->has('province_id')) {
            $q->where('province_id', $request->query('province_id'));
        }
        $districts = $q->orderBy('name_th')->get();
        return $this->returnJson($districts);
    }

    public function indexSubdistrict(Request $request)
    {
        $q = Subdistrict::query();
        if ($request->has('district_id')) {
            $q->where('district_id', $request->query('district_id'));
        }
        $subdistricts = $q->orderBy('name_th')->get();
        return $this->returnJson($subdistricts);
    }
}
