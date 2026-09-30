<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/signup.css">
    <title>AfyaLink | Create Account</title>
</head>

<body>
    <?php
    session_start();
    include("connection.php");

    date_default_timezone_set('Africa/Nairobi');
    $date  = date('Y-m-d');
    $error = '';

    if ($_POST) {

        $fname    = trim($_POST['fname']);
        $lname    = trim($_POST['lname']);
        $address  = trim($_POST['address']);
        $nic      = trim($_POST['nic']);
        $dob      = $_POST['dob'];
        $role     = $_POST['role']      ?? '';
        $email    = trim($_POST['email']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $fullname = $fname . ' ' . $lname;

        // Validate
        if ($role == "") {
            $error = 'Please select a role.';
        } elseif ($password !== $cpassword) {
            $error = 'Passwords do not match. Please try again.';
        } else {
            // Check if email already exists
            $chk = $database->prepare("SELECT email FROM webuser WHERE email = ?");
            $chk->bind_param("s", $email);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                $error = 'An account already exists for this email address.';
            } else {

                // Insert into webuser
                $ins_web = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, ?)");
                $ins_web->bind_param("ss", $email, $role);
                $ins_web->execute();

                if ($role === 'p') {
                    $tel = '';
                    $ins = $database->prepare(
                        "INSERT INTO patient (pname, pemail, ppassword, pnic, paddress, pdob, ptel)
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                    );
                    $ins->bind_param("sssssss", $fullname, $email, $password, $nic, $address, $dob, $tel);
                    $ins->execute();

                    $_SESSION["user"]     = $email;
                    $_SESSION["usertype"] = 'p';
                    $_SESSION["date"]     = $date;
                    header("location: patient/index.php");
                    exit();
                } else {
                    $tel  = '';
                    $spec = 1;
                    $ins = $database->prepare(
                        "INSERT INTO doctor (docname, docemail, docpassword, docnic, doctel, specialties)
                     VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    $ins->bind_param("sssssi", $fullname, $email, $password, $nic, $tel, $spec);
                    $ins->execute();

                    $_SESSION["user"]     = $email;
                    $_SESSION["usertype"] = 'd';
                    $_SESSION["date"]     = $date;
                    header("location: doctor/index.php");
                    exit();
                }
            }
        }
    }
    ?>

    <div class="signup-wrapper">
        <div class="signup-container">
            <div class="signup-left">
                <div class="signup-brand">
                    <div class="brand-icon">🏥</div>
                    <div class="brand-text">
                        <span class="brand-name">AfyaLink</span>
                        <span class="brand-tagline">Join Kenya's Healthcare Network</span>
                    </div>
                </div>
                <div class="signup-info">
                    <h2>Join AfyaLink Today!</h2>
                    <p>Create your account and start your journey to better health with Kenya's most trusted healthcare platform.</p>
                    <div class="info-features">
                        <div class="info-feature">✓ Book appointments with verified doctors</div>
                        <div class="info-feature">✓ Access medical records anytime</div>
                        <div class="info-feature">✓ Get prescription reminders</div>
                        <div class="info-feature">✓ Chat with healthcare professionals</div>
                    </div>
                </div>
                <div class="signup-footer-note">
                    <p>🔒 Your information is safe with us</p>
                </div>
            </div>
            <div class="signup-right">
                <div class="signup-card">
                    <div class="signup-header">
                        <h3>Create Account</h3>
                        <p>Fill in your details to get started</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="signup-form">
                        <!-- Role Selection -->
                        <div class="form-group">
                            <label for="role" class="form-label">I am a:</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="" disabled <?php echo empty($_POST['role']) ? 'selected' : ''; ?>>-- Select your role --</option>
                                <option value="p" <?php echo (($_POST['role'] ?? '') === 'p') ? 'selected' : ''; ?>>Patient</option>
                                <option value="d" <?php echo (($_POST['role'] ?? '') === 'd') ? 'selected' : ''; ?>>Doctor</option>
                                <option value="a" <?php echo (($_POST['role'] ?? '') === 'a') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>

                        <!-- Name Fields -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" name="fname" class="form-control" placeholder="First Name"
                                    value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" name="lname" class="form-control" placeholder="Last Name"
                                    value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" placeholder="Your residential address"
                                value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                        </div>

                        <!-- NIC -->
                        <div class="form-group">
                            <label for="nic" class="form-label">NIC Number</label>
                            <input type="text" name="nic" class="form-control" placeholder="National ID Card Number"
                                value="<?php echo htmlspecialchars($_POST['nic'] ?? ''); ?>" required>
                        </div>

                        <!-- Date of Birth -->
                        <div class="form-group">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control"
                                value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="cpassword" class="form-label">Confirm Password</label>
                            <input type="password" name="cpassword" class="form-control" placeholder="Confirm your password" required>
                        </div>

                        <!-- Buttons -->
                        <div class="form-buttons">
                            <input type="reset" value="Reset" class="btn-reset btn">
                            <input type="submit" value="Sign Up" class="btn-signup btn-primary btn">
                        </div>

                        <div class="signup-footer">
                            <span class="sub-text">Already have an account? </span>
                            <a href="login.php" class="login-link">Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>