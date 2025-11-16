<form action="/konfigurasi/{{ $lokasi->id }}/updatelokasikantor" method="POST" id="frmEditLokasi">
    @csrf
    @method('PUT') <input type="hidden" name="id" value="{{ $lokasi->id }}">

    <div class="input-icon mb-3">
        <span class="input-icon-addon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24h0z" fill="none"/>
                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
            </svg>
        </span>
        <input type="text" value="{{ $lokasi->nama_lokasi }}" id="nama_lokasi_edit" class="form-control" name="nama_lokasi" placeholder="Nama Lokasi" autocomplete="off">
    </div>

    <div class="input-icon mb-3">
        <span class="input-icon-addon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-map" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" />
                <path d="M9 4v13" /><path d="M15 7v13" />
            </svg>
        </span>
        <input type="text" value="{{ $lokasi->lokasi_kantor }}" id="lokasi_kantor_edit" class="form-control" name="lokasi_kantor" placeholder="Koordinat Kantor" autocomplete="off">
    </div>

    <div class="input-icon mb-3">
        <span class="input-icon-addon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-radar-2" width="24" height="24" viewBox="0 0 24 24"
                stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                <path d="M15.51 15.56a5 5 0 1 0 -3.51 1.44" /><path d="M18.832 17.86a9 9 0 1 0 -6.832 3.14" /><path d="M12 12v9" /></svg>
            </span>
        <input type="text" value="{{ $lokasi->radius }}" id="radius_edit" class="form-control" name="radius" placeholder="Radius" autocomplete="off">
    </div>

    <button class="btn btn-primary w-100" type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
        </svg>
        Update Lokasi
    </button>
</form>
