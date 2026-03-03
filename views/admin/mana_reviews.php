<?php
// filepath: c:\xampp\htdocs\Flower_Shop\views\admin\mana_reviews.php
session_start();
include '../../connectdb.php';

// Handle delete review (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo 'success';
    exit;
}


// Handle filter/search
$where = [];
$params = [];
$types = '';
if (!empty($_GET['user'])) {
    $where[] = "(u.full_name LIKE ? OR u.email LIKE ?)";
    $params[] = '%' . $_GET['user'] . '%';
    $params[] = '%' . $_GET['user'] . '%';
    $types .= 'ss';
}
if (!empty($_GET['product'])) {
    $where[] = "p.name LIKE ?";
    $params[] = '%' . $_GET['product'] . '%';
    $types .= 's';
}
if (!empty($_GET['date'])) {
    $where[] = "DATE(r.created_at) = ?";
    $params[] = $_GET['date'];
    $types .= 's';
}
if (isset($_GET['rating']) && $_GET['rating'] !== '') {
    $where[] = "r.rating = ?";
    $params[] = intval($_GET['rating']);
    $types .= 'i';
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Total reviews
$total_reviews = $conn->query("SELECT COUNT(*) as total FROM reviews")->fetch_assoc()['total'];

// Pagination
$limit = 10; // 10 reviews per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Get reviews data (with admin_reply and admin_created_at)
$sql = "
    SELECT r.*, u.full_name, u.email, p.name as product_name
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN products p ON r.product_id = p.id
    $where_sql
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";
if ($params) {
    $types_with_limit = $types . 'ii';
    $params_with_limit = array_merge($params, [$limit, $offset]);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types_with_limit, ...$params_with_limit);
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$reviews = $result->fetch_all(MYSQLI_ASSOC);

// Get total filtered
$count_sql = "
    SELECT COUNT(*) as total
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN products p ON r.product_id = p.id
    $where_sql
";
if ($params) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
} else {
    $count_stmt = $conn->prepare($count_sql);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_filtered = $count_result->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_filtered / $limit));
