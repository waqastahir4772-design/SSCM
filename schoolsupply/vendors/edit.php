<?php
session_start();
$base_url = '../';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('../login.php');
}

check_role(['Admin', 'Manager']);

if (!isset($_GET['id'])) {
    redirect('index.php', 'Invalid request.', 'danger');
}

$id = escape($_GET['id']);
$vendor_res = $conn->query("SELECT * FROM vendors WHERE id = $id");
if ($vendor_res->num_rows == 0) {
    redirect('index.php', 'Vendor not found.', 'danger');
}
$vendor_data = $vendor_res->fetch_assoc();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_vendor'])) {
    $code = escape($_POST['code']);
    $name = escape($_POST['name']);
    $contact_person = escape($_POST['contact_person']);
    $phone = escape($_POST['phone']);
    $email = escape($_POST['email']);
    $address = escape($_POST['address']);
    $city = escape($_POST['city']);
    $status = escape($_POST['status']);

    if (empty($code) || empty($name)) {
        $error = "Code and Name are required.";
    } else {
        $sql = "UPDATE vendors SET 
                code = '$code', 
                name = '$name', 
                contact_person = '$contact_person', 
                phone = '$phone', 
                email = '$email', 
                address = '$address', 
                city = '$city', 
                status = '$status' 
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            redirect('index.php', "Vendor '$name' updated successfully!");
        } else {
            $error = "Error updating vendor: " . $conn->error;
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
          <h1>Edit Vendor</h1>
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
          <h3 class="card-title">Vendor Information</h3>
        </div>
        <form action="" method="post">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Vendor Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control" required value="<?php echo h($vendor_data['name']); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Vendor Code <span class="text-danger">*</span></label>
                  <input type="text" name="code" class="form-control" required value="<?php echo h($vendor_data['code']); ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Contact Person</label>
                  <input type="text" name="contact_person" class="form-control" value="<?php echo h($vendor_data['contact_person']); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Phone Number</label>
                  <input type="text" name="phone" class="form-control" value="<?php echo h($vendor_data['phone']); ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" name="email" class="form-control" value="<?php echo h($vendor_data['email']); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>City</label>
                  <input type="text" name="city" class="form-control" value="<?php echo h($vendor_data['city']); ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Address</label>
                  <textarea name="address" class="form-control" rows="2"><?php echo h($vendor_data['address']); ?></textarea>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="Active" <?php echo $vendor_data['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $vendor_data['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" name="update_vendor" class="btn btn-warning">Update Vendor</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php require_once '../includes/footer.php'; ?>
