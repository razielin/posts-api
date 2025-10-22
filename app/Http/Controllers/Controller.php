<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class Controller
{
    protected function successJson($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    protected function notFoundJson(ModelNotFoundException $e)
    {
        $idsString = implode(',', $e->getIds());
        return response()->json([
            'success' => false,
            'message' => "Entity #$idsString not found"
        ], 404);
    }
}
