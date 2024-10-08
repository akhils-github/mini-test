<?php
$pageTitle = "Admin Login";
ob_start(); // Start output buffering
$content = ob_get_clean(); // Get the buffered content
include('../common/sidebar.php');
session_start();
include('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];
    $is_sold = isset($_POST['is_sold']) ? 1 : 0;

    // Handle image upload
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create directory if it doesn't exist
    }

     // Function to generate a unique filename
     function generateUniqueFilename($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $uniqueName = $basename . '_' . time() . '.' . $ext;
        return $uniqueName;
    }

     // Handle main image
     $mainImageName = generateUniqueFilename($_FILES['image']['name']);
     $mainImagePath = $uploadDir . $mainImageName;
     if (move_uploaded_file($_FILES['image']['tmp_name'], $mainImagePath)) {
         echo "Main image uploaded successfully.<br>";
     } else {
         echo "Error uploading main image.<br>";
     }
    


    // Handle additional images
    $morePics = [];
    if (!empty($_FILES['morepics']['name'][0])) {
        foreach ($_FILES['morepics']['name'] as $key => $filename) {
            $uniqueName = generateUniqueFilename($filename);
            $filePath = $uploadDir . $uniqueName;
            if (move_uploaded_file($_FILES['morepics']['tmp_name'][$key], $filePath)) {
                $morePics[] = $filePath;
            } else {
                echo "Error uploading additional image: $filename<br>";
            }
        }
    }
    $morePicsJson = json_encode($morePics);

    $sql = "INSERT INTO food_items(name, description, price, image, morepic, quantity, category_id, is_sold) 
            VALUES ('$name', '$description', '$price', '$mainImagePath', '$morePicsJson', '$quantity', '$category_id', '$is_sold')";

    if ($conn->query($sql) === TRUE) {
        echo "New food item created successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>



<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item</title>
</head>
<body>
    <h1>Add New Food Item</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Food Item Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" cols="50"></textarea><br><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="image">Main Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <label for="morepics">Additional Images:</label>
        <input type="file" id="morepics" name="morepics[]" multiple><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>

        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <?php
            // Fetch categories
            $result = $conn->query("SELECT id, name FROM categories");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="is_sold">Sold:</label>
        <input type="checkbox" id="is_sold" name="is_sold" value="1"><br><br>

        <input type="submit" value="Add Food Item">
    </form>
</body>
</html> -->
