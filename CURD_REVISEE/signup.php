<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

if (current_user()) {
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';

    // Validation: No field should be empty
    if ($username === '') {
        $errors[] = 'Username is required.';
    }
    if ($name === '') {
        $errors[] = 'Full name is required.';
    }
    if ($email === '') {
        $errors[] = 'Email is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }
    if ($confirm === '') {
        $errors[] = 'Please confirm your password.';
    }

    // Validation: Email must be valid
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    // Validation: Password must be at least 6 characters
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    // Validation: Password and confirmation must match
    if ($password !== '' && $confirm !== '' && $password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        try {
            $conn = get_db_connection();

            // Check if username column exists
            $columnCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'username'");
            $hasUsername = $columnCheck->num_rows > 0;
            
            // Check if username exists (only if column exists)
            if ($hasUsername) {
                // Use SELECT 1 instead of SELECT id to avoid column dependency
                $stmt = $conn->prepare('SELECT 1 FROM users WHERE username = ? LIMIT 1');
                if ($stmt) {
                    $stmt->bind_param('s', $username);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows > 0) {
                        $errors[] = 'This username is already taken.';
                    }
                    $stmt->close();
                }
            }

            // Check if email exists
            if (empty($errors)) {
                // Use SELECT 1 instead of SELECT id to avoid column dependency
                $stmt = $conn->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
                if (!$stmt) {
                    throw new Exception('Database error: ' . $conn->error);
                }
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors[] = 'An account with this email already exists.';
                }
                $stmt->close();
            }

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                
                // Check if username column exists
                $columnCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'username'");
                $hasUsername = $columnCheck->num_rows > 0;
                
                if ($hasUsername) {
                    $stmt = $conn->prepare('INSERT INTO users (username, name, email, password_hash) VALUES (?, ?, ?, ?)');
                    if (!$stmt) {
                        throw new Exception('Database error: ' . $conn->error);
                    }
                    $stmt->bind_param('ssss', $username, $name, $email, $hash);
                } else {
                    $stmt = $conn->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
                    if (!$stmt) {
                        throw new Exception('Database error: ' . $conn->error);
                    }
                    $stmt->bind_param('sss', $name, $email, $hash);
                }
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    $_SESSION['signup_success'] = true;
                    $_SESSION['signup_email'] = $email;
                    header('Location: login.php?signup=success');
                    exit;
                } else {
                    $errors[] = 'Unable to create account. Please try again.';
                }

                $stmt->close();
            }
        } catch (Exception $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Facebook</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            background: #f0f2f5;
            color: #1c1e21;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 980px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 60px;
        }
        .left-section {
            flex: 1;
            padding-right: 32px;
        }
        .logo {
            color: #1877f2;
            font-size: 56px;
            font-weight: bold;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }
        .tagline {
            font-size: 28px;
            line-height: 32px;
            color: #1c1e21;
        }
        .right-section {
            flex: 0 0 432px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 28px;
        }
        h2 {
            font-size: 25px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1c1e21;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-row {
            display: flex;
            gap: 12px;
        }
        .form-row .form-group {
            flex: 1;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px;
            font-size: 15px;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            background: #f5f6f7;
            color: #1c1e21;
            transition: border-color 0.2s;
        }
        input:focus {
            outline: none;
            border-color: #1877f2;
            background: #fff;
        }
        input::placeholder {
            color: #90949c;
        }
        .btn-primary {
            width: 100%;
            padding: 11px;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            background: #42b72a;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 6px;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #36a420;
        }
        .btn-google {
            width: 100%;
            padding: 11px;
            font-size: 17px;
            font-weight: 600;
            color: #1c1e21;
            background: #fff;
            border: 1px solid #dddfe2;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .btn-google:hover {
            background: #f5f6f7;
        }
        .errors {
            background: #fce8e6;
            border: 1px solid #f28b82;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #c5221f;
        }
        .errors ul {
            margin: 0;
            padding-left: 20px;
        }
        .errors li {
            margin-bottom: 4px;
        }
        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
            color: #90949c;
            font-size: 12px;
        }
        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            right: 0;
            height: 1px;
            background: #dadde1;
        }
        .divider span {
            background: #fff;
            padding: 0 16px;
            position: relative;
        }
        .login-link {
            text-align: center;
            font-size: 14px;
            margin-top: 28px;
        }
        .login-link a {
            color: #1877f2;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                gap: 40px;
            }
            .left-section {
                text-align: center;
                padding-right: 0;
            }
            .logo {
                font-size: 48px;
            }
            .tagline {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">facebook</div>
            <div class="tagline">Connect with friends and the world around you on Facebook.</div>
        </div>
        <div class="right-section">
            <div class="card">
                <h2>Create a new account</h2>
                
                <?php if (!empty($errors)) : ?>
                    <div class="errors">
                        <ul>
                            <?php foreach ($errors as $error) : ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" value="<?php echo e($_POST['username'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Full name" value="<?php echo e($_POST['name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email address" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="New password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password_confirmation" placeholder="Confirm password" required>
                    </div>
                    <button type="submit" class="btn-primary">Sign Up</button>
                </form>

                <div class="divider"><span>or</span></div>

                <form method="post" action="login.php">
                    <input type="hidden" name="google_login" value="1">
                    <button type="submit" class="btn-google">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Sign up with Google
                    </button>
                </form>
            </div>
            <div class="login-link">
                Already have an account? <a href="login.php">Log in</a>
            </div>
        </div>
    </div>
</body>
</html>
