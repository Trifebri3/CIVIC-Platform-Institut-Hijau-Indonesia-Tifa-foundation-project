@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            {{-- Menggunakan logo lokal dari folder public/images/logo.png --}}
            <img src="{{ asset('images/logoo.png') }}" class="logo" alt="CIVIC Logo">

            {{-- Nama Platform sebagai fallback atau tambahan jika logo tidak tampil --}}
            <div style="margin-top: 10px; color: #800000; font-size: 22px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em;">
                CIVIC Platform
            </div>
        </a>
    </td>
</tr>
