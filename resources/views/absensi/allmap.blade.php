{{--  @extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <h4 class="mb-3">Peta Lokasi Presensi Siswa ({{ $tanggal }})</h4>
    <div id="map" style="height: 600px;"></div>
</div>
@endsection

@push('myscript')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css">

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script>
    var map = L.map('map').setView([-8.36, 114.15], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = L.markerClusterGroup();

    @foreach ($absensi as $a)
        @if($a->location_masuk)
            @php
                $lok = explode(',', $a->location_masuk);
                $lat = trim($lok[0]);
                $lng = trim($lok[1]);
            @endphp
            var markerMasuk = L.marker([{{ $lat }}, {{ $lng }}])
                .bindPopup(`<strong>{{ $a->nama_lengkap }}</strong><br>Kelas: {{ $a->kelas }}<br><span style="color:green;">Jam Masuk: {{ $a->jam_masuk }}</span>`);
            markers.addLayer(markerMasuk);

            var circleMasuk = L.circle([{{ $lat }}, {{ $lng }}], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 30
            });
            //markers.addLayer(circleMasuk);
            circleMasuk.addTo(map);

        @endif

        @if($a->location_keluar)
            @php
                $lok_keluar = explode(',', $a->location_keluar);
                $lat_keluar = trim($lok_keluar[0]);
                $lng_keluar = trim($lok_keluar[1]);
            @endphp
            var markerKeluar = L.marker([{{ $lat_keluar }}, {{ $lng_keluar }}], {
                icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34]
                })
            }).bindPopup(`<strong>{{ $a->nama_lengkap }}</strong><br>Kelas: {{ $a->kelas }}<br><span style="color:blue;">Jam Pulang: {{ $a->jam_keluar ?? 'Belum' }}</span>`);
            markers.addLayer(markerKeluar);

            var circleKeluar = L.circle([{{ $lat_keluar }}, {{ $lng_keluar }}], {
                color: 'blue',
                fillColor: 'lightblue',
                fillOpacity: 0.5,
                radius: 30
            });
           //markers.addLayer(circleKeluar);
           circleKeluar.addTo(map);

        @endif
    @endforeach

    map.addLayer(markers);
    markers.on('clustermouseover', function (a) {
    var count = a.layer.getChildCount();
    a.layer.bindTooltip(`${count} siswa di lokasi ini`, {
        permanent: false,
        direction: 'top',
        offset: [0, -5],
        opacity: 0.9
    }).openTooltip();
});

</script>



@endpush  --}}
@extends('layouts.admin.dashboard')

@section('content')
<div class="container">
    <h4 class="mb-3">Peta Lokasi Presensi Siswa ({{ $tanggal }})</h4>
    <div id="map" style="height: 600px;"></div>
</div>
@endsection

@push('myscript')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css">

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
    var map = L.map('map').setView([-8.36, 114.15], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = L.markerClusterGroup();
    var circleLayer = L.layerGroup().addTo(map);

    // Fungsi bantu untuk geser posisi lingkaran supaya tidak tumpuk
    function offsetLatLng(lat, lng, offset = 0.00005) {
        const newLat = parseFloat(lat) + (Math.random() - 0.5) * offset;
        const newLng = parseFloat(lng) + (Math.random() - 0.5) * offset;
        return [newLat, newLng];
    }

    @foreach ($absensi as $a)
        @if($a->location_masuk)
            @php
                $lok = explode(',', $a->location_masuk);
                $lat = trim($lok[0]);
                $lng = trim($lok[1]);
            @endphp

            var masukCoords = offsetLatLng({{ $lat }}, {{ $lng }});
            var markerMasuk = L.marker(masukCoords)
                .bindPopup(`<strong>{{ $a->nama_lengkap }}</strong><br>Kelas: {{ $a->kelas }}<br><span style="color:green;">Jam Masuk: {{ $a->jam_masuk }}</span>`);
            markers.addLayer(markerMasuk);

            var circleMasuk = L.circle(masukCoords, {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5,
                radius: 30
            });
            circleMasuk.addTo(circleLayer);
        @endif

        @if($a->location_keluar)
            @php
                $lok_keluar = explode(',', $a->location_keluar);
                $lat_keluar = trim($lok_keluar[0]);
                $lng_keluar = trim($lok_keluar[1]);
            @endphp

            var keluarCoords = offsetLatLng({{ $lat_keluar }}, {{ $lng_keluar }});
            var markerKeluar = L.marker(keluarCoords, {
                icon: L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/190/190411.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34]
                })
            }).bindPopup(`<strong>{{ $a->nama_lengkap }}</strong><br>Kelas: {{ $a->kelas }}<br><span style="color:blue;">Jam Pulang: {{ $a->jam_keluar ?? 'Belum' }}</span>`);
            markers.addLayer(markerKeluar);

            var circleKeluar = L.circle(keluarCoords, {
                color: 'blue',
                fillColor: 'lightblue',
                fillOpacity: 0.5,
                radius: 30
            });
            circleKeluar.addTo(circleLayer);
        @endif
    @endforeach

    map.addLayer(markers);

   // Tampilkan jumlah siswa di cluster secara langsung (tanpa klik/mouseover)
markers.on('clusterclick', function (a) {
    a.layer.spiderfy(); // tetap bisa klik
});

// Tampilkan tooltip langsung saat peta ditampilkan
markers.on('clustermouseover', function (a) {
    const count = a.layer.getChildCount();
    const tooltipText = `${count} siswa di lokasi ini`;

    // Cegah tooltip dobel
    if (!a.layer._tooltip) {
        a.layer.bindTooltip(tooltipText, {
            permanent: true,
            direction: 'top',
            className: 'cluster-tooltip',
            offset: [0, -10]
        }).openTooltip();
    }
});

var allLatLngs = [];

markers.eachLayer(function (layer) {
    if (layer.getLatLng) {
        allLatLngs.push(layer.getLatLng());
    }
});

if (allLatLngs.length > 0) {
    var bounds = L.latLngBounds(allLatLngs);
    map.fitBounds(bounds, { padding: [50, 50] });
}

</script>
@endpush
