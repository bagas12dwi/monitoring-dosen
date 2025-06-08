<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Pertanyaan;
use Illuminate\Http\Request;

class PertanyaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $title;
    public function __construct()
    {
        $this->title = 'Pertanyaan';
        $this->route = 'admin.pertanyaan.index';
        $this->validationRules = [
            'kriteria_id' => 'required',
            'pertanyaan' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.pertanyaan.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $pertanyaans = Pertanyaan::with('kriteria')->get();
        return $this->generateDatatable($pertanyaans, 'admin.pertanyaan');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.pertanyaan.form', [
            'title' => $this->title,
            'pertanyaan' => null,
            'kriterias' => Kriteria::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->storeData($request, Pertanyaan::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pertanyaan $pertanyaan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pertanyaan $pertanyaan)
    {
        return view('admin.pages.pertanyaan.form', [
            'title' => $this->title,
            'pertanyaan' => $pertanyaan,
            'kriterias' => Kriteria::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pertanyaan $pertanyaan)
    {
        return $this->updateData($request, Pertanyaan::class, $pertanyaan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pertanyaan $pertanyaan)
    {
        return $this->destroyData(Pertanyaan::class, $pertanyaan);
    }
}
