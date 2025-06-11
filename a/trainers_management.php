<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'fitness_app';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$upload_directory = "trainer/uploads/";
if (!is_dir($upload_directory)) {
    mkdir($upload_directory, 0777, true);
}

// ADD TRAINER
if (isset($_POST['add_trainer'])) {
    $coach_name = $_POST['trainer'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $specialty = $_POST['specialty'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    $image_path = 'uploads/' . basename($image); // Only save relative path


    if (move_uploaded_file($image_tmp, $image_path)) {
        $sql = "INSERT INTO private_coach (coach_name, email, password, phone, specialty, picture)
                VALUES ('$coach_name', '$email', '$password', '$phone', '$specialty', '$image_path')";

        echo $conn->query($sql)
            ? "<div class='alert alert-success'>Trainer added successfully!</div>"
            : "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to upload image!</div>";
    }
}

// UPDATE TRAINER
if (isset($_POST['update_trainer'])) {
    $edit_id = $_POST['edit_id'];
    $coach_name = $_POST['trainer'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $specialty = $_POST['specialty'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    if (!empty($image)) {
        $final_path = $upload_directory . basename($image);
        move_uploaded_file($image_tmp, $final_path);
        $image_path = 'uploads/' . basename($image); // Save only relative path


        $old_img_query = $conn->query("SELECT picture FROM private_coach WHERE id = $edit_id");
        if ($old_img_query && $old_img_query->num_rows > 0) {
            $old_img = $old_img_query->fetch_assoc()['picture'];
            if (file_exists($old_img)) {
                unlink($old_img);
            }
        }
    } else {
        $img_result = $conn->query("SELECT picture FROM private_coach WHERE id = $edit_id");
        $image_path = ($img_result && $img_result->num_rows > 0) ? $img_result->fetch_assoc()['picture'] : '';
    }

    $sql = "UPDATE private_coach 
            SET coach_name='$coach_name', email='$email', password='$password', phone='$phone', specialty='$specialty', picture='$image_path' 
            WHERE id = $edit_id";

    echo $conn->query($sql)
        ? "<div class='alert alert-success'>Trainer updated successfully!</div>"
        : "<div class='alert alert-danger'>Error updating trainer: " . $conn->error . "</div>";
}

// DELETE TRAINER
if (isset($_POST['delete_trainer'])) {
    $delete_id = $_POST['delete_id'];

    $img_query = $conn->query("SELECT picture FROM private_coach WHERE id = $delete_id");
    if ($img_query && $img_query->num_rows > 0) {
        $img = $img_query->fetch_assoc()['picture'];
        if (file_exists($img)) {
            unlink($img);
        }
    }

    $delete_sql = "DELETE FROM private_coach WHERE id = $delete_id";
    echo $conn->query($delete_sql)
        ? "<div class='alert alert-success'>Trainer deleted successfully!</div>"
        : "<div class='alert alert-danger'>Delete failed: " . $conn->error . "</div>";
}
?>

<div class='container mt-4'>
    <h2 class="text-center mb-4" style="font-weight: bold;">Trainer Management</h2>
    <form method='POST' enctype='multipart/form-data' class="border p-4 mb-4 rounded">
        <h5>Add Trainer</h5>
        <div class='mb-3'><label class='form-label'>Trainer Name:</label><input type='text' name='trainer' class='form-control' required></div>
        <div class='mb-3'><label class='form-label'>Email:</label><input type='email' name='email' class='form-control' required></div>
        <div class='mb-3'><label class='form-label'>Password:</label><input type='password' name='password' class='form-control' required></div>
        <div class='mb-3'><label class='form-label'>Phone:</label><input type='text' name='phone' class='form-control' required></div>
        <div class='mb-3'><label class='form-label'>Specialty:</label><input type='text' name='specialty' class='form-control' required></div>
        <div class='mb-3'><label class='form-label'>Image:</label><input type='file' name='image' class='form-control' required></div>
        <button type='submit' name='add_trainer' class='btn btn-success'>Add Trainer</button>
    </form>

    <table class='table table-striped'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Specialty</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM private_coach ORDER BY id DESC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="trainer/<?= $row['picture'] ?>" width="50" alt="Trainer Image"></td>

                        <td><?= $row['coach_name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['specialty'] ?></td>
                        <td>
                            <button class='btn btn-primary btn-sm' onclick="openEditModal(
                        '<?= $row['id'] ?>',
                        '<?= htmlspecialchars($row['coach_name'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['password'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['phone'], ENT_QUOTES) ?>',
                        '<?= htmlspecialchars($row['specialty'], ENT_QUOTES) ?>')"
                                data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='delete_id' value='<?= $row['id'] ?>'>
                                <button type='submit' name='delete_trainer' class='btn btn-danger btn-sm' onclick='return confirm("Are you sure?");'>Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan='7'>No trainers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Trainer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class='mb-3'><label class='form-label'>Trainer Name:</label><input type='text' name='trainer' id='edit_trainer' class='form-control' required></div>
                <div class='mb-3'><label class='form-label'>Email:</label><input type='email' name='email' id='edit_email' class='form-control' required></div>
                <div class='mb-3'><label class='form-label'>Password:</label><input type='password' name='password' id='edit_password' class='form-control' required></div>
                <div class='mb-3'><label class='form-label'>Phone:</label><input type='text' name='phone' id='edit_phone' class='form-control' required></div>
                <div class='mb-3'><label class='form-label'>Specialty:</label><input type='text' name='specialty' id='edit_specialty' class='form-control' required></div>
                <div class='mb-3'><label class='form-label'>Image:</label><input type='file' name='image' class='form-control'></div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="update_trainer" class="btn btn-success">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function openEditModal(id, name, email, password, phone, specialty) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_trainer').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_password').value = password;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_specialty').value = specialty;
    }
</script>