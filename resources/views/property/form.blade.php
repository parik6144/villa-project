<x-app-layout>
<script>
    window.deleteMediaUrl = (propertyId, mediaId, collection) => `/dashboard/property/${propertyId}/media/${mediaId}/collection/${collection}`;
</script>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($property) ? __('Edit Property') : __('Create Property') }}
        </h2>
    </x-slot>

    <div class="container mx-auto mt-6">
        <form method="POST" action="{{ isset($property) ? route('property.update', $property) : route('property.store') }}" enctype="multipart/form-data">
            @csrf
            @if (isset($property))
                @method('PATCH')
            @endif

            {{-- Select Property Type --}}
            <div class="mt-4">
                <x-input-label for="property_type_id" :value="__('Select Property Type')" />
                <x-select 
                    id="property_type_id" 
                    name="property_type_id" 
                    :options="$propertyTypes" 
                    display-field="name" 
                    :value="old('property_type_id', isset($property) ? $property->property_type_id : null)" 
                />
                <x-input-error :messages="$errors->get('property_type_id')" class="mt-2" />
            </div>

            {{-- Title --}}
            <div class="mt-4">
                <x-input-label for="title" :value="__('Property Title')" />
                <x-text-input 
                    id="title" 
                    class="w-full" 
                    type="text" 
                    name="title" 
                    :value="old('title', isset($property) ? $property->title : '')" 
                    required 
                />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <x-input-label for="description" :value="__('Description')" />
                <textarea 
                    id="description" 
                    class="w-full" 
                    name="description" 
                    rows="5" 
                    required
                >{{ old('description', isset($property) ? $property->description : '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            {{-- Location --}}
            <div class="mt-4">
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input 
                    id="location" 
                    class="w-full" 
                    type="text" 
                    name="location" 
                    :value="old('location', isset($property) ? $property->location : '')" 
                    required 
                />
                <x-input-error :messages="$errors->get('location')" class="mt-2" />
            </div>

            {{-- Sleeping Places --}}
            <div class="mt-4">
                <x-input-label for="sleeping_places" :value="__('Sleeping Places')" />
                <x-text-input 
                    id="sleeping_places" 
                    class="w-full" 
                    type="number" 
                    name="sleeping_places" 
                    :value="old('sleeping_places', isset($property) ? $property->sleeping_places : '')" 
                />
                <x-input-error :messages="$errors->get('sleeping_places')" class="mt-2" />
            </div>

            {{-- Square --}}
            <div class="mt-4">
                <x-input-label for="square" :value="__('Square Footage')" />
                <x-text-input 
                    id="square" 
                    type="number" 
                    class="w-full" 
                    name="square" 
                    :value="old('square', isset($property) ? $property->square : '')" 
                />
                <x-input-error :messages="$errors->get('square')" class="mt-2" />
            </div>

            <x-google-map latitudeField="latitude" longitudeField="longitude" />

            {{-- Coordinates --}}
            <div class="mt-4 grid grid-cols-2 gap-4 w-full">
                <div>
                    <x-input-label for="latitude" :value="__('Latitude')" />
                    <x-text-input 
                        id="latitude" 
                        class="w-full" 
                        type="text" 
                        name="latitude" 
                        :value="old('latitude', isset($property) ? $property->latitude : '39.15')" 
                    />
                </div>
                <div>
                    <x-input-label for="longitude" :value="__('Longitude')" />
                    <x-text-input 
                        id="longitude" 
                        class="w-full" 
                        type="text" 
                        name="longitude" 
                        :value="old('longitude', isset($property) ? $property->longitude : '22.3')" 
                    />
                </div>
            </div>

            {{-- Primary Image Upload --}}
            <div class="mt-4">
                <x-input-label for="primary_image" :value="__('Primary Image')" />
                
                {{-- Display existing primary image if $property is defined --}}
                @if (isset($property) && $property->getMedia('properties')->isNotEmpty())
                    @php $primaryImage = $property->getMedia('properties')->first(); @endphp
                    <div class="relative w-24 h-24 border rounded overflow-hidden mb-2" data-media-id="{{ $primaryImage->id }}">
                        <img src="{{ $primaryImage->getUrl() }}" alt="Primary Image" class="object-cover w-full h-full">
                        <button type="button" 
                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold" 
                                onclick="deleteImage('{{ $primaryImage->id }}', '{{ $property->id }}', 'properties')">×</button>
                    </div>
                @endif

                <x-text-input id="primary_image" class="w-full" type="file" name="primary_image" />
                <x-input-error :messages="$errors->get('primary_image')" class="mt-2" />
            </div>

            {{-- Gallery Upload --}}
            <div class="mt-4">
                <x-input-label for="gallery_images" :value="__('Gallery Images')" />

                <div id="gallery-drop-area" class="border-2 border-dashed border-gray-300 p-4 rounded-lg relative" 
                    style="display: flex; flex-direction: column; align-items: center; cursor: pointer;">
                    <span>Drag and drop images here or click to upload</span>
                    <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple 
                        class="absolute inset-0 opacity-0 cursor-pointer" onchange="handleFileUpload(event)">
                    <div id="gallery-previews" class="flex flex-wrap gap-2 mt-2"></div>
                </div>
                
                {{-- Display existing gallery images if $property is defined --}}
                <div id="existing-images" class="flex flex-wrap gap-2 mt-4">
                    @if (isset($property))
                        @foreach($property->getMedia('properties-gallery') as $media)
                            <div class="relative w-24 h-24 border rounded overflow-hidden" data-media-id="{{ $media->id }}">
                                <img src="{{ $media->getUrl() }}" alt="Gallery Image" class="object-cover w-full h-full">
                                <button type="button" 
                                        class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold" 
                                        onclick="deleteImage('{{ $media->id }}', '{{ $property->id }}', 'properties-gallery')">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <x-input-error :messages="$errors->get('gallery_images')" class="mt-2" />
            </div>

            <!-- Iterate through Attribute Groups -->
            @foreach ($attributeGroups as $group)
                <fieldset class="border p-4 mt-4">
                    <legend class="text-lg font-semibold">{{ $group->name }}</legend>

                    <!-- Iterate through Attributes -->
                    @foreach ($group->attributes as $attribute)
                        <div class="mt-4">
                            <x-input-label :for="'property_attributes[' . $attribute->id . ']'" :value="$attribute->name" :required="$attribute->is_required"/>

                            @switch($attribute->type)
                                @case('select')
                                <x-select 
                                    :id="'property_attributes[' . $attribute->id . ']'" 
                                    :name="'property_attributes[' . $attribute->id . ']'" 
                                    :options="json_decode($attribute->options, true)" 
                                    :value="old('property_attributes[' . $attribute->id . ']', isset($property) ? $property->property_attributes()->where('attribute_id', $attribute->id)->first()->value : '')" 
                                    placeholder="Select an option"
                                />
                                @break
                                
                                @case('text')
                                <x-text-input
                                    :id="'property_attributes[' . $attribute->id . ']'"
                                    :name="'property_attributes[' . $attribute->id . ']'"
                                    class="block mt-1 w-full"
                                    :value="old('property_attributes[' . $attribute->id . ']', isset($property) ? $property->property_attributes()->where('attribute_id', $attribute->id)->first()->value : '')"
                                />
                                @break

                                @case('number')
                                <x-text-input
                                    :id="'property_attributes[' . $attribute->id . ']'"
                                    :name="'property_attributes[' . $attribute->id . ']'"
                                    class="block mt-1 w-full"
                                    type="number"
                                    :value="old('property_attributes[' . $attribute->id . ']', isset($property) && $property->property_attributes()->where('attribute_id', $attribute->id)->first() ? $property->property_attributes()->where('attribute_id', $attribute->id)->first()->value : '')"
                                />
                                @break
                                
                                @case('textarea')
                                <textarea
                                    id="property_attributes[{{ $attribute->id }}]" 
                                    name="property_attributes[{{ $attribute->id }}]" 
                                    class="block mt-1 w-full"
                                >{{ old('property_attributes[' . $attribute->id . ']', isset($property) ? $property->property_attributes()->where('attribute_id', $attribute->id)->first()->value : '') }}</textarea>
                                @break

                                @case('checkbox')
                                <x-checkbox
                                    :id="'property_attributes[' . $attribute->id . ']'"
                                    :name="'property_attributes[' . $attribute->id . ']'"
                                    :checked="old(
                                        'property_attributes[' . $attribute->id . ']', 
                                        isset($property) && $property->property_attributes()->where('attribute_id', $attribute->id)->first() 
                                            ? $property->property_attributes()->where('attribute_id', $attribute->id)->first()->value 
                                            : false
                                    )"
                                />
                                @break

                                @case('multi-checkbox')
                                @php
                                    $propertyAttribute = isset($property) ? $property->property_attributes()->where('attribute_id', $attribute->id)->first() : null;
                                    $selectedValues = json_decode($propertyAttribute ? $propertyAttribute->value : '[]', true);
                                
                                @endphp
                                @foreach (json_decode($attribute->options, true) as $key => $value)
                                    <label>
                                        <x-checkbox 
                                            :name="'property_attributes[' . $attribute->id . '][]'" 
                                            :value="$key" 
                                            :checked="in_array($key, old('property_attributes[' . $attribute->id . ']', $selectedValues))" 
                                        />
                                        {{ $value }}
                                    </label>
                                @endforeach
                                @break

                                @default
                                    <p class="text-red-500">{{ $attribute->type }} {{ __('Unsupported attribute type') }}</p>
                            @endswitch

                            <x-input-error :messages="$errors->get('property_attributes.' . $attribute->id)" class="mt-2" />
                        </div>
                    @endforeach
                </fieldset>
            @endforeach

            <div class="mt-4">
                <x-primary-button>
                    {{ isset($property) ? __('Update Property') : __('Create Property') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        window.deleteImage = function(mediaId, propertyId, collection) {
    if (confirm('Are you sure you want to delete this image?')) {

        fetch(deleteMediaUrl(propertyId, mediaId, collection), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(response => {
            if (response.ok) {
                const blockToDelete = document.querySelector(`[data-media-id="${mediaId}"]`);

                if (blockToDelete) {
                    blockToDelete.remove(); 
                } else {
                    console.error('Block with data-media-id not found.');
                }
                
            } else {
                console.log('Failed to delete the image. Please try again.')
            }
        })
        .catch(error => {
            console.error('An error occurred while deleting the image. Please try again. ', error);
        });
    }
};
    </script>

</x-app-layout>
