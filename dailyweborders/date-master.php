<?php
// Include your DB connection file (adjust path as needed)
require_once 'db_connect.php';

try {
    // Retrieve id and raw date (OrderDate) from dailyweborders
    $sql = "SELECT id, OrderDate FROM dailyweborders ORDER BY id ASC LIMIT 100";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
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
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td class="raw-date"><?php echo htmlspecialchars($row['OrderDate']); ?></td>
                    <td class="formatted-date">--</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Helper function to pad numbers with a leading zero if needed
        function pad(n) {
            return n < 10 ? '0' + n : n;
        }

        // Formats a Date object as YYYY-MM-DD; returns "Invalid Date" if not valid
        function formatDate(date) {
            if (isNaN(date.getTime())) {
                return "Invalid Date";
            }
            var year = date.getFullYear();
            var month = pad(date.getMonth() + 1);
            var day = pad(date.getDate());
            return year + '-' + month + '-' + day;
        }

        // Parse the raw date string
        function parseRawDate(rawDateStr) {
            // Check if rawDateStr is in YYYYMMDD format (8 digits)
            if (/^\d{8}$/.test(rawDateStr)) {
                var year = parseInt(rawDateStr.substring(0, 4), 10);
                var month = parseInt(rawDateStr.substring(4, 6), 10);
                var day = parseInt(rawDateStr.substring(6, 8), 10);
                return new Date(year, month - 1, day);  // Construct as local date
            }
            // Otherwise, try using the Date constructor directly
            return new Date(rawDateStr);
        }

        // On page load, update the formatted date column automatically
        window.addEventListener('DOMContentLoaded', function() {
            var tableRows = document.querySelectorAll('#datesTable tbody tr');
            tableRows.forEach(function(row) {
                var rawDateCell = row.querySelector('.raw-date');
                var formattedDateCell = row.querySelector('.formatted-date');
                var rawDateStr = rawDateCell.textContent.trim();

                var dateObj = parseRawDate(rawDateStr);
                formattedDateCell.textContent = formatDate(dateObj);
            });
        });
    </script>
</body>
</html>
