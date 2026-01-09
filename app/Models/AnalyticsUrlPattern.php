<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsUrlPattern extends Model
{
    protected $table = 'analytics_url_patterns';
    
    protected $fillable = [
        'site_id',
        'domain',
        'pattern',
        'generated_at',
    ];
    
    protected $casts = [
        'generated_at' => 'datetime',
    ];
    
    /**
     * Get the site this pattern belongs to
     */
    public function site()
    {
        return $this->belongsTo(AnalyticsSite::class, 'site_id');
    }

    /**
     * Resolve URL pattern with strict priority rules
     * 
     * Priority Rules (enforced in this order):
     * 1. Advertisement Custom Patterns (HIGHEST PRIORITY)
     * 2. Exact Match (Non-Wildcard)
     * 3. Most Specific Wildcard Match (Maximum Depth)
     * 4. Segment-Level Fallback (Progressive Generalization)
     * 5. Root Catch-All (LOWEST PRIORITY)
     * 
     * @param string $url The URL to match (full URL or path)
     * @param array $customPatterns Array of custom patterns from advertisements (highest priority)
     * @param array $regularPatterns Array of regular patterns (from AnalyticsUrlPattern models or strings)
     * @return string|null The resolved pattern, or null if no match
     */
    public static function resolveUrlPattern(string $url, array $customPatterns = [], array $regularPatterns = []): ?string
    {
        // Extract path from URL if full URL is provided
        $urlPath = parse_url($url, PHP_URL_PATH) ?: $url;
        
        // Normalize: ensure leading slash, remove trailing slash (except root)
        $urlPath = '/' . trim($urlPath, '/');
        if ($urlPath === '') {
            $urlPath = '/';
        }

        // Priority 1: Check custom patterns from advertisements (HIGHEST PRIORITY)
        // These always override everything else - no fallback, no comparison
        foreach ($customPatterns as $customPattern) {
            $pattern = is_string($customPattern) ? $customPattern : $customPattern;
            $pattern = trim($pattern);
            
            if (empty($pattern)) {
                continue;
            }
            
            // Normalize pattern
            $normalizedPattern = '/' . trim($pattern, '/');
            if ($normalizedPattern === '') {
                $normalizedPattern = '/';
            }
            
            // Check if pattern matches
            if (self::matchesPattern($urlPath, $normalizedPattern)) {
                return $pattern; // Return original pattern (not normalized)
            }
        }

        // Priority 2: Exact Match (Non-Wildcard)
        // Find exact patterns (no *) that match character by character
        $exactMatches = [];
        foreach ($regularPatterns as $pattern) {
            $patternStr = is_string($pattern) ? $pattern : (is_object($pattern) && isset($pattern->pattern) ? $pattern->pattern : (string)$pattern);
            $patternStr = trim($patternStr);
            
            if (empty($patternStr)) {
                continue;
            }
            
            // Normalize pattern
            $normalizedPattern = '/' . trim($patternStr, '/');
            if ($normalizedPattern === '') {
                $normalizedPattern = '/';
            }
            
            // Check if it's an exact pattern (no wildcards)
            if (strpos($normalizedPattern, '*') === false) {
                // Exact match check
                if ($urlPath === $normalizedPattern) {
                    $exactMatches[] = $patternStr;
                }
            }
        }
        
        // If we have exact matches, return the first one (they're all equivalent)
        if (!empty($exactMatches)) {
            return $exactMatches[0];
        }

        // Priority 3 & 4: Wildcard Match (Most Specific)
        // Find all wildcard patterns that match
        $wildcardMatches = [];
        foreach ($regularPatterns as $pattern) {
            $patternStr = is_string($pattern) ? $pattern : (is_object($pattern) && isset($pattern->pattern) ? $pattern->pattern : (string)$pattern);
            $patternStr = trim($patternStr);
            
            if (empty($patternStr)) {
                continue;
            }
            
            // Normalize pattern
            $normalizedPattern = '/' . trim($patternStr, '/');
            if ($normalizedPattern === '') {
                $normalizedPattern = '/';
            }
            
            // Check if it's a wildcard pattern and matches
            if (strpos($normalizedPattern, '*') !== false) {
                if (self::matchesPattern($urlPath, $normalizedPattern)) {
                    $wildcardMatches[] = $patternStr;
                }
            }
        }
        
        // If we have wildcard matches, find the most specific one
        if (!empty($wildcardMatches)) {
            return self::selectMostSpecificPattern($urlPath, $wildcardMatches);
        }

        // Priority 5: Root Catch-All (LOWEST PRIORITY)
        // Check for root patterns: '/' or '*'
        foreach ($regularPatterns as $pattern) {
            $patternStr = is_string($pattern) ? $pattern : (is_object($pattern) && isset($pattern->pattern) ? $pattern->pattern : (string)$pattern);
            $patternStr = trim($patternStr);
            
            if ($patternStr === '/' || $patternStr === '*') {
                return $patternStr;
            }
        }

        // No match found
        return null;
    }

    /**
     * Check if a URL path matches a pattern
     * 
     * @param string $urlPath The normalized URL path
     * @param string $pattern The normalized pattern (may contain *)
     * @return bool True if matches
     */
    private static function matchesPattern(string $urlPath, string $pattern): bool
    {
        // Handle root patterns
        if ($pattern === '/' || $pattern === '*') {
            return true; // Matches everything
        }
        
        // Convert pattern to regex
        // Escape special regex characters, then replace * with .*
        $escaped = preg_quote($pattern, '/');
        
        // Replace escaped \* back to * (preg_quote escapes * as \*)
        $escaped = str_replace('\\*', '___WILDCARD___', $escaped);
        
        // Replace wildcard placeholder with regex that matches any characters including slashes
        $regex = str_replace('___WILDCARD___', '.*', $escaped);
        
        // Match from start to end
        $regex = '/^' . $regex . '$/';
        
        return preg_match($regex, $urlPath) === 1;
    }

    /**
     * Select the most specific pattern from a list of matching wildcard patterns
     * 
     * Priority criteria:
     * 1. Highest number of fixed (non-*) segments
     * 2. If tie → deeper path wins (more segments)
     * 3. If tie → more static segments
     * 4. If tie → wildcards deeper in the path (not near root)
     * 
     * @param string $urlPath The URL path being matched
     * @param array $patterns Array of matching patterns
     * @return string The most specific pattern
     */
    private static function selectMostSpecificPattern(string $urlPath, array $patterns): string
    {
        if (empty($patterns)) {
            return '';
        }
        
        if (count($patterns) === 1) {
            return $patterns[0];
        }

        // Calculate priority score for each pattern
        $scoredPatterns = [];
        foreach ($patterns as $pattern) {
            $normalizedPattern = '/' . trim($pattern, '/');
            if ($normalizedPattern === '') {
                $normalizedPattern = '/';
            }
            
            $score = self::calculatePatternPriority($normalizedPattern);
            $scoredPatterns[] = [
                'pattern' => $pattern,
                'normalized' => $normalizedPattern,
                'score' => $score,
            ];
        }

        // Sort by score (descending), then by pattern string for determinism
        usort($scoredPatterns, function ($a, $b) {
            // Primary sort: by score (higher is better)
            if ($b['score'] !== $a['score']) {
                return $b['score'] <=> $a['score'];
            }
            // Secondary sort: by pattern string for determinism
            return strcmp($a['pattern'], $b['pattern']);
        });

        return $scoredPatterns[0]['pattern'];
    }

    /**
     * Calculate priority score for a pattern
     * 
     * Scoring system:
     * +100 per exact segment (non-wildcard)
     * +10 per path depth (segment count)
     * -50 per wildcard
     * 
     * Higher score = more specific = higher priority
     * 
     * @param string $pattern The normalized pattern
     * @return int The priority score
     */
    private static function calculatePatternPriority(string $pattern): int
    {
        // Handle root patterns
        if ($pattern === '/' || $pattern === '*') {
            return 0; // Lowest priority
        }

        // Split pattern into segments
        $segments = array_filter(explode('/', $pattern), function ($segment) {
            return $segment !== '';
        });
        
        $segmentCount = count($segments);
        $exactSegmentCount = 0;
        $wildcardCount = 0;
        $wildcardPositions = [];
        
        foreach ($segments as $index => $segment) {
            if ($segment === '*') {
                $wildcardCount++;
                $wildcardPositions[] = $index;
            } else {
                $exactSegmentCount++;
            }
        }
        
        // Calculate base score
        $score = ($exactSegmentCount * 100) + ($segmentCount * 10) - ($wildcardCount * 50);
        
        // Bonus: prefer wildcards deeper in the path (not near root)
        // Add small bonus for each wildcard that's not in the first segment
        foreach ($wildcardPositions as $position) {
            if ($position > 0) {
                $score += 5; // Small bonus for wildcards not at root
            }
        }
        
        return $score;
    }
}
