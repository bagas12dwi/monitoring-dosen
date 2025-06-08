<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Semester';
        $this->route = 'admin.semester.index';
        $this->validationRules = [
            'tahun_ajaran' => 'required',
            'semester' => 'required',
            'mulai' => 'required',
            'selesai' => 'required',
            'aktif' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.semester.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $semester = Semester::all();
        return $this->generateDatatable($semester, 'admin.semester');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.semester.form', [
            'title' => $this->title,
            'semester' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->aktif) {
            $request['aktif'] = 1;
            Semester::query()->update(['aktif' => 0]);
        } else {
            $request['aktif'] = 0;
        }
        return $this->storeData($request, Semester::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(Semester $semester)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester)
    {
        return view('admin.pages.semester.form', [
            'title' => $this->title,
            'semester' => $semester
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        if ($request->aktif) {
            $request['aktif'] = 1;
            Semester::query()->update(['aktif' => 0]);
        } else {
            $request['aktif'] = 0;
        }
        return $this->updateData($request, Semester::class, $semester);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester)
    {
        return $this->destroyData(Semester::class, $semester);
    }
}
