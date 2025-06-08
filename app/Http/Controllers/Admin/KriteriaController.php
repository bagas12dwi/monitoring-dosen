<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class KriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Kriteria';
        $this->route = 'admin.kriteria.index';
        $this->validationRules = [
            'nama' => 'required',
            'bobot' => 'required|numeric',
        ];
    }
    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.kriteria.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $kriterias = Kriteria::all();
        return $this->generateDatatable($kriterias, 'admin.kriteria');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.kriteria.form', [
            'title' => $this->title,
            'kriteria' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $kriteria = new Kriteria();
        if (!$kriteria->cekBobot($request['bobot'] / 100)) {
            Alert::error('Gagal', 'Total bobot tidak boleh lebih dari 100%');
            return redirect()->back()->withInput();
        }
        $request['bobot'] = $request['bobot'] / 100;
        return $this->storeData($request, Kriteria::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kriteria $kriteria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kriteria $kriteria)
    {
        return view('admin.pages.kriteria.form', [
            'title' =>  $this->title,
            'kriteria' => $kriteria
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kriteria $kriteria)
    {
        $newBobot = $request->bobot / 100;

        // Hitung total bobot lain (tanpa menyertakan bobot milik kriteria ini sendiri)
        $totalBobotLain = Kriteria::where('id', '!=', $kriteria->id)->sum('bobot');
        // Cek apakah total bobot baru valid
        if (($totalBobotLain + $newBobot) > 1) {
            Alert::error('Gagal', 'Total bobot tidak boleh lebih dari 100%');
            return redirect()->back()->withInput();
        }
        $request['bobot'] = $request['bobot'] / 100;
        return $this->updateData($request, Kriteria::class, $kriteria);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kriteria $kriteria)
    {
        return $this->destroyData(Kriteria::class, $kriteria);
    }
}
