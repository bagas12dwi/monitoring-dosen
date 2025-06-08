<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Imports\KelasMahasiswaImport;
use App\Models\Kelas;
use App\Models\KelasMahasiswa;
use App\Models\Perkuliahan;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class KelasMahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Matakuliah';
        $this->route = 'mahasiswa.matkul.index';
        $this->validationRules = [
            'perkuliahan_id' => 'required',
            'semester_id' => 'required',
            'mahasiswa_id' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('mahasiswa.pages.matakuliah.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $kelasMahasiswa = KelasMahasiswa::with(['mahasiswa', 'perkuliahan', 'semester', 'perkuliahan.matkul', 'perkuliahan.dosen'])
            ->where('mahasiswa_id', auth()->user()->id)
            ->get();

        return $this->generateDatatable($kelasMahasiswa, 'mahasiswa.matkul');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $perkuliahan = Perkuliahan::findOrFail($request->perkuliahan_id);
        $request['mahasiswa_id'] = auth()->user()->id;
        $request['perkuliahan_id'] = $perkuliahan->id;
        $request['semester_id'] = $perkuliahan->semester_id;

        $this->storeDataUseAjax($request, KelasMahasiswa::class);
        return response()->json([
            'status' => 'success',
            'redirect' => route($this->route),
            'message' => 'Matakuliah berhasil ditambahkan!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(KelasMahasiswa $kelasMahasiswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KelasMahasiswa $kelasMahasiswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KelasMahasiswa $kelasMahasiswa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KelasMahasiswa $matkul)
    {
        return $this->destroyData(KelasMahasiswa::class, $matkul);
    }

    public function import(Request $request)
    {
        $file = $request->file('importKelasMahasiswa');
        $namaFile = $file->getClientOriginalName();
        $file->move('storage/app/public/KelasMahasiswa/', $namaFile);

        $isLocal = env('IS_LOCAL', false);

        $path = $isLocal
            ? public_path('storage/app/public/KelasMahasiswa/' . $namaFile) // for local
            : public_path('storage/KelasMahasiswa/' . $namaFile);     // for production

        Excel::import(new KelasMahasiswaImport, $path);

        Alert::success('Berhasil', 'Data Kelas Mahasiswa berhasil diimport!');
        return redirect()->route($this->route);
    }
}
