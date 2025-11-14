    <!-- App Bottom Menu -->
    <div class="appBottomMenu">
        <a href="/dashboard" class="item {{ request()->is('dashboard') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="home-outline"></ion-icon>
                <strong>Home</strong>
            </div>
        </a>
        <a href="/presensi/histori" class="item {{ request()->is('presensi/histori') ? 'active' : ''}}">
            <div class="col">
                <ion-icon name="document-text-outline" role="img" class="md hydrated"
                    aria-label="document text outline"></ion-icon>
                <strong>Histori</strong>
            </div>
        </a>
        <a href="/presensi/izin" class="item {{ request()->is('presensi/izin', 'presensi/buatizin') ? 'active' : ''}}">
            <div class="col">
            <ion-icon name="calendar-outline"></ion-icon>
                <strong>Izin</strong>
            </div>
        </a>
        <a href="/settings" class="item {{ request()->is('settings', 'editprofile') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="settings-outline"></ion-icon>
            <strong>Settings</strong>
        </div>
        </a>
    </div>
    <!-- * App Bottom Menu -->
