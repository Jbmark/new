<?php include 'app/views/layouts/header.php'; ?>
<?php include 'app/views/layouts/sidebar.php'; ?>

<div class="content">

    <h1>Edit Customer</h1>
    <p>Update customer information.</p>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="login-error">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="customers.php?action=edit&id=<?= $customer['customer_id'] ?>">

        <div class="form-group">
            <label>Customer Name</label>
            <input
                type="text"
                name="customer_name"
                value="<?= htmlspecialchars($customer['customer_name']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input
                type="text"
                name="contact_number"
                value="<?= htmlspecialchars($customer['contact_number'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label>Email</label>
            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($customer['email'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea
                name="address"
                rows="4"
                style="width:100%;padding:12px;border:1px solid #E0E0E0;border-radius:8px;"
            ><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn">
            Update Customer
        </button>

        <a href="customers.php" class="btn">
            Cancel
        </a>

    </form>

</div>

<?php include 'app/views/layouts/footer.php'; ?>