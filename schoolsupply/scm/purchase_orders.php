<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

check_role(['Admin', 'Manager']);

$user_id = $_SESSION['user_id'];

// Handle PO submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $request_id = escape($_POST['request_id']);
    $partner_id = escape($_POST['partner_id']);
    $order_date = escape($_POST['order_date']);
    $expected_delivery = escape($_POST['expected_delivery_date']);
    $po_no = "PO-" . date('Ymd') . "-" . rand(100, 999);

    $sql = "INSERT INTO purchase_orders (po_no, request_id, partner_id, order_date, expected_delivery_date, status, created_by) 
            VALUES ('$po_no', $request_id, $partner_id, '$order_date', '$expected_delivery', 'Ordered', $user_id)";
    
    if ($conn->query($sql)) {
        $po_id = $conn->insert_id;
        
        // Fetch request details for flow
        $req_res = $conn->query("SELECT pr.*, i.unit_price FROM purchase_requests pr JOIN items i ON pr.item_id = i.id WHERE pr.id = $request_id");
        if ($req_res && $req_res->num_rows > 0) {
            $req = $req_res->fetch_assoc();
            $item_id = $req['item_id'];
            $total_cost = $req['quantity'] * $req['unit_price'];

            // 1. Initial Flow Record (Supplier Stage - Order Placed)
            $conn->query("INSERT INTO supply_chain_flow (item_id, partner_id, po_id, stage, status, cost) 
                         VALUES ($item_id, $partner_id, $po_id, 'Supplier', 'Order Placed', $total_cost)");
        }

        // Update request status to 'Completed'
        $conn->query("UPDATE purchase_requests SET status = 'Completed' WHERE id = $request_id");
        redirect('purchase_orders.php', "Purchase Order $po_no placed successfully and tracking started!");
    }
}

// Handle Receiving Stock
if (isset($_GET['receive'])) {
    $id = escape($_GET['receive']);
    
    // 1. Get PO details
    $po_res = $conn->query("SELECT po.*, pr.item_id, pr.quantity, i.unit_price 
                           FROM purchase_orders po 
                           JOIN purchase_requests pr ON po.request_id = pr.id 
                           JOIN items i ON pr.item_id = i.id
                           WHERE po.id = $id");
    if ($po_res && $po_res->num_rows > 0) {
        $po = $po_res->fetch_assoc();
        $item_id = $po['item_id'];
        $qty = $po['quantity'];

        // 2. Update Item Stock
        $conn->query("UPDATE items SET current_stock = current_stock + $qty WHERE id = $item_id");

        // 3. Log Stock Transaction
        $conn->query("INSERT INTO stock_transactions (item_id, transaction_type, quantity, reference_id, reference_table, performed_by, notes) 
                     VALUES ($item_id, 'IN', $qty, $id, 'purchase_orders', $user_id, 'Stock received from PO " . $po['po_no'] . "')");

        // 4. Update PO Status
        $conn->query("UPDATE purchase_orders SET status = 'Received' WHERE id = $id");

        // 5. Final Flow Record (School Stage - Inventory Updated)
        $total_cost = $qty * $po['unit_price'];
        $partner_id = $po['partner_id'];
        $conn->query("INSERT INTO supply_chain_flow (item_id, partner_id, po_id, stage, status, cost) 
                     VALUES ($item_id, $partner_id, $id, 'School', 'Inventory Updated', $total_cost)");
        
        redirect('purchase_orders.php', "Stock received, inventory updated, and flow tracked!");
    }
}

// Fetch POs
$orders = $conn->query("
    SELECT po.*, sp.name as partner_name, i.name as item_name, pr.quantity, pr.request_no 
    FROM purchase_orders po 
    JOIN scm_partners sp ON po.partner_id = sp.id 
    JOIN purchase_requests pr ON po.request_id = pr.id 
    JOIN items i ON pr.item_id = i.id 
    ORDER BY po.created_at DESC
");

// For add modal
$approved_requests = $conn->query("
    SELECT pr.*, i.name as item_name 
    FROM purchase_requests pr 
    JOIN items i ON pr.item_id = i.id 
    WHERE pr.status = 'Approved' 
    ORDER BY pr.created_at DESC
");
$partners = $conn->query("SELECT id, name, type FROM scm_partners WHERE status = 'Active' ORDER BY name ASC");

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Purchase Orders</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPOModal">
            <i class="fas fa-plus"></i> Place New Order
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <?php echo h($_GET['msg']); ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>PO No</th>
                <th>Request</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Partner</th>
                <th>Exp. Delivery</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders && $orders->num_rows > 0): ?>
                <?php while($o = $orders->fetch_assoc()): ?>
                <tr>
                  <td><?php echo h($o['po_no']); ?></td>
                  <td><?php echo h($o['request_no']); ?></td>
                  <td><?php echo h($o['item_name']); ?></td>
                  <td><?php echo $o['quantity']; ?></td>
                  <td><?php echo h($o['partner_name']); ?></td>
                  <td><?php echo $o['expected_delivery_date']; ?></td>
                  <td>
                    <span class="badge badge-<?php echo get_status_badge($o['status']); ?>">
                      <?php echo h($o['status']); ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($o['status'] == 'Ordered' || $o['status'] == 'In Transit'): ?>
                        <a href="?receive=<?php echo $o['id']; ?>" class="btn btn-xs btn-success" onclick="return confirm('Confirm stock receipt?')"><i class="fas fa-box-open"></i> Receive Stock</a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="8" class="text-center">No orders found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="addPOModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="post">
        <div class="modal-header">
          <h4 class="modal-title">Place Purchase Order</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Approved Request <span class="text-danger">*</span></label>
            <select name="request_id" class="form-control" required>
                <option value="">Select Request</option>
                <?php while($ar = $approved_requests->fetch_assoc()): ?>
                    <option value="<?php echo $ar['id']; ?>"><?php echo h($ar['request_no']); ?> - <?php echo h($ar['item_name']); ?> (Qty: <?php echo $ar['quantity']; ?>)</option>
                <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>SCM Partner <span class="text-danger">*</span></label>
            <select name="partner_id" class="form-control" required>
                <option value="">Select Partner</option>
                <?php while($p = $partners->fetch_assoc()): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo h($p['name']); ?> (<?php echo h($p['type']); ?>)</option>
                <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Order Date <span class="text-danger">*</span></label>
            <input type="date" name="order_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-group">
            <label>Expected Delivery Date</label>
            <input type="date" name="expected_delivery_date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
