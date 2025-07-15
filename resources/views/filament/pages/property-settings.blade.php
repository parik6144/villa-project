<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save" id="property-settings-form">
        {{ $this->form }}
        <x-filament-panels::form.actions 
            :actions="[
                $this->saveAction(),
                $this->cancelAction()
            ]" 
        />
    </x-filament-panels::form>


    @push('scripts')
        <script>
            //Prevent unsvad changes
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('property-settings-form');
                let isDirty = false;

                form.addEventListener('input', function () {
                    isDirty = true;
                });

                window.addEventListener('beforeunload', function (event) {
                    if (isDirty) {
                        event.preventDefault();
                        event.returnValue = '';
                        $wire.mountAction('cancelAction', {});
                    }
                });

                form.addEventListener('submit', function () {
                    isDirty = false;
                });
            });
        </script>
    @endpush

    </x-filament-panels::page>