@component('pages.admin-program.layouts.app', ['program' => $program])
    <div class="relative min-h-[600px] py-10">
        @livewire('admin-program.tracking.detail-user', [
            'program' => $program,
            'user' => $user
        ])
    </div>
@endcomponent
