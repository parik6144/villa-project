<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('user_id', Auth::id())->get();
        return view('service.index', compact('services'));
    }

    public function create()
    {
        $serviceCategories = ServiceCategories::all();
        return view('service.form', compact('serviceCategories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'availability' => 'required|boolean',
            'service_category_id' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $service = Service::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'price' => $validatedData['price'],
            'availability' => $validatedData['availability'],
            'service_category_id' => $validatedData['service_category_id'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('primary_image')) {
            $service->addMediaFromRequest('primary_image')->toMediaCollection('services');
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $file) {
                $service->addMedia($file)->toMediaCollection('services-gallery');
            }
        }

        return redirect()->route('service.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $this->authorizeService($service);
        $serviceCategories = ServiceCategories::all();
        return view('service.form', compact('service', 'serviceCategories'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric',
            'availability' => 'required|boolean',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $service->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'price' => $validatedData['price'],
            'availability' => $validatedData['availability'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
        ]);

        if ($request->hasFile('primary_image')) {
            $service->clearMediaCollection('services');
            $service->addMediaFromRequest('primary_image')->toMediaCollection('services');
        }

        if ($request->hasFile('gallery_images')) {
            $service->clearMediaCollection('services-gallery');
            foreach ($request->file('gallery_images') as $file) {
                $service->addMedia($file)->toMediaCollection('services-gallery');
            }
        }

        return redirect()->route('service.index')->with('success', 'Service updated successfully.');
    }

    private function authorizeService(Service $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function destroyMedia(Service $property, $mediaId, $collection = '')
    {
        $mediaItem = $property->getMedia($collection)->find($mediaId);


        if ($mediaItem) {
            $mediaItem->delete();
            return response()->json(['message' => 'Media deleted successfully.']);
        }

        return response()->json(['message' => 'Media not found.'], 404);
    }

}
