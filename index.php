<?php ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CommunityLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root{ --overlay: rgba(0,0,0,.45); }
        .topbar{ font-size:.9rem; background:#0d0f12; color:#cbd5e1; }
        .topbar a{ color:#cbd5e1;text-decoration:none; }
        .topbar a:hover{ text-decoration:underline; }
        .topbar .logo{ height:40px; width:auto; display:block; }
        .hero{
            min-height: calc(100vh - 60px);
            background: url("public/assets/img/background.png") center center / cover no-repeat;
            position:relative; display:flex; align-items:center; justify-content:center;
            text-align:center; color:#fff;
        }
        .hero::before{ content:""; position:absolute; inset:0; background:var(--overlay); }
        .hero-inner{ position:relative; z-index:1; max-width:920px; padding:24px; }
        .hero h1{ font-weight:800; letter-spacing:.3px; font-size:clamp(1.8rem,3.8vw,3rem); margin:0 0 12px; }
        .hero p{ font-size:clamp(1rem,1.4vw,1.15rem); color:#e6e8eb; margin-bottom:28px; }
        .cta .btn{
            padding:.95rem 1.35rem; border-radius:999px; font-weight:700; min-width:180px;
            background:#000; color:#fff; border:1px solid #000;
        }
        .cta .btn:hover{ background:#111; color:#fff; border-color:#111; }
        footer{ background:#0d0f12; color:#cbd5e1; padding:20px 0; }
        .skip-link{ position:absolute; left:-10000px; top:auto; width:1px; height:1px; overflow:hidden; }
        .skip-link:focus{ left:12px; top:12px; width:auto; height:auto; background:#fff; color:#000; padding:.4rem .6rem; border-radius:.4rem; z-index:9999; }
    </style>
</head>
<body>

<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <a href="index.php" class="d-flex align-items-center justify-content-center">
            <img class="logo" src="public/assets/img/logo.png" alt="CommunityLink logo">
        </a>
        <div>
            <a href="public/assets/auth/login.php" class="btn btn-sm btn-outline-light">Login</a>
        </div>
    </div>
</div>

<header class="hero" role="banner">
    <div class="hero-inner" id="main">
        <h1>Building an inclusive, thriving volunteering culture</h1>
        <p>Connect with local organisations and make an impact in your community.</p>
        <div class="cta d-flex justify-content-center gap-3 flex-wrap">
            <a class="btn" href="public/assets/auth/volunteer_signup.php">Volunteer Register</a>
            <a class="btn" href="public/assets/organisation/org_register.php">Organisation Register</a>
            <a class="btn" href="public/assets/contact/contact.php">Contact Us</a>
        </div>
    </div>
</header>

<footer>
    <div class="container d-flex justify-content-between">
        <span>&copy; <?= date('Y') ?> CommunityLink</span>
        <span><a class="link-light" href="public/assets/contact/contact.php">Contact Us</a></span>
    </div>
</footer>

</body>
</html>
