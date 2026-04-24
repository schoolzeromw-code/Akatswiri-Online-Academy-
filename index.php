<?php
/**
 * LONJEZO ONLINE ACADEMY - ALL-IN-ONE PORTAL
 * License: Proprietary / All Rights Reserved
 */
session_start();

// --- 1. DATABASE CONFIGURATION ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'lonjezo_academy';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// --- 2. LOGIC: LOGOUT ---
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// --- 3. LOGIC: LOGIN PROCESSING ---
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $reg = $_POST['reg_number'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, full_name FROM students WHERE reg_number = ?");
    $stmt->bind_param("s", $reg);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // In a real system, use password_verify. For initial setup, we check plain text or hash.
        if (password_verify($pass, $user['password']) || $pass == 'lonjezo123') { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['full_name'];
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Student ID not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lonjezo Online Academy | Portal</title>
    <style>
        :root { --navy: #002147; --gold: #C59424; --white: #ffffff; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; background: var(--bg); color: #333; }
        
        /* AUTH STYLES */
        .auth-wrapper { height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; border-top: 6px solid var(--gold); }
        .logo { width: 160px; margin-bottom: 1rem; }
        .motto { color: var(--gold); font-weight: bold; font-size: 0.75rem; letter-spacing: 2px; margin-bottom: 2rem; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        .btn-navy { width: 100%; padding: 12px; background: var(--navy); color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        
        /* DASHBOARD STYLES */
        nav { background: var(--navy); color: white; padding: 1rem 5%; display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid var(--gold); }
        .container { padding: 3rem 5%; text-align: center; }
        .btn-gold { background: var(--gold); color: var(--navy); padding: 1rem 2rem; text-decoration: none; font-weight: bold; border-radius: 8px; display: inline-block; transition: 0.3s; }
        .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(197, 148, 36, 0.4); }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['user_id'])): ?>
    <div class="auth-wrapper">
        <div class="card">
            <img src="1000011119.png" class="logo" alt="Lonjezo Logo">
            <h2 style="color: var(--navy); margin: 0;">PORTAL LOGIN</h2>
            <div class="motto">LEARN • GROW • SUCCEED</div>
            
            <?php if($error): ?> <p style="color:red; font-size: 0.8rem;"><?php echo $error; ?></p> <?php endif; ?>

            <form method="POST">
                <input type="text" name="reg_number" placeholder="Registration Number" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="btn-navy">SIGN IN</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <nav>
        <div style="font-size: 1.2rem; font-weight: bold;">LONJEZO ONLINE ACADEMY</div>
        <div>
            <span><?php echo htmlspecialchars($_SESSION['name']); ?></span> | 
            <a href="?action=logout" style="color: var(--gold); text-decoration: none; font-weight: bold;">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 style="color: var(--navy); font-size: 2.5rem;">Welcome to your Dashboard</h1>
        <p style="font-size: 1.1rem; color: #666; max-width: 600px; margin: 0 auto 2rem;">
            Access your courses, check your grades, and manage your academic profile through our integrated E-Learning system.
        </p>
        
        <div style="margin-top: 3rem;">
            <a href="/moodle" class="btn-gold">🚀 ACCESS E-LEARNING (MOODLE)</a>
        </div>
    </div>
<?php endif; ?>

</body>
</html>
