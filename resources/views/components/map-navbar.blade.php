<nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary me-4" href="{{ route('peta') }}">
            <span class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 2.2rem; height: 2.2rem;">
                <i class="fa fa-location-dot"></i>
            </span>
            <span>
                Petakans
                <div class="small text-muted" style="line-height: 1;">Peta Digital Anda</div>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-3 ms-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="{{ route('peta') }}">
                        <i class="fa fa-map"></i> Peta
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="{{ route('tabel') }}">
                        <i class="fa fa-table"></i> Tabel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="#">
                        <i class="fa fa-info-circle"></i> Tentang
                    </a>
                </li>
                @guest
                    <li class="nav-item">
                        <a class="btn btn-primary text-white d-flex align-items-center gap-1 px-3"
                            href="{{ route('login') }}">
                            <i class="fa fa-right-to-bracket"></i> Login
                        </a>
                    </li>
                @endguest
                @auth
                    <li class="nav-item bg danger rounded">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link text white"><i
                                    class="fa-solid fa-right-from-bracket"></i></button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
