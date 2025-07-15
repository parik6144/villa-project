<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Attribute;
use App\Models\PropertyAttribute;
use App\Models\AttributeGroup;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminNotification;
use App\Models\User;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('user_id', Auth::id())->get();
        return view('property.index', compact('properties'));
    }

    public function create()
    {
        $propertyTypes = PropertyType::pluck('name', 'id')->toArray();
        $attributeGroups = AttributeGroup::with('attributes')->get();
        return view('property.form', compact('attributeGroups', 'propertyTypes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'sleeping_places' => 'nullable|integer',
            'square' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
	    'floor_plan_list.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'property_attributes.*' => 'nullable',
        ]);

        $property = Property::create([
            'property_type_id' => $validatedData['property_type_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'sleeping_places' => $validatedData['sleeping_places'] ?? null,
            'square' => $validatedData['square'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,
        ]);

        // Add primary image to media library
        if ($request->hasFile('primary_image')) {
            $property->addMediaFromRequest('primary_image')->toMediaCollection('properties');
        }

        // Process gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $property->addMedia($file)->toMediaCollection('properties-gallery');
            }
        }
	
	// Process floor_plan_list images
        if ($request->hasFile('floor_plan_list')) {
            foreach ($request->file('floor_plan_list') as $file) {
                $property->addMedia($file)->toMediaCollection('floor-plan-list-gallery');
            }
        }

        // Store attributes
        $this->saveAttributes($property, $validatedData);

            //send notification to the admin
            $message = "A new property has been added to the site:\n\n" .
                "Property Title: {$property->title}\n" .
                "Property Type: {$property->propertyType->name}\n" .
                "Location: {$property->location}\n" .
                "Sleeping Places: {$property->sleeping_places}\n" .
                "Square Footage: {$property->square}\n" .
                "Latitude: {$property->latitude}\n" .
                "Longitude: {$property->longitude}\n\n" .
                "Description:\n{$property->description}";
    
            // Update the message subject
            $subject = "New Property Added to the Site";
    
            // Send notification to the admin
            $notification = new AdminNotification($message, $subject);
            $notification->send();

        return redirect()->route('property.index')->with('success', 'Property created successfully.');
    }

    public function edit(Property $property)
    {
        $this->authorizeProperty($property);
        $propertyTypes = PropertyType::pluck('name', 'id')->toArray();
        $attributeGroups = AttributeGroup::with('attributes')->get();
        return view('property.form', compact('property', 'attributeGroups', 'propertyTypes'));
    }

    public function update(Request $request, Property $property)
    {
        $validatedData = $request->validate([
            'property_type_id' => 'required|exists:property_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'sleeping_places' => 'nullable|integer',
            'square' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
	    'floor_plan_list.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'property_attributes.*' => 'nullable',
        ]);

        // Update property details
        $property->update([
            'property_type_id' => $validatedData['property_type_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'sleeping_places' => $validatedData['sleeping_places'] ?? null,
            'square' => $validatedData['square'] ?? null,
            'latitude' => $validatedData['latitude'] ?? null,
            'longitude' => $validatedData['longitude'] ?? null,
        ]);

        // Update primary image
        if ($request->hasFile('primary_image')) {
            $property->clearMediaCollection('properties');
            $property->addMediaFromRequest('primary_image')->toMediaCollection('properties');
        }

        // Add gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $property->addMedia($file)->toMediaCollection('properties-gallery');
            }
        }
	
	// Add floor_plan_list images
        if ($request->hasFile('floor_plan_list')) {
            foreach ($request->file('floor_plan_list') as $file) {
                $property->addMedia($file)->toMediaCollection('floor-plan-list-gallery');
            }
        }

        // Update attributes
        $this->saveAttributes($property, $validatedData);

        return redirect()->route('property.index')->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
        $this->authorizeProperty($property);
        $property->delete();
        return redirect()->route('property.index')->with('success', 'Property deleted successfully.');
    }

    private function authorizeProperty(Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function saveAttributes(Property $property, array $validatedData)
    {
        $attributes = Attribute::all();
        foreach ($attributes as $attribute) {
            $currentId = $attribute->id;
            if (isset($validatedData['property_attributes'][$currentId]) && !empty($validatedData['property_attributes'][$currentId])) {
                $value = $validatedData['property_attributes'][$currentId];
                if ($attribute->type === 'multi-checkbox') {
                    $value = json_encode($value);
                }
                PropertyAttribute::updateOrCreate(
                    ['property_id' => $property->id, 'attribute_id' => $currentId],
                    ['value' => $value]
                );
            } else {
                PropertyAttribute::where('property_id', $property->id)
                    ->where('attribute_id', $currentId)
                    ->delete();
            }
        }
    }

    public function destroyMedia(Property $property, $mediaId, $collection = '')
    {
        $mediaItem = $property->getMedia($collection)->find($mediaId);


        if ($mediaItem) {
            $mediaItem->delete();
            return response()->json(['message' => 'Media deleted successfully.']);
        }

        return response()->json(['message' => 'Media not found.'], 404);
    }
}
