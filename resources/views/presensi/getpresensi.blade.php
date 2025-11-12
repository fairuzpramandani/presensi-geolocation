@foreach ($presensi as $d)
    @php
        $batas_jam_masuk = "08:00:00";
        $keterangan_text = "";
        $keterangan_class = "";

        if ($d->jam_in > $batas_jam_masuk) {
            try {
                $jam_masuk = new \DateTime($d->jam_in);
                $batas_waktu = new \DateTime($batas_jam_masuk);
                $diff = $jam_masuk->diff($batas_waktu);
                $jam_terlambat = $diff->h;
                $menit_terlambat = $diff->i;

                $keterangan_text = "Terlambat ";

                if ($jam_terlambat > 0) {
                    $keterangan_text .= $jam_terlambat . " Jam " . $menit_terlambat . " Menit";
                } else {
                    $keterangan_text .= $menit_terlambat . " Menit";
                }

                $keterangan_class = "bg-danger";
            } catch (Exception $e) {
                $keterangan_text = "Terlambat";
                $keterangan_class = "bg-danger";
            }
        } else {
            $keterangan_text = "Tepat Waktu";
            $keterangan_class = "bg-success";
        }
        $path_in = Storage::url('uploads/absen/'.$d->foto_in);
        $path_out = Storage::url('uploads/absen/'.$d->foto_out);
    @endphp

    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $d->email }}</td>
        <td>{{ $d->nama_lengkap }}</td>
        <td>{{ $d->nama_dept }}</td>
        <td>{{ $d->jam_in }}</td>
        <td>
            @if ($d->foto_in)
                <img src="{{ url($path_in) }}" class="avatar" alt="">
            @endif
        </td>
        <td>{{ $d->jam_out ?? 'Belum Pulang' }}</td>
        <td>
            @if ($d->foto_out)
                <img src="{{ url($path_out) }}" class="avatar" alt="">
            @endif
        </td>
        <td>
            <span class="badge {{ $keterangan_class }}">{{ $keterangan_text }}</span>
        </td>
        <td>
            <a href="/" class="btn btn-primary tampilkanpeta" id="{{ $d->id }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7" /><path d="M9 4v13" />
                    <path d="M15 7v5" /><path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z" />
                    <path d="M19 18v.01" />
                </svg>
            </a>
        </td>
    </tr>
@endforeach

<script>
    $(function(){
        $(".tampilkanpeta").click(function(e){
            var id = $(this).attr("id");
            $.ajax({
                type:'POST',
                url:'/tampilkanpeta',
                data:{
                    _token:"{{ csrf_token() }}",
                    id : id
                },
                cache:false,
                success:function(respond){
                    $("#loadmap").html(respond);
                }
            });
            $("#modal-tampilkanpeta").modal("show");
        });
    });
</script>
