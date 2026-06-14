<aside class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-header">
            <a class="sidebar-brand" href="{{ auth()->check() ? route('buku.index') : url('/') }}">
                Pustaka<span>40</span>
            </a>
        </div>

        @auth
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    @if (Auth::user()->profile_photo_url)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="Foto profil {{ Auth::user()->name }}">
                    @else
                        <span>{{ Auth::user()->initials }}</span>
                    @endif
                </div>
                <div>
                    <div class="fw-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-white-50 small text-uppercase">{{ Auth::user()->role }}</div>
                </div>
            </div>
        @endauth

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu</div>
            <ul class="nav flex-column gap-1">
                @auth
                    @if (Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}" href="{{ route('buku.index') }}">Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}">Kategori</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('genre.*') ? 'active' : '' }}" href="{{ route('genre.index') }}">Genre</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('anggota.*') ? 'active' : '' }}" href="{{ route('anggota.index') }}">Anggota</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}" href="{{ route('peminjaman.index') }}">Peminjaman</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('buku.*') ? 'active' : '' }}" href="{{ route('buku.index') }}">Buku</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}" href="{{ route('peminjaman.index') }}">Peminjaman</a>
                        </li>
                    @endif
                @endauth
            </ul>
        </nav>

        <div class="sidebar-footer">
            @auth
                <a class="nav-link px-0" href="{{ route('profile.edit') }}">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link px-0">Logout</button>
                </form>
            @else
                @if (Route::has('login'))
                    <a class="nav-link px-0" href="{{ route('login') }}">Login</a>
                @endif

                @if (Route::has('register'))
                    <a class="nav-link px-0" href="{{ route('register') }}">Daftar</a>
                @endif
            @endauth
        </div>
    </div>
</aside>
