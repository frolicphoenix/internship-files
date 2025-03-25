<?php
// Include database connection and helper functions
include 'db_connect.php';
include 'functions.php';

// Get total unique signups count from the 'dailyweborders' table
$tsql = 'SELECT COUNT(DISTINCT Email) FROM dailyweborders';
$stmt = $pdo->prepare($tsql);
$stmt->execute();
$totalSignups = $stmt->fetchColumn();

// Retrieve all orders to group by year based on distinct emails
$orderDataSql = "SELECT OrderDate, Email FROM dailyweborders";
$stmt = $pdo->prepare($orderDataSql);
$stmt->execute();

$yearEmails = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Parse the order date using a helper function
    $dateObj = parseOrderDate($row['OrderDate']);
    if ($dateObj) {
        // Extract the year from the date
        $year = $dateObj->format('Y');
        if (!isset($yearEmails[$year])) {
            $yearEmails[$year] = [];
        }
        $yearEmails[$year][$row['Email']] = true;
    }
}

// Convert the grouped data into an array with year and order count
$ordersByYear = [];
foreach ($yearEmails as $year => $emails) {
    $ordersByYear[] = [
        'order_year'  => $year,
        'order_count' => count($emails)
    ];
}
// Sort the array by year in ascending order
usort($ordersByYear, fn($a, $b) => $a['order_year'] - $b['order_year']);

// Check if a specific year is selected via the URL (?year=xxxx)
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <!-- Include Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .card th, .card td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .card th {
            background-color: #f4f4f4;
        }

        .graph-container {
            margin-top: 20px;
            max-width: 1200px;
            width: 100%;
        }
        .graph-container canvas {
            width: 100% !important;
            height: 500px !important;
        }

        .summary-cards {
            display: flex;
        }   
    </style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#">Dashboard</a></li>
            <li><a href="order_list.php">Orders</a></li>
            <li><a href="signup_list.php">Customers</a></li>
        </ul>
    </aside>

    <!-- Main Content section -->
    <main class="main-content">
        <header class="dashboard-header">
            <h1>Welcome to the Dashboard</h1>
        </header>

        <!-- Summary Cards Section -->
        <section class="summary-cards">
            <div class="card">
                <h3>Total Signups</h3>
                <p><?php echo $totalSignups; ?></p>
            </div>
            <div class="card">
                <h3>Signups By Year</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Order Count</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ordersByYear as $data): ?>
                        <tr>
                            <td>
                                <!-- Link to reload the page with a specific year selected -->
                                <a href="?year=<?php echo urlencode($data['order_year']); ?>">
                                    <?php echo htmlspecialchars($data['order_year']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($data['order_count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div>
                <!-- If a year is selected, display monthly and weekly charts -->
                <?php if ($selectedYear): ?>
                    <div class="graph-container">
                        <h3>Monthly Signups for <?php echo htmlspecialchars($selectedYear); ?></h3>
                        <canvas id="monthlyChart"></canvas>
                    </div>

                    <div class="graph-container">
                        <h3>Weekly Signups for <?php echo htmlspecialchars($selectedYear); ?></h3>
                        <canvas id="weeklyChart"></canvas>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>
</div>

<?php if ($selectedYear): ?>
<script>
    // Store the selected year for use in API calls and chart titles
    const selectedYear = "<?php echo htmlspecialchars($selectedYear); ?>";

    // Fetch monthly signup data for the selected year.
    fetch('monthly_data.php?year=' + selectedYear)
        .then(res => res.json())
        .then(monthlyData => {
            // monthlyData should be an array of 12 counts (one per month)
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthLabels = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Monthly Signups',
                        data: monthlyData,
                        borderColor: 'rgba(75,192,192,1)',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: true },
                        title: {
                            display: true,
                            text: 'Monthly Signups for ' + selectedYear
                        }
                    }
                }
            });
        })
        .catch(err => console.error("Error loading monthly data:", err));

    // Fetch weekly signup data for the selected year
    fetch('weekly_data.php?year=' + selectedYear)
        .then(res => res.json())
        .then(weeklyData => {
            // weeklyData should be an array of 53 counts (one for each week)
            const ctx2 = document.getElementById('weeklyChart').getContext('2d');
            // Create labels for weeks "Week 1" through "Week 53"
            const weekLabels = [];
            for (let w = 1; w <= 53; w++) {
                weekLabels.push("Week " + w);
            }
            new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Weekly Signups',
                        data: weeklyData,
                        borderColor: 'rgba(255,99,132,1)',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    },
                    plugins: {
                        legend: { display: true },
                        title: {
                            display: true,
                            text: 'Weekly Signups for ' + selectedYear
                        }
                    }
                }
            });
        })
        .catch(err => console.error("Error loading weekly data:", err));
</script>
<?php endif; ?>
</body>
</html>
