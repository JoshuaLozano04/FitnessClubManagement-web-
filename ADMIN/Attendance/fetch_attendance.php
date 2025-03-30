<?php
include 'database.php';

if (isset($_POST['date'])) {
    $date = $_POST['date'];

    $trainee_query = "SELECT * FROM attendance WHERE checkin_date = '$date' ORDER BY checkin_time ASC";
    $trainee_result = mysqli_query($conn, $trainee_query);

    if (mysqli_num_rows($trainee_result) > 0) {
        while ($row = mysqli_fetch_assoc($trainee_result)) {
            echo "<tr class='trainee-row'>
                    <td>
                        <img src='../storage/profiles/" . $row['trainee_image'] . "' alt='Trainee Image'>
                        <span class='trainee-name'>" . htmlspecialchars($row['trainee_name']) . "</span>
                    </td>
                    <td>" . date('F d, Y', strtotime($row['checkin_date'])) . "</td>
                    <td>" . date('h:i A', strtotime($row['checkin_time'])) . "</td>
                    <td>";

            if (!empty($row['checkout_time'])) {
                echo date('h:i A', strtotime($row['checkout_time']));
            } else {
                echo "<span class='not-checked-out'>Not Checked Out</span>";
            }
            
            echo "</td>
                  <td><button class='view-btn'>View</button></td>
                </tr>";
        }
    } else {
        echo "<tr>
                <td colspan='5' class='no-records'>No records found for this date</td>
              </tr>";
    }
}
?>
