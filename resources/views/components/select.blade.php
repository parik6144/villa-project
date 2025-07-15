@props(['options' => [], 'name' => '', 'id' => '', 'value' => '', 'placeholder' => ''])

<div>
    <select name="{{ $name }}" id="{{ $id }}" {{ $attributes->merge(['class' => 'block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
        @if ($placeholder)
            <option value="" disabled {{ old($name, $value) === null ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif

        @foreach ($options as $key => $label)
            <option value="{{ $key }}" {{ $key == old($name, $value) ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>

    @if ($errors->has($name))
        <p class="text-red-500 text-sm mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
