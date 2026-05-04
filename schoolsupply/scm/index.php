<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

// Get SCM statistics
$stats = [
    'suppliers' => get_count('scm_partners', "type='Supplier' AND status='Active'"),
    'manufacturers' => get_count('scm_partners', "type='Manufacturer' AND status='Active'"),
    'distributors' => get_count('scm_partners', "type='Distributor' AND status='Active'"),
    'retailers' => get_count('scm_partners', "type='Retailer' AND status='Active'"),
    'pending_requests' => get_count('purchase_requests', "status='Pending'"),
    'low_stock' => get_count('items', 'current_stock <= reorder_level AND deleted_at IS NULL')
];

// Get recent SCM activity
$recent_activity = $conn->query("
    SELECT po.created_at as activity_date, i.name as item_name, 'Purchase Order' as stage, 
           sp.name as partner_name, po.status 
    FROM purchase_orders po
    JOIN scm_partners sp ON po.partner_id = sp.id
    JOIN purchase_requests pr ON po.request_id = pr.id
    JOIN items i ON pr.item_id = i.id
    ORDER BY po.created_at DESC LIMIT 5
");

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>SCM Dashboard</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <!-- SCM Stats -->
      <div class="row">
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-primary">
            <div class="info-box-content">
              <span class="info-box-text">Suppliers</span>
              <span class="info-box-number"><?php echo $stats['suppliers']; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-success">
            <div class="info-box-content">
              <span class="info-box-text">Manufacturers</span>
              <span class="info-box-number"><?php echo $stats['manufacturers']; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-warning">
            <div class="info-box-content">
              <span class="info-box-text">Distributors</span>
              <span class="info-box-number"><?php echo $stats['distributors']; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-info">
            <div class="info-box-content">
              <span class="info-box-text">Retailers</span>
              <span class="info-box-number"><?php echo $stats['retailers']; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-danger">
            <div class="info-box-content">
              <span class="info-box-text">Requests</span>
              <span class="info-box-number"><?php echo $stats['pending_requests']; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
          <div class="info-box bg-olive">
            <div class="info-box-content">
              <span class="info-box-text">Low Stock</span>
              <span class="info-box-number"><?php echo $stats['low_stock']; ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="row">
        <div class="col-md-3">
          <a href="purchase_requests.php" class="btn btn-block btn-primary p-3 mb-3">
            <i class="fas fa-shopping-cart fa-2x mb-2 d-block"></i> Create Purchase Request
          </a>
        </div>
        <div class="col-md-3">
          <a href="purchase_orders.php" class="btn btn-block btn-success p-3 mb-3">
            <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i> Place Purchase Order
          </a>
        </div>
        <div class="col-md-3">
          <a href="suppliers.php" class="btn btn-block btn-warning p-3 mb-3">
            <i class="fas fa-industry fa-2x mb-2 d-block"></i> Add New Supplier
          </a>
        </div>
        <div class="col-md-3">
          <a href="supply_chain_flow.php" class="btn btn-block btn-info p-3 mb-3">
            <i class="fas fa-project-diagram fa-2x mb-2 d-block"></i> Track Supply Flow
          </a>
        </div>
      </div>

      <!-- Recent SCM Activity -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Recent Supply Chain Activity</h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Activity</th>
                    <th>Partner</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($recent_activity && $recent_activity->num_rows > 0): ?>
                    <?php while($act = $recent_activity->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($act['activity_date'])); ?></td>
                      <td><?php echo h($act['item_name']); ?></td>
                      <td><span class="badge badge-primary"><?php echo h($act['stage']); ?></span></td>
                      <td><?php echo h($act['partner_name']); ?></td>
                      <td>
                        <span class="badge badge-<?php echo get_status_badge($act['status']); ?>">
                          <?php echo h($act['status']); ?>
                        </span>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">No recent SCM activity found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
