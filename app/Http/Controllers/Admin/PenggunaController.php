<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Pengguna';
        $this->route = 'admin.pengguna.index';
        $this->validationRules = [
            'nim' => 'required',
            'nama' => 'required',
            'role' => 'nullable',
            'password' => 'required'
        ];
    }
    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.pengguna.index', [
            'title' => $this->title
        ]);
    }

    public function data()
    {
        $pengguna = User::where('role', 'admin')->get();
        return $this->generateDatatable($pengguna, 'admin.pengguna');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.pengguna.form', [
            'title' => $this->title,
            'pengguna' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request['password'] = bcrypt('12345678');
        $request['role'] = 'admin';
        return $this->storeData($request, User::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $pengguna)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $pengguna)
    {
        return view('admin.pages.pengguna.form', [
            'title' => $this->title,
            'pengguna' => $pengguna
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $pengguna)
    {
        $request['password'] = bcrypt('12345678');
        $request['role'] = 'admin';
        return $this->updateData($request, User::class, $pengguna);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $pengguna)
    {
        return $this->destroyData(User::class, $pengguna);
    }
}
