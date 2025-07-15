@props(['name' => '', 'id' => '', 'checked' => false, 'label' => '', 'value' => ''])

<div class="flex items-center">
    <input type="checkbox" 
           name="{{ $name }}" 
           id="{{ $id }}" 
           value="{{ $value }}" 
           {{ $checked ? 'checked' : '' }}
           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
    />

    <label for="{{ $id }}" class="ml-2 block text-sm text-gray-900">
        {{ $label }}
    </label>

    @if ($errors->has($name))
        <p class="text-red-500 text-sm mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>