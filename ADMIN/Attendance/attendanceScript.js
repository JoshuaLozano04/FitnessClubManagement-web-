$(document).ready(function() {
    $("#attendance-date").on("change", function() {
        let selectedDate = $(this).val();
        
        $.ajax({
            url: "Attendance/fetch_attendance.php",
            type: "POST",
            data: { date: selectedDate },
            success: function(response) {
                $("#attendance-table-body").html(response);
            }
        });
    });
});

function searchTrainee() {
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll(".trainee-row");

    rows.forEach(row => {
        let name = row.querySelector(".trainee-name").textContent.toLowerCase();
        row.style.display = name.includes(input) ? "" : "none";
    });
}

$(document).ready(function() {
    $(document).on("click", ".view-btn", function() {
        let traineeId = $(this).data("id");

        $.ajax({
            url: "Attendance/fetch_attendance_details.php",
            type: "POST",
            data: { id: traineeId },
            success: function(response) {
                let data = JSON.parse(response);
                $("#trainee-name").text(data.trainee_name);
                $("#checkin-time").text(data.checkin_time);
                $("#checkout-time").text(data.checkout_time ? data.checkout_time : "Not Checked Out");

                if (data.checkout_time) {
                    let checkin = new Date(`2024-01-01T${data.checkin_time}`);
                    let checkout = new Date(`2024-01-01T${data.checkout_time}`);
                    let diffMs = checkout - checkin;
                    let hours = Math.floor(diffMs / (1000 * 60 * 60));
                    let minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                    $("#total-hours").text(`${hours} hrs ${minutes} mins`);
                } else {
                    $("#total-hours").text("N/A");
                }

                $("#detailsModal").fadeIn();
            }
        });
    });

    $(".close").on("click", function() {
        $("#detailsModal").fadeOut();
    });
});
