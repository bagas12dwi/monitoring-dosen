<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Kelas';
        $this->route = 'admin.kelas.index';
        $this->validationRules = [
            'kode' => 'required',
            'jurusan' => 'required',
            'angkatan' => 'required',
            'nama' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.kelas.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $kelas = Kelas::all();
        return $this->generateDatatable($kelas, 'admin.kelas');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.kelas.form', [
            'title' => $this->title,
            'kelas' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request['jurusan'] = $request->jurusan ?? 'MI';
        $request['kode'] = $request['jurusan'] . '-' . $request['angkatan'] . '-' . $request['nama'];
        return $this->storeData($request, Kelas::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        return view('admin.pages.kelas.form', [
            'title' => $this->title,
            'kelas' => $kelas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $request['jurusan'] = $request->jurusan ?? 'MI';
        $request['kode'] = $request['jurusan'] . '-' . $request['angkatan'] . '-' . $request['nama'];
        return $this->updateData($request, Kelas::class, $kelas);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        return $this->destroyData(Kelas::class, $kelas);
    }
}
