<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $role = Role::all();
        return $this->returnJson($role);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_description' => 'nullable|string|max:255',
        ]);
        $role = Role::create([
            'role_name' => $request->role_name,
            'role_description' => $request->role_description,
        ]);
        if (!$role) {
            return $this->returnError('เพิ่มข้อมูลไม่สำเร็จ', 500);
        }

        $this->log('เพิ่มบทบาท', "เพิ่มบทบาท: {$role->role_name}");

        return $this->returnCreated($role);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->returnError('ไม่พบข้อมูล', 404);
        }

        return $this->returnJson($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'role_description' => 'nullable|string|max:255',
        ]);

        $role = Role::find($id);

        if (!$role) {
            return $this->returnError('ไม่พบข้อมูล', 404);
        }

        $role->role_name = $request->role_name;
        $role->role_description = $request->role_description;
        $role->save();

        $this->log('แก้ไขบทบาท', "แก้ไขบทบาท ID: {$role->id}");

        return $this->returnSuccess('อัพเดทข้อมูลสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return $this->returnError('ไม่พบข้อมูล', 404);
        }

        $deletedRoleName = $role->role_name;
        $role->delete();

        // ✅ Log
        $this->log('ลบบทบาท', "ลบบทบาท: {$deletedRoleName} (ID: {$id})");

        return $this->returnSuccess('ลบข้อมูลสำเร็จ');
    }
}
