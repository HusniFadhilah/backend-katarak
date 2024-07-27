@extends('layouts.template.auth')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Kebijakan Privasi') }}</h5>
                </div>

                <div class="card-body">
                    <b>Terakhir Diperbarui: 11 Juli 2024</b>
                    <p>
                        <a href="https://doltinuku.id">Doltinuku Developer</a> sebagai pihak yang diberi tugas oleh Organisasi Profesi Dokter Spesialis Mata di <a href="https://ophthalmology.fk.undip.ac.id/">Universitas Diponegoro</a> menghargai privasi Anda. Kebijakan privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi yang Anda berikan kepada kami melalui aplikasi kami. Kami menyarankan Anda untuk membaca kebijakan privasi ini dengan seksama untuk memahami langkah-langkah yang kami ambil untuk melindungi informasi Anda.
                    </p>

                    <h5>1. Informasi yang Kami Kumpulkan</h5>

                    <p>
                        Kami dapat mengumpulkan informasi pribadi dari Anda saat Anda menggunakan aplikasi kami, termasuk namun tidak terbatas pada:
                        <ul>
                            <li>Nama dan ID</li>
                            <li>Alamat Email</li>
                            <li>Nomor Telepon</li>
                            {{-- <li>Data lokasi dari GPS untuk memfasilitasi skrining di lokasi yang tepat</li> --}}
                        </ul>
                        Informasi ini dikumpulkan hanya untuk tujuan internal kami, seperti untuk menginformasikan Anda kepada dokter, kader, dan memberikan layanan yang disediakan pada unit yang bekerjasama dengan kami.
                    </p>

                    <h5>2. Penggunaan Informasi Pribadi</h5>
                    <p>
                        Informasi pribadi yang Anda berikan kepada kami akan digunakan untuk:
                    </p>
                    <ul>
                        <li>Memfasilitasi skrining katarak oleh kader kesehatan di puskesmas.</li>
                        <li>Mengirimkan hasil analisis foto mata oleh dokter spesialis mata.</li>
                        <li>Menghubungi Anda untuk memberikan informasi terkait layanan kami.</li>
                    </ul>

                    <h5>3. Keamanan Informasi</h5>
                    <p>
                        Kami melindungi informasi pribadi Anda dengan menerapkan langkah-langkah keamanan yang sesuai, termasuk akses terbatas dan teknologi keamanan yang mutakhir, untuk mencegah akses yang tidak sah.
                    </p>

                    <h5>4. Pembagian Informasi Pribadi</h5>
                    <p>
                        Kami tidak akan membagikan informasi pribadi Anda kepada pihak ketiga tanpa izin Anda, kecuali yang diperlukan untuk memberikan layanan yang Anda minta atau jika diperlukan oleh hukum.
                    </p>

                    <h5>5. Penanganan Data</h5>
                    <p>
                        Kami akan menyimpan informasi pribadi Anda hanya selama diperlukan untuk tujuan yang telah dijelaskan dalam kebijakan ini atau sebagaimana diperlukan untuk mematuhi kewajiban hukum kami.
                    </p>

                    <h5>6. Hak Anda</h5>
                    <p>
                        Anda (kader dan dokter) memiliki hak untuk memperbarui, mengoreksi, atau menghapus informasi pribadi Anda kapan saja dengan mengakses akun Anda di aplikasi kami.
                    </p>

                    <h5>7. Perubahan pada Kebijakan Privasi</h5>
                    <p>
                        Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu dengan mengumumkan perubahan tersebut di dalam aplikasi kami atau melalui situs web kami. Kami menganjurkan Anda untuk secara berkala memeriksa kebijakan privasi kami untuk memastikan bahwa Anda tetap memahami bagaimana kami melindungi informasi Anda.
                    </p>

                    <h5>8. Kontak</h5>
                    <p>
                        Jika Anda memiliki pertanyaan atau kekhawatiran tentang kebijakan privasi ini atau bagaimana kami mengelola informasi pribadi Anda, silakan hubungi kami melalui email di <a href="mailto:info@doltinukuid@gmail.com">doltinukuid@gmail.com</a> atau melalui fitur yang tersedia di aplikasi kami.
                    </p>

                    <p>
                        Kebijakan ini terakhir diperbarui pada 11 Juli 2024.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>

</script>
@endpush
