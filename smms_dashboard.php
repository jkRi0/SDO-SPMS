<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

require_login();

$user = current_user();
$role = $_SESSION['role'] ?? '';

// Check if user has access to SMMS
$smmsRoles = ['admin', 'school head', 'accounting', 'budget', 'cashier'];
if (!in_array(strtolower($role), $smmsRoles)) {
    // Redirect to portal selection if user doesn't have SMMS access
    header('Location: portal_selection.php');
    exit;
}

$pageTitle = 'SMMS Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php require_once __DIR__ . '/header.php'; ?>
    <style>
        .dashboard-container {
            padding: 2rem;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .placeholder-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .placeholder-icon {
            font-size: 4rem;
            color: #fa709a;
            margin-bottom: 1.5rem;
        }
        
        .placeholder-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .placeholder-description {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 2px dashed #dee2e6;
        }
        
        .feature-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .feature-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .feature-description {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-card">
            <h1 class="mb-3">
                <i class="fas fa-school me-2"></i>SMMS Dashboard
            </h1>
            <p class="mb-0">
                Welcome, <?php echo htmlspecialchars($user['username']); ?>! 
                School MOOE Monitoring System - Coming Soon
            </p>
        </div>
        
        <div class="placeholder-card">
            <div class="placeholder-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h2 class="placeholder-title">Under Development</h2>
            <p class="placeholder-description">
                The School MOOE Monitoring (SMM) system is currently being developed. 
                This dashboard will provide comprehensive tools for monitoring school expenses, 
                budget allocation, and financial reporting.
            </p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="feature-title">Budget Tracking</h3>
                    <p class="feature-description">
                        Monitor MOOE budget allocation and utilization across schools
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3 class="feature-title">Expense Monitoring</h3>
                    <p class="feature-description">
                        Track and categorize school expenses in real-time
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Financial Reports</h3>
                    <p class="feature-description">
                        Generate comprehensive financial reports and analytics
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3 class="feature-title">Fund Management</h3>
                    <p class="feature-description">
                        Manage and allocate funds to different school programs
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="portal_selection.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to Portal Selection
            </a>
        </div>
    </div>
    
    <?php require_once __DIR__ . '/footer.php'; ?>
    
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
