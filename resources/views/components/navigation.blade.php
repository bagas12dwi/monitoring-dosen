<nav class="navbar navbar-dark align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0">
    <div class="d-flex flex-column p-0 w-100">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="/">
            <div class="sidebar-brand-text mx-3">
                <span>Feedback Dosen</span>
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <ul class="navbar-nav text-light w-100" id="accordionSidebar">
            <div class="top-content mb-3">
                <div class="d-flex flex-column align-items-center justify-content-center w-100 mb-3">
                    <img class="rounded-circle img-profile border mb-2"
                        style="object-fit: cover; height: 3em; width: 3em" alt="Avatar"
                        src="{{ URL::asset('assets/img/default.jpg') }}">
                    <a href="{{ route('profile.index') }}"
                        class="fw-semibold mb-0 mx-4 text-center nav-link">{{ auth()->user()->nama }}</a>
                    @if (auth()->user()->role == 'admin')
                        <p class="m-0" style="font-size: 10pt">Universitas Negeri Surabaya</p>
                    @else
                        <p class="m-0" style="font-size: 10pt">{{ auth()->user()->nim }}</p>
                    @endif
                </div>
                @if (auth()->user()->role == 'mahasiswa')
                    <div class="d-flex mx-5 justify-content-between align-items-center">
                        <span class="text-white" style="font-size: 9pt;">Pengisian Progress Dosen</span>
                        <span class="text-white" style="font-size: 9pt;">{{ $progress }}%</span>
                    </div>
                    <div class="progress my-2 w-75 mx-auto" role="progressbar" aria-label="Warning example"
                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $progress }}%"></div>
                    </div>

                    <div class="card-body border rounded-3 d-flex justify-content-center align-items-center w-75 mx-auto"
                        style="height: 3em">
                        <p class="mb-0">{{ $progress_text }}</p>
                    </div>
                @elseif(auth()->user()->role == 'dosen')
                    <div class="d-flex mx-5 justify-content-between align-items-center">
                        <span class="text-white" style="font-size: 9pt;">Kepuasan <span
                                class="fw-bold">Mahasiswa</span></span>
                        <span class="text-white" style="font-size: 9pt;">{{ $satisfaction }}%</span>
                    </div>
                    <div class="progress my-2 w-75 mx-auto" role="progressbar" aria-label="Warning example"
                        aria-valuenow="{{ $satisfaction }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $satisfaction }}%"></div>
                    </div>
                    <div class="card-body border mt-3 rounded-3 d-flex justify-content-center align-items-center w-75 mx-auto"
                        style="height: 3em">
                        <p class="mb-0">{{ $semester_text }}</p>
                    </div>
                @elseif (auth()->user()->role == 'admin')
                    <div class="d-flex mx-5 justify-content-between align-items-center">
                        <span class="text-white" style="font-size: 9pt;">Capaian Kinerja <span
                                class="fw-bold">Dosen</span></span>
                        <span class="text-white" style="font-size: 9pt;">{{ $satisfaction }}%</span>
                    </div>
                    <div class="progress my-2 w-75 mx-auto" role="progressbar" aria-label="Warning example"
                        aria-valuenow="{{ $satisfaction }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $satisfaction }}%"></div>
                    </div>
                    <div class="card-body border mt-3 rounded-3 d-flex justify-content-center align-items-center w-75 mx-auto"
                        style="height: 3em">
                        <p class="mb-0">{{ $semester_text }}</p>
                    </div>
                @endif
            </div>

            @php
                $routeDashboard = '/';
                if (auth()->user()->role == 'admin') {
                    $routeDashboard = route('admin.dashboard');
                } elseif (auth()->user()->role == 'mahasiswa') {
                    $routeDashboard = route('mahasiswa.dashboard');
                } elseif (auth()->user()->role == 'dosen') {
                    $routeDashboard = route('dosen.dashboard');
                }
            @endphp
            <li class="nav-item w-100">
                <a class="nav-link w-100 {{ request()->routeIs('dashboard') || request()->routeIs('mahasiswa.dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('dosen.dashboard') ? 'active' : '' }}"
                    href="{{ $routeDashboard }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if (auth()->user()->role == 'admin')
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.dosen.*') ? 'active' : '' }}"
                        href="{{ route('admin.dosen.index') }}">
                        <i class="fas fa-stream"></i>
                        <span>Data Dosen</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}"
                        href="{{ route('admin.mahasiswa.index') }}">
                        <i class="fas fa-stream"></i>
                        <span>Data Mahasiswa</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.kriteria.*') ? 'active' : '' }}"
                        href="{{ route('admin.kriteria.index') }}">
                        <i class="fas fa-list-alt"></i>
                        <span>Data Kriteria</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.pertanyaan.*') ? 'active' : '' }}"
                        href="{{ route('admin.pertanyaan.index') }}">
                        <i class="fas fa-list"></i>
                        <span>Data Pertanyaan</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.matkul.*') ? 'active' : '' }}"
                        href="{{ route('admin.matkul.index') }}">
                        <i class="fas fa-book"></i>
                        <span>Data Matkul</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.semester.*') ? 'active' : '' }}"
                        href="{{ route('admin.semester.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Data Semester</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}"
                        href="{{ route('admin.kelas.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Data Kelas</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.perkuliahan.*') ? 'active' : '' }}"
                        href="{{ route('admin.perkuliahan.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Data Perkuliahan</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}"
                        href="{{ route('admin.feedback.index') }}">
                        <i class="fas fa-stream"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}"
                        href="{{ route('admin.laporan.index') }}">
                        <i class="fas fa-print"></i>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}"
                        href="{{ route('admin.pengguna.index') }}">
                        <i class="fas fa-user-alt"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role == 'mahasiswa')
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('mahasiswa.matkul.*') ? 'active' : '' }}"
                        href="{{ route('mahasiswa.matkul.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Data Mata Kuliah</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('mahasiswa.feedback.*') ? 'active' : '' }}"
                        href="{{ route('mahasiswa.feedback.index') }}">
                        <i class="fas fa-stream"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('mahasiswa.log.*') ? 'active' : '' }}"
                        href="{{ route('mahasiswa.log.index') }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Log Aktivitas</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role == 'dosen')
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('dosen.feedback.*') ? 'active' : '' }}"
                        href="{{ route('dosen.feedback.index') }}">
                        <i class="fas fa-stream"></i>
                        <span>Feedback</span>
                    </a>
                </li>
                <li class="nav-item w-100">
                    <a class="nav-link w-100 {{ request()->routeIs('dosen.laporan.*') ? 'active' : '' }}"
                        href="{{ route('dosen.laporan.index') }}">
                        <i class="fas fa-print"></i>
                        <span>Laporan</span>
                    </a>
                </li>
            @endif
            <li class="nav-item w-100">
                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <a class="nav-link w-100" href="#" onclick="$('#logoutForm').submit()">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </form>
            </li>

            {{-- <li class="nav-item"><a class="nav-link {{ request()->routeIs('ortu.balita.*') ? 'active' : '' }}"
                    href="{{ route('ortu.balita.index') }}"><i class="fas fa-desktop"></i><span>Data
                        Balita</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('balita.*') ? 'active' : '' }}"
                    href="{{ route('balita.index') }}"><i class="fas fa-desktop"></i><span>Data Balita</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('lansia.*') ? 'active' : '' }}"
                    href="{{ route('lansia.index') }}"><i class="far fa-list-alt"></i></i><span>Data
                        Lansia</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('kader.*') ? 'active' : '' }}"
                    href="{{ route('kader.index') }}"><i class="far fa-list-alt"></i></i><span>Data
                        Kader</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('kader.profile') ? 'active' : '' }}"
                    href="{{ route('kader.profile', [
                        'kader' => auth()->user()->nik,
                    ]) }}"><i
                        class="far fa-list-alt"></i></i><span>Data Kader</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('penimbangan.*') ? 'active' : '' }}"
                    href="{{ route('penimbangan.index') }}"><i
                        class="fas fa-clipboard"></i><span>Penimbangan</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}"
                    href="{{ route('jadwal.index') }}"><i class="fas fa-calendar-alt"></i><span>Jadwal
                        Posyandu</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('kader.jadwal') ? 'active' : '' }}"
                    href="{{ route('kader.jadwal') }}"><i class="fas fa-calendar-alt"></i><span>Jadwal
                        Posyandu</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('pengguna.*') ? 'active' : '' }}"
                    href="{{ route('pengguna.index') }}"><i class="fas fa-user-friends"></i><span>Pengguna</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('ortu.riwayat.*') ? 'active' : '' }}"
                    href="{{ route('ortu.riwayat.index') }}"><i class="fas fa-history"></i><span>Riwayat</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('laporan.balita') ? 'active' : '' }}"
                    href="{{ route('laporan.balita') }}"><i class="fas fa-baby"></i><span>Laporan Posyandu
                        Balita</span></a>
            </li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('laporan.lansia') ? 'active' : '' }}"
                    href="{{ route('laporan.lansia') }}"><i class="fas fa-blind"></i><span>Laporan Posyandu
                        Lansia</span></a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <a class="nav-link " href="#" onclick="$('#logoutForm').submit()">
                        <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                    </a>
                </form>
            </li> --}}
            {{-- <hr class="sidebar-divider mt-2">
            <div class="sidebar-heading">Transaksi</div>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('transaction.index') ? 'active' : '' }}" href="#">
                    <i class="fas fa-cart-plus"></i>
                    <span>Transaksi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('sale.index') ? 'active' : '' }}" href="#">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Penjualan</span>
                </a>
            </li>
            <hr class="sidebar-divider mt-2">
            <div class="sidebar-heading">Laporan</div>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('report.index') ? 'active' : '' }}"
                    href="#"><i class="fas fa-file"></i><span>Laporan</span></a>
            </li>
            <hr class="sidebar-divider mt-2">
            <div class="sidebar-heading">Pengaturan</div>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.index') ? 'active' : '' }}"
                    href="#"><i class="fas fa-user"></i><span>Pengguna</span></a>
            </li>
            <hr class="sidebar-divider mt-2"> --}}
        </ul>
        <div class="d-none d-md-inline text-center"><button class="btn rounded-circle border-0" id="sidebarToggle"
                type="button"></button></div>
    </div>
</nav>
