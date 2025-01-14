$(document).ready(function() {
    // Fetch admin type
    $.ajax({
        url: 'get_admin_type.php',
        method: 'GET',
        success: function(response) {
            $('#adminType').text(response);
        }
    });

    $('#mockTable').DataTable({
        "ajax": {
            "url": "get_data.php",
            "dataSrc": ""
        },
        "columns": [
            { "data": "first_name" },
            { "data": "last_name" },
            { "data": "email" },
            { "data": "gender" },
            { "data": "salary" },
            { "data": "position" },
            { "data": "top_size" },
            { "data": "date_started" }
        ],
        "order": [[0, "desc"]],
        "pageLength": 50
    });

    $('#logoutBtn').click(function() {
        $.ajax({
            url: 'logout.php',
            method: 'POST',
            success: function() {
                window.location.href = 'login.php';
            }
        });
    });

});
