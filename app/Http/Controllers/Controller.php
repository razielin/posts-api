<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

abstract class Controller
{
    protected function successJson($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    protected function notFoundJson()
    {
        return response()->json([
            'success' => false,
            'message' => 'Entity not found'
        ], 404);
    }
}
