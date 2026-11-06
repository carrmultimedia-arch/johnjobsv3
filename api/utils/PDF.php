<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    /**
     * Generates a PDF from HTML content and streams it to the browser.
     *
     * @param string $html The HTML content to render.
     * @param string $filename The name of the file for the download.
     * @param string $dompdfPath The path to the dompdf autoloader.
     */
    public static function stream(string $html, string $filename, string $dompdfPath)
    {
        // Check if the dompdf autoloader exists.
        if (!file_exists($dompdfPath)) {
            http_response_code(500);
            echo "PDF generation library not found.";
            error_log("Dompdf autoloader not found at: " . $dompdfPath);
            exit;
        }
        require_once $dompdfPath;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream the file to the browser for download.
        $dompdf->stream($filename, ["Attachment" => 1]);
    }

    /**
     * Formats plain text content with basic Markdown-like features for PDF rendering.
     * Supports headings (##), unordered lists (* or -), and paragraphs.
     *
     * @param string $plainText The plain text content.
     * @return string The HTML formatted content.
     */
    public static function formatContentForPdf(string $plainText): string
    {
        $html = '';
        $lines = explode("\n", $plainText);
        $inList = false;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            if (str_starts_with($trimmedLine, '## ')) {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                $html .= '<h2>' . htmlspecialchars(substr($trimmedLine, 3)) . '</h2>';
            } elseif (str_starts_with($trimmedLine, '* ') || str_starts_with($trimmedLine, '- ')) {
                if (!$inList) { $html .= '<ul>'; $inList = true; }
                $html .= '<li>' . htmlspecialchars(substr($trimmedLine, 2)) . '</li>';
            } elseif ($trimmedLine === '---') {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                $html .= '<hr>';
            } elseif (!empty($trimmedLine)) {
                if ($inList) { $html .= '</ul>'; $inList = false; }
                // Replace newlines within a "paragraph" with <br> tags.
                // Each non-heading/non-list line is treated as a potential paragraph.
                $html .= '<p>' . nl2br(htmlspecialchars($trimmedLine)) . '</p>';
            } else {
                // Empty line, if not in a list, can act as a paragraph separator.
                // If in a list, an empty line breaks the list.
                if ($inList) { $html .= '</ul>'; $inList = false; }
            }
        }

        if ($inList) { $html .= '</ul>'; } // Close any open list at the end

        return $html;
    }
}