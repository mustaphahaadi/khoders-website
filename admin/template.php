<?php
/**
 * KHODERS WORLD Admin Template
 * Main template for the admin panel
 */

// Get current route
$currentRoute = Router::getCurrentRoute();

// Set default page title
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'KHODERS WORLD Admin');
}

// Get user info
$user = Auth::user();

// Start output buffering to prevent headers already sent issues
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <base href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/">
  <title><?php echo PAGE_TITLE; ?></title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
  <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <link rel="stylesheet" type="text/css" href="assets/js/select.dataTables.min.css">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/khoders-custom.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="../assets/img/khoders/logo.png" />
  <style>
    /* Modern ETH Color Scheme - Matching Homepage #136ad5 Blue */
    :root { --eth-blue: #136ad5; --eth-dark: #223a58; --eth-light: #f5faff; }
    
    /* Modern Sidebar - Fast & Clean */
    .sidebar { 
      background: linear-gradient(180deg, #136ad5 0%, #1a5fc5 100%) !important; 
      box-shadow: 2px 0 20px rgba(19, 106, 213, 0.15) !important;
      animation: slideInLeft 0.3s ease !important;
    }
    .sidebar .nav-item .nav-link { 
      color: rgba(255,255,255,0.8) !important; 
      padding: 10px 18px !important; 
      margin: 4px 8px !important;
      border-radius: 8px !important;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important; 
      position: relative !important;
    }
    .sidebar .nav-item .nav-link:hover { 
      color: #fff !important; 
      background: rgba(255,255,255,0.15) !important; 
      transform: translateX(4px) !important;
      box-shadow: 0 4px 12px rgba(255,255,255,0.1) !important;
    }
    .sidebar .nav-item .nav-link.active { 
      color: #fff !important; 
      background: rgba(255,255,255,0.25) !important; 
      font-weight: 600 !important; 
      box-shadow: 0 4px 16px rgba(255,255,255,0.2) !important;
      transform: translateX(4px) !important;
    }
    .sidebar .nav-category { 
      color: rgba(255,255,255,0.5) !important; 
      font-size: 10px !important; 
      font-weight: 800 !important; 
      text-transform: uppercase !important; 
      letter-spacing: 1.2px !important; 
      padding: 12px 18px 8px !important; 
      margin-top: 8px !important; 
    }
    @keyframes slideInLeft { from { transform: translateX(-10px); opacity: 0.9; } to { transform: translateX(0); opacity: 1; } }
    
    /* Cards - Modern */
    .card { 
      border: none !important; 
      border-radius: 12px !important; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important; 
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important; 
      background: #ffffff !important;
    }
    .card:hover { 
      box-shadow: 0 8px 24px rgba(19, 106, 213, 0.12) !important; 
      transform: translateY(-2px) !important;
    }
    .card-body { padding: 24px !important; }
    .card-title { color: #223a58 !important; font-weight: 700 !important; font-size: 18px !important; margin-bottom: 8px !important; }
    .card-subtitle { color: #6B7280 !important; font-size: 13px !important; }
    
    /* Buttons - ETH Blue Theme */
    .btn { 
      border-radius: 8px !important; 
      font-weight: 600 !important; 
      transition: all 0.2s ease !important; 
      border: none !important;
      font-size: 14px !important;
    }
    .btn-primary { 
      background: linear-gradient(135deg, #136ad5 0%, #0d4fa3 100%) !important; 
      color: white !important;
      box-shadow: 0 4px 12px rgba(19, 106, 213, 0.3) !important;
    }
    .btn-primary:hover { 
      transform: translateY(-2px) !important; 
      box-shadow: 0 6px 16px rgba(19, 106, 213, 0.4) !important;
    }
    .btn-outline-primary { 
      color: #136ad5 !important; 
      border: 2px solid #136ad5 !important; 
    }
    .btn-outline-primary:hover { 
      background: #136ad5 !important; 
      color: white !important;
      transform: translateY(-1px) !important;
    }
    .btn-outline-danger { 
      color: #EF4444 !important; 
      border: 2px solid #EF4444 !important; 
    }
    .btn-outline-danger:hover { 
      background: #EF4444 !important; 
      color: white !important;
      transform: translateY(-1px) !important;
    }
    
    /* Tables - Modern */
    .table thead th { 
      background: #f5faff !important; 
      color: #223a58 !important; 
      font-weight: 700 !important; 
      border: none !important; 
      padding: 14px 16px !important; 
      font-size: 12px !important; 
      text-transform: uppercase !important; 
      letter-spacing: 0.6px !important; 
    }
    .table tbody td { 
      padding: 14px 16px !important; 
      border-bottom: 1px solid #E5E7EB !important; 
      color: #4B5563 !important; 
      font-size: 14px !important;
    }
    .table tbody tr { transition: all 0.2s ease !important; }
    .table tbody tr:hover { 
      background: #f5faff !important; 
      box-shadow: 0 2px 8px rgba(19, 106, 213, 0.05) !important;
    }
    .table tbody tr:last-child td { border-bottom: none !important; }
    
    /* Badges */
    .badge { 
      padding: 6px 12px !important; 
      border-radius: 20px !important; 
      font-weight: 600 !important; 
      font-size: 11px !important; 
    }
    .badge.bg-success { background: #D1FAE5 !important; color: #065F46 !important; }
    .badge.bg-warning { background: #FEF3C7 !important; color: #92400E !important; }
    .badge.bg-danger { background: #FEE2E2 !important; color: #991B1B !important; }
    .badge.bg-info { background: #DBEAFE !important; color: #1E40AF !important; }
    .badge.bg-primary { background: #DBEAFE !important; color: #136ad5 !important; }
    
    /* Alerts */
    .alert { 
      border: none !important; 
      border-radius: 8px !important; 
      border-left: 4px solid !important; 
      padding: 14px 18px !important; 
      animation: slideDown 0.3s ease !important;
    }
    .alert-success { background: #D1FAE5 !important; border-left-color: #10B981 !important; color: #065F46 !important; }
    .alert-danger { background: #FEE2E2 !important; border-left-color: #EF4444 !important; color: #991B1B !important; }
    .alert-warning { background: #FEF3C7 !important; border-left-color: #F59E0B !important; color: #92400E !important; }
    .alert-info { background: #DBEAFE !important; border-left-color: #136ad5 !important; color: #0d4fa3 !important; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    /* Forms */
    .form-control, .form-select { 
      border: 1.5px solid #E5E7EB !important; 
      border-radius: 8px !important; 
      padding: 10px 14px !important; 
      font-size: 14px !important; 
      transition: all 0.2s ease !important; 
    }
    .form-control:focus, .form-select:focus { 
      border-color: #136ad5 !important; 
      box-shadow: 0 0 0 3px rgba(19, 106, 213, 0.1) !important;
      outline: none !important;
    }
    
    /* Navbar */
    .navbar { 
      background: white !important; 
      border-bottom: 1px solid #E5E7EB !important; 
      box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important; 
    }
    .navbar-brand { 
      font-weight: 700 !important; 
      color: #136ad5 !important; 
      font-size: 18px !important;
    }
    
    /* Dropdowns */
    .dropdown-menu { 
      border: none !important; 
      border-radius: 8px !important; 
      box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; 
      padding: 8px 0 !important; 
      animation: fadeInDown 0.2s ease !important;
    }
    .dropdown-item { 
      padding: 10px 18px !important; 
      color: #4B5563 !important; 
      transition: all 0.15s ease !important; 
      font-size: 14px !important;
    }
    .dropdown-item:hover { 
      background: #f5faff !important; 
      color: #136ad5 !important; 
      padding-left: 20px !important;
    }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>
<body class="with-welcome-text">
  <div class="container-scroller">
    <!-- partial:partials/_navbar.php -->
    <?php include 'partials/_navbar.php'; ?>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_sidebar.php -->
      <?php include 'partials/_sidebar.php'; ?>
      <!-- partial -->
      <div class="main-panel">
        <?php 
        // Dispatch the router to include the appropriate page
        Router::dispatch();
        ?>
        
        <!-- partial:partials/_footer.php -->
        <?php include 'partials/_footer.php'; ?>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="assets/vendors/chart.js/Chart.min.js"></script>
  <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="assets/vendors/datatables.net/jquery.dataTables.js"></script>
  <script src="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="assets/js/dashboard.js"></script>
  <!-- End custom js for this page-->
</body>
</html>
