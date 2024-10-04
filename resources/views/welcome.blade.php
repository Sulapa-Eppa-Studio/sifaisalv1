<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!---google fonts hear-->
    <link href="https://fonts.googleapis.com/css?family=Roboto&amp;display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/bootstrap.min.css">
    <!--font awesome css-->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/all.min.css">
    <!--animate css hear-->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/animate.css">
    <!-- light_case css -->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/lightcase.css">
    <!--swiper css-->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/swiper.min.css">
    <!-- shortcut image png is loaded -->
    <link rel="shortcut icon" href="{{ asset('modern') }}/assets/images/logo/logo_launch.png">
    <!-- style CSS is loaded -->
    <link rel="stylesheet" href="{{ asset('modern') }}/assets/css/style.css">

    <title>SiFaisal</title>

</head>

<body data-spy="scroll" class="overflow-auto">
    <!-- preloader start here -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- preloader ending here -->

    <!-- search area -->
    <div class="search-area">
        <div class="search-input">
            <div class="search-close">
                <i class="fas fa-times"></i>
            </div>
            <form>
                <input type="text" name="text" placeholder="Search Heare">
                <button class="search-btn">
                    <span class="serch-icon">
                        <i class="fas fa-search"></i>
                    </span>
                </button>
            </form>
        </div>
    </div>
    <!-- search area -->


    <!-- service area start -->
    <div class="service-area">
        <div class="container">
            <div class="post-heading text-center">
                <h3>Selamat Datang</h3>
                <p>
                    Di Aplikasi SiFaisal (Sistem Informasi veriFikasi Anggaran yang dIlaksanakan Secara kontraktuAL).
                </p>
            </div>
            <div class="section-wrapper">
                <div class="row justify-content-center">

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_admin_panel_url() }}/admin" target="get_admin_panel_url">Admin</a></h4>
                                <p>
                                    Mengelola seluruh sistem dan memiliki kontrol penuh atas semua modul dan pengguna.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_sp_panel_url() }}/penyedia-jasa" target="get_sp_panel_url">Penyedia Jasa</a></h4>
                                <p>
                                    Bertanggung jawab atas pengelolaan data dan informasi terkait penyedia jasa dalam
                                    sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_ppk_panel_url() }}/ppk" target="get_ppk_panel_url">Pejabat Pembuat Komitmen (PPK)</a></h4>
                                <p>
                                    Mengelola komitmen dalam pelaksanaan proyek atau pengadaan barang/jasa.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_spm_panel_url() }}/spm" target="get_spm_panel_url">Pejabat Penandatangan SPM (PP-SPM)</a></h4>
                                <p>
                                    Bertanggung jawab atas penandatanganan Surat Perintah Membayar (SPM) dalam sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_treasurer_panel_url() }}/treasurer" target="get_treasurer_panel_url">Bendahara Pengeluaran</a></h4>
                                <p>
                                    Mengelola pengeluaran anggaran dan bertanggung jawab atas pencatatan serta pelaporan
                                    keuangan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <div class="post-content text-center service-content wow">
                            <div class="post-thumb">
                                <img src="{{ asset('modern') }}/assets/images/service/icon/13.png" alt="service-image">
                            </div>
                            <div class="post-text">
                                <h4><a href="{{ get_kpa_panel_url() }}/kpa" target="get_kpa_panel_url">Kuasa Pengguna Anggaran (KPA)</a></h4>
                                <p>
                                    Memiliki kewenangan dalam penggunaan anggaran sesuai dengan tujuan dan peraturan
                                    yang berlaku.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- service area ends -->

    <!--scroll up-->
    <div class="scroll-top">
        <div class="scrollToTop active">
            <span>
                <i class="fas fa-arrow-up"></i>
            </span>
        </div>
    </div>
    <!--scroll up-->

    <!-- Optional JavaScript -->
    <script src="{{ asset('modern') }}/assets/js/jquery.js"></script>
    <script src="{{ asset('modern') }}/assets/js/bootstrap.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/fontawesome.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/jquery.waypoints.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/jquery.counterup.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/count-down.js"></script>
    <script src="{{ asset('modern') }}/assets/js/isotope-min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/lightcase.js"></script>
    <script src="{{ asset('modern') }}/assets/js/swiper.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/theia-sticky-sidebar.js"></script>
    <script src="{{ asset('modern') }}/assets/js/wow.min.js"></script>
    <script src="{{ asset('modern') }}/assets/js/active.js"></script>

</body>

</html>
