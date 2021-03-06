<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Akun;
use App\Models\KategoriAkun;
use Illuminate\Support\Facades\File; 

class AkunController extends Controller
{
    public function tambahakun()
    {
        // Check Login
        if (!Auth::user()) {
            return redirect('/login');
        }

        $year = Carbon::now()->format('Y');
        $auth = Auth::user();
        $nama = $auth->name;
        $pecah = explode(' ', $nama);
        $forename = $pecah[0];
        if (empty($pecah[1])) {
            $surname = "";
        } else {
            $surname = $pecah[1];
        }

        $akun = Akun::orderBy('kd_akun','ASC')->get();
        $kategori = KategoriAkun::orderBy('kode','ASC')->get();
        return view('akun.tambahakun', [
            'akun' => $akun,
            'kategori' => $kategori,
            'forename' => $forename,
            'surname' => $surname,
            'year' => $year
        ]);
    }

    public function addakun(Request $request)
    {
        $this->validate($request, [
            'nama_akun' => 'required|unique:akun',
            'kd_akun' => 'required|unique:akun',
            'posisi' => 'required|in:debit,kredit'
        ]);

        Akun::create([
            'nama_akun' => $request->nama_akun,
            'kd_akun' => $request->kd_akun,
            'posisi' => $request->posisi,
            'katakun_id' => $request->kategori,
            'tipe' => $request->tipe
        ]);
        return redirect()->back()->with('success', 'Akun berhasil ditambahkan.');
    }

    public function eakun(Request $request, $id)
    {
        $data = Akun::where('id', $id)->first();

        if ($request->nama_akun == $data->nama_akun) {
            if ($request->kd_akun != $data->kd_akun) {
                $this->validate($request, [
                    'kd_akun' => 'required|unique:akun',
                    'posisi' => 'required|in:debit,kredit'
                ]);
            }
            Akun::where('id', $request->id,)->update([
                'nama_akun' => $request->nama_akun,
                'kd_akun' => $request->kd_akun,
                'posisi' => $request->posisi,
                'katakun_id' => $request->kategori,
                'tipe' => $request->tipe
            ]);
            return redirect()->back()->with('success', 'Akun berhasil diubah');
        } 
        elseif ($request->nama_akun != $data->nama_akun) {
            if ($request->kd_akun != $data->kd_akun) {
                $this->validate($request, [
                    'nama_akun' => 'required|unique:akun',
                    'kd_akun' => 'required|unique:akun',
                    'posisi' => 'required|in:debit,kredit'
                ]);
            }
            $this->validate($request, [
                'nama_akun' => 'required|unique:akun',
                'posisi' => 'required|in:debit,kredit'
            ]);
            Akun::where('id', $request->id,)->update([
                'nama_akun' => $request->nama_akun,
                'kd_akun' => $request->kd_akun,
                'posisi' => $request->posisi,
                'katakun_id' => $request->kategori,
                'tipe' => $request->tipe
            ]);
            return redirect()->back()->with('success', 'Akun berhasil diubah');
        }
    }

    public function dakun($id)
    {
        Akun::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }

    public function addkatakun(Request $request)
    {
        $this->validate($request, [
            'akun' => 'required|unique:kategori_akun',
            'kode' => 'required|unique:kategori_akun'
        ]);
        KategoriAkun::create([
            'akun' => $request->akun,
            'kode' => $request->kode
        ]);
        return redirect()->back()->with('success', 'Kategori Akun berhasil ditambahkan.');
    }

    public function ekatakun(Request $request, $id)
    {
        $data = KategoriAkun::where('id', $id)->first();

        if ($request->akun == $data->akun) {
            if ($request->kode != $data->kode) {
                $this->validate($request, [
                    'kode' => 'required|unique:kategori_akun'
                ]);
            }
            KategoriAkun::where('id', $request->id,)->update([
                'akun' => $request->akun,
                'kode' => $request->kode
            ]);
            return redirect()->back()->with('success', 'Kategori Akun berhasil diubah');
        } 
        elseif ($request->akun != $data->akun) {
            if ($request->kode != $data->kode) {
                $this->validate($request, [
                    'akun' => 'required|unique:kategori_akun',
                    'kode' => 'required|unique:kategori_akun'
                ]);
            }
            $this->validate($request, [
                'akun' => 'required|unique:kategori_akun',
            ]);
            KategoriAkun::where('id', $request->id,)->update([
                'akun' => $request->akun,
                'kode' => $request->kode
            ]);
            return redirect()->back()->with('success', 'Akun berhasil diubah');
        }
    }

    public function dkatakun($id)
    {
        KategoriAkun::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Kategori Akun berhasil dihapus.');
    }

    // Reset Akun
    public function delakun()
    {
        if (Akun::exists()) {
            Akun::query()->delete();
            KategoriAkun::query()->delete();
            File::deleteDirectory(public_path('/assets/image'));
            return redirect()->back()->with('success', 'Reset data akun berhasil');
        } else {
            return redirect()->back()->with('warning', 'Proses ditolak karena data kosong');
        }
    }
    
}
