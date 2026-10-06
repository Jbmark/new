<?php

require_once '../../config/database.php';

$order = null;
$error = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $orderInput = strtoupper(trim($_POST['order_id'] ?? ''));
    $contact = trim($_POST['contact_number'] ?? '');

    // Accept ORD-15 or 15
    $order_id = preg_replace('/[^0-9]/', '', $orderInput);

    if (empty($order_id) || empty($contact)) {

        $error = "Please enter both your Order ID and Contact Number.";

    } else {

        /*
         * Find the order using:
         * 1. Order ID
         * 2. Exact customer contact number
         *
         * The contact number is now stored properly
         * in the customers table.
         */
        $stmt = $pdo->prepare("
            SELECT
                o.order_id,
                o.customer_id,
                o.order_source,
                o.delivery_address,
                o.remarks,
                o.status,
                o.total_amount,
                o.created_at,
                c.customer_name,
                c.contact_number
            FROM orders o
            INNER JOIN customers c
                ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
              AND c.contact_number = ?
            LIMIT 1
        ");

        $stmt->execute([
            $order_id,
            $contact
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {

            $error = "Order not found. Please double-check your Order ID and Contact Number.";

        } else {

            $success_message = "Order found successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Track Order Status - Leah's Popstick</title>

    <link rel="stylesheet" href="../css/shop.css">

</head>

<body style="background:#F8F6EF;">

<header>

    <div class="topbar">

        <div class="logo">

            <div>
                <h2>LEAH'S POPSTICK</h2>
                <small>Wood Products Manufacturing</small>
            </div>

        </div>

        <div class="top-links">

            <a
                href="track.php"
                style="color:#6B8E3B;"
            >
                Track Order Status
            </a>

        </div>

    </div>


    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="index.php#products">
            Products
        </a>

        <a href="order.php">
            Request Bulk Order
        </a>

        <a
            href="track.php"
            class="active"
        >
            Track Order
        </a>

    </nav>

</header>


<div class="track-container">

    <a
        href="index.php"
        class="back-link"
    >
        ← Back to Digital Catalog Storefront
    </a>


    <h1
        style="
            color:#1B5E20;
            font-size:24px;
            margin-bottom:6px;
            font-weight:700;
        "
    >
        Track Purchase Order Progress
    </h1>


    <p
        style="
            color:#666;
            font-size:14px;
            margin-bottom:25px;
        "
    >
        Enter your Order ID and registered Contact Number to view your order status.
    </p>


    <form method="POST">

        <input
            type="text"
            name="order_id"
            placeholder="Order ID (Example: ORD-1)"
            required
        >

        <input
            type="text"
            name="contact_number"
            placeholder="Registered Contact Number"
            required
        >

        <button
            class="hero-btn"
            type="submit"
            style="
                width:100%;
                border:none;
                padding:14px;
                background:#6B8E3B;
                color:white;
                border-radius:8px;
                font-weight:700;
                font-size:15px;
                cursor:pointer;
                margin-top:10px;
                text-transform:uppercase;
            "
        >
            Track Order
        </button>

    </form>


    <?php if ($error) { ?>

        <div class="error-box">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>

    <?php } ?>


    <?php if ($order) { ?>

        <div class="order-card">

            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    border-bottom:1px solid #E5E9F0;
                    padding-bottom:12px;
                    margin-bottom:15px;
                "
            >

                <h2
                    style="
                        margin:0;
                        font-size:18px;
                        font-family:monospace;
                        color:#333;
                    "
                >
                    Order #ORD-<?= htmlspecialchars($order['order_id']) ?>
                </h2>


                <div
                    class="status-badge status-<?= strtolower(htmlspecialchars($order['status'])) ?>"
                    style="margin:0;"
                >
                    <?= htmlspecialchars($order['status']) ?>
                </div>

            </div>


            <div
                style="
                    font-size:14px;
                    color:#444;
                    line-height:1.6;
                    margin-bottom:20px;
                "
            >

                <p>
                    <strong>Customer:</strong>
                    <?= htmlspecialchars($order['customer_name']) ?>
                </p>


                <p>
                    <strong>Contact Number:</strong>
                    <?= htmlspecialchars($order['contact_number']) ?>
                </p>


                <p>
                    <strong>Order Date:</strong>
                    <?= date(
                        "M d, Y h:i A",
                        strtotime($order['created_at'])
                    ) ?>
                </p>


                <p>
                    <strong>Order Source:</strong>
                    <?= htmlspecialchars($order['order_source']) ?>
                </p>


                <p>
                    <strong>Delivery Address:</strong>
                    <?= htmlspecialchars($order['delivery_address']) ?>
                </p>


                <?php if (!empty($order['remarks'])) { ?>

                    <p>
                        <strong>Remarks:</strong>
                        <?= htmlspecialchars($order['remarks']) ?>
                    </p>

                <?php } ?>


                <p
                    style="
                        font-size:16px;
                        margin-top:10px;
                        color:#1B5E20;
                    "
                >
                    <strong>
                        Total Amount:
                        ₱<?= number_format(
                            $order['total_amount'],
                            2
                        ) ?>
                    </strong>
                </p>

            </div>


            <!-- ORDER STATUS TIMELINE -->

            <div class="timeline">

                <?php if ($order['status'] === 'Cancelled') { ?>

                    <div class="step cancelled">

                        <i class="fa-solid fa-circle-xmark"></i>

                        Order Cancelled

                    </div>

                <?php } else { ?>


                    <!-- PENDING -->

                    <div
                        class="step
                        <?= in_array(
                            $order['status'],
                            ['Pending', 'Processing', 'Completed']
                        ) ? 'done' : '' ?>"
                    >

                        <i
                            class="fa-solid
                            <?= in_array(
                                $order['status'],
                                ['Pending', 'Processing', 'Completed']
                            )
                            ? 'fa-circle-check'
                            : 'fa-circle' ?>"
                        ></i>

                        Order Request Received

                    </div>


                    <!-- PROCESSING -->

                    <div
                        class="step
                        <?= in_array(
                            $order['status'],
                            ['Processing', 'Completed']
                        ) ? 'done' : '' ?>"
                    >

                        <i
                            class="fa-solid
                            <?= in_array(
                                $order['status'],
                                ['Processing', 'Completed']
                            )
                            ? 'fa-circle-check'
                            : 'fa-circle' ?>"
                        ></i>

                        Processing

                    </div>


                    <!-- COMPLETED -->

                    <div
                        class="step
                        <?= $order['status'] === 'Completed'
                        ? 'done'
                        : '' ?>"
                    >

                        <i
                            class="fa-solid
                            <?= $order['status'] === 'Completed'
                            ? 'fa-circle-check'
                            : 'fa-circle' ?>"
                        ></i>

                        Order Completed

                    </div>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

</div>


</body>
</html>