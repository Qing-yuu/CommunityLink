<?php
require_once __DIR__ . '/../../../app/config/db.php';
global $pdo;

define('ORG_MAX', 100);
define('CONTACT_MAX', 100);
define('EMAIL_MAX', 191);

$errors = [];
$vals = [
    'org_name' => '',
    'contact_person_full_name' => '',
    'email' => '',
    'phone' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($vals as $k => $_) $vals[$k] = trim($_POST[$k] ?? '');

    if ($vals['org_name'] === '') $errors['org_name'] = 'Organisation name is required.';
    elseif (mb_strlen($vals['org_name']) > ORG_MAX) $errors['org_name'] = 'Max ' . ORG_MAX . ' characters.';

    if ($vals['contact_person_full_name'] === '') $errors['contact_person_full_name'] = 'Contact person is required.';
    elseif (mb_strlen($vals['contact_person_full_name']) > CONTACT_MAX) $errors['contact_person_full_name'] = 'Max ' . CONTACT_MAX . ' characters.';

    if ($vals['email'] === '' || !filter_var($vals['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Valid email is required.';
    elseif (strlen($vals['email']) > EMAIL_MAX)
        $errors['email'] = 'Email must be ≤ ' . EMAIL_MAX . ' characters.';

    if ($vals['phone'] === '') $errors['phone'] = 'Phone is required.';

    if (!$errors) {
        try {
            $sql = 'INSERT INTO organisations (org_name,contact_person_full_name,email,phone) VALUES (:org,:contact,:email,:phone)';
            $st = $pdo->prepare($sql);
            $st->execute([
                ':org' => $vals['org_name'],
                ':contact' => $vals['contact_person_full_name'],
                ':email' => $vals['email'],
                ':phone' => $vals['phone']
            ]);
            header('Location: /Lab04_Group03/public/assets/auth/success.php?msg=Organisation+registered+successfully&to=../../index.php');
            exit;
        } catch (PDOException $e) {
            $code = $e->getCode();
            $msg = $code === '23000' ? 'Record already exists.' : ($code === '22001' ? 'Field too long.' : $e->getMessage());
            $errors['_'] = 'Submit failed. ' . $msg;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Organisation register · CommunityLink</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
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
            margin-top: 12px
        }

        .form-label {
            font-weight: 600;
            color: #000
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
        <span class="flex-grow-1 text-center">Organization Registration Form</span>
        <span style="width:30px;"></span>
    </div>
    <div class="card-body">
        <p class="text-muted">Please provide your organisation’s contact details, and we will get in touch with you as
            soon as possible.</p>

        <?php if (!empty($errors['_'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['_']) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Organisation name*</label>
                <input name="org_name" maxlength="<?= ORG_MAX ?>"
                       class="form-control <?= isset($errors['org_name']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($vals['org_name']) ?>" required>
                <?php if (isset($errors['org_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['org_name']) ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact person name*</label>
                <input name="contact_person_full_name" maxlength="<?= CONTACT_MAX ?>"
                       class="form-control <?= isset($errors['contact_person_full_name']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($vals['contact_person_full_name']) ?>" required>
                <?php if (isset($errors['contact_person_full_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['contact_person_full_name']) ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Email*</label>
                <input type="email" name="email" maxlength="<?= EMAIL_MAX ?>"
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($vals['email']) ?>" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div><?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone*</label>
                <input name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($vals['phone']) ?>" required pattern="^\+?[0-9\s\-]{7,15}$"
                       title="Please enter a valid phone number">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div><?php endif; ?>
            </div>

            <div class="submit-wrap">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

