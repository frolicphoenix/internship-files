<?php
// Include the database connection file.
// Adjust the path if your file structure differs.
require_once 'db_connect.php';

try {
    // Retrieve the id and OrderDate from the dailyweborders table.
    // Records are ordered by id in ascending order, limited to 100 rows.
    $sql = "SELECT id, OrderDate FROM dailyweborders ORDER BY id ASC LIMIT 100";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    // Fetch all records as an associative array.
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there is a database error, output the error message and stop execution.
    echo "Database error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Date Master</title>
</head>
<body>
    <h1>Date Master</h1>
    <!-- Create a table to display the raw and formatted dates -->
    <table border="1" cellpadding="5" cellspacing="0" id="datesTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Raw Date (from DB)</th>
                <th>Formatted Date</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $row): ?>
                <tr>
                    <!-- Output the ID -->
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <!-- Output the raw date from the database -->
                    <td class="raw-date"><?php echo htmlspecialchars($row['OrderDate']); ?></td>
                    <!-- Placeholder for the formatted date -->
                    <td class="formatted-date">--</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Display message if no records were found -->
            <tr>
                <td colspan="3">No records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Helper function to pad single digit numbers with a leading zero.
        function pad(n) {
            return n < 10 ? '0' + n : n;
        }

        // Formats a Date object as 'YYYY-MM-DD'.
        // Returns "Invalid Date" if the Date object is not valid.
        function formatDate(date) {
            if (isNaN(date.getTime())) {
                return "Invalid Date";
            }
            var year = date.getFullYear();
            var month = pad(date.getMonth() + 1);  // Months are 0-based.
            var day = pad(date.getDate());
            return year + '-' + month + '-' + day;
        }

        // Parse a raw date string.
        // If the string is in YYYYMMDD format (8 digits), extract year, month, and day.
        // Otherwise, attempt to create a Date object directly.
        function parseRawDate(rawDateStr) {
            if (/^\d{8}$/.test(rawDateStr)) {
                var year = parseInt(rawDateStr.substring(0, 4), 10);
                var month = parseInt(rawDateStr.substring(4, 6), 10);
                var day = parseInt(rawDateStr.substring(6, 8), 10);
                return new Date(year, month - 1, day);  // Adjust month for 0-index.
            }
            return new Date(rawDateStr);
        }

        // Once the DOM is fully loaded, process each table row to update the formatted date.
        window.addEventListener('DOMContentLoaded', function() {
            var tableRows = document.querySelectorAll('#datesTable tbody tr');
            tableRows.forEach(function(row) {
                var rawDateCell = row.querySelector('.raw-date');
                var formattedDateCell = row.querySelector('.formatted-date');
                var rawDateStr = rawDateCell.textContent.trim();

                // Parse the raw date string and format it.
                var dateObj = parseRawDate(rawDateStr);
                formattedDateCell.textContent = formatDate(dateObj);
            });
        });
    </script>
</body>
</html>
