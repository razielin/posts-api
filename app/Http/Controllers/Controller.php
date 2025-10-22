<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function successJson($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }
}
