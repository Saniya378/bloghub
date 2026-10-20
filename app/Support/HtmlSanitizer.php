<?php

namespace App\Support;

/**
 * HTML Sanitizer for article content.
 * Uses an allowlist approach to prevent XSS attacks.
 */
class HtmlSanitizer
{
    /**
     * Allowed HTML tags.
     */
    private const ALLOWED_TAGS = [
        'p', 'br', 'hr',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del', 'ins',
        'a', 'img',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'div', 'span',
        'sup', 'sub',
    ];

    /**
     * Allowed attributes per tag.
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'code' => ['class'],
        'pre' => ['class'],
        'div' => ['class', 'id'],
        'span' => ['class', 'id'],
        'p' => ['class', 'id'],
        'h1' => ['id'], 'h2' => ['id'], 'h3' => ['id'],
        'h4' => ['id'], 'h5' => ['id'], 'h6' => ['id'],
        'table' => ['class'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];

    /**
     * Allowed URL schemes.
     */
    private const ALLOWED_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    /**
     * Sanitize HTML content.
     */
    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // Use DOMDocument for robust parsing
        $dom = new \DOMDocument();
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        
        // Load HTML wrapped in body with UTF-8 encoding
        $dom->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        // Recursively clean child nodes of body
        $children = [];
        foreach ($body->childNodes as $child) {
            $children[] = $child;
        }
        foreach ($children as $child) {
            self::cleanNode($child);
        }

        // Extract sanitized HTML
        $result = '';
        foreach ($body->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return trim($result);
    }

    /**
     * Recursively clean a DOM node and its children.
     */
    private static function cleanNode(\DOMNode $node): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            return;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            $node->parentNode?->removeChild($node);
            return;
        }

        $tagName = strtolower($node->nodeName);

        // Disallowed executable tags: remove completely along with content
        if (in_array($tagName, ['script', 'style', 'iframe', 'embed', 'object', 'applet'], true)) {
            $node->parentNode?->removeChild($node);
            return;
        }

        // Remove disallowed tags but keep their inner text/children
        if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
            $fragment = $node->ownerDocument->createDocumentFragment();
            while ($node->firstChild) {
                $fragment->appendChild($node->firstChild);
            }
            $node->parentNode?->replaceChild($fragment, $node);
            return;
        }

        // Clean attributes
        $allowedAttrs = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];
        $attrsToRemove = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->name);
                
                if (!in_array($attrName, $allowedAttrs, true)) {
                    $attrsToRemove[] = $attr->name;
                    continue;
                }

                // Special handling for href and src - validate URL scheme
                if (in_array($attrName, ['href', 'src'], true)) {
                    $value = $attr->value;
                    $scheme = parse_url($value, PHP_URL_SCHEME);
                    
                    // Allow relative URLs (no scheme)
                    if ($scheme !== null && !in_array(strtolower($scheme), self::ALLOWED_SCHEMES, true)) {
                        $attrsToRemove[] = $attr->name;
                        continue;
                    }

                    // Block javascript: and data: URLs
                    if ($scheme !== null && in_array(strtolower($scheme), ['javascript', 'data', 'vbscript'], true)) {
                        $attrsToRemove[] = $attr->name;
                    }
                }
            }

            foreach ($attrsToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }

        // Add rel="noopener noreferrer" to external links
        if ($tagName === 'a' && $node->hasAttribute('href')) {
            $href = $node->getAttribute('href');
            $host = request() ? request()->getHost() : '';
            if (str_starts_with($href, 'http') && ($host === '' || !str_contains($href, $host))) {
                $node->setAttribute('rel', 'noopener noreferrer');
                $node->setAttribute('target', '_blank');
            }
        }

        // Recursively clean children
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }
        foreach ($children as $child) {
            self::cleanNode($child);
        }
    }
}
