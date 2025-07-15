<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Services') }}
        </h2>
    </x-slot>


    @if (session('success'))
        <div class="bg-green-500 text-white p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end my-4">
        <a href="{{ route('service.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Add New Service') }}
        </a>
    </div>

    @if($services->isEmpty())
        <p>No services found. Please add a new service.</p>
    @else
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b">Title</th>
                <th class="py-2 px-4 border-b">Description</th>
                <th class="py-2 px-4 border-b">Location</th>
                <th class="py-2 px-4 border-b">Price</th>
                <th class="py-2 px-4 border-b">Availability</th>
                <th class="py-2 px-4 border-b">Approved</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $service->title }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->description }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->location }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->price }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->availability ? 'Available' : 'Unavailable' }}</td>
                    <td class="py-2 px-4 border-b">{{ $service->is_approved ? 'Approved' : 'Not Approved' }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('service.edit', $service->id) }}" class="text-blue-500">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</x-app-layout>
