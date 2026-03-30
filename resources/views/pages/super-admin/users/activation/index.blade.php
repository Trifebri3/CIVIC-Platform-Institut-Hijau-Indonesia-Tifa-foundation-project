@component('pages.user.layouts.guest')
    <x-slot name="title">Form</x-slot>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 sm:rounded-xl">
                <div class="p-0 text-gray-900">
                    @livewire('user.activation-stepper')
                </div>
            </div>
@endcomponent
