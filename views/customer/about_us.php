<?php include '../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | Flower Shop</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8f8f8; }
        .about-container {
            max-width: 800px;
            margin: 40px auto 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 12px #eee;
            padding: 40px 32px;
        }
        .about-title {
            color: #e75480;
            font-size: 2.2em;
            font-weight: bold;
            margin-bottom: 18px;
            text-align: center;
        }
        .about-section {
            margin-bottom: 28px;
        }
        .about-section h2 {
            color: #b97a56;
            font-size: 1.3em;
            margin-bottom: 10px;
        }
        .about-section p {
            color: #444;
            font-size: 1.08em;
            line-height: 1.7;
        }
        .about-team {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 24px;
        }
        .team-member {
            background: #f9f3f5;
            border-radius: 8px;
            padding: 18px 22px;
            text-align: center;
            width: 180px;
            box-shadow: 0 1px 4px #eee;
        }
        .team-member img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .team-member .name {
            font-weight: bold;
            color: #e75480;
            margin-bottom: 4px;
        }
        .team-member .role {
            color: #b97a56;
            font-size: 0.98em;
        }
        @media (max-width: 600px) {
            .about-container { padding: 18px 6px; }
            .about-title { font-size: 1.3em; }
            .about-team { flex-direction: column; gap: 16px; }
            .team-member { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="about-container">
        <div class="about-title">About Flower Shop</div>
        <div class="about-section">
            <h2>Our Story</h2>
            <p>
                Founded in 2023, Flower Shop was born from a passion for bringing beauty and joy to every occasion. 
                We believe flowers are more than just gifts—they are a language of love, celebration, and comfort. 
                Our mission is to deliver fresh, stunning arrangements that make every moment memorable.
            </p>
        </div>
        <div class="about-section">
            <h2>What We Offer</h2>
            <p>
                From classic bouquets to modern floral designs, we offer a wide range of flowers for all occasions—birthdays, anniversaries, weddings, and more. 
                Our team carefully selects each bloom to ensure quality and freshness, and we pride ourselves on fast, reliable delivery.
            </p>
        </div>
        <div class="about-section">
            <h2>Meet Our Team</h2>
            <div class="about-team">
                <div class="team-member">
                    <img src="../../assets/img/flower_5768957.png" alt="Team Member">
                    <div class="name">Hoang Huong Chi</div>
                    <div class="role">Founder &amp; Florist</div>
                </div>
                <div class="team-member">
                    <img src="../../assets/img/flower_5768957.png" alt="Team Member">
                    <div class="name">Thai Ha Trang</div>
                    <div class="role">Designer</div>
                </div>
                <div class="team-member">
                    <img src="../../assets/img/flower_5768957.png" alt="Team Member">
                    <div class="name">Nguyen Thi Ha Anh</div>
                    <div class="role">Customer Support</div>
                </div>
            </div>
        </div>
        <div class="about-section">
            <h2>Contact Us</h2>
            <p>
                Have questions or need a custom arrangement?<br>
                Email: <a href="mailto:support@flower_shop.com">support@flower_shop.com</a><br>
                Phone: 0123-456-789<br>
                Address: 123 Blossom Street, Hanoi, Vietnam
            </p>
        </div>
    </div>
</body>
</html>
<?php include '../../includes/footer.php'; ?>