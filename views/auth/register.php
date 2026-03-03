<?php
session_start();
include '../../connectdb.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Kiểm tra email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
            $stmt->bind_param("sssss", $full_name, $email, $password, $phone, $address);
            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
include '../../includes/header.php';
?>

<div class="register-bg">
    <div class="register-box">
        <div class="register-title">REGISTER</div>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <div class="form-row">
                <div class="form-col">
                    <label class="register-label" for="full_name">Full Name</label>
                    <input class="register-input" type="text" name="full_name" id="full_name" required autofocus>
                </div>
                <div class="form-col">
                    <label class="register-label" for="email">Email</label>
                    <input class="register-input" type="email" name="email" id="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label class="register-label" for="phone">Phone</label>
                    <input class="register-input" type="text" name="phone" id="phone">
                </div>
                <div class="form-col">
                    <label class="register-label" for="address">Address</label>
                    <input class="register-input" type="text" name="address" id="address">
                </div>
            </div>
            <div class="form-row">
                <div class="form-col">
                    <label class="register-label" for="password">Password</label>
                    <input class="register-input" type="password" name="password" id="password" required>
                </div>
                <div class="form-col">
                    <label class="register-label" for="confirm_password">Confirm Password</label>
                    <input class="register-input" type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>
            <button class="register-btn" type="submit">Register</button>
        </form>
        <hr class="register-divider">
        <div class="register-new-title">Already have an account?</div>
        <form action="login.php" method="get">
            <button class="register-create-btn" type="submit">Sign in</button>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<style>
.register-bg {
    height: calc(100vh - 70px);
    min-height: unset;
    background: url('https://www.odealarose.com/media/cache/1920_1080_webp/build/images/flower-delivery.webp') center center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding-top: 0;
}
.register-box {
    position: relative;
    z-index: 1;
    background: #fff;
    border: 1px solid #222;
    border-radius: 12px;
    max-width: 700px;
    width: 100%;
    margin: 48px auto;
    padding: 48px 48px 36px 48px;
    box-sizing: border-box;
    box-shadow: 0 4px 24px #eee;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 650px;
}
.register-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-align: center;
    margin-bottom: 32px;
    width: 100%;
}
.register-label {
    font-size: 1.1rem;
    color: #222;
    margin-bottom: 6px;
    display: block;
    font-weight: 500;
}
.register-input {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 24px;
    border-radius: 6px;
    border: 1.5px solid #222;
    font-size: 1.1rem;
    font-family: 'Montserrat', Arial, sans-serif;
    background: #fff;
    box-sizing: border-box;
    height: 48px;
}
.register-input:focus {
    outline: none;
    border-color: #e75480;
}
.register-btn {
    width: 60%;
    margin: 24px auto 0 auto;
    display: block;
    background: #222;
    color: #fff;
    border: none;
    border-radius: 24px;
    padding: 12px 0;
    font-size: 16px;
    font-family: 'Montserrat', Arial, sans-serif;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}
.register-btn:hover {
    background: #e75480;
}
.register-divider {
    border: none;
    border-top: 1px solid #eee;
    margin: 18px 0;
    width: 100%;
}
.register-new-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
    font-style: italic;
    color: #222;
    margin-bottom: 10px;
    text-align: center;
    width: 100%;
}
.register-create-btn {
    width: 150px;
    margin: 24px auto 0 auto;
    display: block;
    background: #fff;
    color: #222;
    border: 1.5px solid #222;
    border-radius: 24px;
    padding: 12px 0;
    font-size: 16px;
    font-family: 'Montserrat', Arial, sans-serif;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.register-create-btn:hover {
    background: #e75480;
    color: #fff;
    border-color: #e75480;
}
.error {
    color: #e75480;
    text-align: center;
    margin-bottom: 12px;
    font-size: 14px;
}
.form-row {
    display: flex;
    gap: 32px;
    margin-bottom: 0;
    width: 100%;
}
.form-col {
    flex: 1;
    display: flex;
    flex-direction: column;
}
@media (max-width: 900px) {
    .register-box {
        max-width: 98vw;
        padding: 24px 8vw;
    }
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    .register-btn, .register-create-btn {
        width: 100%;
    }
}
</style>