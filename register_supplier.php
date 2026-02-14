<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($supplier_name === '' || $username === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        $db = get_db();
        $db->beginTransaction();
        try {
            // Create supplier
            $stmt = $db->prepare('INSERT INTO suppliers (name) VALUES (?)');
            $stmt->execute([$supplier_name]);
            $supplier_id = $db->lastInsertId();

            // Get supplier role id
            $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
            $roleStmt->execute(['supplier']);
            $role = $roleStmt->fetch();
            if (!$role) {
                throw new Exception('Supplier role not found. Please import init_db.sql.');
            }

            // Create user
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, role_id, supplier_id) VALUES (?,?,?,?)');
            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $role['id'],
                $supplier_id
            ]);

            $db->commit();
            $success = 'Registration successful. You can now log in.';
        } catch (Exception $e) {
            $db->rollBack();
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $error = 'Username already exists.';
            } else {
                $error = 'Error during registration. Please contact admin.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Registration - Supplier Transaction Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3b7ca8;
            --primary-dark: #2c5a7f;
            --primary-light: #4a8fc7;
            --bg-light: #f5f7fa;
            --text-dark: #1a1a1a;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --success-green: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-wrapper {
            width: 100%;
            max-width: 900px;
            padding: 2rem;
        }

        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            height: 520px;
        }

        .register-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .register-branding {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .register-icon {
            margin-bottom: 2rem;
        }

        /* Match login branding size for consistency */
        .register-icon img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            border: 3px solid white;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .register-icon img:hover {
            transform: scale(1.08);
        }

        .deped-name {
            font-family: 'times new roman', serif;
            font-size: 1.5rem; /* same as login */
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .system-title {
            font-size: 1rem; /* same as login */
            font-weight: 700;
            line-height: 1.5;
            opacity: 0.50;
        }

        .register-form-section {
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-form-title {
            color: var(--primary-blue);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .register-form-title i {
            font-size: 1.6rem;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: white;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(58, 124, 168, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .btn-register {
            background: var(--primary-blue);
            border: none;
            font-weight: 600;
            padding: 0.55rem 1.2rem;
            border-radius: 6px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            color: white;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(58, 124, 168, 0.2);
        }

        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 124, 168, 0.35);
            color: white;
        }

        .btn-register:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(58, 124, 168, 0.25);
        }

        .login-link {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .login-link p {
            color: var(--text-dark);
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .login-link a {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-dark);
        }

        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #16a34a;
        }

        .form-icon {
            margin-right: 0.5rem;
            color: var(--primary-blue);
        }

        @media (max-width: 768px) {
            .register-container {
                grid-template-columns: 1fr;
            }

            .register-branding {
                padding: 2.5rem;
            }

            .register-form-section {
                padding: 2.5rem;
            }

            .deped-name {
                font-size: 1rem;
            }

            .system-title {
                font-size: 1.2rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>
<body>
<div class="register-wrapper">
    <div class="register-card">
        <div class="register-container">

            <div class="register-branding">
                <div class="register-icon">
                    <img src="assets/images/DEPED LOGO.jpg" alt="DepEd Logo">
                </div>
                <div class="deped-name">DEPED DIVISION OFFICE</div>
                <h2 class="system-title">Supplier Transaction Monitoring System</h2>
            </div>

            <div class="register-form-section">
                <h5 class="register-form-title">
                    <i class="fas fa-user-plus"></i> Create Account
                </h5>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <!-- <br><small><a href="login.php">Click here to login</a></small> -->
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">
                            <i class="fas fa-store form-icon"></i>Supplier Name
                        </label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" required
                               placeholder="Enter your company name">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user form-icon"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required
                               placeholder="Choose a unique username">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-key form-icon"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required
                               placeholder="Create a strong password">
                    </div>

                    <div class="mb-4">
                        <label for="password_confirm" class="form-label">
                            <i class="fas fa-lock form-icon"></i>Confirm Password
                        </label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required
                               placeholder="Confirm your password">
                    </div>

                    <button type="submit" class="btn btn-register w-100">
                        <i class="fas fa-check"></i> Create Account
                    </button>
                </form>

                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include __DIR__ . '/footer.php'; ?>

