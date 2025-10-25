<?php

use classes\ContactDAO;

require __DIR__ . '/../../../app/config/db.php';
require __DIR__ . '/../../../app/classes/ContactDAO.php';

global $pdo;
$dao = new ContactDAO($pdo);

define('NAME_MAX', 100);
define('EMAIL_MAX', 191);
define('SUBJECT_MAX', 150);
define('MESSAGE_MAX', 2000);

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) $old[$k] = trim($_POST[$k] ?? '');

    if ($old['name'] === '') $errors['name'] = 'Name is required.';
    elseif (mb_strlen($old['name']) > NAME_MAX) $errors['name'] = 'Max ' . NAME_MAX . ' characters.';

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
    elseif (strlen($old['email']) > EMAIL_MAX) $errors['email'] = 'Email must be ≤ ' . EMAIL_MAX . ' characters.';

    if ($old['subject'] === '') $errors['subject'] = 'Subject is required.';
    elseif (mb_strlen($old['subject']) > SUBJECT_MAX) $errors['subject'] = 'Max ' . SUBJECT_MAX . ' characters.';

    if ($old['message'] === '') $errors['message'] = 'Message is required.';
    elseif (mb_strlen($old['message']) > MESSAGE_MAX) $errors['message'] = 'Max ' . MESSAGE_MAX . ' characters.';

    if (!$errors) {
        try {
            $id = $dao->create($old);
            $to = 'amy.tan@communitylink.com';
            $mailSubject = '[Contact] ' . $old['subject'];
            $headers = "From: {$old['name']} <{$old['email']}>\r\nReply-To: {$old['email']}\r\nContent-Type: text/plain; charset=UTF-8\r\n";
            $body = "New contact form submission (#{$id})\nName: {$old['name']}\nEmail: {$old['email']}\nSubject: {$old['subject']}\n\n{$old['message']}\n";
            @mail($to, $mailSubject, $body, $headers);
            header('Location: /Lab04_Group03/public/assets/auth/success.php?msg=Message+sent+successfully&to=../../index.php');
            exit;
        } catch (Throwable $e) {
            $msg = $e instanceof PDOException && $e->getCode() === '22001' ? 'Field too long.' : $e->getMessage();
            if ($e instanceof PDOException && $e->getCode() === '23000') $msg = 'Record already exists.';
            $errors['_'] = 'Submit failed. ' . $msg;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us · CommunityLink</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #fff;
            color: #000
        }

        .card {
            max-width: 600px;
            margin: 40px auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            border: 1px solid #000
        }

        .card-header {
            background: #000;
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1.25rem
        }

        .back-btn {
            background: transparent;
            color: #fff;
            border: none;
            font-size: 1.25rem;
            text-decoration: none
        }

        .back-btn:hover {
            color: #ccc;
            text-decoration: none
        }

        .submit-wrap {
            text-align: center;
            margin-top: 16px
        }

        .btn-primary {
            background: #000;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: .65rem 1.8rem;
            font-weight: 700;
            transition: .2s
        }

        .btn-primary:hover {
            background: #333
        }

        .form-label {
            font-weight: 600;
            color: #000
        }

        .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 .2rem rgba(0, 0, 0, .25)
        }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <a href="../../../index.php" class="back-btn">&larr;</a>
        <span class="flex-grow-1 text-center">Contact Us</span>
        <span style="width:30px;"></span>
    </div>
    <div class="card-body">
        <p class="text-muted">Please provide your message and we will get back to you as soon as possible.</p>

        <?php if (!empty($errors['_'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['_']) ?></div>
        <?php endif; ?>

        <form id="contactForm" class="needs-validation" method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Full name*</label>
                <input name="name" maxlength="<?= NAME_MAX ?>"
                       class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($old['name']) ?>" required>
                <div class="invalid-feedback"><?= isset($errors['name']) ? htmlspecialchars($errors['name']) : 'Name is required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email*</label>
                <input type="email" name="email" maxlength="<?= EMAIL_MAX ?>"
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($old['email']) ?>" required>
                <div class="invalid-feedback"><?= isset($errors['email']) ? htmlspecialchars($errors['email']) : 'Valid email is required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Subject*</label>
                <input name="subject" maxlength="<?= SUBJECT_MAX ?>"
                       class="form-control <?= isset($errors['subject']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($old['subject']) ?>" required>
                <div class="invalid-feedback"><?= isset($errors['subject']) ? htmlspecialchars($errors['subject']) : 'Subject is required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Message*</label>
                <textarea name="message" maxlength="<?= MESSAGE_MAX ?>"
                          class="form-control <?= isset($errors['message']) ? 'is-invalid' : ''; ?>"
                          required><?= htmlspecialchars($old['message']) ?></textarea>
                <div class="invalid-feedback"><?= isset($errors['message']) ? htmlspecialchars($errors['message']) : 'Message is required.' ?></div>
            </div>

            <div class="submit-wrap">
                <button class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>
</div>
<script>
    const form = document.getElementById('contactForm');
    form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    form.querySelectorAll('input,textarea').forEach(el => {
        el.addEventListener('input', () => {
            if (form.classList.contains('was-validated')) el.reportValidity();
        });
        el.addEventListener('blur', () => {
            form.classList.add('was-validated');
        });
    });
</script>
</body>
</html>

