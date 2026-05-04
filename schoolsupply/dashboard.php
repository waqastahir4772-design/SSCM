<?php
session_start();
$base_url = ''; 
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

// Get statistics
$stats = [
    'total_items' => get_count('items', 'deleted_at IS NULL'),
    'low_stock' => get_count('items', 'current_stock <= reorder_level AND deleted_at IS NULL'),
    'total_vendors' => get_count('vendors', "status='Active'"),
    'pending_requests' => get_count('purchase_requests', "status='Pending'"),
    'suppliers' => get_count('scm_partners', "type='Supplier' AND status='Active'"),
    'manufacturers' => get_count('scm_partners', "type='Manufacturer' AND status='Active'"),
    'distributors' => get_count('scm_partners', "type='Distributor' AND status='Active'"),
    'retailers' => get_count('scm_partners', "type='Retailer' AND status='Active'")
];

// Data for Charts
$in_stock = $stats['total_items'] - $stats['low_stock'];

// Get Unified Activity Feed
$activity_query = "
    (SELECT i.created_at as activity_date, i.name as title, 'New Item' as type, 'success' as color FROM items i WHERE i.deleted_at IS NULL)
    UNION
    (SELECT pr.created_at, pr.request_no, 'Purchase Request' as type, 'warning' as color FROM purchase_requests pr)
    UNION
    (SELECT po.created_at, po.po_no, 'Purchase Order' as type, 'info' as color FROM purchase_orders po)
    ORDER BY activity_date DESC LIMIT 8
";
$activities = $conn->query($activity_query);

require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Professional Dashboard</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <!-- Quick Action Center -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card card-outline card-primary">
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-2 col-4">
                  <a href="items/add.php" class="btn btn-app"><i class="fas fa-plus-circle text-success"></i> Add Item</a>
                </div>
                <div class="col-md-2 col-4">
                  <a href="scm/purchase_requests.php" class="btn btn-app"><i class="fas fa-shopping-cart text-warning"></i> New Request</a>
                </div>
                <div class="col-md-2 col-4">
                  <a href="scm/purchase_orders.php" class="btn btn-app"><i class="fas fa-file-invoice text-primary"></i> New PO</a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="vendors/add.php" class="btn btn-app"><i class="fas fa-truck text-info"></i> Add Vendor</a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="items/index.php" class="btn btn-app"><i class="fas fa-warehouse text-navy"></i> Inventory</a>
                </div>
                <div class="col-md-2 col-4">
                    <a href="scm/supply_chain_flow.php" class="btn btn-app"><i class="fas fa-project-diagram text-purple"></i> Track Flow</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats row -->
      <div class="row">
        <?php 
        $boxes = [
            ['count' => $stats['total_items'], 'title' => 'Total Items', 'color' => 'info', 'icon' => 'boxes', 'link' => 'items/index.php'],
            ['count' => $stats['low_stock'], 'title' => 'Low Stock', 'color' => 'warning', 'icon' => 'exclamation-triangle', 'link' => 'items/index.php?filter=low_stock'],
            ['count' => $stats['total_vendors'], 'title' => 'Active Vendors', 'color' => 'success', 'icon' => 'truck', 'link' => 'vendors/index.php'],
            ['count' => $stats['pending_requests'], 'title' => 'Pending Requests', 'color' => 'danger', 'icon' => 'clock', 'link' => 'scm/purchase_requests.php']
        ];
        foreach($boxes as $box): ?>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-<?php echo $box['color']; ?>">
            <div class="inner">
              <h3><?php echo $box['count']; ?></h3>
              <p><?php echo $box['title']; ?></p>
            </div>
            <div class="icon"><i class="fas fa-<?php echo $box['icon']; ?>"></i></div>
            <a href="<?php echo $box['link']; ?>" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="row">
        <!-- Chart Column -->
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Stock Status Overview</h3>
            </div>
            <div class="card-body">
              <canvas id="stockChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
          </div>
        </div>

        <!-- Activity Feed Column -->
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">System Activity Feed</h3>
            </div>
            <div class="card-body p-0">
              <ul class="products-list product-list-in-card pl-2 pr-2">
                <?php if ($activities && $activities->num_rows > 0): ?>
                    <?php while($act = $activities->fetch_assoc()): ?>
                    <li class="item">
                      <div class="product-img">
                        <span class="badge badge-<?php echo $act['color']; ?> p-2"><i class="fas fa-circle"></i></span>
                      </div>
                      <div class="product-info">
                        <a href="javascript:void(0)" class="product-title"><?php echo h($act['type']); ?>
                          <span class="badge badge-light float-right text-gray"><?php echo date('M d, H:i', strtotime($act['activity_date'])); ?></span></a>
                        <span class="product-description">
                          Reference: <b><?php echo h($act['title']); ?></b>
                        </span>
                      </div>
                    </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="item text-center p-3 text-muted">No recent activity</li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Stock Chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['In Stock', 'Low Stock'],
        datasets: [{
          data: [<?php echo $in_stock; ?>, <?php echo $stats['low_stock']; ?>],
          backgroundColor: ['#28a745', '#ffc107'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
      }
    });
  });
</script>

<?php require_once 'includes/footer.php'; ?>
