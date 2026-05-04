<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo $base_url; ?>dashboard.php" class="brand-link text-center">
    <span class="brand-text font-weight-light">School SSCMS</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <i class="fas fa-user-circle fa-2x text-white"></i>
      </div>
      <div class="info">
        <a href="#" class="d-block"><?php echo $_SESSION['full_name'] ?? 'Guest'; ?></a>
        <span class="badge badge-success"><?php echo $_SESSION['role'] ?? 'User'; ?></span>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <li class="nav-item">
          <a href="<?php echo $base_url; ?>dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?php echo $base_url; ?>items/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/items/') !== false ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-boxes"></i>
            <p>Items Management</p>
          </a>
        </li>

        <?php if ($_SESSION['role'] !== 'User'): ?>
        <li class="nav-item">
          <a href="<?php echo $base_url; ?>vendors/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/vendors/') !== false ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-truck"></i>
            <p>Vendors Management</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-header">SUPPLY CHAIN MANAGEMENT</li>

        <li class="nav-item">
          <a href="<?php echo $base_url; ?>scm/index.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], '/scm/index.php') !== false ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>SCM Dashboard</p>
          </a>
        </li>

        <?php if ($_SESSION['role'] !== 'User'): ?>
        <li class="nav-item <?php echo in_array($current_page, ['suppliers.php', 'manufacturers.php', 'distributors.php', 'retailers.php']) ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-network-wired"></i>
            <p>
              SCM Partners
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?php echo $base_url; ?>scm/suppliers.php" class="nav-link <?php echo $current_page == 'suppliers.php' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Suppliers</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $base_url; ?>scm/manufacturers.php" class="nav-link <?php echo $current_page == 'manufacturers.php' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Manufacturers</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $base_url; ?>scm/distributors.php" class="nav-link <?php echo $current_page == 'distributors.php' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Distributors</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo $base_url; ?>scm/retailers.php" class="nav-link <?php echo $current_page == 'retailers.php' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Retailers</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="<?php echo $base_url; ?>scm/purchase_requests.php" class="nav-link <?php echo $current_page == 'purchase_requests.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Purchase Requests</p>
          </a>
        </li>

        <?php if ($_SESSION['role'] !== 'User'): ?>
        <li class="nav-item">
          <a href="<?php echo $base_url; ?>scm/purchase_orders.php" class="nav-link <?php echo $current_page == 'purchase_orders.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>Purchase Orders</p>
          </a>
        </li>
        <?php endif; ?>

        <li class="nav-item">
          <a href="<?php echo $base_url; ?>scm/supply_chain_flow.php" class="nav-link <?php echo $current_page == 'supply_chain_flow.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Supply Chain Flow</p>
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
