<?php
require_once 'model/connect.php';

if (isset($_POST['sendcontact'])) {
    $namect = trim($_POST['contact-name'] ?? '');
    $emailct = trim($_POST['contact-email'] ?? '');
    $subject = trim($_POST['contact-subject'] ?? '');
    $contentct = trim($_POST['contact-content'] ?? '');

    // Basic validation
    if ($namect === '' || $emailct === '' || $subject === '' || $contentct === '' || !filter_var($emailct, FILTER_VALIDATE_EMAIL)) {
        header('Location: lienhe.php?cf=failed');
        exit;
    }

    try {
        $stmt = $conn->prepare('INSERT INTO contacts (name, email, title, contents, created) VALUES (:name, :email, :title, :contents, NOW())');
        $success = $stmt->execute([
            ':name' => $namect,
            ':email' => $emailct,
            ':title' => $subject,
            ':contents' => $contentct,
        ]);

        if ($success) {
            header('Location: lienhe.php?cs=success');
            exit;
        } else {
            error_log('Contact insert failed: ' . implode(' | ', $stmt->errorInfo()));
            header('Location: lienhe.php?cf=failed');
            exit;
        }
    } catch (PDOException $e) {
        error_log('Contact insert error: ' . $e->getMessage());
        header('Location: lienhe.php?cf=failed');
        exit;
    }
}
?>