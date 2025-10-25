<?php
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/classes/VolunteerDAO.php';
require_once __DIR__ . '/../app/config/auth.php';
require_login();

//check volunteer
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']) || (($_SESSION['user']['identity'] ?? '') !== 'volunteer')) {
    header('Location: /assets/auth/login.php?error=' . urlencode('Please sign in.'));
    exit;
}

$userId = (int)($_SESSION['user']['user_id'] ?? 0);
$email = $_SESSION['user']['email'] ?? '';

$st = $pdo->prepare("SELECT v.* FROM volunteers v WHERE v.user_id=:uid LIMIT 1");
$st->execute([':uid' => $userId]);
$vol = $st->fetch(PDO::FETCH_ASSOC);

if (!$vol) {
    $ins = $pdo->prepare("INSERT INTO volunteers (user_id, full_name, phone, skills, status, profile_picture) VALUES (:uid,'','','','inactive',NULL)");
    $ins->execute([':uid' => $userId]);
    $st->execute([':uid' => $userId]);
    $vol = $st->fetch(PDO::FETCH_ASSOC);
}

function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}


//Verify input and update database
$ok = false;
$err = '';
$isEditing = isset($_GET['edit']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $skills = trim($_POST['skills'] ?? '');

    if ($full_name === '' || $phone === '' || $skills === '') {
        $err = 'All fields are required.';
        $isEditing = true;
    } else {
        $uploadRel = $vol['profile_picture'] ?: null;
        $hasFile = isset($_FILES['profile_picture']) && is_uploaded_file($_FILES['profile_picture']['tmp_name']);

        if ($hasFile) {
            $dir = __DIR__ . '/volunteer_profiles';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['profile_picture']['tmp_name']);
            finfo_close($finfo);

            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            if (!isset($allowed[$mime])) {
                $err = 'Profile picture must be JPG, PNG, GIF, or WEBP.';
                $isEditing = true;
            } else {
                $ext = $allowed[$mime];
                $name = 'vol_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $targetAbs = $dir . '/' . $name;

                if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetAbs)) {
                    $err = 'Failed to upload profile picture.';
                    $isEditing = true;
                } else {
                    $newRel = 'volunteer_profiles/' . $name;

                    if (!empty($vol['profile_picture']) && str_starts_with($vol['profile_picture'], 'volunteer_profiles/')) {
                        $oldAbs = __DIR__ . '/' . $vol['profile_picture'];
                        if (is_file($oldAbs)) @unlink($oldAbs);
                    }
                    $uploadRel = $newRel;
                }
            }
        }

        if ($err === '') {
            $upd = $pdo->prepare("UPDATE volunteers SET full_name=:n, phone=:p, skills=:s, profile_picture=:pic WHERE volunteer_id=:id");
            $upd->execute([
                ':n' => $full_name,
                ':p' => $phone,
                ':s' => $skills,
                ':pic' => $uploadRel,
                ':id' => $vol['volunteer_id']
            ]);

            $st->execute([':uid' => $userId]);
            $vol = $st->fetch(PDO::FETCH_ASSOC);

            header('Location: volunteer_dashboard.php?updated=1');
            exit;
        }
    }
}

$updated = isset($_GET['updated']);
$avatarUrl = !empty($vol['profile_picture'])
    ? '/volunteer/' . $vol['profile_picture']
    : 'https://via.placeholder.com/160x160?text=Avatar';
$displayName = $vol['full_name'] ?: $email;
$statusBadge = ($vol['status'] === 'active') ? 'bg-success' : (($vol['status'] === 'hired') ? 'bg-primary' : 'bg-secondary');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Volunteer Dashboard · CommunityLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06)
        }

        .card > .card-header {
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .avatar-wrap {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .btn-pill {
            border-radius: 999px
        }

        .narrow-card {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand fw-semibold me-auto" href="/">CommunityLink</a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small mb-0">Volunteer: <?= e($email) ?></span>
            <a href="../public/assets/auth/logout.php" class="btn btn-sm btn-light text-primary border-primary fw-semibold rounded-pill px-3">
                Logout
            </a>


        </div>
    </div>
</nav>

<main class="container py-4">
    <?php if ($updated): ?>
        <div class="alert alert-success">Profile updated successfully.</div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="alert alert-danger"><?= e($err) ?></div>
    <?php endif; ?>

    <?php if (!$isEditing): ?>

        <section class="text-center mb-4">
            <h2 class="fw-bold mb-3">Hi, <?= e($displayName) ?></h2>
            <div class="avatar-wrap mb-3 mx-auto">
                <img src="<?= e($avatarUrl) ?>" alt="avatar">
            </div>
            <div>
                <span class="badge <?= $statusBadge ?>"><?= e($vol['status']) ?></span>
            </div>
        </section>

        <div class="row g-4">
            <div class="col-12 col-lg-5 mx-auto">
                <div class="card">
                    <div class="card-header bg-white position-relative d-flex align-items-center">
                        <h5 class="mb-0 position-absolute top-50 start-50 translate-middle text-center pe-none">
                            Your details
                        </h5>
                        <a href="volunteer_dashboard.php?edit=1"
                           class="btn btn-primary btn-sm px-3 fw-semibold btn-pill ms-auto">
                            Edit
                        </a>
                    </div>


                    <div class="card-body text-center">
                        <dl class="mb-0">
                            <dt class="fw-semibold">Full name</dt>
                            <dd><?= e($vol['full_name'] ?: '—') ?></dd>
                            <dt class="fw-semibold">Email</dt>
                            <dd><?= e($email) ?></dd>
                            <dt class="fw-semibold">Phone</dt>
                            <dd><?= e($vol['phone'] ?: '—') ?></dd>
                            <dt class="fw-semibold">Skills</dt>
                            <dd><?= nl2br(e($vol['skills'] ?: '—')) ?></dd>
                        </dl>
                    </div>


                </div>
            </div>
        </div>

    <?php else: ?>

        <div class="row g-4">
            <div class="col-12 col-lg-6 mx-auto">
                <div class="card narrow-card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit profile</h5>
                        <a href="volunteer_dashboard.php" class="btn btn-primary btn-sm btn-pill text-white">Cancel</a>
                    </div>

                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" novalidate>
                            <div class="text-center mb-4">
                                <div class="avatar-wrap mx-auto">
                                    <img id="preview" src="<?= e($avatarUrl) ?>" alt="avatar">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full name</label>
                                <input name="full_name" class="form-control" required
                                       value="<?= e($vol['full_name'] ?? '') ?>">
                                <div class="invalid-feedback">Full name is required.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control" required value="<?= e($vol['phone'] ?? '') ?>">
                                <div class="invalid-feedback">Phone is required.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Skills / Interests</label>
                                <textarea name="skills" class="form-control" rows="4"
                                          required><?= e($vol['skills'] ?? '') ?></textarea>
                                <div class="invalid-feedback">Skills are required.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Profile picture</label>
                                <input type="file" class="form-control" name="profile_picture" accept="image/*"
                                       onchange="previewImage(event)">
                                <div class="form-text">JPG/PNG/GIF/WEBP. A few MB max.</div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-primary btn-pill px-4 fw-semibold">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }

    function previewImage(e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        const img = document.getElementById('preview');
        if (img) img.src = url;
    }
</script>
</body>
</html>
