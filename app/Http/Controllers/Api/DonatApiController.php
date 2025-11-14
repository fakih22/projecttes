<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donat;

class DonatApiController extends Controller
{
    public function index()
    {
        $donats = Donat::with('category')->get()->map(function ($donat) {
            if ($donat->gambar) {
                $donat->image_url = route('img', ['path' => $donat->gambar]);
            } else {
                $donat->image_url = null;
            }

            return $donat;
        });

        return response()->json($donats);
    }

    public function show($id)
    {
        $donat = Donat::with('category')->find($id);

        if (!$donat) {
            return response()->json(['message' => 'Donat tidak ditemukan'], 404);
        }

        $donat->image_url = $donat->gambar
            ? route('img', ['path' => $donat->gambar])
            : null;

        return response()->json($donat);
    }
}
