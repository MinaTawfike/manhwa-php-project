<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use App\Models\Chapter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

/**
 * SEO: Sitemap Controller
 * 
 * Generates XML sitemap for Google and other search engines.
 * Includes homepage, all comics, and all chapters with proper metadata.
 * 
 * Performance: Cached for 1 hour to avoid database overload.
 * Compatible with APP_DEBUG=false for production.
 */
class SitemapController extends Controller
{
    /**
     * Generate XML sitemap
     * 
     * Returns XML sitemap with:
     * - Homepage
     * - All comics with last modified dates
     * - All chapters with last modified dates
     * 
     * Uses absolute URLs for proper crawling
     * 
     * @return Response XML sitemap
     */
    public function index(): Response
    {
        // Cache sitemap for 1 hour to improve performance
        $sitemap = Cache::remember('sitemap.xml', 3600, function () {
            $baseUrl = config('app.url');
            
            // Start XML structure
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            
            // Add homepage
            $xml .= $this->createUrlEntry(
                $baseUrl,
                now()->toIso8601String(),
                'daily',
                '1.0'
            );
            
            // Add all comics with eager loading to prevent N+1 queries
            $comics = Comic::select('id', 'slug', 'updated_at', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            foreach ($comics as $comic) {
                $xml .= $this->createUrlEntry(
                    route('comics.show', $comic),
                    $comic->updated_at->toIso8601String(),
                    'weekly',
                    '0.8'
                );
            }
            
            // Add all chapters with eager loading to prevent N+1 queries
            $chapters = Chapter::with('comic:id,slug')
                ->select('id', 'comic_id', 'number', 'updated_at', 'created_at')
                ->orderBy('updated_at', 'desc')
                ->get();
            
            foreach ($chapters as $chapter) {
                if ($chapter->comic) {
                    $xml .= $this->createUrlEntry(
                        route('chapters.show', [$chapter->comic, $chapter]),
                        $chapter->updated_at->toIso8601String(),
                        'monthly',
                        '0.6'
                    );
                }
            }
            
            $xml .= '</urlset>';
            
            return $xml;
        });
        
        return response($sitemap)
            ->header('Content-Type', 'application/xml');
    }
    
    /**
     * Create a URL entry for sitemap
     * 
     * @param string $url Absolute URL
     * @param string $lastModified ISO 8601 date
     * @param string $changeFrequency How often content changes
     * @param string $priority Priority 0.0-1.0
     * @return string XML URL entry
     */
    private function createUrlEntry(
        string $url,
        string $lastModified,
        string $changeFrequency,
        string $priority
    ): string {
        return sprintf(
            '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%s</priority></url>',
            htmlspecialchars($url, ENT_QUOTES),
            $lastModified,
            $changeFrequency,
            $priority
        );
    }
}
