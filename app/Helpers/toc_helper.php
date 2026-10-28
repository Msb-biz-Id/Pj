<?php

if (!function_exists('parse_auto_toc')) {
    /**
     * Parse HTML content to extract h2 and h3 headings for Auto Table of Contents
     * and inject anchor IDs into HTML content.
     */
    function parse_auto_toc(string $html): array
    {
        if (empty(trim($html))) {
            return ['toc' => [], 'html' => $html];
        }

        $toc = [];
        // Regex match h2 and h3 tags
        $pattern = '/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/i';

        $parsedHtml = preg_replace_callback($pattern, function ($matches) use (&$toc) {
            $level = (int)$matches[1];
            $attrs = $matches[2];
            $titleText = strip_tags($matches[3]);
            $slug = url_title($titleText, '-', true);

            $toc[] = [
                'level' => $level,
                'title' => $titleText,
                'slug' => $slug
            ];

            return sprintf('<h%d id="%s"%s>%s</h%d>', $level, $slug, $attrs, $matches[3], $level);
        }, $html);

        return [
            'toc' => $toc,
            'html' => $parsedHtml
        ];
    }
}

if (!function_exists('generate_auto_toc')) {
    /**
     * Alias for parse_auto_toc called by blog/detail and auto_toc partials.
     */
    function generate_auto_toc(string $html): array
    {
        return parse_auto_toc($html);
    }
}
