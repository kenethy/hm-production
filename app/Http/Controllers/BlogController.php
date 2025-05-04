<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories
        $categories = BlogCategory::ordered()->get();

        // Get active category from request or default to 'all'
        $activeCategory = $request->query('category');

        // Get active tag from request
        $activeTag = $request->query('tag');

        // Get search query from request
        $search = $request->query('search');

        // Get blog posts based on filters
        $query = BlogPost::with(['category', 'author', 'tags'])
            ->published()
            ->latest();

        // Filter by category
        if ($activeCategory) {
            $query->whereHas('category', function ($q) use ($activeCategory) {
                $q->where('slug', $activeCategory);
            });
        }

        // Filter by tag
        if ($activeTag) {
            $query->whereHas('tags', function ($q) use ($activeTag) {
                $q->where('slug', $activeTag);
            });
        }

        // Filter by search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Get paginated posts
        $posts = $query->paginate(9);

        // Get featured posts
        $featuredPosts = BlogPost::with(['category', 'author'])
            ->published()
            ->featured()
            ->latest()
            ->take(3)
            ->get();

        // Get popular tags
        $popularTags = BlogTag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get();

        // Set meta description based on filters
        $metaDescription = 'Blog Hartono Motor - Tips dan informasi seputar perawatan mobil untuk Anda.';

        if ($activeCategory) {
            $category = $categories->where('slug', $activeCategory)->first();
            $metaDescription = "Artikel tentang {$category->name} dari Hartono Motor. Tips dan informasi seputar perawatan mobil untuk Anda.";
        } elseif ($activeTag) {
            $tag = $popularTags->where('slug', $activeTag)->first();
            $metaDescription = "Artikel dengan tag {$tag->name} dari Hartono Motor. Tips dan informasi seputar perawatan mobil untuk Anda.";
        } elseif ($search) {
            $metaDescription = "Hasil pencarian untuk '{$search}' - Blog Hartono Motor. Tips dan informasi seputar perawatan mobil untuk Anda.";
        }

        return view('pages.blog.index', [
            'title' => 'Blog',
            'metaDescription' => $metaDescription,
            'metaKeywords' => 'blog bengkel mobil, tips perawatan mobil, servis mobil, sparepart mobil, hartono motor blog',
            'posts' => $posts,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'activeCategory' => $activeCategory,
            'activeTag' => $activeTag,
            'search' => $search,
        ]);
    }

    public function show($slug)
    {
        // Get the blog post by slug
        $post = BlogPost::with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment view count
        $post->incrementViewCount();

        // Get related posts from the same category
        $relatedPosts = BlogPost::with(['category', 'author'])
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->published()
            ->latest()
            ->take(3)
            ->get();

        // Get popular tags
        $popularTags = BlogTag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get();

        // Create meta description from excerpt or truncated content
        $metaDescription = $post->excerpt;
        if (empty($metaDescription)) {
            $metaDescription = strip_tags($post->content);
            $metaDescription = substr($metaDescription, 0, 160);
            if (strlen($metaDescription) >= 160) {
                $metaDescription .= '...';
            }
        }

        // Create meta keywords from tags
        $metaKeywords = $post->tags->pluck('name')->join(', ');
        if (empty($metaKeywords)) {
            $metaKeywords = 'blog bengkel mobil, tips perawatan mobil, servis mobil, hartono motor blog';
        }

        // Get image for Open Graph
        $ogImage = asset('storage/' . $post->featured_image);

        // Create breadcrumbs
        $breadcrumbs = [
            ['label' => 'Blog', 'url' => route('blog.index')],
            ['label' => $post->category->name, 'url' => route('blog.category', $post->category->slug)],
            ['label' => $post->title]
        ];

        return view('pages.blog.show', [
            'title' => $post->title,
            'metaDescription' => $metaDescription,
            'metaKeywords' => $metaKeywords,
            'ogImage' => $ogImage,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'popularTags' => $popularTags,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function category($slug)
    {
        // Get the category by slug and ensure it exists
        BlogCategory::where('slug', $slug)->firstOrFail();

        // Redirect to the blog index with category filter
        return redirect()->route('blog.index', ['category' => $slug]);
    }

    public function tag($slug)
    {
        // Get the tag by slug and ensure it exists
        BlogTag::where('slug', $slug)->firstOrFail();

        // Redirect to the blog index with tag filter
        return redirect()->route('blog.index', ['tag' => $slug]);
    }
}
