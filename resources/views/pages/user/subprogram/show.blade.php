@component('pages.user.layouts.app')
    <div class="min-h-screen bg-[#fafafa] pb-20">
        {{-- 1. HEADER & DETAIL (Nama, Tgl, Jam, Assets semua sudah ada di sini) --}}
        <livewire:user.subprogram.show :subProgram="$subProgram" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
            {{-- Grid Utama: 2/3 Materi, 1/3 Ujian --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- 2. KOLOM KIRI: MATERI KELAS --}}
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.3em] text-gray-400 italic px-2">Course Materials</h3>
                    <livewire:user.kelas.show :subProgram="$subProgram" />
                </div>

                {{-- 3. KOLOM KANAN: EXAMINATIONS --}}
                

            </div>
        </div>
    </div>
@endcomponent
