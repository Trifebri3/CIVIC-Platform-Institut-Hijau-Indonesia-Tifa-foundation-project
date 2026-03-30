@component('public.layouts.appumum')

<div class="relative w-full h-screen bg-slate-900">
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- UI Overlay --}}
    <div class="absolute top-6 left-6 z-[1000] pointer-events-none">
        <div class="bg-black/80 backdrop-blur-md p-6 rounded-[2rem] text-white shadow-2xl border border-white/10 pointer-events-auto">
            <h2 class="text-xl font-black italic uppercase leading-none text-[#800000]">IHI Map Explorer</h2>
            <p class="text-[8px] font-bold opacity-50 mt-2 uppercase tracking-[0.3em]">Direct Database Tracking</p>
        </div>
    </div>

    {{-- Map Canvas --}}
    <div id="map" class="w-full h-full"></div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // 1. Inisialisasi Map
        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false
        }).setView([-2.5489, 118.0149], 5);

        // 2. Tile Layer (Dark Mode)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // 3. Loop Data dari Laravel ke JS
        @foreach($programs as $p)
            (function() {
                const lat = {{ $p->latitude }};
                const lng = {{ $p->longitude }};

                const icon = L.divIcon({
                    className: 'custom-icon',
                    html: `<div style="background-color: #800000; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 10px rgba(128,0,0,0.5);"></div>`,
                    iconSize: [14, 14]
                });

                const popupHtml = `
                    <div class="p-3" style="min-width: 200px; font-family: sans-serif;">
                        <img src="{{ $p->main_photo ? asset('storage/'.$p->main_photo) : asset('images/banner.png') }}"
                             style="width:100%; height:100px; object-fit:cover; border-radius:12px; margin-bottom:10px;">
                        <h3 style="margin:0; font-size:14px; font-weight:900; color:#800000; text-transform:uppercase;">{{ $p->program_name }}</h3>
                        <p style="margin:5px 0; font-size:11px; color:#555;"><b>PJ:</b> {{ $p->coordinator_name }}</p>
                        <p style="margin:5px 0; font-size:10px; color:#888;">{{ $p->village }}, {{ $p->district }}</p>
                        <a href="https://wa.me/{{ $p->coordinator_phone }}" target="_blank"
                           style="display:block; background:#800000; color:white; text-align:center; padding:8px; border-radius:8px; text-decoration:none; font-size:10px; font-weight:bold; margin-top:10px;">
                           HUBUNGI KORLAP
                        </a>
                    </div>
                `;

                L.marker([lat, lng], { icon: icon })
                    .addTo(map)
                    .bindPopup(popupHtml);
            })();
        @endforeach
    </script>

    <style>
        .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 5px; }
        .leaflet-popup-tip-container { display: none; }
    </style>
</div>

@endcomponent
