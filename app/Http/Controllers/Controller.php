<?php

namespace App\Http\Controllers;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    public function returnJson($data, $status = 200)
    {
        return response()->json(['data' => $data], $status);
    }

    public function returnError($message, $status = 400)
    {
        return response()->json(['error' => $message], $status);
    }

    public function returnSuccess($message, $status = 200)
    {
        return response()->json(['success' => $message], $status);
    }

    public function returnNotFound($message, $status = 404)
    {
        return response()->json(['error' => $message], $status);
    }

    public function returnCreated($data, $status = 201)
    {
        if (is_array($data)) {
            return response()->json(['data' => $data], $status);
        }

        return response()->json(['success' => $data], $status);
    }

    // Log
    public function log($action, $description, $userId = null, $ip = null)
    {
        Log::create([
            'action' => $action,
            'description' => $description,
            'user_id' => $userId ?? Auth::id(),
            'ip_address' => $ip ?? request()->ip(),
        ]);
    }
}
