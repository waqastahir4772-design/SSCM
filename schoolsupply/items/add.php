<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

$error = '';
$categories = $conn->query("SELECT * FROM item_categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_item'])) {
    $code = escape($_POST['code']);
    $name = escape($_POST['name']);
    $category_id = escape($_POST['category_id']);
    $unit_type = escape($_POST['unit_type']);
    $unit_price = escape($_POST['unit_price']);
    $current_stock = escape($_POST['current_stock']);
    $reorder_level = escape($_POST['reorder_level']);
    $location = escape($_POST['location']);
    $description = escape($_POST['description']);

    if (empty($code) || empty($name) || empty($category_id)) {
        $error = "Please fill in all required fields (Code, Name, Category).";
    } else {
        // Check for unique code
        $check = $conn->query("SELECT id FROM items WHERE code = '$code' AND deleted_at IS NULL");
        if ($check->num_rows > 0) {
            $error = "Item code already exists.";
        } else {
            $sql = "INSERT INTO items (code, name, category_id, unit_type, unit_price, current_stock, reorder_level, location, description) 
                    VALUES ('$code', '$name', $category_id, '$unit_type', $unit_price, $current_stock, $reorder_level, '$location', '$description')";
            
            if ($conn->query($sql)) {
                // Log transaction
                $item_id = $conn->insert_id;
                $performed_by = $_SESSION['user_id'];
                $conn->query("INSERT INTO stock_transactions (item_id, transaction_type, quantity, performed_by, notes) 
                             VALUES ($item_id, 'ADJUSTMENT', $current_stock, $performed_by, 'Initial stock entry')");
                             
                redirect('index.php', "Item '$name' added successfully!");
            } else {
                $error = "Error adding item: " . $conn->error;
            }
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add New Item</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php">Items</a></li>
            <li class="breadcrumb-item active">Add New</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo h($error); ?></div>
      <?php endif; ?>

      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Item Information</h3>
        </div>
        <form action="" method="post">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Item Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control" required placeholder="e.g. Science Textbook Grade 10">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Item Code <span class="text-danger">*</span></label>
                  <input type="text" name="code" class="form-control" required placeholder="e.g. BK-SCI-10">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Category <span class="text-danger">*</span></label>
                  <select name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                      <option value="<?php echo $cat['id']; ?>"><?php echo h($cat['name']); ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Unit Type</label>
                  <select name="unit_type" class="form-control">
                    <option value="Pcs">Pcs</option>
                    <option value="Boxes">Boxes</option>
                    <option value="Meters">Meters</option>
                    <option value="Packets">Packets</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Unit Price</label>
                  <input type="number" step="0.01" name="unit_price" class="form-control" value="0.00">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Current Stock</label>
                  <input type="number" name="current_stock" class="form-control" value="0">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Reorder Level</label>
                  <input type="number" name="reorder_level" class="form-control" value="10">
                  <small class="text-muted">System will alert when stock falls below this level.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Storage Location</label>
                  <input type="text" name="location" class="form-control" placeholder="e.g. Shelf A-102">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="save_item" class="btn btn-primary">Save Item</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
