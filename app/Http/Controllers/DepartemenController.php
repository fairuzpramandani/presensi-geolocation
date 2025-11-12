<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Departemen;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $search_keyword = $request->nama_dept;
        $query = Departemen::query();
        if (!empty($search_keyword)) {
            $query->where(function($q) use ($search_keyword) {
                $q->where('nama_dept', 'like', $search_keyword . '%')
                  ->orWhere('kode_dept', 'like', $search_keyword . '%');
            });
        }
        $departemen = $query->orderBy('kode_dept')->paginate(10);
        $departemen->appends($request->all());
        return view('departemen.index', compact('departemen'));
    }

    public function store(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $nama_dept = $request->nama_dept;

        $cek = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        if ($cek) {
            return redirect()->back()->with(['warning' => 'Kode Departemen ' . $kode_dept . ' sudah ada.']);
        }

        $data =
        [
            'kode_dept' => $kode_dept,
            'nama_dept' => $nama_dept,
        ];

        try {
            $simpan = DB::table('departemen')->insert($data);
            if ($simpan) {
                return redirect()->back()->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return redirect()->back()->with(['warning' => 'Data Tidak Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Tidak Berhasil Disimpan']);
        }
    }

    public function edit(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        return view('departemen.edit', compact('departemen'));
    }

    public function update($kode_dept, Request $request)
    {
        $nama_dept_baru = $request->nama_dept;
        $kode_dept_baru = $request->kode_dept;

        try {
            $cek = DB::table('departemen')
                ->where('kode_dept', $kode_dept_baru)
                ->where('kode_dept', '!=', $kode_dept)
                ->first();

            if ($cek) {
                return redirect()->back()->with(['warning' => 'Kode Dept ' . $kode_dept_baru . ' sudah digunakan.']);
            }

            $data = [
                'kode_dept' => $kode_dept_baru,
                'nama_dept' => $nama_dept_baru
            ];

            $update = DB::table('departemen')->where('kode_dept', $kode_dept)->update($data);

            if ($update) {
                return redirect('/departemen')->with(['success' => 'Data Berhasil DiUpdate']);
            } else {
                 return redirect('/departemen')->with(['warning' => 'Tidak Ada Perubahan Data']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Gagal DiUpdate.']);
        }
    }

    public function delete($kode_dept)
    {
        try {
            $delete = DB::table('departemen')->where('kode_dept', $kode_dept)->delete();

            if ($delete) {
                return redirect()->back()->with(['success' => 'Data Berhasil Dihapus']);
            } else {
                return redirect()->back()->with(['warning' => 'Data Gagal Dihapus']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Gagal Dihapus, pastikan tidak ada karyawan di departemen ini.']);
        }
    }
}
