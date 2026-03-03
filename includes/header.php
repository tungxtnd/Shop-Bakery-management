<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flower Shop</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; }
        .header-top {
            background: #e3f5e1;
            height: 8px;
        }
        .header-main {
            background: #FAD1CC;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 40px;
            height: 70px;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 40px;
            flex: 1;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo-img {
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            border: 1px solid #eee;
        }
        .nav {
            display: flex;
            gap: 40px;
            position: relative;
        }
        .nav-item {
            position: relative;
        }
        .nav a {
            text-decoration: none;
            color: #3c3c3c;
            font-size: 14px;
            font-weight: 100; /* Montserrat Thin */
            letter-spacing: 1px;
            font-family: 'Montserrat', Verdana;
            padding: 8px 0;
            display: inline-block;
            transition: color 0.2s;
        }
        .nav a::after {
            content: "";
            display: block;
            height: 2px;
            width: 0;
            background: #b97a56;
            transition: width 0.3s;
            margin: 0 auto;
        }
        .nav a:hover, .nav a:focus {
            color: #b97a56;
        }
        .nav a:hover::after, .nav a:focus::after {
            width: 100%;
        }
        /* Dropdown styles */
        .dropdown {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background: #fff;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            border-radius: 4px;
            z-index: 10;
        }
        .dropdown a {
            color: #222;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 500;
            display: block;
            background: none;
        }
        .dropdown a:hover {
            background: #FAD1CC;
            color: #b97a56;
        }
        .nav-item:hover .dropdown {
            display: block;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-left: 60px;
        }
        .call {
            display: flex;
            align-items: center;         
            text-decoration: none;
            color: #3c3c3c;
            font-size: 14px;
            font-weight: 100; 
            letter-spacing: 1px;
            font-family: 'Montserrat', Verdana;
            color: #444;
        }
        .call-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            font-size: 10px;
            font-weight: 100; 
            color: #6f6f6f;
        }
        .call-icon {
            margin-right: 7px;
            font-size: 25px;
        }
        .call-number {
            margin-left: 0;
            color: #3c3c3c;
            font-family: 'Montserrat', Verdana;
            font-size: 14px;
        }
        .icon-cart {
            font-size: 24px;
            color: #222;
            margin-right: 10px;
        }
        .sign-in {
            font-size: 17px;
            color: #222;
            text-decoration: none;
            color: #3c3c3c;
            font-size: 14px;
            font-weight: 100; 
            letter-spacing: 1px;
            font-family: 'Montserrat', Verdana;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        .dot {
            color: #e74c3c;
            font-size: 22px;
            margin-right: 5px;
            align-self: center;
            line-height: 1;
        }
        .user-icon:hover .dropdown {
            display: block;
        }
        .user-icon .dropdown {
            left: -50px;
            top: 20px;
            position: absolute;
            background: #fff;
            min-width: 130px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
            border-radius: 4px;
            z-index: 10;
        }
    </style>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="header-main">
        <div class="header-left">
            <div class="logo">
                <a href="/shop-bakery-management/homepage.php" style="background:none; border:none; display:inline-block;">
                    <div class="logo-img" style="background:none; border:none;">
                        <img src="/shop-bakery-management/assets/img/web_logo.png" alt="Blossom Logo" style="width:60px; height:60px; object-fit:contain;">
                    </div>
                </a>
            </div>
            <nav class="nav">
                <div class="nav-item">
                    <a href="/shop-bakery-management/shop.php">BOUQUET</a>
                </div>
                <div class="nav-item">
                    <a href="<?php echo ($currentPage == 'homepage.php') ? '#collection' : '/shop-bakery-management/views/customer/collection.php'; ?>">
                        COLLECTION
                    </a>
                    <div class="dropdown">
                        <a href="/shop-bakery-management/views/customer/collection.php?c=all">All collections</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=0">Birthday</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=1">Anniversary</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=2">Congratulations</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=3">Parent's Day</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=4">Teacher's Day</a>
                        <a href="/shop-bakery-management/views/customer/collection.php?c=5">International's Day</a>
                    </div>
                </div>
                <div class="nav-item">
                    <a href="#about">OUR STORY</a>
                    <div class="dropdown">
                        <a href="/shop-bakery-management/views/customer/about_us.php">About Us</a>
                        <a href="/shop-bakery-management/views/customer/our_team.php">Our Team</a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="header-actions">
            <div class="call">
                <span class="call-icon"><i class="fa fa-phone"></i></span>
                <div class="call-text">
                    <span>CALL TO ORDER</span>
                    <span class="call-number">+84 9001090</span>
                </div>
            </div>
            <span class="icon-cart" style="cursor:pointer;"
                onclick="
                    <?php if (isset($_SESSION['user_id'])): ?>
                        window.location.href='/shop-bakery-management/views/customer/noti.php';
                    <?php else: ?>
                        alert('You need to log in first!');
                    <?php endif; ?>
                "
            >
            <i class="fa-solid fa-bell"></i>
            </span>
            <span class="icon-cart" style="cursor:pointer;"
                onclick="
                    <?php if (isset($_SESSION['user_id'])): ?>
                        window.location.href='/shop-bakery-management/views/customer/cart.php';
                    <?php else: ?>
                        alert('You need to log in first!');
                    <?php endif; ?>
                "
            >
                <i class="fa-solid fa-cart-shopping"></i>
            </span>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-icon" style="position:relative; cursor:pointer;">
                    <i class="fa-solid fa-user"></i>
                    <div class="dropdown">
                        <a href="/shop-bakery-management/views/customer/account.php" style="padding:10px 20px; display:block; text-decoration:none; color:#222;">Profile</a>
                        <a href="/shop-bakery-management/views/customer/orderhistory.php" style="padding:10px 20px; display:block; text-decoration:none; color:#222;">My Orders</a>
                    </div>
                </div>
                <a href="/shop-bakery-management/views/auth/logout.php" class="sign-in" onclick="return confirm('Are you sure you want to logout?');"><span class="dot">•</span><b>LOGOUT</b></a>
            <?php else: ?>
                <a href="/shop-bakery-management/views/auth/login.php" class="sign-in"><span class="dot">•</span><b>SIGN-IN</b></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>