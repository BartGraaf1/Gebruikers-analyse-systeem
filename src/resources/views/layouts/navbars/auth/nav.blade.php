<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <!-- Link to the homepage or relevant section -->
                    <a class="opacity-5 text-dark" href="{{ url('/') }}">Home</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    <!-- Dynamically display the current page based on the URL path -->
                    {{ str_replace('-', ' ', Request::segment(1)) }}
                </li>
                @if(Request::segment(2))
                    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                        {{ str_replace('-', ' ', Request::segment(2)) }}
                    </li>
                @endif
                @if(Request::segment(3))
                    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                        {{ str_replace('-', ' ', Request::segment(3)) }}
                    </li>
                @endif
            </ol>
        </nav>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="/user-profile" class="nav-link text-body p-0">
                        <i class="fa fa-user me-sm-1"></i>
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <a href="{{ url('/logout')}}" class="nav-link text-body font-weight-bold px-0">
                        <span class="d-sm-inline d-none">Sign Out</span>
                    </a>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
