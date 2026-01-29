<?php
session_start();

// === PASSWORD (hashed) ===
$ADMIN_PASSWORD_HASH = '$2y$12$i.ZB/5P4Lticy8ylwdp1Ku.ELHZ901aSMG5hcCotbKm7VzuObAwD.';
// =========================

// reCAPTCHA config
$recaptchaSecret = '6LeI0FgsAAAAADW90cC8pMwEOwgJ_-Y7B9rNmKfU';
$recaptchaSiteKey = '6LeI0FgsAAAAAKFrxG4qWSMa0D7yfPoVMJZ-pQ_y';

// Login
if (isset($_POST['password'])) {
    // Verify reCAPTCHA
    $recaptchaOk = false;
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    if (!empty($recaptchaResponse)) {
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret'   => $recaptchaSecret,
            'response' => $recaptchaResponse
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $recaptchaOk = !empty($result['success']);
    }

    if (!$recaptchaOk) {
        $loginError = 'Please confirm you are not a robot.';
    } elseif (password_verify($_POST['password'], $ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $loginError = 'Incorrect password';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Check auth
if (empty($_SESSION['admin_logged_in'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin — SV Magazine</title>
        <style>
            body { font-family: 'Montserrat', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f9f9f9; }
            .login-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); max-width: 400px; width: 90%; }
            h1 { font-size: 24px; margin: 0 0 20px; }
            input[type="password"] { width: 100%; padding: 12px; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 15px; margin-bottom: 15px; box-sizing: border-box; }
            button { width: 100%; padding: 12px; background: #00b7cd; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
            button:hover { background: #009db0; }
            .error { color: #c53030; font-size: 14px; margin-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>SV Magazine Admin</h1>
            <?php if (!empty($loginError)): ?>
                <p class="error"><?php echo htmlspecialchars($loginError); ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required autofocus>
                <div class="g-recaptcha" data-sitekey="<?php echo $recaptchaSiteKey; ?>" style="margin-bottom: 15px;"></div>
                <button type="submit">Log In</button>
            </form>
        </div>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </body>
    </html>
    <?php
    exit;
}

// Load data
$contactsFile = __DIR__ . '/../data/contacts.json';
$magazinesFile = __DIR__ . '/../data/magazines.json';

$contacts = json_decode(file_get_contents($contactsFile), true) ?: [];
$magazines = json_decode(file_get_contents($magazinesFile), true) ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — SV Magazine</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', Arial, sans-serif; background: #f9f9f9; color: #000; }
        .admin-header { background: #000; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .admin-header h1 { font-size: 18px; }
        .admin-header a { color: rgba(255,255,255,0.7); font-size: 14px; text-decoration: none; }
        .admin-header a:hover { color: white; }
        .admin-content { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .card h2 { font-size: 20px; margin-bottom: 20px; }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #8c8c8c; margin-bottom: 6px; }
        .form-row input { width: 100%; padding: 10px 14px; border: 1px solid #e5e5e5; border-radius: 6px; font-size: 14px; font-family: inherit; }
        .form-row input:focus { outline: none; border-color: #00b7cd; }
        .btn { display: inline-block; padding: 10px 24px; font-size: 13px; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; font-family: inherit; }
        .btn-primary { background: #00b7cd; color: white; }
        .btn-primary:hover { background: #009db0; }
        .btn-danger { background: #e53e3e; color: white; }
        .btn-danger:hover { background: #c53030; }
        .btn-sm { padding: 6px 14px; font-size: 12px; }
        .mag-item { border: 1px solid #e5e5e5; border-radius: 8px; padding: 15px; margin-bottom: 10px; display: flex; gap: 15px; align-items: center; }
        .mag-item img { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; }
        .mag-item .mag-info { flex: 1; }
        .mag-item .mag-info strong { display: block; margin-bottom: 4px; }
        .mag-item .mag-info small { color: #8c8c8c; }
        .msg { padding: 10px 15px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; }
        .msg-success { background: #e6f9f0; color: #1a7d4b; }
        .msg-error { background: #fde8e8; color: #c53030; }
        .add-form { border-top: 1px solid #e5e5e5; padding-top: 20px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>SV Magazine Admin</h1>
        <div>
            <a href="../index.html" style="margin-right: 15px;">View Site</a>
            <a href="?logout=1">Log Out</a>
        </div>
    </div>

    <div class="admin-content">
        <?php if (!empty($_GET['saved'])): ?>
            <div class="msg msg-success">Changes saved successfully.</div>
        <?php endif; ?>

        <!-- Contacts -->
        <div class="card">
            <h2>Contact Information</h2>
            <form method="POST" action="save-contacts.php">
                <div class="form-row">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($contacts['phone'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($contacts['email'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label>Instagram URL</label>
                    <input type="url" name="instagram" value="<?php echo htmlspecialchars($contacts['instagram'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save Contacts</button>
            </form>
        </div>

        <!-- Magazines -->
        <div class="card">
            <h2>Magazines</h2>
            <p style="color: #8c8c8c; font-size: 14px; margin-bottom: 20px;">Newest first. Drag to reorder (coming soon).</p>

            <?php foreach ($magazines as $i => $mag): ?>
            <div class="mag-item">
                <img src="<?php echo htmlspecialchars($mag['cover']); ?>" alt="<?php echo htmlspecialchars($mag['title']); ?>">
                <div class="mag-info">
                    <strong><?php echo htmlspecialchars($mag['title']); ?></strong>
                    <small><?php echo htmlspecialchars($mag['year']); ?></small>
                </div>
                <form method="POST" action="save-magazines.php" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="index" value="<?php echo $i; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this issue?')">Delete</button>
                </form>
            </div>
            <?php endforeach; ?>

            <div class="add-form">
                <h3 style="font-size: 16px; margin-bottom: 15px;">Add New Issue</h3>
                <form method="POST" action="save-magazines.php">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="SV Magazine — Issue 9" required>
                    </div>
                    <div class="form-row">
                        <label>Year</label>
                        <input type="text" name="year" placeholder="2026" required>
                    </div>
                    <div class="form-row">
                        <label>Cover Image URL</label>
                        <input type="url" name="cover" placeholder="https://..." required>
                    </div>
                    <div class="form-row">
                        <label>PDF URL</label>
                        <input type="url" name="pdf" placeholder="https://..." required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Issue (to top)</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