// Handle reply (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'], $_POST['reply_content'])) {
    $id = intval($_POST['reply_id']);
    $reply = trim($_POST['reply_content']);
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE reviews SET admin_reply = ?, admin_created_at = ? WHERE id = ?");
    $stmt->bind_param('ssi', $reply, $now, $id);
    $stmt->execute();

    // Get review info for notification
    $review_info = $conn->query("SELECT user_id, product_id FROM reviews WHERE id = $id")->fetch_assoc();
    if ($review_info) {
        $type = 'admin_message';
        $message = 'Admin replied to your review: ' . $reply;
        $created_at = $now;
        $noti_stmt = $conn->prepare("INSERT INTO notifications (user_id, target_user_id, product_id, type, message, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $noti_stmt->bind_param("iiisss", $review_info['user_id'], $review_info['user_id'], $review_info['product_id'], $type, $message, $created_at);
        $noti_stmt->execute();
        $noti_stmt->close();
    }

    // Return reply and created_at as JSON
    echo json_encode([
        'reply' => htmlspecialchars($reply),
        'created_at' => date('Y-m-d H:i', strtotime($now))
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Reviews</title>
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #fff;
        margin: 0;
    }
    .admin-navbar {
            background: #e75480;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            height: 60px;
        }
        .admin-navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0 32px;
            font-size: 18px;
            line-height: 60px;
            display: block;
            transition: background 0.2s;
        }
        .admin-navbar a:hover, .admin-navbar a.active {
            background: #d84372;
        }
    .breadcrumbs {
        margin: 24px 0 10px 0;
        font-size: 1.08rem;
        color: #888;
    }
    .breadcrumbs a { color: #d84372; text-decoration: none; }
    .review-stats {
        margin-bottom: 18px;
        font-size: 1.08rem;
    }
    .review-stats span { color: #d84372; font-weight: 500; }
    .review-filter {
        background: #faf6f8;
        border-radius: 8px;
        padding: 14px 18px;
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        align-items: center;
    }
    .review-filter input, .review-filter select {
        padding: 7px 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
        min-width: 170px;
    }
    .review-filter button {
        background: #d84372;
        color: #fff;
        padding: 7px 18px;
        border-radius: 5px;
        border: none;
        font-size: 1rem;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .review-filter button:hover { opacity: 0.85; }
    .review-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px #eee;
    }
    .review-table th, .review-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f0e0de;
        text-align: left;
        font-size: 1.05rem;
        vertical-align: top;
    }
    .review-table th {
        background: #f8eaea;
        color: #d84372;
        font-weight: 600;
        text-align: left;
    }
    .review-table tr:last-child td { border-bottom: none; }
    .review-table td {
        background: #fff;
    }
    .review-table tr:nth-child(even) { background: #faf6f8; }
    .review-table tr:hover td { background: #f5eaea; transition: background 0.2s; }
    .review-actions button {
        border: none;
        background: #f8eaea;
        cursor: pointer;
        color: #d84372;
        font-size: 1.15rem;
        padding: 5px 10px;
        border-radius: 5px;
        margin-right: 4px;
        transition: background 0.1s;
    }
    .review-actions button:hover {
        background: #f2d6d6;
    }
    .bulk-actions {
        margin: 16px 0 0 0;
        display: flex;
        gap: 10px;
    }
    .bulk-actions button {
        background: #d84372;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 6px 18px;
        font-size: 1rem;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .bulk-actions button:hover { opacity: 0.85; }
    .reply-content {
        margin-top: 10px;
        color: #219653;
        font-size: 1.05em;
        font-weight: 500;
        background: #f3fff3;
        border-left: 4px solid #2ecc40;
        border-radius: 5px;
        padding: 8px 12px;
        display: block;
        max-width: 420px;
        white-space: pre-line;
        transition: background 0.2s;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px);}
        to { opacity: 1; transform: translateY(0);}
    }
    .reply-content .reply-date {
        color: #888;
        font-size: 0.97em;
        font-weight: 400;
        margin-left: 8px;
    }
    .reply-box {
        margin-top: 8px;
        margin-left: 70%;
        background: #f8f8f8;
        border-radius: 6px;
        padding: 10px 12px;
        border: 1px solid #eee;
        max-width: 350px;
        box-shadow: 0 2px 8px #e0f7e0;
        animation: fadeIn 0.3s;
    }
    .pagination {
        margin: 30px 0 30px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        font-size: 1.08rem;
    }
    .pagination a, .pagination span {
        min-width: 32px;
        min-height: 32px;
        line-height: 32px;
        padding: 0;
        border-radius: 6px;
        border: 1.5px solid #e2bcbc;
        background: #fff;
        color: #d84372;
        text-decoration: none;
        font-weight: 500;
        text-align: center;
        display: inline-block;
        box-sizing: border-box;
        font-size: 1rem;
        transition: background 0.15s, color 0.15s;
    }
    .pagination a:hover {
        background: #f8eaea;
        color: #d84372;
    }
    .pagination .active {
        background: #d84372;
        color: #fff;
        font-weight: bold;
        border-color: #d17c7c;
        pointer-events: none;
    }
    .pagination .dots {
        background: none;
        border: none;
        color: #bbb;
        padding: 0 6px;
        min-width: unset;
        min-height: unset;
        line-height: 32px;
        font-size: 1.1em;
    }
    .toast {
        position: fixed;
        left: 50%;
        bottom: 40px;
        transform: translateX(-50%);
        background: #2ecc40;
        color: #fff;
        padding: 14px 32px;
        border-radius: 8px;
        font-size: 1.1rem;
        box-shadow: 0 2px 12px #aaa;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s;
    }
    .toast.show { opacity: 1; pointer-events: auto; }
    @media (max-width: 900px) {
        .review-table, .review-table th, .review-table td { font-size: 0.97rem; }
        .review-table, .review-table th, .review-table td { display: block; width: 100%; }
        .review-table th, .review-table td { box-sizing: border-box; }
        .review-table tr { margin-bottom: 18px; border-radius: 10px; box-shadow: 0 2px 8px #eee; }
    }
</style>
</head>
<body>
    <nav class="admin-navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="mana_orders.php">Manage Orders</a>
        <a href="mana_products.php">Manage Products</a>
        <a href="mana_reviews.php" class="active">Manage Reviews</a>
        <a href="mana_users.php">Manage Users</a>
        <a href="mana_noti.php">Manage Notifications</a>
        <a href="/flower_shop/views/auth/logout.php" style="margin-left:auto;" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
    </nav>
<div class="breadcrumbs">
    <a href="dashboard.php">Dashboard</a> &gt; Review Management
</div>
<div class="review-stats">
    Total reviews: <span><?php echo $total_reviews; ?></span>
</div>
<form class="review-filter" method="get">
    <input type="text" name="user" placeholder="User name or email" value="<?php echo htmlspecialchars($_GET['user'] ?? ''); ?>">
    <input type="text" name="product" placeholder="Product name" value="<?php echo htmlspecialchars($_GET['product'] ?? ''); ?>">
    <input type="date" name="date" value="<?php echo htmlspecialchars($_GET['date'] ?? ''); ?>">
    <select name="rating">
        <option value="">Rating</option>
        <?php for($i=5;$i>=1;$i--): ?>
            <option value="<?php echo $i; ?>" <?php if(isset($_GET['rating']) && $_GET['rating']!=='' && $_GET['rating']==$i) echo 'selected'; ?>><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
    <button type="submit">Filter</button>
</form>

<form method="post" id="bulkForm">
    <div class="bulk-actions">
        <button type="button" onclick="bulkAction('delete')">Delete</button>
    </div>
    <table class="review-table">
        <tr>
            <th style="width:36px;"><input type="checkbox" onclick="toggleAll(this)"></th>
            <th style="width:48px;">ID</th>
            <th style="width:170px;">User</th>
            <th style="width:180px;">Product</th>
            <th style="width:110px;">Rating</th>
            <th style="width:140px;">Date</th>
            <th>Comments</th>
            <th style="width:110px;">Actions</th>
        </tr>
        <?php foreach($reviews as $r): ?>
        <tr id="review-row-<?php echo $r['id']; ?>">
            <td><input type="checkbox" name="review_ids[]" value="<?php echo $r['id']; ?>"></td>
            <td><?php echo $r['id']; ?></td>
            <td>
                <?php echo htmlspecialchars($r['full_name'] ?: $r['email']); ?><br>
                <span style="color:#888;font-size:0.97em;"><?php echo htmlspecialchars($r['email']); ?></span>
            </td>
            <td><?php echo htmlspecialchars($r['product_name']); ?></td>
            <td>
                <?php
                $stars = intval($r['rating']);
                for($i=1;$i<=5;$i++) {
                    echo '<span style="color:'.($i<=$stars?'#f7b731':'#ddd').';font-size:1.1em;">&#9733;</span>';
                }
                ?>
            </td>
            <td><?php echo date('Y-m-d H:i', strtotime($r['created_at'])); ?></td>
            <td style="max-width:350px;overflow-x:auto;">
                <?php echo nl2br(htmlspecialchars($r['comment'])); ?>
                <?php if (!empty($r['admin_reply'])): ?>
                    <div class="reply-content" id="reply-content-<?php echo $r['id']; ?>">
                        Admin reply:
                        <span><?php echo nl2br(htmlspecialchars($r['admin_reply'])); ?></span>
                        <?php if (!empty($r['admin_created_at'])): ?>
                            <span class="reply-date">(<?php echo date('Y-m-d H:i', strtotime($r['admin_created_at'])); ?>)</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="reply-content" id="reply-content-<?php echo $r['id']; ?>" style="display:none"></div>
                <?php endif; ?>
            </td>
            <td class="review-actions">
                <button type="button" onclick="deleteReview(<?php echo $r['id']; ?>)" title="Delete">&#128465;</button>
                <button type="button" onclick="showReplyBox(<?php echo $r['id']; ?>)" title="Reply">&#128172;</button>
            </td>
        </tr>
        <tr id="reply-row-<?php echo $r['id']; ?>" style="display:none;">
            <td colspan="8">
                <div class="reply-box" id="reply-box-<?php echo $r['id']; ?>">
                    <textarea id="reply-text-<?php echo $r['id']; ?>" rows="2" style="width:98%;resize:vertical;" placeholder="Type your reply..."></textarea>
                    <div style="margin-top:6px;">
                        <button type="button" onclick="sendReply(<?php echo $r['id']; ?>)" style="background:#2ecc40;color:#fff;padding:4px 14px;border-radius:4px;border:none;">Send</button>
                        <button type="button" onclick="hideReplyBox(<?php echo $r['id']; ?>)" style="background:#ccc;padding:4px 10px;border-radius:4px;border:none;">Cancel</button>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($reviews)): ?>
        <tr><td colspan="8" style="text-align:center;color:#888;">No reviews found.</td></tr>
        <?php endif; ?>
    </table>
</form>
<div class="pagination">
    <?php
    // Pagination logic: show max 7 page numbers, with ... if needed
    $max_links = 7;
    $start = max(1, $page - 3);
    $end = min($total_pages, $page + 3);

    if ($total_pages > $max_links) {
        if ($start > 1) {
            echo '<a href="?'.http_build_query(array_merge($_GET, ['page'=>1])).'">1</a>';
            if ($start > 2) echo '<span class="dots">...</span>';
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
                echo '<span class="active">'.$i.'</span>';
            } else {
                echo '<a href="?'.http_build_query(array_merge($_GET, ['page'=>$i])).'">'.$i.'</a>';
            }
        }
        if ($end < $total_pages) {
            if ($end < $total_pages - 1) echo '<span class="dots">...</span>';
            echo '<a href="?'.http_build_query(array_merge($_GET, ['page'=>$total_pages])).'">'.$total_pages.'</a>';
        }
    } else {
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                echo '<span class="active">'.$i.'</span>';
            } else {
                echo '<a href="?'.http_build_query(array_merge($_GET, ['page'=>$i])).'">'.$i.'</a>';
            }
        }
    }
    ?>
