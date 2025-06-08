<?php

namespace App\Providers;

use App\Helpers\Helper;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use PHPUnit\TextUI\Help;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('components.navigation', function ($view) {
            if (auth()->check() && auth()->user()->role === 'mahasiswa') {

                $data = Helper::getCountProgressMahasiswa();
                $progress = $data['value'];
                $color = $data['color'];
                $progress_text = $data['progress_text'];

                $view->with([
                    'progress' => $progress ?? 0,
                    'color' => $color,
                    'progress_text' => $progress_text
                ]);
            }
            if (auth()->check() && auth()->user()->role === 'dosen') {
                $data = Helper::getCountSatisfactionDosen();
                $satisfaction = $data['value'];
                $color = $data['color'];

                $semester = Semester::where('aktif', true)->first();
                $tgl_mulai = Carbon::parse($semester->mulai);
                $tgl_selesai = Carbon::parse($semester->selesai);
                $month_mulai = $tgl_mulai->translatedFormat('F');
                $month_selesai = $tgl_selesai->translatedFormat('F');
                $year_mulai = $tgl_mulai->translatedFormat('Y');
                $year_selesai = $tgl_selesai->translatedFormat('Y');

                $semester_text = '';
                if ($year_mulai != $year_selesai) {
                    $semester_text = "$month_mulai $year_mulai - $month_selesai $year_selesai";
                } else {
                    $semester_text = "$month_mulai - $month_selesai $year_mulai";
                }

                $view->with([
                    'satisfaction' => $satisfaction ?? 0,
                    'color' => $color,
                    'semester_text' => $semester_text
                ]);
            }

            if (auth()->check() && auth()->user()->role == 'admin') {
                $data = Helper::getCountSatisfactionAdmin();
                $satisfaction = $data['value'];
                $color = $data['color'];
                $semester_text = 'Fakultas Vokasi';

                $view->with([
                    'satisfaction' => $satisfaction ?? 0,
                    'color' => $color,
                    'semester_text' => $semester_text
                ]);
            }
        });
    }
}
