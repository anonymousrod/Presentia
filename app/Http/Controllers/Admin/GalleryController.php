<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\OptimizesImages;

class GalleryController extends Controller
{
    use OptimizesImages;
    public function index()
    {
        $galleries = Gallery::latest()->paginate(12);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:51200',
            'title' => 'nullable|string|max:255',
        ]);

        $count = 0;
        foreach ($request->file('images') as $image) {
            $path = $this->optimizeAndStoreImage($image, 'galleries');

            Gallery::create([
                'title' => $request->title,
                'image_path' => $path,
                'is_active' => true,
            ]);
            $count++;
        }

        return back()->with('success', $count . ' image(s) ajoutée(s) à la galerie avec succès.');
    }

    public function toggleActive(Gallery $gallery)
    {
        $gallery->update([
            'is_active' => !$gallery->is_active,
        ]);

        return back()->with('success', 'Statut de l\'image mis à jour.');
    }

    public function destroy(Gallery $gallery)
    {
        if (Storage::disk('public')->exists($gallery->image_path)) {
            Storage::disk('public')->delete($gallery->image_path);
        }
        $gallery->delete();

        return back()->with('success', 'Image supprimée avec succès.');
    }

    public function bulkDestroy(Request $request)
    {
        if ($request->has('gallery_ids')) {
            $request->merge([
                'gallery_ids' => array_map(function($id) {
                    return decode_id($id);
                }, (array) $request->gallery_ids)
            ]);
        }

        $request->validate([
            'gallery_ids' => 'required|array',
            'gallery_ids.*' => 'exists:galleries,id',
        ]);

        $galleries = Gallery::whereIn('id', $request->gallery_ids)->get();
        foreach ($galleries as $gallery) {
            if (Storage::disk('public')->exists($gallery->image_path)) {
                Storage::disk('public')->delete($gallery->image_path);
            }
            $gallery->delete();
        }

        return back()->with('success', count($galleries) . ' image(s) supprimée(s) avec succès.');
    }
}
