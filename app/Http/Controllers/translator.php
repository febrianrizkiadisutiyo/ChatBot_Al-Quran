<?php

namespace App\Http\Controllers;

use App\Models\translations;
use Illuminate\Http\Request;

class translator extends Controller
{
    public function daftarTranslator(Request $request)
    {
        $cek = false;
        if ($request->has('search')) {
            $daftar = translations::where('language_name', 'LIKE', '%' . $request->search.'%')->paginate(10);
            $cek = true;
        } else {
            // jika tidak melakukan request
            $daftar = translations::paginate(10);
        }
        return view('daftarTranslations', compact('daftar'), [
            'cek' => $cek
        ]);
    }
}
