<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Properties') }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-6">

    @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end my-4">
        <a href="{{ route('property.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Add New Property') }}
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b border-gray-300 text-left">{{ __('Image') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-left">{{ __('Title') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-left">{{ __('Type') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-left">{{ __('Location') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-center">{{ __('Sleeping Places') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-center">{{ __('Square') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-center">{{ __('Availability') }}</th>
                <th class="py-2 px-4 border-b border-gray-300 text-center">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($properties as $property)
                <tr>
                    <td class="py-2 px-4 border-b text-center">
                    <img src="{{ $property->getFirstMediaUrl('properties') }}" alt="Primary Image" class="w-32 h-auto mt-4">
                    </td>
                    <td class="py-2 px-4 border-b">{{ $property->title }}</td>
                    <td class="py-2 px-4 border-b">{{ $property->propertyType->name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b">{{ $property->location }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $property->sleeping_places }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $property->square }} m²</td>
                    <td class="py-2 px-4 border-b text-center">{{ $property->availability ? 'Available' : 'Not Available' }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        <a href="{{ route('property.edit', $property->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                            {{ __('Edit') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="py-4 px-4 text-center text-gray-500">
                        {{ __('You have no properties yet.') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    </div>
</x-app-layout>
