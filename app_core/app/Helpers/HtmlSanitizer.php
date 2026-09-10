<?php

namespace App\Helpers;

class HtmlSanitizer
{
    /**
     * Return a safe http/https URL or empty string if the scheme is dangerous.
     */
    public static function safeUrl(?string $url): string
    {
        if ($url === null || trim($url) === '') return '';
        $scheme = strtolower(parse_url(trim($url), PHP_URL_SCHEME) ?? '');
        if (!in_array($scheme, ['http', 'https'], true)) return '';
        return $url;
    }

    /**
     * Return a safe Google Maps embed URL or empty string.
     * Only allows https://www.google.com/maps/embed* URLs.
     */
    public static function safeMapUrl(?string $url): string
    {
        if ($url === null || trim($url) === '') return '';
        $u = trim($url);
        if (!str_starts_with($u, 'https://www.google.com/maps/embed')) return '';
        return $u;
    }


    private const ALLOWED_TAGS = ['p','br','b','strong','em','i','u','ul','ol','li',
        'h1','h2','h3','h4','h5','h6','blockquote','pre','code','hr','figure','figcaption',
        'a','img','span','div','table','thead','tbody','tr','td','th'];

    private const SAFE_ATTRS = ['href','src','alt','title','width','height','class','id',
        'colspan','rowspan','target','rel','loading'];

    public static function clean(string $html): string
    {
        if (trim($html) === '') return '';

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        self::sanitizeNode($doc->documentElement ?? $doc->firstChild);

        // Extract content of the wrapper div
        $body = $doc->getElementsByTagName('div')->item(0);
        if (!$body) return e($html);

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return $out;
    }

    private static function sanitizeNode(\DOMNode $node): void
    {
        $toRemove = [];
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                if (!in_array($tag, self::ALLOWED_TAGS)) {
                    $toRemove[] = $child;
                    continue;
                }
                // Strip unsafe attributes
                $badAttrs = [];
                foreach ($child->attributes as $attr) {
                    $name = strtolower($attr->name);
                    $value = $attr->value;
                    if (!in_array($name, self::SAFE_ATTRS)) {
                        $badAttrs[] = $name;
                        continue;
                    }
                    // Block javascript: in href/src
                    if (in_array($name, ['href','src']) && preg_match('/^\s*javascript:/i', $value)) {
                        $badAttrs[] = $name;
                    }
                    // Force rel="noopener noreferrer" on external links
                    if ($name === 'href' && str_starts_with($value, 'http')) {
                        $child->setAttribute('rel', 'noopener noreferrer');
                        $child->setAttribute('target', '_blank');
                    }
                }
                foreach ($badAttrs as $a) $child->removeAttribute($a);
                self::sanitizeNode($child);
            }
        }
        foreach ($toRemove as $el) {
            // Replace with text content so content isn't lost
            $text = $node->ownerDocument->createTextNode($el->textContent);
            $node->replaceChild($text, $el);
        }
    }
}
