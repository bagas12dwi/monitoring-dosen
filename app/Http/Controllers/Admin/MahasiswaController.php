<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $title;
    public function __construct()
    {
        $this->title = 'Mahasiswa';
        $this->route = 'admin.mahasiswa.index';
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
        return view('admin.pages.mahasiswa.index', [
            'title' => $this->title,
        ]);
    }

    public function data()
    {
        $users = User::where('role', 'mahasiswa')->get();
        return $this->generateDatatable($users, 'admin.mahasiswa');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.mahasiswa.form', [
            'title' => $this->title,
            'mahasiswa' => null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request['password'] = bcrypt('12345678');
        return $this->storeData($request, User::class);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $mahasiswa) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $mahasiswa)
    {
        return view('admin.pages.mahasiswa.form', [
            'title' => $this->title,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $mahasiswa)
    {
        unset($this->validationRules['password']);
        return $this->updateData($request, User::class, $mahasiswa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $mahasiswa)
    {
        return $this->destroyData(User::class, $mahasiswa);
    }

    public function import(Request $request)
    {
        $file = $request->file('importMahasiswa');
        $namaFile = $file->getClientOriginalName();
        $file->move('storage/app/public/Mahasiswa/', $namaFile);

        $isLocal = env('IS_LOCAL', false);

        $path = $isLocal
            ? public_path('storage/app/public/Mahasiswa/' . $namaFile) // for local
            : public_path('storage/Mahasiswa/' . $namaFile);     // for production

        Excel::import(new UserImport, $path);

        Alert::success('Berhasil', 'Data Mahasiswa berhasil diimport!');
        return redirect()->route($this->route);
    }
}
