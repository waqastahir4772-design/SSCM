<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('index.php', 'Invalid request.', 'danger');
}

$id = escape($_GET['id']);
$item_res = $conn->query("SELECT * FROM items WHERE id = $id AND deleted_at IS NULL");
if ($item_res->num_rows == 0) {
    redirect('index.php', 'Item not found.', 'danger');
}
$item_data = $item_res->fetch_assoc();

$error = '';
$categories = $conn->query("SELECT * FROM item_categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_item'])) {
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
        $error = "Please fill in all required fields.";
    } else {
        // Check for unique code excluding current id
        $check = $conn->query("SELECT id FROM items WHERE code = '$code' AND id != $id AND deleted_at IS NULL");
        if ($check->num_rows > 0) {
            $error = "Item code already exists.";
        } else {
            $sql = "UPDATE items SET 
                    code = '$code', 
                    name = '$name', 
                    category_id = $category_id, 
                    unit_type = '$unit_type', 
                    unit_price = $unit_price, 
                    current_stock = $current_stock, 
                    reorder_level = $reorder_level, 
                    location = '$location', 
                    description = '$description' 
                    WHERE id = $id";
            
            if ($conn->query($sql)) {
                redirect('index.php', "Item '$name' updated successfully!");
            } else {
                $error = "Error updating item: " . $conn->error;
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
          <h1>Edit Item</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo h($error); ?></div>
      <?php endif; ?>

      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">Item Information</h3>
        </div>
        <form action="" method="post">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Item Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control" required value="<?php echo h($item_data['name']); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Item Code <span class="text-danger">*</span></label>
                  <input type="text" name="code" class="form-control" required value="<?php echo h($item_data['code']); ?>">
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
                      <option value="<?php echo $cat['id']; ?>" <?php echo $item_data['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo h($cat['name']); ?>
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Unit Type</label>
                  <select name="unit_type" class="form-control">
                    <option value="Pcs" <?php echo $item_data['unit_type'] == 'Pcs' ? 'selected' : ''; ?>>Pcs</option>
                    <option value="Boxes" <?php echo $item_data['unit_type'] == 'Boxes' ? 'selected' : ''; ?>>Boxes</option>
                    <option value="Meters" <?php echo $item_data['unit_type'] == 'Meters' ? 'selected' : ''; ?>>Meters</option>
                    <option value="Packets" <?php echo $item_data['unit_type'] == 'Packets' ? 'selected' : ''; ?>>Packets</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Unit Price</label>
                  <input type="number" step="0.01" name="unit_price" class="form-control" value="<?php echo $item_data['unit_price']; ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Current Stock</label>
                  <input type="number" name="current_stock" class="form-control" value="<?php echo $item_data['current_stock']; ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Reorder Level</label>
                  <input type="number" name="reorder_level" class="form-control" value="<?php echo $item_data['reorder_level']; ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Storage Location</label>
                  <input type="text" name="location" class="form-control" value="<?php echo h($item_data['location']); ?>">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="form-control" rows="3"><?php echo h($item_data['description']); ?></textarea>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="update_item" class="btn btn-warning">Update Item</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
