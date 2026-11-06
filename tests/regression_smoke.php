<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pdo = DB::getInstance()->getConnection();

runTest('database connection', function () use ($pdo) {
    expectTrue($pdo instanceof PDO, 'Database connection should be an instance of PDO');
});

runTest('settings service create and update', function () use ($pdo) {
    $pdo->beginTransaction();
    try {
        $service = new SettingsService();
        $created = $service->create([
            'key' => 'regression_test_key',
            'value' => 'alpha',
            'category' => 'test'
        ]);
        expectNotEmpty($created['id'], 'Settings create should return an inserted row with an id');
        expectSame('alpha', $created['value'], 'Settings create should persist the value');

        $updated = $service->update($created['id'], [
            'value' => 'beta'
        ]);
        expectSame('beta', $updated['value'], 'Settings update should persist the new value');
    } finally {
        $pdo->rollBack();
    }
});

runTest('application service create and status update', function () use ($pdo) {
    $pdo->beginTransaction();
    try {
        $service = new ApplicationService();
        $created = $service->create([
            'title' => 'Regression Application',
            'status' => 'draft',
            'notes' => 'created by smoke test'
        ]);
        expectNotEmpty($created['id'], 'Application create should return an inserted row with an id');

        $updated = $service->setStatus($created['id'], 'applied');
        expectTrue($updated, 'Application status update should return true');
        $refetched = $service->getById($created['id']);
        expectSame('applied', $refetched['status'], 'Application status should be updated in storage');
    } finally {
        $pdo->rollBack();
    }
});

runTest('resume and cover letter preserve metadata and timestamps', function () use ($pdo) {
    $pdo->beginTransaction();
    try {
        $resume = (new ResumeService())->create([
            'title' => 'Regression Resume',
            'content' => 'Resume content',
            'target_role' => 'Product Manager',
            'company' => 'Example Co.'
        ]);
        $resumeMetadata = json_decode($resume['metadata'], true);
        expectSame('Product Manager', $resumeMetadata['target_role'], 'Resume target role should be stored in metadata');
        expectSame('Example Co.', $resumeMetadata['company'], 'Resume company should be stored in metadata');
        expectTrue($resume['created_at'] !== '0000-00-00 00:00:00', 'Resume should receive a real creation timestamp');

        $letter = (new CoverLetterService())->create([
            'company' => 'Example Co.',
            'title' => 'Product Manager',
            'generated_text' => 'Cover letter content',
            'tone' => 'warm'
        ]);
        $letterMetadata = json_decode($letter['metadata'], true);
        expectSame('Example Co.', $letterMetadata['company'], 'Cover-letter company should be stored in metadata');
        expectSame('warm', $letterMetadata['tone'], 'Cover-letter tone should be stored in metadata');
        expectSame('Cover letter content', $letter['content'], 'Generated cover-letter text should be stored in content');
        expectTrue($letter['created_at'] !== '0000-00-00 00:00:00', 'Cover letter should receive a real creation timestamp');
    } finally {
        $pdo->rollBack();
    }
});

runTest('conversation thread lookup uses metadata', function () use ($pdo) {
    $pdo->beginTransaction();
    try {
        $service = new ConversationService();
        $created = $service->create([
            'channel' => 'email',
            'subject' => 'Regression Thread',
            'content' => 'Regression content',
            'metadata' => ['thread_id' => 'thread-123']
        ]);
        expectNotEmpty($created['id'], 'Conversation create should return an inserted row with an id');

        $thread = $service->getThread('thread-123');
        expectTrue(count($thread) >= 1, 'Conversation thread lookup should return at least one row');
        expectSame('Regression Thread', $thread[0]['subject'], 'Conversation thread lookup should return the created row');
    } finally {
        $pdo->rollBack();
    }
});

runTest('export service delete removes the export row', function () use ($pdo) {
    $pdo->beginTransaction();
    try {
        $service = new ExportService();
        $stmt = $pdo->prepare('INSERT INTO exports (type, format, status) VALUES (?, ?, ?)');
        $stmt->execute(['smoke', 'json', 'pending']);
        $id = (int)$pdo->lastInsertId();

        $deleted = $service->delete($id);
        expectTrue($deleted, 'Export delete should return true');

        $stmt = $pdo->prepare('SELECT id FROM exports WHERE id = ?');
        $stmt->execute([$id]);
        expectTrue($stmt->fetch() === false, 'Export delete should remove the row from storage');
    } finally {
        $pdo->rollBack();
    }
});

echo "Regression smoke tests completed successfully.\n";
