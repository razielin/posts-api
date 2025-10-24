<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

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

    protected function failedJson(string $message)
    {
        return response()->json(['success' => false, 'message' => $message], Response::HTTP_BAD_REQUEST);
    }
}
