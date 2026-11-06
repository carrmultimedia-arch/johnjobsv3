<?php
// Prevent any stray HTML (warnings/notices) from corrupting the JSON response
ini_set('display_errors', '0');
error_reporting(E_ALL);
ob_start();

header('Content-Type: application/json');

$file = '/home/carrmulti/www/johnjobsv2/jobs_today.html';

if (!file_exists($file)) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'File not found at: ' . $file]);
    exit;
}

if (!is_writable($file)) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'File is not writable: ' . $file]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$jobId = trim($data['job_id'] ?? '');

if ($jobId === '') {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing job_id']);
    exit;
}

$content = file_get_contents($file);
if ($content === false) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not read file']);
    exit;
}

// Find the exact opening tag for this job-id, e.g.:
// <div class="col-lg-4 job-item" data-job-id="F7kP3mQa">
$needle = '<div class="col-lg-4 job-item" data-job-id="' . $jobId . '">';
$startPos = strpos($content, $needle);

if ($startPos === false) {
    ob_end_clean();
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Job block not found for job_id="' . $jobId . '"']);
    exit;
}

// Walk forward from the opening tag, balancing <div ...> against </div>,
// to find exactly where this card's wrapping div closes.
$len = strlen($content);
$cursor = $startPos;
$depth = 0;
$endPos = false;

while ($cursor < $len) {
    $nextOpen = stripos($content, '<div', $cursor);
    $nextClose = stripos($content, '</div>', $cursor);

    if ($nextClose === false) {
        break; // malformed HTML, bail out below
    }

    if ($nextOpen !== false && $nextOpen < $nextClose) {
        $depth++;
        $cursor = $nextOpen + 4;
    } else {
        $depth--;
        $cursor = $nextClose + 6;
        if ($depth === 0) {
            $endPos = $cursor;
            break;
        }
    }
}

if ($endPos === false) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not find matching closing tag for job_id="' . $jobId . '"']);
    exit;
}

$newContent = substr($content, 0, $startPos) . substr($content, $endPos);

if (file_put_contents($file, $newContent) === false) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not write file']);
    exit;
}

// Discard any stray warning output that made it into the buffer, then send clean JSON
ob_end_clean();
echo json_encode(['success' => true]);