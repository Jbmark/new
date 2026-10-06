<?php

require_once 'config/database.php';
require_once 'app/models/CustomerModel.php';

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        global $pdo;

        $this->customerModel = new CustomerModel($pdo);
    }

    // Check if user is allowed
    private function checkPermission()
    {
        if (
            !isset($_SESSION['user']) ||
            !in_array($_SESSION['user']['role_name'], ['Owner', 'Office Staff'])
        ) {
            echo "Access Denied.";
            exit;
        }
    }

    // Customer list
    public function index()
    {
        $this->checkPermission();

        $customers = $this->customerModel->getAllCustomers();

        include 'app/views/customers/index.php';
    }

    // Add customer
    public function create()
    {
        $this->checkPermission();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['customer_name'] ?? '');
            $contact = trim($_POST['contact_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($name === '') {
                $_SESSION['error'] = "Customer name is required.";
                header("Location: customers.php?action=create");
                exit;
            }

            $this->customerModel->create(
                $name,
                $contact,
                $email,
                $address
            );

            $_SESSION['success'] = "Customer added successfully.";

            header("Location: customers.php");
            exit;
        }

        include 'app/views/customers/create.php';
    }

    // Edit customer
    public function edit($id)
    {
        $this->checkPermission();

        $customer = $this->customerModel->getById($id);

        if (!$customer) {
            echo "Customer not found.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = trim($_POST['customer_name'] ?? '');
            $contact = trim($_POST['contact_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if ($name === '') {
                $_SESSION['error'] = "Customer name is required.";
                header("Location: customers.php?action=edit&id=" . $id);
                exit;
            }

            $this->customerModel->update(
                $id,
                $name,
                $contact,
                $email,
                $address
            );

            $_SESSION['success'] = "Customer updated successfully.";

            header("Location: customers.php");
            exit;
        }

        include 'app/views/customers/edit.php';
    }
}