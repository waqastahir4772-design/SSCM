<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

$where = "i.deleted_at IS NULL";
if (isset($_GET['filter']) && $_GET['filter'] == 'low_stock') {
    $where .= " AND i.current_stock <= i.reorder_level";
}
if (!empty($_GET['q'])) {
    $q = escape($_GET['q']);
    $where .= " AND (i.name LIKE '%$q%' OR i.code LIKE '%$q%' OR i.location LIKE '%$q%')";
}

$query = "SELECT i.*, ic.name as category_name 
          FROM items i 
          LEFT JOIN item_categories ic ON i.category_id = ic.id 
          WHERE $where 
          ORDER BY i.created_at DESC";
$items = $conn->query($query);

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Items Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Item</a>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['type'] ?? 'success'; ?> alert-dismissible fade show" role="alert">
          <?php echo h($_GET['msg']); ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Items List</h3>
          <div class="card-tools">
            <form action="" method="get" class="form-inline">
              <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Search items..." value="<?php echo h($_GET['q'] ?? ''); ?>">
              <select name="filter" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">Status: All</option>
                <option value="low_stock" <?php echo (isset($_GET['filter']) && $_GET['filter'] == 'low_stock') ? 'selected' : ''; ?>>Low Stock</option>
              </select>
              <button type="submit" class="btn btn-sm btn-default"><i class="fas fa-search"></i></button>
            </form>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Unit Price</th>
                <th>Stock</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($items && $items->num_rows > 0): ?>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                  <td><?php echo h($item['code']); ?></td>
                  <td><?php echo h($item['name']); ?></td>
                  <td><?php echo h($item['category_name']); ?></td>
                  <td><?php echo format_currency($item['unit_price']); ?></td>
                  <td><?php echo $item['current_stock']; ?></td>
                  <td><?php echo h($item['location']); ?></td>
                  <td>
                    <?php 
                    $status_label = $item['current_stock'] <= $item['reorder_level'] ? 'Low Stock' : 'In Stock';
                    ?>
                    <span class="badge badge-<?php echo get_status_badge($status_label); ?>">
                      <?php echo $status_label; ?>
                    </span>
                  </td>
                  <td>
                    <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                    <?php if ($_SESSION['role'] == 'Admin'): ?>
                    <a href="delete.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this item?')"><i class="fas fa-trash"></i></a>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center">No items found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
