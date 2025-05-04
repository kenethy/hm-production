<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Promo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate a dynamic sitemap
     */
    public function index()
    {
        $content = view('sitemap.index');
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate a sitemap for blog posts
     */
    public function posts()
    {
        $posts = BlogPost::where('is_published', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.posts', compact('posts'));
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate a sitemap for blog categories
     */
    public function categories()
    {
        $categories = BlogCategory::orderBy('updated_at', 'desc')->get();

        $content = view('sitemap.categories', compact('categories'));
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate a sitemap for blog tags
     */
    public function tags()
    {
        $tags = BlogTag::orderBy('updated_at', 'desc')->get();

        $content = view('sitemap.tags', compact('tags'));
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate a sitemap for promos
     */
    public function promos()
    {
        $promos = Promo::where('is_active', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $content = view('sitemap.promos', compact('promos'));
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
}
