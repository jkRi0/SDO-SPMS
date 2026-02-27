<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/audit.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $db = get_db();
        $stmt = $db->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['supplier_id'] = $user['supplier_id'];

            // Log successful login to activity_logs for admin visibility
            create_log($db, $user['id'], 'login', 'user', $user['id'], 'Successful login');

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Supplier Transaction Monitoring System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3b7ca8;
            --primary-dark: #2c5a7f;
            --primary-light: #4a8fc7;
            --bg-light: #f5f7fa;
            --text-dark: #1a1a1a;
            --border-color: #e5e7eb;
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

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            padding: 2rem;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .login-branding {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .logo-wrapper {
            margin-bottom: 2rem;
        }

        .logo-wrapper img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            border: 3px solid white;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .logo-wrapper img:hover {
            transform: scale(1.08);
        }

        .deped-name {
            font-family: 'times new roman', serif;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }

        .system-title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            opacity: 0.50;
        }

        .login-form-section {
            padding: 3.5rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-title {
            color: var(--primary-blue);
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .login-form-title i {
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

        .position-relative .form-control {
            padding-right: 2.5rem;
        }

        .btn-primary {
            background: var(--primary-blue);
            border: none;
            font-weight: 600;
            padding: 0.55rem 1.2rem;
            border-radius: 6px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(58, 124, 168, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(58, 124, 168, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(58, 124, 168, 0.25);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .register-link p {
            color: var(--text-dark);
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .register-link a {
            color: var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-dark);
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-branding {
                padding: 2.5rem;
            }

            .login-form-section {
                padding: 2.5rem;
            }

            .system-title {
                font-size: 1.2rem;
            }

            .deped-name {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-container">
            <!-- LEFT SIDE - BRANDING -->
            <div class="login-branding">
                <div class="logo-wrapper">
                    <img src="assets/images/DEPED LOGO.jpg" alt="DepEd Logo">
                </div>
                <div class="deped-name">DEPED DIVISION OFFICE</div>
                <h2 class="system-title">Supplier Transaction Monitoring System</h2>
            </div>

            <div class="login-form-section">
                <h5 class="login-form-title">
                    <i class="fas fa-lock"></i> SIGN IN
                </h5>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                               placeholder="Enter your username" autocomplete="username">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="password" name="password" required
                                   placeholder="Enter your password" autocomplete="current-password">
                            <button type="button" class="btn btn-show-password" id="togglePassword" title="Show/Hide password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Sign In</span>
                        </button>
                        <a href="supplier_oauth_login.php" class="btn btn-outline-danger d-flex align-items-center gap-2 ms-2">
                            <i class="fab fa-google"></i>
                            <span>Google Login (Supplier)</span>
                        </a>
                    </div>
                </form>

                <div class="register-link">
                    <p>New supplier? <a href="#" id="showRegisterForm">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REGISTRATION FORM (Hidden by default) -->
<div class="login-wrapper" id="registerFormWrapper" style="display: none;">
    <div class="login-card">
        <div class="login-container">
            <!-- LEFT SIDE - BRANDING -->
            <div class="login-branding">
                <div class="logo-wrapper">
                    <img src="assets/images/DEPED LOGO.jpg" alt="DepEd Logo">
                </div>
                <div class="deped-name">DEPED DIVISION OFFICE</div>
                <h2 class="system-title">Supplier Transaction Monitoring System</h2>
            </div>

            <div class="login-form-section">
                <h5 class="login-form-title">
                    <i class="fas fa-user-plus"></i> CREATE ACCOUNT
                </h5>

                <div id="registerErrorAlert"></div>
                <div id="registerSuccessAlert"></div>

                <form id="registerForm" method="post" novalidate>
                    <div class="mb-3">
                        <label for="reg_supplier_name" class="form-label">
                            <i class="fas fa-store form-icon"></i>Supplier Name
                        </label>
                        <input type="text" class="form-control" id="reg_supplier_name" name="supplier_name" required
                               placeholder="Enter your company name">
                    </div>

                    <div class="mb-3">
                        <label for="reg_username" class="form-label">
                            <i class="fas fa-user form-icon"></i>Username
                        </label>
                        <input type="text" class="form-control" id="reg_username" name="username" required
                               placeholder="Choose a unique username">
                    </div>

                    <div class="mb-3">
                        <label for="reg_password" class="form-label">
                            <i class="fas fa-key form-icon"></i>Password
                        </label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="reg_password" name="password" required
                                   placeholder="Create a strong password">
                            <button type="button" class="btn btn-show-password" id="toggleRegPassword" title="Show/Hide password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reg_password_confirm" class="form-label">
                            <i class="fas fa-lock form-icon"></i>Confirm Password
                        </label>
                        <div class="position-relative">
                            <input type="password" class="form-control" id="reg_password_confirm" name="password_confirm" required
                                   placeholder="Confirm your password">
                            <button type="button" class="btn btn-show-password" id="toggleRegPasswordConfirm" title="Show/Hide password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-check"></i> Create Account
                    </button>
                </form>

                <div class="register-link">
                    <p>Already have an account? <a href="#" id="showLoginForm">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show/Hide Password Toggle
    function setupPasswordToggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (!input || !button) return;
        
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.querySelector('i').classList.toggle('fa-eye');
            button.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    // Setup password toggles
    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('reg_password', 'toggleRegPassword');
    setupPasswordToggle('reg_password_confirm', 'toggleRegPasswordConfirm');
    
    // Form switching
    const loginFormWrapper = document.querySelector('.login-wrapper');
    const registerFormWrapper = document.getElementById('registerFormWrapper');
    const showRegisterForm = document.getElementById('showRegisterForm');
    const showLoginForm = document.getElementById('showLoginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (showRegisterForm && showLoginForm && registerFormWrapper) {
        showRegisterForm.addEventListener('click', (e) => {
            e.preventDefault();
            loginFormWrapper.style.opacity = '0';
            setTimeout(() => {
                loginFormWrapper.style.display = 'none';
                registerFormWrapper.style.display = 'block';
                registerFormWrapper.style.opacity = '0';
                setTimeout(() => {
                    registerFormWrapper.style.opacity = '1';
                }, 50);
            }, 300);
        });
        
        showLoginForm.addEventListener('click', (e) => {
            e.preventDefault();
            registerFormWrapper.style.opacity = '0';
            setTimeout(() => {
                registerFormWrapper.style.display = 'none';
                loginFormWrapper.style.display = 'block';
                loginFormWrapper.style.opacity = '0';
                setTimeout(() => {
                    loginFormWrapper.style.opacity = '1';
                }, 50);
            }, 300);
        });
        
        // Handle registration form submission
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(registerForm);
            
            try {
                const response = await fetch('register_supplier.php', {
                    method: 'POST',
                    body: formData
                });
                
                const text = await response.text();
                
                // Check if registration was successful
                if (text.includes('Registration successful')) {
                    document.getElementById('registerSuccessAlert').innerHTML = 
                        '<div class="alert alert-success" role="alert"><i class="fas fa-check-circle"></i> Registration successful. Redirecting to login...</div>';
                    
                    setTimeout(() => {
                        registerForm.reset();
                        showLoginForm.click();
                        document.getElementById('registerSuccessAlert').innerHTML = '';
                    }, 2000);
                } else if (text.includes('Username already exists')) {
                    document.getElementById('registerErrorAlert').innerHTML = 
                        '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i> Username already exists.</div>';
                } else if (text.includes('Passwords do not match')) {
                    document.getElementById('registerErrorAlert').innerHTML = 
                        '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i> Passwords do not match.</div>';
                } else if (text.includes('All fields are required')) {
                    document.getElementById('registerErrorAlert').innerHTML = 
                        '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i> All fields are required.</div>';
                } else {
                    document.getElementById('registerErrorAlert').innerHTML = 
                        '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i> Error during registration. Please contact admin.</div>';
                }
            } catch (error) {
                document.getElementById('registerErrorAlert').innerHTML = 
                    '<div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-circle"></i> An error occurred. Please try again.</div>';
            }
        });
    }
</script>
<style>
    .login-wrapper,
    #registerFormWrapper {
        transition: opacity 0.3s ease;
    }
    
    .login-wrapper {
        opacity: 1;
    }
    
    .btn-show-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--primary-blue);
        padding: 0.5rem;
        cursor: pointer;
        font-size: 0.9rem;
        transition: color 0.3s ease;
        z-index: 10;
    }
    
    .btn-show-password:hover {
        color: var(--primary-dark);
    }
    
    .form-icon {
        margin-right: 0.5rem;
        color: var(--primary-blue);
    }
</style>
</body>
</html>

