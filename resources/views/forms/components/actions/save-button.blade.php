<x-filament-actions::action
    :action="$action"
    :badge="$getBadge()"
    :badge-color="$getBadgeColor()"
    dynamic-component="filament::button"
    :icon-position="$getIconPosition()"
    :labeled-from="$getLabeledFromBreakpoint()"
    :outlined="$isOutlined()"
    :size="$getSize()"
    class="fi-ac-btn-action"
    x-data="{
        form: null,
        isUploadingFile: false,
        label: '{{ $getLabel() }}'
    }"
    x-html="isUploadingFile ? '{{ __('filament-support::components/button.messages.uploading_file') }}' : label"
    x-bind:disabled="isUploadingFile"
    x-bind:class='{ "opacity-70 cursor-wait": isUploadingFile }'
    x-init="
        form = $el.closest('form')

        form?.addEventListener('file-upload-started', () => {
            isUploadingFile = true
        })

        form?.addEventListener('file-upload-finished', () => {
            isUploadingFile = false
        })
    "
>
    {{ $getLabel() }}
</x-filament-actions::action>
