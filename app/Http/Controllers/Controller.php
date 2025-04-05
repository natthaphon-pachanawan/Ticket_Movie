<?php

namespace App\Http\Controllers;

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

    public function returnCreated($message, $status = 201)
    {
        return response()->json(['success' => $message], $status);
    }
}
