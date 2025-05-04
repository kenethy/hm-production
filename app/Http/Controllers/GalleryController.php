<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories
        $categories = GalleryCategory::ordered()->get();

        // Get active category from request or default to 'all'
        $activeCategory = $request->query('category', 'all');

        // Get gallery items based on category filter
        $query = Gallery::with('category')->ordered();

        if ($activeCategory !== 'all') {
            $query->whereHas('category', function ($q) use ($activeCategory) {
                $q->where('slug', $activeCategory);
            });
        }

        $galleryItems = $query->paginate(9);

        // Get featured gallery items
        $featuredItems = Gallery::with('category')
            ->featured()
            ->ordered()
            ->take(3)
            ->get();

        return view('pages.gallery-new', [
            'title' => 'Galeri',
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'galleryItems' => $galleryItems,
            'featuredItems' => $featuredItems,
        ]);
    }

    public function show($id)
    {
        $galleryItem = Gallery::with('category')->findOrFail($id);

        // Get related gallery items from the same category
        $relatedItems = Gallery::with('category')
            ->where('category_id', $galleryItem->category_id)
            ->where('id', '!=', $galleryItem->id)
            ->ordered()
            ->take(4)
            ->get();

        return view('pages.gallery-detail', [
            'title' => $galleryItem->title,
            'galleryItem' => $galleryItem,
            'relatedItems' => $relatedItems,
        ]);
    }
}
