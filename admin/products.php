<?php
$pageTitle = "User Details";
ob_start(); // Start output buffering
$content = ob_get_clean(); // Get the buffered content
include('../index.php'); 


session_start();
include('../config/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
}

$id = $_GET['id'];
if($id ){
$result = $conn->query("SELECT image, morepic FROM food_items WHERE id = $id");
$foodItem = $result->fetch_assoc();
if ($foodItem) {
    // Delete main image file if it exists
    if ($foodItem['image'] && file_exists($foodItem['image'])) {
        unlink($foodItem['image']);
    }

       // Delete additional images if they exist
       if ($foodItem['morepic']) {
        $morePics = json_decode($foodItem['morepic'], true);
        foreach ($morePics as $pic) {
            if (file_exists($pic)) {
                unlink($pic);
            }
        }
    }
     // Prepare and execute deletion query
     $stmt = $conn->prepare("DELETE FROM food_items WHERE id = ?");
     $stmt->bind_param("i", $id);
 
     if ($stmt->execute()) {
         echo "Food item deleted successfully. <a href='food_items_list.php'>Back to Food Items List</a>";
     } else {
         echo "Error: " . $stmt->error;
     }
 
     $stmt->close();
 } else {
     echo "Food item not found.";
}
}


// Fetch food items
$result = $conn->query("SELECT food_items.id, food_items.name, food_items.description, food_items.price, food_items.image, food_items.morepic, categories.name AS category_name
                        FROM food_items
                        LEFT JOIN categories ON food_items.category_id = categories.id");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Items List</title>
</head>
<style>
        .food-item img {
            max-width: 150px;
            max-height: 150px;
            display: block;
            margin: 10px 0;
        }
        .food-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .food-item .morepics img {
            max-width: 100px;
            max-height: 100px;
            margin: 5px;
        }
    </style>
<body>
    <h1>Food Items List</h1>
    <a href="add_food_item.php">Add New Food Item</a><br><br>
    <table >
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Category</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><!-- Display main image -->
            <?php if ($row['image']): ?>
                <img width=100 src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <?php endif; ?></td>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['price']); ?></td>
            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
            <td>

                <a href="edit_food_item.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                <a  href="#?id=<?php echo $row['id']; ?> "onclick="return confirm('Are you sure you want to delete this food item?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>