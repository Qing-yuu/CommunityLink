<?php
$msg  = trim((string)($_GET['msg'] ?? 'Action completed successfully.'));
$home = '../../index.php';
$sec  = 3;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Success</title>
    <meta http-equiv="refresh" content="<?= $sec ?>;url=<?= htmlspecialchars($home) ?>">
    <style>
        body {
            background: #fff;
            color: #000;
            font: 16px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;
            margin: 0;
        }
        .wrap {
            max-width: 640px;
            margin: 15vh auto;
            padding: 24px;
        }
        .card {
            background: #fff;
            border: 1px solid #000;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,.08);
            padding: 40px 30px;
            text-align: center;
        }
        .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
        }
        h1 {
            margin: 0 0 12px;
            font-size: 22px;
            font-weight: 700;
        }
        p {
            margin: 0 0 20px;
            color: #64748b;
        }
        a.btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 999px;
            font-weight: 600;
            background: #000;
            color: #fff;
            border: none;
            transition: background .2s;
        }
        a.btn:hover {
            background: #333;
        }

    </style>
</head>
<body>
<div class="wrap">
    <div class="card" role="status" aria-live="polite">
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle cx="26" cy="26" r="25" fill="#16a34a"/>
                <path fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" d="M14 27l7 7 17-17"/>
            </svg>
        </div>
        <h1><?= htmlspecialchars($msg) ?></h1>
        <p>You will be redirected to the home page in <?= $sec ?> seconds...</p>
        <a class="btn" href="<?= htmlspecialchars($home) ?>">Back to Home now</a>
    </div>
</div>
</body>
</html>
