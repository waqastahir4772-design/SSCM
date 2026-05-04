<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    $item_id = escape($_POST['item_id']);
    $quantity = escape($_POST['quantity']);
    $description = escape($_POST['description']);
    $dept_id = $_SESSION['department_id'];
    $req_no = "REQ-" . date('Ymd') . "-" . rand(100, 999);

    $sql = "INSERT INTO purchase_requests (request_no, department_id, item_id, quantity, description, requested_by, status) 
            VALUES ('$req_no', $dept_id, $item_id, $quantity, '$description', $user_id, 'Pending')";
    
    if ($conn->query($sql)) {
        redirect('purchase_requests.php', "Request $req_no submitted successfully!");
    }
}

// Handle approvals (Managers/Admins)
if (isset($_GET['approve']) && ($role == 'Admin' || $role == 'Manager')) {
    $id = escape($_GET['approve']);
    $conn->query("UPDATE purchase_requests SET status = 'Approved', approved_by = $user_id, approved_at = NOW() WHERE id = $id");
    redirect('purchase_requests.php', "Request approved!");
}

// Handle rejections (Managers/Admins)
if (isset($_GET['reject']) && ($role == 'Admin' || $role == 'Manager')) {
    $id = escape($_GET['reject']);
    $conn->query("UPDATE purchase_requests SET status = 'Rejected', approved_by = $user_id WHERE id = $id");
    redirect('purchase_requests.php', "Request rejected.", "danger");
}

// Fetch requests
$query = "SELECT pr.*, i.name as item_name, d.name as dept_name, u.full_name as requester_name 
          FROM purchase_requests pr 
          JOIN items i ON pr.item_id = i.id 
          JOIN departments d ON pr.department_id = d.id 
          JOIN users u ON pr.requested_by = u.id ";

if ($role == 'User') {
    $dept_id = $_SESSION['department_id'];
    $query .= " WHERE pr.department_id = $dept_id ";
}
$query .= " ORDER BY pr.created_at DESC";
$requests = $conn->query($query);

// For the add modal
$items = $conn->query("SELECT id, name, current_stock FROM items WHERE deleted_at IS NULL ORDER BY name ASC");

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Purchase Requests</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRequestModal">
            <i class="fas fa-plus"></i> New Request
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['type'] ?? 'success'; ?> alert-dismissible fade show">
          <?php echo h($_GET['msg']); ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>Req No</th>
                <th>Dept</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Requester</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($requests && $requests->num_rows > 0): ?>
                <?php while($r = $requests->fetch_assoc()): ?>
                <tr>
                  <td><?php echo h($r['request_no']); ?></td>
                  <td><?php echo h($r['dept_name']); ?></td>
                  <td><?php echo h($r['item_name']); ?></td>
                  <td><?php echo $r['quantity']; ?></td>
                  <td><?php echo h($r['requester_name']); ?></td>
                  <td>
                    <span class="badge badge-<?php echo get_status_badge($r['status']); ?>">
                      <?php echo h($r['status']); ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($r['status'] == 'Pending' && ($role == 'Admin' || $role == 'Manager')): ?>
                      <a href="?approve=<?php echo $r['id']; ?>" class="btn btn-xs btn-success" onclick="return confirm('Approve this request?')"><i class="fas fa-check"></i> Approve</a>
                      <a href="?reject=<?php echo $r['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Reject this request?')"><i class="fas fa-times"></i> Reject</a>
                    <?php elseif ($r['status'] == 'Approved' && ($role == 'Admin' || $role == 'Manager')): ?>
                        <a href="purchase_orders.php?request_id=<?php echo $r['id']; ?>" class="btn btn-xs btn-primary"><i class="fas fa-file-invoice"></i> Create PO</a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center">No requests found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="addRequestModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="post">
        <div class="modal-header">
          <h4 class="modal-title">Create Purchase Request</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Item <span class="text-danger">*</span></label>
            <select name="item_id" class="form-control" required>
                <option value="">Select Item</option>
                <?php while($i = $items->fetch_assoc()): ?>
                    <option value="<?php echo $i['id']; ?>"><?php echo h($i['name']); ?> (In Stock: <?php echo $i['current_stock']; ?>)</option>
                <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" required min="1">
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Explain why this is needed..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="submit_request" class="btn btn-primary">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
