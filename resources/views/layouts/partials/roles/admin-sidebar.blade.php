@php $role = Fungsi::getRoleSession(); @endphp
<!-- Heading -->
<div class="sidebar-heading">
    Home
</div>

<!-- Nav Item - Home -->
<li class="nav-item {{ request()->is('dashboard','profile','profile/*') ? ' active' : ''}}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#home" aria-expanded="true" aria-controls="home">
        <i class="fas fa-fw fa-home"></i>
        <span>Home</span>
    </a>
    <div id="home" class="collapse" aria-labelledby="headingUtilities" data-parent="">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item pl-2{{ request()->is('dashboard') ? ' active' : ''}}" href="{{ route('dashboard') }}">
                <span>Dashboard</span></a>
            <a class="collapse-item pl-2{{ request()->is('profile','profile/*') ? ' active' : ''}}" href="{{ route('profile.edit',Auth::id()) }}">
                <span>My Profile</span></a>
        </div>
    </div>
</li>


<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Menu Utama
</div>

<li class="nav-item{{ request()->is('user','user/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('user') }}">
        <i class="fas fa-fw fa-users"></i>
        <span>Kelola User</span>
    </a>
</li>
<li class="nav-item{{ request()->is('job','job/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('job') }}">
        <i class="icon-work"></i>
        <span>Kelola Pekerjaan</span>
    </a>
</li>
<li class="nav-item{{ request()->is('patient','patient/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('patient') }}">
        <i class="icon-people"></i>
        <span>Kelola Pasien</span>
    </a>
</li>
<li class="nav-item{{ request()->is('past-medical','past-medical/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('past-medical') }}">
        <i class="ion-ios-medical"></i>
        <span>Kelola Riwayat Penyakit</span>
    </a>
</li>
<li class="nav-item{{ request()->is('eye-disorder','eye-disorder/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('eye-disorder') }}">
        <i class="icon-eye"></i>
        <span>Kelola Data Keluhan Mata</span>
    </a>
</li>
<li class="nav-item{{ request()->is('eye-examination','eye-examination/*') ? ' active' : ''}}">
    <a class="nav-link" href="{{ route('eye-examination') }}">
        <i class="fa fa-eye"></i>
        <span>Kelola Data Pemeriksaan</span>
    </a>
</li>
