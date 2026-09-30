<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
    <title>AfyaLink | Login to Your Account</title>
</head>

<body>
    <?php

    session_start();

    $_SESSION["user"] = "";
    $_SESSION["usertype"] = "";

    date_default_timezone_set('Africa/Nairobi');
    $date = date('Y-m-d');
    $_SESSION["date"] = $date;

    include("connection.php");

    if ($_POST) {

        $email = $_POST['useremail'];
        $password = $_POST['userpassword'];

        $error = '<label for="prompter" class="form-label">&nbsp;</label>';

        $result = $database->query("select * from webuser where email='$email'");
        if ($result->num_rows == 1) {
            $utype = $result->fetch_assoc()['usertype'];
            if ($utype == 'p') {
                $checker = $database->query("select * from patient where pemail='$email' and ppassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'p';
                    header('location: patient/index.php');
                } else {
                    $error = '<label for="prompter" class="form-label form-label--error">Wrong credentials: Invalid email or password</label>';
                }
            } elseif ($utype == 'a') {
                $checker = $database->query("select * from admin where aemail='$email' and apassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'a';
                    header('location: admin/index.php');
                } else {
                    $error = '<label for="prompter" class="form-label form-label--error">Wrong credentials: Invalid email or password</label>';
                }
            } elseif ($utype == 'd') {
                $checker = $database->query("select * from doctor where docemail='$email' and docpassword='$password'");
                if ($checker->num_rows == 1) {
                    $_SESSION['user'] = $email;
                    $_SESSION['usertype'] = 'd';
                    header('location: doctor/index.php');
                } else {
                    $error = '<label for="prompter" class="form-label form-label--error">Wrong credentials: Invalid email or password</label>';
                }
            }
        } else {
            $error = '<label for="prompter" class="form-label form-label--error">We can\'t find any account for this email.</label>';
        }
    } else {
        $error = '<label for="prompter" class="form-label">&nbsp;</label>';
    }

    ?>

    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-left">
                <div class="login-brand">
                    <div class="brand-icon">🏥</div>
                    <div class="brand-text">
                        <span class="brand-name">AfyaLink</span>
                        <span class="brand-tagline">Healthcare for All Kenyans</span>
                    </div>
                </div>
                <div class="login-info">
                    <h2>Welcome Back!</h2>
                    <p>Login to access your dashboard and manage your healthcare journey with AfyaLink.</p>
                    <div class="info-features">
                        <div class="info-feature">✓ Book appointments instantly</div>
                        <div class="info-feature">✓ Access your medical records</div>
                        <div class="info-feature">✓ Chat with doctors online</div>
                        <div class="info-feature">✓ Manage prescriptions</div>
                    </div>
                </div>
                <div class="login-footer-note">
                    <p>Protected by secure encryption</p>
                </div>
            </div>
            <div class="login-right">
                <div class="login-card">
                    <div class="login-header">
                        <h3>Sign In</h3>
                        <p>Login with your details to continue</p>
                    </div>
                    <form action="" method="POST" class="login-form">
                        <div class="form-group">
                            <label for="useremail" class="form-label">Email Address</label>
                            <input type="email" name="useremail" class="input-text" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="userpassword" class="form-label">Password</label>
                            <input type="password" name="userpassword" class="input-text" placeholder="Enter your password" required>
                        </div>
                        <div class="form-group">
                            <?php echo $error ?>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Login" class="login-btn btn-primary btn">
                        </div>
                        <div class="login-footer">
                            <span class="sub-text">Don't have an account? </span>
                            <a href="signup.php" class="signup-link">Sign Up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>