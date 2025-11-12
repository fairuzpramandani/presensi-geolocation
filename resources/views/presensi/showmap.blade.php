<style>
    #map { height: 300px; }
</style>
<div id="map"></div>
<script>
    var map;
    window.initMap = function() {
        var lokasi = "{{ $presensi->location_in ?? '' }}";

        if (!lokasi) {
            $("#map").html("Lokasi presensi tidak ditemukan.");
            return;
        }

        var lok = lokasi.split(",");
        var latitude = lok[0];
        var longitude = lok[1];

        if (map != undefined) { map.remove(); }

        map = L.map('map', {
            attributionControl: false
        }).setView([latitude, longitude], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        var namaKaryawan = "{{ $presensi->nama_lengkap }}";
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup("<b>" + namaKaryawan + "</b>")
            .openPopup();
        L.circle([latitude, longitude], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 100
        }).addTo(map);

        setTimeout(function() {
            map.invalidateSize();
        }, 500);
    }
</script>
