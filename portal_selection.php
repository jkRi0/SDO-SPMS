<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get database connection
$db = get_db();

// Get user role information
$userId = $_SESSION['user_id'];
$stmt = $db->prepare('SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$roleName = strtolower($user['role_name']);

// Define portal access
$stmsRoles = ['admin', 'supplier', 'proponent', 'procurement', 'supply', 'accounting', 'budget', 'cashier'];
$smmsRoles = ['admin', 'school head', 'accounting', 'budget', 'cashier'];

$canAccessSTMS = in_array($roleName, $stmsRoles);
$canAccessSMMS = in_array($roleName, $smmsRoles);

// If user only has access to one portal, redirect automatically
if ($canAccessSTMS && !$canAccessSMMS) {
    header('Location: dashboard.php');
    exit;
} elseif (!$canAccessSTMS && $canAccessSMMS) {
    header('Location: smms_dashboard.php');
    exit;
}

$pageTitle = 'Select Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php require_once __DIR__ . '/header.php'; ?>
    <style>
        .portal-container {
            padding: 2rem;
        }
        
        .portal-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .portal-title {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .portal-subtitle {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
        
        .user-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .user-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        
        .user-role {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .portals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .portal-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .portal-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .portal-card.stms:hover {
            border-color: #007bff;
        }
        
        .portal-card.smms:hover {
            border-color: #dc3545;
        }
        
        .portal-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .portal-card.stms .portal-icon {
            color: #007bff;
        }
        
        .portal-card.smms .portal-icon {
            color: #dc3545;
        }
        
        .portal-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .portal-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .portal-features {
            text-align: left;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .portal-features ul {
            list-style: none;
            padding: 0;
        }
        
        .portal-features li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .portal-features li i {
            margin-right: 0.5rem;
            font-size: 0.8rem;
            color: #28a745;
        }
        
        .accessible-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .portal-container {
                padding: 1rem;
            }
            
            .portals-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .portal-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid portal-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="portal-header">
                    <h1 class="portal-title">
                        <i class="fas fa-th-large me-2"></i>Select Portal
                    </h1>
                    <p class="portal-subtitle">Choose the system you want to access</p>
                </div>
                
                <div class="user-info">
                    <div class="user-name">
                        <i class="fas fa-user-circle me-2"></i>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    <div class="user-role">
                        Role: <?php echo htmlspecialchars(ucwords($user['role_name'])); ?>
                    </div>
                </div>
                
                <div class="portals-grid">
                    <?php if ($canAccessSTMS): ?>
                    <div class="portal-card stms" onclick="window.location.href='dashboard.php'">
                        <div class="accessible-badge">
                            <i class="fas fa-check-circle me-1"></i>Accessible
                        </div>
                        <div class="portal-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h2 class="portal-name">STMS</h2>
                        <p class="portal-description">
                            Supplier Transaction Monitoring System
                        </p>
                        <div class="portal-features">
                            <ul>
                                <li><i class="fas fa-check"></i> Transaction monitoring</li>
                                <li><i class="fas fa-check"></i> Supplier management</li>
                                <li><i class="fas fa-check"></i> Workflow tracking</li>
                                <li><i class="fas fa-check"></i> Department notifications</li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($canAccessSMMS): ?>
                    <div class="portal-card smms" onclick="window.location.href='smms_dashboard.php'">
                        <div class="accessible-badge">
                            <i class="fas fa-check-circle me-1"></i>Accessible
                        </div>
                        <div class="portal-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <h2 class="portal-name">SMMS</h2>
                        <p class="portal-description">
                            School MOOE Monitoring System
                        </p>
                        <div class="portal-features">
                            <ul>
                                <li><i class="fas fa-check"></i> MOOE budget tracking</li>
                                <li><i class="fas fa-check"></i> School expense monitoring</li>
                                <li><i class="fas fa-check"></i> Financial reporting</li>
                                <li><i class="fas fa-check"></i> Budget allocation</li>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once __DIR__ . '/footer.php'; ?>
    
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
