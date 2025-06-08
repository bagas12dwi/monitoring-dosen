<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Matkul;
use App\Models\Perkuliahan;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;

class PerkuliahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Perkuliahan';
        $this->route = 'admin.perkuliahan.index';
        $this->validationRules = [
            'semester_id' => 'required',
            'matkul_id' => 'required',
            'kelas_id' => 'required',
            'dosen_id' => 'required',
            'ruangan' => 'nullable',
            'jam_mulai' => 'nullable',
            'jam_selesai' => 'nullable',
            'day' => 'nullable',
        ];
    }
    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.perkuliahan.index', [
            'title' => $this->title,
        ]);
    }

    public function data()
    {
        $perkuliahan = Perkuliahan::with(['dosen', 'matkul', 'semester', 'kelas'])->get();
        return $this->generateDatatable($perkuliahan, 'admin.perkuliahan');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dosen = User::where('role', 'dosen')->get();
        $semester = Semester::all();
        $kelas = Kelas::all();
        $matkul = Matkul::all();
        return view('admin.pages.perkuliahan.form', [
            'title' => $this->title,
            'perkuliahan' => null,
            'dosens' => $dosen,
            'semesters' => $semester,
            'kelas' => $kelas,
            'matkuls' => $matkul,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->storeData($request, Perkuliahan::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Perkuliahan $perkuliahan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Perkuliahan $perkuliahan)
    {
        $dosen = User::where('role', 'dosen')->get();
        $semester = Semester::all();
        $kelas = Kelas::all();
        $matkul = Matkul::all();

        return view('admin.pages.perkuliahan.form', [
            'title' => $this->title,
            'perkuliahan' => $perkuliahan,
            'dosens' => $dosen,
            'semesters' => $semester,
            'kelas' => $kelas,
            'matkuls' => $matkul,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Perkuliahan $perkuliahan)
    {
        return $this->updateData($request, Perkuliahan::class, $perkuliahan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perkuliahan $perkuliahan)
    {
        return $this->destroyData(Perkuliahan::class, $perkuliahan);
    }
}
