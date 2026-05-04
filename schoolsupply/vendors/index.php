<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

// Manager and Admin only
check_role(['Admin', 'Manager']);

$vendors = $conn->query("SELECT * FROM vendors ORDER BY name ASC");

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Vendors Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Vendor</a>
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
        <div class="card-header">
          <h3 class="card-title">Direct Vendors List</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>City</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($vendors && $vendors->num_rows > 0): ?>
                <?php while($vendor = $vendors->fetch_assoc()): ?>
                <tr>
                  <td><?php echo h($vendor['code']); ?></td>
                  <td><?php echo h($vendor['name']); ?></td>
                  <td><?php echo h($vendor['contact_person']); ?></td>
                  <td><?php echo h($vendor['phone']); ?></td>
                  <td><?php echo h($vendor['email']); ?></td>
                  <td><?php echo h($vendor['city']); ?></td>
                  <td>
                    <span class="badge badge-<?php echo get_status_badge($vendor['status']); ?>">
                      <?php echo h($vendor['status']); ?>
                    </span>
                  </td>
                  <td>
                    <a href="edit.php?id=<?php echo $vendor['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center">No vendors found.</td>
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
