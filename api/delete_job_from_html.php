<?php
// This script is for demonstration purposes in a local, single-user environment.
// Directly modifying HTML files on the server via client-side requests is a significant security risk
// and is NOT recommended for production environments.
// For production, dynamic content should be managed via a database.

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $jobLinkToDelete = $input['jobLink'] ?? null;

    if ($jobLinkToDelete) {
        // Construct the absolute path to jobs_today.html
        // dirname(__DIR__) goes up one directory from 'api' to 'd:\johnjobsv2'
        $htmlFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'jobs_today.html';

        if (!file_exists($htmlFilePath)) {
            $response['message'] = 'HTML file not found: ' . $htmlFilePath;
            error_log($response['message']);
        } elseif (!is_writable($htmlFilePath)) {
            $response['message'] = 'HTML file is not writable. Check file permissions for ' . $htmlFilePath;
            error_log($response['message']);
        } else {
            $originalHtmlContent = file_get_contents($htmlFilePath);

            $dom = new DOMDocument();
            // Suppress warnings for malformed HTML (common with fragments or older HTML)
            libxml_use_internal_errors(true);
            // Load the entire HTML document
            $dom->loadHTML($originalHtmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            $jobFoundAndDeleted = false;

            // Find all job-item divs
            // The `normalize-space(@class)` is used to handle multiple classes and ensure exact match
            $jobItems = $xpath->query("//div[contains(concat(' ', normalize-space(@class), ' '), ' job-item ')]");

            foreach ($jobItems as $jobItem) {
                // Check if this job-item contains the apply link we want to delete
                // We look for an 'a' tag with the specific href within the 'job-secondary' div inside the current job-item
                $linkElement = $xpath->query(".//div[contains(concat(' ', normalize-space(@class), ' '), ' job-secondary ')]/a[@href='" . $jobLinkToDelete . "']", $jobItem);
                if ($linkElement->length > 0) {
                    // Found the job, remove its parent job-item div from the DOM
                    $jobItem->parentNode->removeChild($jobItem);
                    $jobFoundAndDeleted = true;
                    break; // Assuming job links are unique, so we can stop after finding one
                }
            }

            if ($jobFoundAndDeleted) {
                // Save the modified DOM back to the file.
                // saveHTML() on the document object will output the full HTML, including doctype, html, body,
                // which is appropriate since the original file is a complete HTML document.
                $modifiedHtmlContent = $dom->saveHTML();
                file_put_contents($htmlFilePath, $modifiedHtmlContent);
                $response['success'] = true;
                $response['message'] = 'Job successfully deleted.';
            } else {
                $response['message'] = 'Job with the specified link not found in HTML.';
                error_log($response['message']);
            }
        }
    } else {
        $response['message'] = 'No job link provided for deletion.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>