</div>
<script>
function toggleAll(source) {
    document.querySelectorAll('input[name="review_ids[]"]').forEach(cb => cb.checked = source.checked);
}
function bulkAction(action) {
    if(confirm('Are you sure you want to perform this action?')) {
        alert('Bulk delete is not implemented in this demo.');
    }
}
function deleteReview(id) {
    if(confirm('Are you sure you want to delete this review?')) {
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'delete_id=' + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(data => {
            if(data.trim() === 'success') {
                document.getElementById('review-row-' + id).remove();
            } else {
                alert('Delete failed!');
            }
        });
    }
}
function showReplyBox(id) {
    // Hide all other reply boxes
    document.querySelectorAll('tr[id^="reply-row-"]').forEach(row => row.style.display = 'none');
    document.getElementById('reply-row-' + id).style.display = 'table-row';
    setTimeout(function() {
        document.getElementById('reply-text-' + id).focus();
    }, 100);
}
function hideReplyBox(id) {
    document.getElementById('reply-row-' + id).style.display = 'none';
}
function sendReply(id) {
    var btn = document.querySelector('#reply-box-' + id + ' button[onclick^="sendReply"]');
    var content = document.getElementById('reply-text-' + id).value.trim();
    if(!content) {
        alert('Please enter your reply!');
        return;
    }
    btn.disabled = true;
    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'reply_id=' + encodeURIComponent(id) + '&reply_content=' + encodeURIComponent(content)
    })
    .then(res => res.json())
    .then(data => {
        showToast('Reply sent!');
        setTimeout(function() {
            location.reload();
        }, 900); // Wait for toast to show, then reload
    })
    .finally(() => {
        btn.disabled = false;
    });
}
function showToast(msg) {
    var toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(function() {
        toast.classList.remove('show');
    }, 1800);
}
</script>
</body>
</html>