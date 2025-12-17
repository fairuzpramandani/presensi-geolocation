<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Departemen;

class DepartemenController extends Controller
{
    public function listDepartemen()
    {
        $departemen = Departemen::all(['kode_dept', 'nama_dept']);
        return response()->json([
            'status' => 'success',
            'data' => $departemen
        ]);
    }
}
