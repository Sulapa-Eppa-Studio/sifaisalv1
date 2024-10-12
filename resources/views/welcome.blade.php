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

    <style>
        .post-heading {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .post-heading img {
            width: auto;
            /* Sesuaikan ukuran logo sesuai kebutuhan */
            height: 50px;
            margin-left: 8px;
        }

        .post-heading h3 {
            margin: 0;
        }

        .post-heading p {
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }
    </style>

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
                <h3>Selamat Datang Di Aplikasi</h3>
                <img src="{{ asset('images/logo-app.png') }}" alt="Logo">
                <p>
                    Sistem Informasi Verifikasi Pertanggungjawaban Anggaran Kegiatan Yang
                    Dilaksanakan Secara Kontraktual
                </p>
            </div>
            <div class="section-wrapper">
                <div class="row justify-content-center">

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_ppk_panel_url() }}/admin" target="get_ppk_panel_url">

                            <div class="post-content text-center service-content wow">
                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-admin.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_admin_panel_url() }}/admin"
                                            target="get_admin_panel_url">Admin</a>
                                    </h4>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_ppk_panel_url() }}/penyedia-jasa" target="get_ppk_panel_url">
                            <div class="post-content text-center service-content wow">
                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-penyedia-jasa.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_sp_panel_url() }}/penyedia-jasa"
                                            target="get_sp_panel_url">Penyedia
                                            Jasa</a></h4>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_ppk_panel_url() }}/ppk" target="get_ppk_panel_url">
                            <div class="post-content text-center service-content wow">

                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-ppk.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_ppk_panel_url() }}/ppk" target="get_ppk_panel_url">Pejabat
                                            Pembuat
                                            Komitmen (PPK)</a></h4>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_spm_panel_url() }}/spm" target="get_spm_panel_url">
                            <div class="post-content text-center service-content wow">
                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-ppspm.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_spm_panel_url() }}/spm" target="get_spm_panel_url">Pejabat
                                            Penandatangan SPM (PP-SPM)</a></h4>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_treasurer_panel_url() }}/treasurer" target="get_treasurer_panel_url">
                            <div class="post-content text-center service-content wow">
                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-bendahara.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_treasurer_panel_url() }}/treasurer"
                                            target="get_treasurer_panel_url">Bendahara Pengeluaran</a></h4>

                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                        <a href="{{ get_kpa_panel_url() }}/kpa" target="get_admin_panel_url">
                            <div class="post-content text-center service-content wow">
                                <div class="post-thumb">
                                    <img src="{{ asset('images/ic-kpa.svg') }}" alt="service-image">
                                </div>
                                <div class="post-text">
                                    <h4><a href="{{ get_kpa_panel_url() }}/kpa" target="get_kpa_panel_url">Kuasa
                                            Pengguna
                                            Anggaran (KPA)</a></h4>
                                </div>
                            </div>
                        </a>
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
