<?php
define('EMAIL_MAX', 191);
define('PASSWORD_MAX', 64);

$err = $_GET['error'] ?? '';
$next = isset($_GET['next']) ? '?next=' . urlencode($_GET['next']) : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign in · CommunityLink</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
            color: #000
        }

        .card {
            width: 420px;
            max-width: 100%;
            border-radius: 22px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
            overflow: hidden;
            border: 1px solid #000
        }

        .card-header {
            background: #000;
            color: #fff;
            padding: .8rem 1.2rem;
            font-weight: 700;
            font-size: 1.1rem
        }

        .back-btn {
            color: #fff;
            text-decoration: none;
            font-size: 1.25rem
        }

        .back-btn:hover {
            color: #ccc
        }

        .forgot a {
            font-size: .9rem;
            color: #000;
            text-decoration: none
        }

        .forgot a:hover {
            text-decoration: underline;
            color: #333
        }

        .form-label {
            font-weight: 600;
            color: #000
        }

        .btn-primary {
            border-radius: 999px;
            padding: .5rem 2rem;
            font-weight: 700;
            background: #000;
            color: #fff;
            border: none
        }

        .btn-primary:hover {
            background: #333
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <a href="../../../index.php" class="back-btn">&larr;</a>
        <span>CommunityLink</span>
        <span style="width:24px"></span>
    </div>
    <div class="card-body p-4">
        <?php if ($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <form method="post" action="authenticate.php<?= $next ?>" novalidate>
            <div class="mb-4">
                <label class="form-label" for="email">Email</label>
                <input id="email" class="form-control" type="email" name="email" maxlength="<?= EMAIL_MAX ?>" required>
                <div class="invalid-feedback">Valid email is required.</div>
            </div>
            <div class="mb-4">
                <label class="form-label" for="password">Password</label>
                <input id="password" class="form-control" type="password" name="password" minlength="8"
                       maxlength="<?= PASSWORD_MAX ?>" required>
                <div class="invalid-feedback">Password is required.</div>
                <div class="forgot text-end mt-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#forgotModal">Forgot password?</a>
                </div>
            </div>
            <div class="text-center mt-4">
                <button class="btn btn-primary px-4" type="submit">Login</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="forgotModal" tabindex="-1" aria-labelledby="forgotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header justify-content-center">
                <h1 class="modal-title fs-5" id="forgotModalLabel">Forgot password</h1>
            </div>
            <div class="modal-body text-center">
                Please contact <strong>amy.tan@communitylink.com</strong>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
