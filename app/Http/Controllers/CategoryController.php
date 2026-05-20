<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

/**
 * Public Category Controller
 * 
 * Handles public category pages for browsing comics by category.
 * 
 * Features:
 * - SEO-friendly slug-based URLs
 * - Pagination for large comic libraries
 * - Eager loading to prevent N+1 queries
 * - Sorting support
 * - Responsive design compatible
 * 
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * Display the specified category with its comics.
     * 
     * Shows category details and paginated list of comics.
     * Uses eager loading to prevent N+1 queries.
     * 
     * @param Category $category The category to display
     * @return View
     */
    public function show(Category $category): View
    {
        // Eager load comics with chapters and categories to prevent N+1 queries
        $comics = $category->comics()
            ->with(['chapters', 'categories'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('categories.show', compact('category', 'comics'));
    }
}
