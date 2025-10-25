<?php

use classes\VolunteerDAO;
use classes\UserDAO;

require __DIR__ . '/../../../app/config/db.php';
require __DIR__ . '/../../../app/classes/VolunteerDAO.php';
require __DIR__ . '/../../../app/classes/UserDAO.php';


const EMAIL_MAX = 191;
const MAX_IMG_SIZE = 2 * 1024 * 1024;
const ALLOWED_IMG_EXT = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
const NAME_MAX = 100;
const SKILLS_MAX = 500;
const PASSWORD_MAX = 64;


$errors = [];
$old = [
    'full_name' => '',
    'email' => '',
    'phone' => '',
    'skills' => '',
    'password' => '',
    'confirm_password' => ''
];


function validPassword(string $pw): bool
{
    $c = 0;
    $c += (int)preg_match('/[A-Z]/', $pw);
    $c += (int)preg_match('/[a-z]/', $pw);
    $c += (int)preg_match('/\d/', $pw);
    $c += (int)preg_match('/[\W_]/', $pw);
    return strlen($pw) >= 8 && $c >= 3 && strlen($pw) <= PASSWORD_MAX;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($old as $k => $v) {
        $old[$k] = trim($_POST[$k] ?? '');
    }

    if ($old['full_name'] === '')
        $errors['full_name'] = 'Full name is required.';
    elseif (mb_strlen($old['full_name']) > NAME_MAX)
        $errors['full_name'] = "Max " . NAME_MAX . " chars.";

    if ($old['email'] === '' ||
        !filter_var($old['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Valid email required.';
    elseif (strlen($old['email']) > EMAIL_MAX)
        $errors['email'] = 'Email must be ≤ ' . EMAIL_MAX . ' characters.';

    if ($old['skills'] === '')
        $errors['skills'] = 'Skills are required.';
    elseif (mb_strlen($old['skills']) > SKILLS_MAX)
        $errors['skills'] = "Max " . SKILLS_MAX . " chars.";


    if ($old['password'] === '')
        $errors['password'] = 'Password is required.';
    elseif (!validPassword($old['password']))
        $errors['password'] =
            '8-64 chars & any 3 of: upper, lower, number, symbol.';

    if ($old['confirm_password'] === '')
        $errors['confirm_password'] = 'Please confirm password.';
    elseif ($old['password'] !== $old['confirm_password'])
        $errors['confirm_password'] = 'Passwords do not match.';

    if ($old['phone'] === '')
        $errors['phone'] = 'Phone is required.';

    $profilePicture = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $f = $_FILES['profile_picture'];
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ALLOWED_IMG_EXT, true))
            $errors['profile_picture'] = 'Only jpeg/png/gif/webp.';
        elseif ($f['size'] > MAX_IMG_SIZE)
            $errors['profile_picture'] = 'Image ≤ 2 MB.';
        elseif ($f['error'] !== UPLOAD_ERR_OK)
            $errors['profile_picture'] = 'Upload error ' . $f['error'] . '.';
    } else {
        $errors['profile_picture'] = 'Profile picture required.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $userDao = new UserDAO($pdo);
            if ($userDao->emailExists($old['email']))
                throw new RuntimeException('Email already exists.');

            $userId = $userDao->create([
                'email' => $old['email'],
                'identity' => 'volunteer',
                'is_active' => 0,
                'password' => $old['password'],
            ]);

            $root = dirname(__DIR__, 3);
            $dir = $root . '/volunteer/volunteer_profiles/';
            if (!is_dir($dir) && !mkdir($dir, 0777, true))
                throw new RuntimeException('Cannot create upload dir');

            $safe = preg_replace('/[^a-zA-Z0-9._-]/', '_',
                pathinfo($f['name'], PATHINFO_FILENAME));
            $fname = time() . '_' . $safe . '.' . $ext;
            $target = $dir . $fname;
            if (!move_uploaded_file($f['tmp_name'], $target))
                throw new RuntimeException('Move upload failed.');

            $volDao = new VolunteerDAO($pdo);
            $volDao->create([
                'user_id' => $userId,
                'full_name' => $old['full_name'],
                'phone' => $old['phone'],
                'skills' => $old['skills'],
                'status' => 'unhired',
                'profile_picture' => 'volunteer_profiles/' . $fname,
            ]);

            $pdo->commit();
            header('Location: success.php');
            exit;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors['_'] = 'Submit failed: ' . $e->getMessage();
        }
    }
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Signup - CommunityLink</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #fff;
            color: #000;
        }

        .card {
            max-width: 600px;
            margin: 40px auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
            border: 1px solid #000;
        }

        .card-header {
            background: #000;
            color: #fff;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1.25rem;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }


        .back-btn {
            background: transparent;
            color: #fff;
            border: none;
            font-size: 1.25rem;
            line-height: 1;
            text-decoration: none;
        }

        .back-btn:hover {
            color: #aaa;
            text-decoration: none;
        }

        .preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-top: 10px;
        }

        .submit-wrap {
            text-align: center;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #000;
            border: none;
            border-radius: 999px;
            padding: .65rem 1.8rem;
            font-weight: 700;
            transition: all .2s;
        }

        .btn-primary:hover {
            background-color: #333;
        }

        .form-control:focus {
            border-color: #000;
            box-shadow: 0 0 0 .2rem rgba(0, 0, 0, .25);
        }

        .progress-bar {
            transition: width .3s ease;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <a href="../../../index.php" class="back-btn">&larr;</a>
        <span class="flex-grow-1 text-center">Become a Volunteer</span>
        <span style="width:30px;"></span>
    </div>
    <div class="card-body">
        <p class="text-muted">Fill in the form below to sign up as a volunteer. All fields are required.</p>


        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <strong>There were some problems with your submission:</strong>
                <ul class="mb-0">
                    <?php foreach ($errors as $msg): ?>
                        <li><?= htmlspecialchars($msg) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>



        <?php if (!empty($errors['_'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['_']) ?></div>
        <?php endif; ?>

        <form id="signup" class="needs-validation" method="post" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label class="form-label">Full name*</label>
                <input name="full_name"
                       class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($old['full_name']) ?>"
                       required minlength="2" maxlength="100"
                       pattern="^[A-Za-z\s.'-]+$"
                       title="2-100 letters / spaces / . ' - allowed">
                <div class="invalid-feedback"><?= isset($errors['full_name']) ? htmlspecialchars($errors['full_name']) : 'Full name is required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email*</label>
                <input type="email"
                       name="email"
                       maxlength="191"
                       required
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       value="<?= htmlspecialchars($old['email']) ?>">
                <div class="invalid-feedback">
                    <?= isset($errors['email']) ? htmlspecialchars($errors['email']) : 'Valid email is required.' ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password*</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>"
                       required minlength="8" maxlength="64"
                       pattern="^(?=.{8,64}$).*$"
                       title="8-64 characters">
                <div class="invalid-feedback">
                    <?= isset($errors['password']) ? htmlspecialchars($errors['password']) : 'Password requirements not met.' ?>
                </div>

                <div class="progress mt-2" style="height:8px;">
                    <div id="strength-bar" class="progress-bar bg-danger" role="progressbar" style="width:0%"></div>
                </div>
                <small id="strength-text" class="form-text text-muted"></small>
            </div>


            <div class="mb-3">
                <label class="form-label">Confirm Password*</label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                class="form-control <?= isset($errors['confirm_password'])?'is-invalid':'';?>"
                required minlength="8" maxlength="64">
                <div class="invalid-feedback">
                    <?= isset($errors['confirm_password']) ? htmlspecialchars($errors['confirm_password']) : 'Passwords must match.' ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone*</label>
                <input class="form-control <?= isset($errors['phone']) ? 'is-invalid' : ''; ?>" name="phone"
                       value="<?= htmlspecialchars($old['phone']) ?>" required pattern="^\+?[0-9\s\-]{7,15}$"
                       title="Please enter a valid phone number">
                <div class="invalid-feedback"><?= isset($errors['phone']) ? htmlspecialchars($errors['phone']) : 'Valid phone is required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Skills / Interests*</label>
                <textarea name="skills"
                          class="form-control <?= isset($errors['skills']) ? 'is-invalid' : ''; ?>"
                          required maxlength="500"><?= htmlspecialchars($old['skills']) ?></textarea>
                <div class="invalid-feedback"><?= isset($errors['skills']) ? htmlspecialchars($errors['skills']) : 'Skills/Interests are required.' ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture*</label>
                <input type="file"
                       name="profile_picture"
                       accept=".jpg,.jpeg,.png,.gif,.webp"
                       class="form-control <?= isset($errors['profile_picture']) ? 'is-invalid' : ''; ?>"
                       onchange="validateImage(this);previewImage(event)"
                       required>
                <div class="invalid-feedback"><?= isset($errors['profile_picture']) ? htmlspecialchars($errors['profile_picture']) : 'Profile picture is required.' ?></div>
                <img id="preview" class="preview d-none" alt="Preview">
            </div>

            <div class="submit-wrap">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            preview.src = URL.createObjectURL(input.files[0]);
            preview.classList.remove('d-none');
        }
    }

    const form = document.getElementById('signup');
    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    form.querySelectorAll('input, textarea').forEach(el => {
        el.addEventListener('input', () => {
            if (form.classList.contains('was-validated')) {
                el.reportValidity();
            }
        });
        el.addEventListener('blur', () => {
            form.classList.add('was-validated');
        });
    });

    const pw = document.getElementById('password');
    const cpw = document.getElementById('confirm_password');

    function syncMatch() {
        if (cpw.value && pw.value !== cpw.value) {
            cpw.setCustomValidity('Passwords do not match.');
        } else {
            cpw.setCustomValidity('');
        }
    }

    pw.addEventListener('input', syncMatch);
    cpw.addEventListener('input', syncMatch);

    const strengthBar = document.getElementById('strength-bar');
    const strengthText = document.getElementById('strength-text');
    pw.addEventListener('input', function () {
        const val = this.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[a-z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[\W_]/.test(val)) score++;
        const percent = (score / 5) * 100;
        let color = 'bg-danger', text = 'Weak';
        if (score >= 4 && val.length >= 8) {
            color = 'bg-success';
            text = 'Strong';
        } else if (score >= 3) {
            color = 'bg-warning';
            text = 'Medium';
        }
        strengthBar.className = 'progress-bar ' + color;
        strengthBar.style.width = percent + '%';
        strengthText.textContent = text;
    });
</script>

<script>
    function validateImage(input) {
        const f = input.files[0];
        if (!f) return;
        if (f.size > 2 * 1024 * 1024) {
            alert("Image must be 2 MB or smaller.");
            input.value = '';
        }
    }
</script>

</body>
</html>

