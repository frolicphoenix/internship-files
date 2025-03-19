<?php
session_start();
require_once("includes/db_connect.php");
require_once("includes/countries.php");

if(empty($_SESSION["csrf_token"])) {
    $_SESSION['csrf_token'] = bin2hex((random_bytes(32)));
}

// if(isset($_SESSION['csrf_token'])) {
//     echo "CSRF token exists: " . $_SESSION['csrf_token'];
// } else {
//     echo "CSRF token has not been created yet";
// }

//countries list
$countries = get_countries();

// if(empty($countries)) {
//     $countries = [
//         'US' => 'United States',
//         'CA' => 'Canada',
//         'GB' => 'United Kingdom'
//     ];
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Unsubscribe Form</title>

    <link rel="stylesheet" href="includes/styles.css">
</head>
<body>
<div class="unsubscribe-card">
        <div class="form-header">
            <h1>Privacy Unsubscribe Form</h1>
            <p>Please fill out the form below to update your communication preferences</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success"><?php echo htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="POST" action="includes/process_unsubscribe.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="pub" value="<?php echo htmlspecialchars(strtoupper($_GET['pub'])) ?>">
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" 
                       pattern="[A-Za-z ]+" title="Only letters and spaces allowed"
                       required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name"
                       pattern="[A-Za-z ]+" title="Only letters and spaces allowed"
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country" required>
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $code => $name): ?>
                        <option value="<?php echo htmlspecialchars($code) ?>">
                            <?php echo htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit">Update Preferences</button>
        </form>
        <div id="message"></div>
    </div>
    <script href="includes/script.js"></script>
</body>
</html>
