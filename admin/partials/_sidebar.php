<?php
// Helper function to check if the current route matches
function is_active($route) {
    $currentRoute = Router::getCurrentRoute();
    return $currentRoute === $route ? 'active' : '';
}
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('index'); ?>" href="index.php">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    
    <li class="nav-item nav-category">Content Management</li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('events') || is_active('event-editor') ? 'active' : ''; ?>" href="index.php?route=events">
        <i class="menu-icon mdi mdi-calendar-text"></i>
        <span class="menu-title">Events</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('team') || is_active('team-editor') ? 'active' : ''; ?>" href="index.php?route=team">
        <i class="menu-icon mdi mdi-account-group"></i>
        <span class="menu-title">Team Members</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('projects') || is_active('project-editor') ? 'active' : ''; ?>" href="index.php?route=projects">
        <i class="menu-icon mdi mdi-laptop"></i>
        <span class="menu-title">Projects</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('blog') || is_active('blog-editor') ? 'active' : ''; ?>" href="index.php?route=blog">
        <i class="menu-icon mdi mdi-file-document-multiple"></i>
        <span class="menu-title">Blog Posts</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('courses') || is_active('course-editor') ? 'active' : ''; ?>" href="index.php?route=courses">
        <i class="menu-icon mdi mdi-book-open"></i>
        <span class="menu-title">Programs/Courses</span>
      </a>
    </li>
    
    <li class="nav-item nav-category">Member Management</li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('members'); ?>" href="index.php?route=members">
        <i class="menu-icon mdi mdi-account-multiple"></i>
        <span class="menu-title">Members</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('contacts'); ?>" href="index.php?route=contacts">
        <i class="menu-icon mdi mdi-contact-mail"></i>
        <span class="menu-title">Contact Messages</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('newsletter'); ?>" href="index.php?route=newsletter">
        <i class="menu-icon mdi mdi-email-outline"></i>
        <span class="menu-title">Newsletter</span>
      </a>
    </li>
    
    <li class="nav-item nav-category">System</li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('form-logs'); ?>" href="index.php?route=form-logs">
        <i class="menu-icon mdi mdi-file-document"></i>
        <span class="menu-title">Form Logs</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo is_active('site-settings'); ?>" href="index.php?route=site-settings">
        <i class="menu-icon mdi mdi-cog"></i>
        <span class="menu-title">Site Settings</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../index.php?page=events" target="_blank">
        <i class="menu-icon mdi mdi-web"></i>
        <span class="menu-title">View Website</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="logout.php">
        <i class="menu-icon mdi mdi-logout"></i>
        <span class="menu-title">Logout</span>
      </a>
    </li>
  </ul>
</nav>
