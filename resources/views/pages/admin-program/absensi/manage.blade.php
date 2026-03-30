@component('pages.admin-program.layouts.app')
    <div class="min-h-screen bg-[#fafafa] py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb Luxury --}}
            <nav class="flex mb-8 px-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin-program.content.index') }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-[#800000] italic transition-all">
                            Content Database
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#800000] italic">Attendance Control</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Panggil Volt Component Manage Absensi --}}
            {{-- Kita kirim variable $subProgram yang didapat dari Route --}}
            <livewire:admin-program.absensi.manage :subProgram="$subProgram" />

        </div>
    </div>
@endcomponent
