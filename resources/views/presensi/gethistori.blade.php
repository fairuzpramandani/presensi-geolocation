@if ($histori->isEmpty())
    <div class="alert alert-outline-warning">
        <p>Data Belum Ada</p>
    </div>
@else
    <ul class="listview image-listview">

        @foreach ($histori as $d)

        @php
            $jam_masuk_tepat_waktu = "08:00:00";
            $is_ontime = $d->jam_in <= $jam_masuk_tepat_waktu;
            $status_text = $is_ontime ? "Tepat Waktu" : "Terlambat";
            $status_class = $is_ontime ? "success" : "danger";
        @endphp

        <li>
            <div class="item">
                <img src="{{ asset('storage/uploads/karyawan/'.Auth::guard('karyawan')->user()->foto) }}" alt="foto profil" class="image">
                <div class="in">
                    <div>
                        <b>{{ date("d F Y", strtotime($d->tgl_presensi)) }}</b>
                        <span class="badge bg-{{ $status_class }}">{{ $status_text }}</span>
                    </div>
                        <div class="text-muted mt-1">
                        <div class="text-{{ $status_class }}" style="display: flex;">
                            <span style="width: 100px;"> <ion-icon name="log-in-outline"></ion-icon>
                                Masuk:
                            </span>
                            <b>{{ $d->jam_in }}</b>
                        </div>
                        <div style="display: flex;">
                            <span style="width: 100px;"> <ion-icon name="log-out-outline"></ion-icon>
                                @if ($d->jam_out)
                                    Pulang:
                                @else
                                    <i>Pulang:</i>
                                @endif
                            </span>
                            @if ($d->jam_out)
                                <b>{{ $d->jam_out }}</b>
                            @else
                                <i>(Belum Pulang)</i>
                            @endif
                        </div>
                </div>
            </div>
        </li>
        @endforeach

    </ul>
@endif
