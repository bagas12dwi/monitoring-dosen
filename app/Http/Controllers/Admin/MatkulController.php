<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matkul;
use Illuminate\Http\Request;

class MatkulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Mata Kuliah';
        $this->route = 'admin.matkul.index';
        $this->validationRules = [
            'nama' => 'required',
            'kode' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.matkul.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $matkul = Matkul::all();
        return $this->generateDatatable($matkul, 'admin.matkul');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.matkul.form', [
            'title' => $this->title,
            'matkul' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        return $this->storeData($request, Matkul::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Matkul $matkul)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Matkul $matkul)
    {
        return view('admin.pages.matkul.form', [
            'title' => $this->title,
            'matkul' => $matkul
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Matkul $matkul)
    {
        return $this->updateData($request, Matkul::class, $matkul);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Matkul $matkul)
    {
        return $this->destroyData(Matkul::class, $matkul);
    }
}
