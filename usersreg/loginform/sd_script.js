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
            { "data": "date_started" },
            { "data": "actions", "orderable": false }
        ],
        "order": [[0, "desc"]],
        "pageLength": 50
    });

    $('#mockTable').on('click', '.update-btn', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        var data = $('#mockTable').DataTable().row(row).data();

        // Populate modal with data
        $('#updateId').val(id);
        $('#updateFirstName').val(data.first_name);
        $('#updateLastName').val(data.last_name);
        $('#updateEmail').val(data.email);
        $('#updateGender').val(data.gender);
        $('#updateSalary').val(data.salary);
        $('#updatePosition').val(data.position);
        $('#updateTopSize').val(data.top_size);
        $('#updateDateStarted').val(data.date_started);

        // Show the modal
        $('#updateModal').show();
    });

    $('.close').click(function() {
        $('#updateModal').hide();
    });

    // Close modal when clicking outside of it
    $(window).click(function(event) {
        if (event.target == $('#updateModal')[0]) {
            $('#updateModal').hide();
        }
    });

    // Handle form submission
    // $('#updateForm').submit(function(e) {
    //     e.preventDefault();
    
    //     var formData = $(this).serialize();
    
    //     $.ajax({
    //         url: 'update_data.php',
    //         method: 'POST',
    //         data: formData,
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.success) {
    //                 alert('Data updated successfully!');
    //                 $('#updateModal').hide();
    //                 $('#mockTable').DataTable().ajax.reload();
    //             } else {
    //                 alert('Failed to update data: ' + response.error);
    //             }
    //         },
    //         error: function() {
    //             alert('An error occurred while updating the data.');
    //         }
    //     });
    // });
    

    $('#updateForm').submit(function(e) {
        e.preventDefault();
        // Here you would typically send an AJAX request to update the data
        console.log('Form submitted with data:', $(this).serialize());
        $('#updateModal').hide();
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
