<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

check_role(['Admin', 'Manager']);

$type = 'Supplier';

// Handle Add Partner
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_partner'])) {
    $name = escape($_POST['name']);
    $code = escape($_POST['code']);
    $contact = escape($_POST['contact_person'] ?? '');
    $phone = escape($_POST['phone'] ?? '');
    $email = escape($_POST['email'] ?? '');
    $material = escape($_POST['material_type'] ?? '');
    
    $sql = "INSERT INTO scm_partners (name, code, type, contact_person, phone, email, material_type, status) 
            VALUES ('$name', '$code', '$type', '$contact', '$phone', '$email', '$material', 'Active')";
    
    if ($conn->query($sql)) {
        redirect('suppliers.php', "$type added successfully!");
    } else {
        $error = "Error: " . $conn->error;
    }
}

$partners = $conn->query("SELECT * FROM scm_partners WHERE type = '$type' ORDER BY name ASC");

require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><?php echo $type; ?>s Management</h1>
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPartnerModal">
            <i class="fas fa-plus"></i> Add New <?php echo $type; ?>
          </button>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Material Type</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($partners && $partners->num_rows > 0): ?>
                <?php while($p = $partners->fetch_assoc()): ?>
                <tr>
                  <td><?php echo h($p['code']); ?></td>
                  <td><?php echo h($p['name']); ?></td>
                  <td><?php echo h($p['contact_person']); ?></td>
                  <td><?php echo h($p['phone']); ?></td>
                  <td><?php echo h($p['email']); ?></td>
                  <td><?php echo h($p['material_type']); ?></td>
                  <td><span class="badge badge-<?php echo get_status_badge($p['status']); ?>"><?php echo h($p['status']); ?></span></td>
                  <td>
                    <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="8" class="text-center">No <?php echo strtolower($type); ?>s found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Simple Add Modal Placeholder -->
<div class="modal fade" id="addPartnerModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" method="post">
        <div class="modal-header">
          <h4 class="modal-title">Add New <?php echo $type; ?></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Code</label>
            <input type="text" name="code" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Contact Person</label>
            <input type="text" name="contact_person" class="form-control">
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="form-group">
            <label>Material Type</label>
            <input type="text" name="material_type" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" name="save_partner" class="btn btn-primary">Save <?php echo $type; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
