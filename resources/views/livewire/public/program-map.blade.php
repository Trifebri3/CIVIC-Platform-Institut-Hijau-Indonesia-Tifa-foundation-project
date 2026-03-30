<?php

use App\Models\ProgramKhusus;
use Livewire\Volt\Component;

new class extends Component {
    public function with()
    {
        return [
            'locations' => ProgramKhusus::whereNotNull('latitude')
                            ->whereNotNull('longitude')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'name' => $item->program_name,
                                    'lat'  => (float) $item->latitude,
                                    'lng'  => (float) $item->longitude,
                                    'info' => [
                                        'coordinator' => $item->coordinator_name,
                                        'phone'       => $item->coordinator_phone,
                                        'address'     => "{$item->village}, {$item->district}, {$item->city_regency}",
                                        'photo'       => $item->main_photo ? asset('storage/'.$item->main_photo) : asset('images/banner.png')
                                    ]
                                ];
                            })
        ];
    }
}; ?>

<div class="w-full h-screen relative" x-data="initMap(@js($locations))" x-init="startMap()" wire:ignore>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 5px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .leaflet-popup-tip-container { display: none; }
    </style>

    {{-- UI Overlay --}}
    <div class="absolute top-6 left-6 z-[1000] pointer-events-none">
        <div class="bg-black/80 backdrop-blur-md p-6 rounded-[2.5rem] text-white shadow-2xl border border-white/10 pointer-events-auto max-w-xs">
            <h2 class="text-xl font-black italic uppercase leading-none tracking-tighter text-[#800000]">IHI Map Explorer</h2>
            <p class="text-[8px] font-bold opacity-50 mt-2 uppercase tracking-[0.3em]">Live Tracking Program</p>
        </div>
    </div>

    {{-- Canvas Peta --}}
    <div id="map" class="w-full h-full z-0 bg-slate-900"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function initMap(locations) {
            return {
                map: null,
                locations: locations,
                startMap() {
                    if (this.map) return;
                    this.map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-2.5489, 118.0149], 5);
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(this.map);
                    L.control.zoom({ position: 'bottomright' }).addTo(this.map);

                    this.locations.forEach(loc => {
                        const icon = L.divIcon({
                            className: 'custom-div-icon',
                            html: `<div style="background-color: #800000; width: 14px; height: 14px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 15px rgba(128,0,0,0.6);"></div>`,
                            iconSize: [14, 14], iconAnchor: [7, 7]
                        });

                        const popup = `
                            <div class="p-3" style="min-width: 200px;">
                                <div class="rounded-2xl overflow-hidden mb-3 shadow-inner">
                                    <img src="${loc.info.photo}" class="w-full h-28 object-cover">
                                </div>
                                <h3 style="margin:0; font-size: 14px; font-weight: 900; text-transform: uppercase; color: #800000;">${loc.name}</h3>
                                <p style="margin:5px 0; font-size: 10px; color: #444;"><b>PJ:</b> ${loc.info.coordinator}</p>
                                <a href="https://wa.me/${loc.info.phone}" target="_blank" style="display: block; background: #800000; color: white; text-align: center; padding: 10px; border-radius: 12px; text-decoration: none; font-size: 10px; font-weight: 800; margin-top: 12px;">HUBUNGI KORLAP</a>
                            </div>`;

                        L.marker([loc.lat, loc.lng], { icon: icon }).addTo(this.map).bindPopup(popup);
                    });
                }
            }
        }
    </script>
</div>
