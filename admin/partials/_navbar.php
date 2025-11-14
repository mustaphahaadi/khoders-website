<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="index.php">
        <img src="assets/images/logo.svg" alt="KHODERS WORLD logo" />
      </a>
      <a class="navbar-brand brand-logo-mini" href="index.php">
        <img src="assets/images/logo-mini.svg" alt="KHODERS WORLD logo" />
      </a>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-top"> 
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text">Welcome, <span class="text-black fw-bold"><?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></span></h1>
        <h3 class="welcome-sub-text">KHODERS WORLD Campus Coding Club Admin Dashboard</h3>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown d-none d-lg-block">
        <a class="nav-link dropdown-bordered dropdown-toggle dropdown-toggle-split" id="messageDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false"> Quick Actions </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="messageDropdown">
          <a class="dropdown-item py-3" href="../index.php" target="_blank">
            <p class="mb-0 font-weight-medium float-left">View Website</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-3" href="members.php">
            <p class="mb-0 font-weight-medium float-left">Manage Members</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-3" href="events.php">
            <p class="mb-0 font-weight-medium float-left">Manage Events</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item py-3" href="projects.php">
            <p class="mb-0 font-weight-medium float-left">Manage Projects</p>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown"> 
        <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="icon-bell"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">
          <a class="dropdown-item py-3">
            <p class="mb-0 font-weight-medium float-left">Notifications</p>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-success">
                <i class="mdi mdi-account-plus"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">New Member Registrations</h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                <?php echo $stats['members_week']; ?> this week
              </p>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-info">
                <i class="mdi mdi-email-outline"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <h6 class="preview-subject font-weight-normal">New Contact Messages</h6>
              <p class="font-weight-light small-text mb-0 text-muted">
                View in Contact Management
              </p>
            </div>
          </a>
        </div>
      </li>
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-xs rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image"> </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image">
            <p class="mb-1 mt-3 font-weight-semibold"><?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?></p>
            <p class="fw-light text-muted mb-0">KHODERS Administrator</p>
          </div>
          <a class="dropdown-item" href="logout.php"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
