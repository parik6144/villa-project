<x-app-layout>

    <script>
        window.deleteMediaUrl = (serviceId, mediaId, collection) => `/dashboard/service/${serviceId}/media/${mediaId}/collection/${collection}`;
    </script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($service) ? __('Edit Service') : __('Create Service') }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-6">
        <form method="POST" action="{{ isset($service) ? route('service.update', $service) : route('service.store') }}" enctype="multipart/form-data">
            @csrf
            @if (isset($service))
                @method('PATCH')
            @endif

            <div class="mb-4">
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                              :value="old('title', isset($service) ? $service->title : '')" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="description" :value="__('Description')" />
                <textarea id="description" class="block mt-1 w-full" name="description" required>{{ old('description', isset($service) ? $service->description : '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="service_category_id" :value="__('Service Category')" />
                <select id="service_category_id" name="service_category_id" class="block mt-1 w-full">
                    @foreach($serviceCategories as $category)
                        <option value="{{ $category->id }}" {{ old('service_category_id', isset($service) ? $service->service_category_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('service_category_id')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input id="location" class="block mt-1 w-full" type="text" name="location"
                              :value="old('location', isset($service) ? $service->location : '')" required />
                <x-input-error :messages="$errors->get('location')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="price" :value="__('Price')" />
                <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price"
                              :value="old('price', isset($service) ? $service->price : '')" required />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <x-google-map latitudeField="latitude" longitudeField="longitude" />

            <div class="mt-4 grid grid-cols-2 gap-4 w-full">
                <div>
                    <x-input-label for="latitude" :value="__('Latitude')" />
                    <x-text-input id="latitude" class="w-full" type="text" name="latitude"
                                  :value="old('latitude', isset($service) ? $service->latitude : '39.15')" />
                </div>
                <div>
                    <x-input-label for="longitude" :value="__('Longitude')" />
                    <x-text-input id="longitude" class="w-full" type="text" name="longitude"
                                  :value="old('longitude', isset($service) ? $service->longitude : '22.3')" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="primary_image" :value="__('Primary Image')" />

                @if (isset($service) && $service->getMedia('services')->isNotEmpty())
                    @php $primaryImage = $service->getMedia('services')->first(); @endphp
                    <div class="relative w-24 h-24 border rounded overflow-hidden mb-2" data-media-id="{{ $primaryImage->id }}">
                        <img src="{{ $primaryImage->getUrl() }}" alt="Primary Image" class="object-cover w-full h-full">
                        <button type="button"
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold"
                                onclick="deleteImage('{{ $primaryImage->id }}', '{{ $service->id }}', 'services')">×</button>
                    </div>
                @endif

                <x-text-input id="primary_image" class="w-full" type="file" name="primary_image" />
                <x-input-error :messages="$errors->get('primary_image')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="gallery_images" :value="__('Gallery Images')" />
                <div id="gallery-drop-area" class="border-2 border-dashed border-gray-300 p-4 rounded-lg relative"
                     style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                    <span>Drag and drop images here or click to upload</span>
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple
                           class="absolute inset-0 opacity-0 cursor-pointer" onchange="handleFileUpload(event)">
                    <div id="gallery-previews" class="flex flex-wrap gap-2 mt-2"></div>
                </div>

                <div id="existing-images" class="flex flex-wrap gap-2 mt-4">
                    @if (isset($service))
                        @foreach($service->getMedia('services-gallery') as $media)
                            <div class="relative w-24 h-24 border rounded overflow-hidden" data-media-id="{{ $media->id }}">
                                <img src="{{ $media->getUrl() }}" alt="Gallery Image" class="object-cover w-full h-full">
                                <button type="button"
                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold"
                                        onclick="deleteImage('{{ $media->id }}', '{{ $service->id }}', 'services-gallery')">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <x-input-error :messages="$errors->get('gallery_images')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="availability" :value="__('Availability')" />
                <select id="availability" class="block mt-1 w-full" name="availability" required>
                    <option value="1" {{ old('availability', isset($service) ? $service->availability : '') ? 'selected' : '' }}>Available</option>
                    <option value="0" {{ old('availability', isset($service) ? !$service->availability : '') ? 'selected' : '' }}>Unavailable</option>
                </select>
                <x-input-error :messages="$errors->get('availability')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ isset($service) ? __('Update Service') : __('Create Service') }}
                </x-primary-button>
            </div>
        </form>
    </div>

</x-app-layout>
