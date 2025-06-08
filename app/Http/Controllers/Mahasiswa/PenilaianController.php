<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Imports\PenilaianImport;
use App\Models\KelasMahasiswa;
use App\Models\LogAktivitas;
use App\Models\Penilaian;
use App\Models\Perkuliahan;
use App\Models\Pertanyaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Return_;
use RealRashid\SweetAlert\Facades\Alert;

class PenilaianController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $title;
    public function __construct()
    {
        $this->title = 'Feedback';
        $this->route = 'mahasiswa.feedback.index';
    }
    public function index()
    {
        $uniqueDosens = Helper::getDataFeedback();
        // dd($uniqueDosens);
        return view('mahasiswa.pages.feedback.index', [
            'title' => $this->title,
            'pertanyaans' => Pertanyaan::all(),
            'data' => $uniqueDosens
        ]);
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
        // Validate all once (as arrays)
        // $this->validate($request, $this->validationRules);

        // ✅ Set simplified rules for single-item validation in storeDataUseAjax()
        $this->validationRules = [
            'mahasiswa_id' => 'required|integer',
            'perkuliahan_id' => 'required|integer',
            'pertanyaan_id' => 'required|integer',
            'nilai' => 'required|integer',
            'komentar' => 'nullable',
        ];

        $perkuliahan = Perkuliahan::with(['dosen', 'matkul'])->where('id', $request->perkuliahan_id)->first();
        $text = "Menginputkan Penilaian Untuk " . $perkuliahan->dosen->nama . " Mata kuliah " . $perkuliahan->matkul->nama;

        foreach ($request['pertanyaan_id'] as $index => $pertanyaan_id) {
            $data = new Request([
                'mahasiswa_id' => auth()->user()->id,
                'perkuliahan_id' => $request->perkuliahan_id,
                'pertanyaan_id' => $pertanyaan_id,
                'nilai' => $request->nilai[$index],
                'komentar' => $request->komentar
            ]);

            $this->storeDataUseAjax($data, Penilaian::class);
        }

        LogAktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => $text
        ]);

        Alert::success('Berhasil', 'Data Penilaian Berhasil Disimpan!');
        return redirect()->route('mahasiswa.feedback.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(Penilaian $feedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penilaian $feedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penilaian $feedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penilaian $feedback)
    {
        //
    }

    public function import(Request $request)
    {
        $file = $request->file('importPenilaian');
        $namaFile = $file->getClientOriginalName();
        $file->move('storage/app/public/Penilaian/', $namaFile);

        $isLocal = env('IS_LOCAL', false);

        $path = $isLocal
            ? public_path('storage/app/public/Penilaian/' . $namaFile) // for local
            : public_path('storage/Penilaian/' . $namaFile);     // for production

        Excel::import(new PenilaianImport, $path);

        Alert::success('Berhasil', 'Data Penilaian berhasil diimport!');
        return redirect()->route($this->route);
    }
}
