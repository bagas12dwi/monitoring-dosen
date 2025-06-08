<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = "Log Aktivitas";
        $this->route = 'mahasiswa.log.index';
    }

    public function index()
    {
        return view('mahasiswa.pages.log.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $logAktivitas = LogAktivitas::where('user_id', auth()->user()->id)->get();
        return $this->generateDatatable($logAktivitas, 'mahasiswa.log');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LogAktivitas $logAktivitas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LogAktivitas $logAktivitas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LogAktivitas $logAktivitas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LogAktivitas $logAktivitas)
    {
        //
    }
}
