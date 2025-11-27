<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('id', 'desc')->get();

        return Inertia::render('Banners/Index', [
            'banners' => $banners,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'imageUrl' => 'required|string',
            'active' => 'boolean',
        ]);

        Banner::create([
            'title' => $request->title,
            'imageUrl' => $request->imageUrl,
            'active' => $request->active ?? true,
        ]);

        return back()->with('success', 'Banner created successfully');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'imageUrl' => 'sometimes|string',
            'active' => 'sometimes|boolean',
        ]);

        $banner->update($request->only(['title', 'imageUrl', 'active']));

        return back()->with('success', 'Banner updated successfully');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->active = !$banner->active;
        $banner->save();

        return back()->with('success', 'Banner status updated');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return back()->with('success', 'Banner deleted successfully');
    }
}
