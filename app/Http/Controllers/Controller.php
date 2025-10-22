<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

abstract class Controller
{
    protected function successJson($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }
}
