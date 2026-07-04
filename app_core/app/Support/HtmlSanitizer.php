<?php

namespace App\Support;

class HtmlSanitizer
{
    private const DANGEROUS_TAGS = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'link', 'meta'];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div>'.$html.'</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        foreach (self::DANGEROUS_TAGS as $tag) {
            foreach (iterator_to_array($doc->getElementsByTagName($tag)) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        foreach (iterator_to_array($xpath->query('//*[@*]')) as $node) {
            /** @var \DOMElement $node */
            foreach (iterator_to_array($node->attributes) as $attr) {
                $name = strtolower($attr->nodeName);
                $value = trim($attr->nodeValue);

                if (str_starts_with($name, 'on')) {
                    $node->removeAttribute($attr->nodeName);
                    continue;
                }

                if (in_array($name, ['href', 'src'], true) && preg_match('/^\s*javascript:/i', $value)) {
                    $node->removeAttribute($attr->nodeName);
                }
            }
        }

        $wrapper = $doc->getElementsByTagName('div')->item(0);
        $inner = '';
        foreach (iterator_to_array($wrapper->childNodes) as $child) {
            $inner .= $doc->saveHTML($child);
        }

        return $inner;
    }
}
