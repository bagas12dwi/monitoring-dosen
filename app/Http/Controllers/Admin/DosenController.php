<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Dosen';
        $this->route = 'admin.dosen.index';
        $this->validationRules = [
            'nama' => 'required',
            'nim' => 'required|unique:users,nim',
            'angkatan' => 'nullable',
            'role' => 'nullable',
            'password' => 'required'
        ];
    }

    public function index()
    {
        $this->confirmDeleteMessage();
        return view('admin.pages.dosen.index', [
            'title' => $this->title,
        ]);
    }

    public function data()
    {
        $users = User::where('role', 'dosen')->get();
        return $this->generateDatatable($users, 'admin.dosen');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.dosen.form', [
            'title' => $this->title,
            'dosen' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request['password'] = bcrypt('12345678');
        $request['role'] = 'dosen';
        return $this->storeData($request, User::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $dosen)
    {
        return view('admin.pages.dosen.form', [
            'title' => $this->title,
            'dosen' => $dosen
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $dosen)
    {
        unset($this->validationRules['password']);
        unset($this->validationRules['role']);
        return $this->updateData($request, User::class, $dosen);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $dosen)
    {
        return $this->destroyData(User::class, $dosen);
    }

    public function import(Request $request)
    {
        $file = $request->file('importDosen');
        $namaFile = $file->getClientOriginalName();
        $file->move('storage/app/public/Dosen/', $namaFile);

        $isLocal = env('IS_LOCAL', false);

        $path = $isLocal
            ? public_path('storage/app/public/Dosen/' . $namaFile)
            : public_path('storage/Dosen/' . $namaFile);

        Excel::import(new UserImport, $path);
        Alert::success('Berhasil', 'Data Dosen berhasil diimport!');
        return redirect()->route('dosen.index');
    }
}
