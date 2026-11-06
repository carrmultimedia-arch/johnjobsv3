<?php

/**
 * MODULE: Document Formatter
 * PURPOSE: Single shared converter from the plain/markdown-ish text stored
 * in resumes.content / cover_letters.content into styled HTML, used for
 * both the on-screen editable preview and the PDF export. Replaces the two
 * copy-pasted formatContentForPdf() methods that used to live separately
 * in ResumesController and CoverLettersController.
 *
 * Supported inline/line syntax coming out of the AI prompt:
 *   ## Heading            -> <h2>
 *   - text / * text       -> <li> (wrapped in <ul>)
 *   ---                   -> <hr>
 *   **bold**               -> <strong>
 *   *italic*                -> <em>
 *   blank line             -> paragraph break
 */
class DocumentFormatter {

    /**
     * Convert raw stored content into a styled inner-HTML string
     * (no <html>/<body> wrapper) suitable for a contenteditable preview.
     */
    public static function toHtml(string $content): string {
        $content = $content ?? '';
        $escaped = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        $lines = explode("\n", $escaped);
        $html = '';
        $inList = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                continue;
            }

            if (preg_match('/^##\s*(.+)/', $trimmed, $m)) {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                $html .= '<h2>' . self::inline($m[1]) . '</h2>';
            } elseif (preg_match('/^[-*]\s+(.+)/', $trimmed, $m)) {
                if (!$inList) { $html .= '<ul>'; $inList = true; }
                $html .= '<li>' . self::inline($m[1]) . '</li>';
            } elseif (preg_match('/^-{3,}$/', $trimmed)) {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                $html .= '<hr>';
            } else {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                $html .= '<p>' . self::inline($line) . '</p>';
            }
        }
        if ($inList) { $html .= '</ul>'; }

        return $html;
    }

    /** Handle **bold** and *italic* within an already-escaped line. */
    private static function inline(string $escapedLine): string {
        $line = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $escapedLine);
        $line = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $line);
        return $line;
    }

    /** Shared CSS for both the preview div and the PDF, so they match. */
    public static function css(): string {
        return '
            body, .doc-preview { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; line-height: 1.5; }
            .doc-preview h1, body h1 { font-size: 18px; margin-bottom: 4px; }
            .doc-preview h2, body h2 { font-size: 13px; margin-top: 16px; margin-bottom: 6px; border-bottom: 1px solid #999; padding-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
            .doc-preview p, body p { margin: 4px 0; }
            .doc-preview ul, body ul { margin: 4px 0 4px 18px; padding: 0; }
            .doc-preview li, body li { margin-bottom: 2px; }
            .doc-preview hr, body hr { border: none; border-top: 1px solid #ccc; margin: 10px 0; }
            .doc-preview strong, body strong { font-weight: 700; }
            .doc-preview em, body em { font-style: italic; }
        ';
    }

    /** Full standalone HTML document, for Dompdf. */
    public static function toPdfHtml(string $content, string $title, ?string $editedHtml = null): string {
        $titleEscaped = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $body = $editedHtml !== null && $editedHtml !== ''
            ? $editedHtml
            : self::toHtml($content);

        return '<html><head><meta charset="UTF-8"><style>' . self::css() . '</style></head><body>'
            . '<h1>' . $titleEscaped . '</h1>'
            . $body
            . '</body></html>';
    }
}