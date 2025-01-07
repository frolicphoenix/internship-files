$(document).ready(function() {
    // initialize DataTables on the element 'mockTable'
    $('#mockTable').DataTable({
        "ajax": {
            "url": "get_data.php",
            "dataSrc": ""
        },
        // define columns on the table
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
        // initial sorting order
        "order": [[0, "desc"]],
        // number of rows per page
        "pageLength": 50
    });
});
