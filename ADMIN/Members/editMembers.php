<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $membership_start = $_POST['membership_start'];
    $membership_end = $_POST['membership_end'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET fullname = ?, membership_start = ?, membership_end = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $fullname, $membership_start, $membership_end, $status, $id);

    if ($stmt->execute()) {
        header("Location: /PumpingIronGym/ADMIN/index.php?page=Members/members");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" href="editMemberStyle.css">
</head>
<body>
    <div class="edit-member-content">
        <form class="edit-member-form" method="POST" action="/PumpingIronGym/ADMIN/index.php?page=Members/editMembers">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <table>
                <tr>
                    <td><label for="fullname">Name:</label></td>
                    <td><input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="membership_start">Membership Start:</label></td>
                    <td><input type="date" name="membership_start" value="<?php echo $user['membership_start']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="membership_end">Membership End:</label></td>
                    <td><input type="date" name="membership_end" value="<?php echo $user['membership_end']; ?>" required></td>
                </tr>
                <tr>
                    <td><label for="status">Status:</label></td>
                    <td>
                        <select name="status" required>
                            <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><button type="submit">Save Changes</button></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>

<?php
mysqli_close($conn);
?>