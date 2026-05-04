<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

// Fetch flow data
$query = "
    SELECT scf.*, i.name as item_name, sp.name as partner_name, po.po_no 
    FROM supply_chain_flow scf
    JOIN items i ON scf.item_id = i.id
    LEFT JOIN purchase_orders po ON scf.po_id = po.id
    LEFT JOIN scm_partners sp ON scf.partner_id = sp.id
    ORDER BY scf.updated_at DESC
";
$flows = $conn->query($query);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Supply Chain Flow</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <!-- Timeline -->
          <div class="timeline">
            <?php if ($flows && $flows->num_rows > 0): ?>
                <?php while($f = $flows->fetch_assoc()): ?>
                <div class="time-label">
                  <span class="bg-blue"><?php echo date('d M. Y', strtotime($f['updated_at'])); ?></span>
                </div>
                <div>
                  <i class="fas fa-truck bg-info"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($f['updated_at'])); ?></span>
                    <h3 class="timeline-header">Stage: <b><?php echo h($f['stage']); ?></b></h3>
                    <div class="timeline-body">
                      Item: <b><?php echo h($f['item_name']); ?></b><br>
                      Partner: <?php echo h($f['partner_name'] ?? 'N/A'); ?><br>
                      Status: <span class="badge badge-info"><?php echo h($f['status']); ?></span><br>
                      Cost: <?php echo format_currency($f['cost']); ?>
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="time-label">
                  <span class="bg-gray">No tracking data yet</span>
                </div>
                <div>
                  <i class="fas fa-info bg-gray"></i>
                  <div class="timeline-item">
                    <div class="timeline-body">
                      Purchase orders will automatically create flow entries when they reach different stages of the supply chain.
                    </div>
                  </div>
                </div>
            <?php endif; ?>
            <div>
              <i class="fas fa-clock bg-gray"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
