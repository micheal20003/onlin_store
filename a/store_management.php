<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'fitness_app';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Store Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        img {
            max-width: 50px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4" style="font-weight: bold;">Store Management</h2>
        <form method="POST" enctype="multipart/form-data" class="border p-4 mb-4 rounded">
            <h4>Add New Product</h4>
            <div class="mb-3">
                <label class="form-label">Product Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price:</label>
                <input type="number" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Image:</label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        </form>

        <?php
        if (isset($_POST['add_product'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $image = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];

            $upload_directory = "img/";
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            $image_path = $upload_directory . basename($image);

            if (move_uploaded_file($image_tmp, $image_path)) {
                $sql = "INSERT INTO products (name, price, image) VALUES ('$name', '$price', '$image_path')";
                if ($conn->query($sql)) {
                    echo "<div class='alert alert-success'>Product added successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Failed to upload the image!</div>";
            }
        }

        if (isset($_GET['delete'])) {
            $id = $_GET['delete'];
            $conn->query("DELETE FROM products WHERE id=$id");
            echo "<div class='alert alert-danger'>Product deleted!</div>";
        }
        ?>

        <table class="table table-striped">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM products");
            while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src='<?php echo $row['image']; ?>' alt="Product Image"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td>
                        <a href='?delete=<?php echo $row['id']; ?>' class='btn btn-danger btn-sm' onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        <button
                            class='btn btn-primary btn-sm'
                            onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', '<?php echo $row['price']; ?>')"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal">Edit</button>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">New Name:</label>
                        <input type="text" name="edit_name" id="edit_name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Price:</label>
                        <input type="number" name="edit_price" id="edit_price" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Image:</label>
                        <input type="file" name="edit_image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_product" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_POST['update_product'])) {
        $id = $_POST['edit_id'];
        $name = $_POST['edit_name'];
        $price = $_POST['edit_price'];
        $image = $_FILES['edit_image']['name'];

        $update_query = "UPDATE products SET ";
        $updates = [];

        if (!empty($name)) $updates[] = "name='$name'";
        if (!empty($price)) $updates[] = "price='$price'";

        if (!empty($image)) {
            $image_tmp = $_FILES['edit_image']['tmp_name'];
            $image_path = "img/" . basename($image);
            move_uploaded_file($image_tmp, $image_path);
            $updates[] = "image='$image_path'";
        }

        if (!empty($updates)) {
            $update_query .= implode(", ", $updates);
            $update_query .= " WHERE id=$id";

            if ($conn->query($update_query)) {
                echo "<div class='alert alert-success'>Product updated successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating product: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>No changes made.</div>";
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(id, name, price) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
        }
    </script>
</body>

</html>