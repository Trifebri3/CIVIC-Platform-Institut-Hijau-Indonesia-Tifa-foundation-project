@component('pages.super-admin.layouts.app')
    <div class="py-12 bg-[#fafafa] min-h-screen">
        {{-- Passing parameter template ke component Volt --}}
        <livewire:super-admin.templates.edit :template="$template" />
    </div>
@endcomponent
