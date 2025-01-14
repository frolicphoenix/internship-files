$(document).ready(function() {
    // Fetch admin type
    $.ajax({
        url: 'get_admin_type.php',
        method: 'GET',
        success: function(response) {
            $('#adminType').text(response);
        }
    });

    // initialize DataTbale on the table with ID 'mockTable
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
            { "data": "actions", "orderable": false } // orderable=flase means non-sortable
        ],
        "order": [[0, "desc"]],
        "pageLength": 50
    });

    // event listener for update button
    $('#mockTable').on('click', '.update-btn', function() {
        //get the id from button click
        var id = $(this).data('id');
        //find the closest table row that has this button
        var row = $(this).closest('tr');
        //get the data for that row from DataTable
        var data = $('#mockTable').DataTable().row(row).data();

        //populating modal with data
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

    //event listener for delete button
    $('#mockTable').on('click', '.delete-btn', function() {

        var id = $(this).data('id');

        if (confirm('Are you sure you want to delete this record?')) {
            
            $.ajax({
                url: 'delete_data.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Record deleted successfully!');
                        $('#mockTable').DataTable().ajax.reload();
                    } else {
                        alert('Failed to delete record: ' + response.error);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the record.');
                }
            });
        }
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
    $('#updateForm').submit(function(e) {
        e.preventDefault();
    
        var formData = $(this).serialize();

        // send an AJAX request to update the data
        $.ajax({
            url: 'update_data.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Data updated successfully!');
                    $('#updateModal').hide();
                    $('#mockTable').DataTable().ajax.reload();
                } else {
                    alert('Failed to update data: ' + response.error);
                }
            },
            error: function() {
                alert('An error occurred while updating the data.');
            }
        });
    });
    

    $('#updateForm').submit(function(e) {
        e.preventDefault();
        
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
