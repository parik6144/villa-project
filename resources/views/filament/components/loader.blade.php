<div 
    wire:loading.delay.long  
    class="fixed inset-0 z-[99999] pointer-events-auto"
>
    <div class="z-[999999] absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 outline-none">
        <x-filament::loading-indicator wire:loading.delay.long class="h-8 w-8" id="loading-indicator"/>
    </div>

</div>
@push('scripts')
<script>
/*
    document.addEventListener('livewire:init', function () {
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            const activeElement = document.activeElement;
            if (
                activeElement &&
                activeElement.tagName === 'INPUT' &&
                activeElement.type === 'search'
            ) {
                return;
            }

            if (activeElement != document.body) activeElement.blur();
        });
    });
*/
</script>
@endpush