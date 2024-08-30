<?php
include '../components/connection.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location: admin_login.php');
}

class Exporter
{

    public function generateReport_MostPerformingProduct()
    {
        global $conn;

        $query = "SELECT p.id, p.name, SUM(o.qty) AS total_quantity, SUM(o.qty * p.price) AS total_revenue
    FROM orders AS o
    JOIN products AS p ON o.product_id = p.id
    GROUP BY p.id, p.name
    ORDER BY total_quantity DESC
    LIMIT 10
    ";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $mostPerformingProductData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set the appropriate headers to indicate the file type
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=most_performing_product.xls');
        header('Cache-Control: max-age=0');

        // Output the Excel data
        echo "Product ID\tProduct Name\tTotal Quantity Sold\tTotal Revenue\r\n";
        foreach ($mostPerformingProductData as $row) {
            echo $row['id'] . "\t";
            echo $row['name'] . "\t";
            echo $row['total_quantity'] . "\t";
            echo $row['total_revenue'] . "\r\n";
        }

        exit;
    }


    public function generateReport_OrdersFromCertainDates($startDate, $endDate)
    {
        global $conn;

        $query = "SELECT o.*, p.name AS product_name
              FROM orders AS o
              JOIN products AS p ON o.product_id = p.id
              WHERE o.date >= ? AND o.date <= ?";

        $stmt = $conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if the query returned any data
        if ($stmt->rowCount() === 0) {

            function formatDateWithOrdinalSuffix($date) {
                return date('jS M Y', strtotime($date));
            }

            $formattedStartDate = formatDateWithOrdinalSuffix($startDate);
            $formattedEndDate = formatDateWithOrdinalSuffix($endDate);
           
            echo "No data with the given date range '{$formattedStartDate}' to '{$formattedEndDate}'!";
        } else {
            // Set the headers to make the browser treat the response as an Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=orders_by_date_range_" . date('d-m-Y', strtotime($startDate)) . "_" . date('d-m-Y', strtotime($endDate)) . ".xls");

            echo "Order ID\tUser ID\tName\tNumber\tEmail\tAddress\tAddress Type\tMethod\tProduct ID\tProduct Name\tPrice\tQty\tDate\tStatus\tTotal Revenue\r\n";
            foreach ($ordersData as $row) {
                echo $row['id'] . "\t";
                echo $row['user_id'] . "\t";
                echo $row['name'] . "\t";
                echo $row['number'] . "\t";
                echo $row['email'] . "\t";
                echo $row['address'] . "\t";
                echo $row['address_type'] . "\t";
                echo $row['method'] . "\t";
                echo $row['product_id'] . "\t";
                echo $row['product_name'] . "\t";
                echo $row['price'] . "\t";
                echo $row['qty'] . "\t";
                echo $row['date'] . "\t";
                echo $row['status'] . "\t";
                echo ($row['qty'] * $row['price']) . "\r\n";
            }
        }

        exit;
    }


    public function generateReport_UserWithHighestPurchases()
    {
        global $conn;

        // Find the user with the most purchases including their name and the product with the highest ttl qty sold
        $query = "SELECT
        u.name AS user_name,
        (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS total_purchases,
        (
            SELECT p.name 
            FROM products p
            JOIN orders o ON p.id = o.product_id
            WHERE o.user_id = u.id
            GROUP BY p.id
            ORDER BY SUM(o.qty) DESC
            LIMIT 1
        ) AS product_name,
        (
            SELECT SUM(o.qty) 
            FROM orders o
            JOIN products p ON p.id = o.product_id
            WHERE o.user_id = u.id
            GROUP BY p.id
            ORDER BY SUM(o.qty) DESC
            LIMIT 1
        ) AS total_quantity_sold
    FROM users u
    ORDER BY total_purchases DESC
    LIMIT 10;";


        $stmt = $conn->prepare($query);
        $stmt->execute();

        $reportData = $stmt->fetch(PDO::FETCH_ASSOC);

        header("Content-Type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=user_with_most_orders_report.xls");

        echo "User with Highest Purchases\tTotal Purchases\tProduct with Highest Total Quantity Sold\tTotal Quantity Sold\r\n";
        echo $reportData['user_name'] . "\t" . $reportData['total_purchases'] . "\t" . $reportData['product_name'] . "\t" . $reportData['total_quantity_sold'] . "\r\n";

        exit;
    }

    public function generateReport_OrdersByStatus($status)
    {
        global $conn;

        $query = "SELECT o.*, p.name AS product_name
                  FROM orders AS o
                  JOIN products AS p ON o.product_id = p.id
                  WHERE o.status = ?";

        $stmt = $conn->prepare($query);
        $stmt->execute([$status]);
        $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if the query returned any data
        if ($stmt->rowCount() === 0) {
            echo "No data with the given status '$status'!";
        } else {
            $currentDate = date("Y-m-d");
            // Set the headers to make the browser treat the response as an Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=orders_by_status_" . $status . "_" . $currentDate . ".xls");

            echo "Order ID\tUser ID\tName\tNumber\tEmail\tAddress\tAddress Type\tMethod\tProduct ID\tProduct Name\tPrice\tQuantity\tDate\tStatus\tTotal Revenue\r\n";
            foreach ($ordersData as $row) {
                echo $row['id'] . "\t";
                echo $row['user_id'] . "\t";
                echo $row['name'] . "\t";
                echo $row['number'] . "\t";
                echo $row['email'] . "\t";
                echo $row['address'] . "\t";
                echo $row['address_type'] . "\t";
                echo $row['method'] . "\t";
                echo $row['product_id'] . "\t";
                echo $row['product_name'] . "\t";
                echo $row['price'] . "\t";
                echo $row['qty'] . "\t";
                echo $row['date'] . "\t";
                echo $row['status'] . "\t";
                echo ($row['qty'] * $row['price']) . "\r\n";
            }
        }

        exit;
    }

    public function generateReport_OrdersMadeByAUser($userId)
    {
        global $conn;

        $query = "SELECT o.*, p.name AS product_name
              FROM orders AS o
              JOIN products AS p ON o.product_id = p.id
              WHERE o.user_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        $ordersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userNameQuery = "SELECT name FROM users WHERE id = ?";

        $userNameQueryStmt = $conn->prepare($userNameQuery);
        $userNameQueryStmt->execute([$userId]);
        $getNameFromId = $userNameQueryStmt->fetch()['name'];

        // Check if the query returned any data
        if ($stmt->rowCount() === 0) {
            echo "No data available for user: '$getNameFromId'!";
        } else {
            $currentDate = date("Y-m-d");
            // Set the headers to make the browser treat the response as an Excel file
            header("Content-Type: application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=orders_made_by_user_" . $getNameFromId . "_" . $currentDate . ".xls");

            echo "Order ID\tUser ID\tName\tNumber\tEmail\tAddress\tAddress Type\tMethod\tProduct ID\tProduct Name\tPrice\tQuantity\tDate\tStatus\tTotal Revenue\r\n";
            foreach ($ordersData as $row) {
                echo $row['id'] . "\t";
                echo $row['user_id'] . "\t";
                echo $row['name'] . "\t";
                echo $row['number'] . "\t";
                echo $row['email'] . "\t";
                echo $row['address'] . "\t";
                echo $row['address_type'] . "\t";
                echo $row['method'] . "\t";
                echo $row['product_id'] . "\t";
                echo $row['product_name'] . "\t";
                echo $row['price'] . "\t";
                echo $row['qty'] . "\t";
                echo $row['date'] . "\t";
                echo $row['status'] . "\t";
                echo ($row['qty'] * $row['price']) . "\r\n";
            }
        }

        exit;
    }

    public function generateReport_StockByStatus($type)
{
    global $conn;

    if ($type === 'in-stock') {
        $query = "SELECT name, price, image, category_id, product_detail, qty
                  FROM products
                  WHERE qty > 0";
        $filename = "in_stock_report_" . date("Y-m-d") . ".xls";
    } elseif ($type === 'out-of-stock') {
        $query = "SELECT name, price, image, category_id, product_detail, qty
                  FROM products
                  WHERE qty <= 0";
        $filename = "out_of_stock_report_" . date("Y-m-d") . ".xls";
    } else {
        echo "Invalid report type.";
        exit;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($productsData) === 0) {
        echo "No data available for the selected type: '$type'!";
    } else {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-disposition: attachment; filename=" . $filename);

        echo "Product Name\tPrice\tImage\tCategory ID\tProduct Detail\tQuantity\r\n";
        foreach ($productsData as $row) {
            echo $row['name'] . "\t";
            echo $row['price'] . "\t";
            echo $row['category_id'] . "\t";
            echo $row['product_detail'] . "\t";
            echo $row['qty'] . "\r\n";
        }
    }

    exit;
}

}

// Create an instance of exporter class 
$exporter = new Exporter();

/**
 * Generate reports for shop App
 */
if (isset($_GET['type'])) {
    switch ($_GET['type']) {
        case 'most-perf-prod':
            $exporter->generateReport_MostPerformingProduct();
            break;
        case 'gen-date-range':
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $startDate = $_POST["startDate"];
                $endDate = $_POST["endDate"];
                $exporter->generateReport_OrdersFromCertainDates($startDate, $endDate);
            }
            break;
        case 'user-most-orders':
            $exporter->generateReport_UserWithHighestPurchases();
            break;

        case 'gen-status':
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $status = $_POST["status"];
                $exporter->generateReport_OrdersByStatus($status);
            }
            break;

        case 'gen-users-orders':
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $userId = $_POST["id"];
                $exporter->generateReport_OrdersMadeByAUser($userId);
            }
            break;
    }
}


$fetch_users = $conn->query("SELECT id, name FROM users");
$users = $fetch_users->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    <?php include 'admin_style.css'; ?>
</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin dashboard</title>
</head>

<body>

    <?php include '../components/admin_header.php'; ?>
    <div class="main">
        <div class="banner">
            <h1>reports</h1>
        </div>
        <div class="title2">
            <a href="index.php">home </a><span>/ reports</span>
        </div>
        <section class="accounts">
            <h1 class="heading">Generate various reports</h1>
            <div class="box-container">

                <a href="reports.php?type=most-perf-prod" class="btn" style="margin-bottom: 22px;">Generate Report for Top Selling Products</a>

                <a href="#" class="btn" onclick="toggleDateInputsVisibility(event)" style="margin-bottom: 22px;">Generate Orders Report by Date Range</a>
                <div id="dateInputs" style="display: none;">
                    <div class="input-field">
                        <input placeholder="Enter start date" name="startDate" class="form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')">
                        <small></small>
                    </div>
                    <div class="input-field">
                        <input placeholder="Enter end date" name="endDate" class="form-control" type="text" onfocus="(this.type='date')" onblur="(this.type='text')">
                        <small></small>
                    </div>
                    <button name="submit-date-range-btn" class="btn" onclick="submitDateRange()">Submit</button>
                </div>

                <a href="reports.php?type=user-most-orders" class="btn" style="margin-bottom: 22px;">Generate report for user with the most orders </a>

                <a href="#" class="btn" onclick="toggleStatusDropdownVisibility(event)" style="margin-bottom: 22px;">Generate Orders Report by Status</a>
                <div id="statusDropdown" style="display: none;">
                    <div class="input-field">
                        <select name="statusSelect">
                            <option value="" disabled selected hidden> Check orders with status </option>
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <small></small>
                    </div>
                    <button onclick="submitOrderStatus()" name="submit-statusSelect-btn" class="btn">Submit</button>
                </div>

                <a href="#" class="btn" onclick="toggleUserDropdownVisibility(event)">Generate Users and their Orders Report</a>
                <div id="userDropdown" style="display: none;">
                    <div class="input-field">
                        <select name="userSelect">
                            <option value="" disabled selected hidden> Check orders made by user</option>

                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                            <?php endforeach; ?>

                        </select>
                        <small></small>
                    </div>
                    <button onclick="submitOrderMadeByUser()" name="submit-userSelect-btn" class="btn">Submit</button>
                </div>

            </div>
        </section>
    </div>

    <script type="text/javascript" src="script.js"></script>

    <script>
        function toggleDateInputsVisibility(event) {
            event.preventDefault();
            const dateInputs = document.getElementById("dateInputs");
            dateInputs.style.display = dateInputs.style.display === "none" ? "block" : "none";
        }

        function toggleStatusDropdownVisibility(event) {
            event.preventDefault();
            const statusDropdown = document.getElementById('statusDropdown');
            statusDropdown.style.display = statusDropdown.style.display === 'none' ? 'block' : 'none';
        }

        function toggleUserDropdownVisibility(event) {
            event.preventDefault();
            const userDropdown = document.getElementById('userDropdown');
            userDropdown.style.display = userDropdown.style.display === 'none' ? 'block' : 'none';
        }

        function submitDateRange() {
            const startDateEl = document.querySelector('[name="startDate"]');
            const endDateEl = document.querySelector('[name="endDate"]');

            const btn = document.querySelector('[name="submit-date-range-btn"]');

            // Function to show error state for an input field
            const showError = (input, message) => {
                const formField = input.parentElement;
                formField.classList.remove('success');
                formField.classList.add('error');
                const error = formField.querySelector('small');
                error.textContent = message;
            };

            const hideError = (input) => {
                const formField = input.parentElement;
                formField.classList.remove('error');
                formField.classList.add('success');
            };

            // Function to validate the date input
            const validateDateInput = (input) => {
                if (!input.value) {
                    showError(input, 'Please enter a value');
                    return false;
                }
                return true;
            };


            btn.addEventListener('click', (e) => {
                // Check each field for errors and if any fields have errors, don't submit the form
                let isValidForm = true;

                if (!validateDateInput(startDateEl)) {
                    isValidForm = false;
                }

                if (!validateDateInput(endDateEl)) {
                    isValidForm = false;
                }

                if (isValidForm) {

                    fetch("reports.php?type=gen-date-range", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: `startDate=${startDateEl.value}&endDate=${endDateEl.value}`,
                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data.startsWith("No data")) {
                                alert(data);
                            } else {
                                const blob = new Blob([data], {
                                    type: "application/vnd.ms-excel"
                                });
                                const url = URL.createObjectURL(blob);
                                const a = document.createElement("a");
                                a.href = url;
                                a.download = "orders_by_date_range.xls";
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);

                                dateInputs.style.display = "none";
                            }
                        });
                } else {
                    // Prevent form submission
                    e.preventDefault();
                }
            });

        }

        function submitOrderStatus() {

            const selectedStatus = document.querySelector('[name="statusSelect"]');
            const btn = document.querySelector('[name="submit-statusSelect-btn"]');

            const showError = (input, message) => {
                const formField = input.parentElement;
                formField.classList.remove('success');
                formField.classList.add('error');
                const error = formField.querySelector('small');
                error.textContent = message;
            };
            const validateDateInput = (input) => {
                if (!input.value) {
                    showError(input, 'Please select a status');
                    return false;
                }
                return true;
            };


            btn.addEventListener('click', (e) => {
                // Check each field for errors and if any fields have errors, don't submit the form
                let isValidForm = true;

                if (!validateDateInput(selectedStatus)) {
                    isValidForm = false;
                }

                if (isValidForm) {
                    // Make a fetch request to generate the report based on the selected status
                    fetch(`reports.php?type=gen-status`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: `status=${selectedStatus.value}`,

                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data.startsWith("No data")) {
                                alert(data);
                            } else {
                                const blob = new Blob([data], {
                                    type: "application/vnd.ms-excel"
                                });
                                const url = URL.createObjectURL(blob);
                                const a = document.createElement("a");
                                a.href = url;
                                a.download = `orders_by_status_${selectedStatus.value}.xls`;
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);

                                statusDropdown.style.display = "none";
                            }
                        });
                } else {
                    // Prevent form submission
                    e.preventDefault();
                }
            });


        }

        function submitOrderMadeByUser() {

            const selectedUser = document.querySelector('[name="userSelect"]');
            const btn = document.querySelector('[name="submit-userSelect-btn"]');

            const showError = (input, message) => {
                const formField = input.parentElement;
                formField.classList.remove('success');
                formField.classList.add('error');
                const error = formField.querySelector('small');
                error.textContent = message;
            };
            const validateDateInput = (input) => {
                if (!input.value) {
                    showError(input, 'Please select a status');
                    return false;
                }
                return true;
            };


            btn.addEventListener('click', (e) => {
                // Check each field for errors and if any fields have errors, don't submit the form
                let isValidForm = true;

                if (!validateDateInput(selectedUser)) {
                    isValidForm = false;
                }

                if (isValidForm) {
                    // Make a fetch request to generate the report based on the selected status
                    fetch(`reports.php?type=gen-users-orders`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: `id=${selectedUser.value}`,

                        })
                        .then(response => response.text())
                        .then(data => {
                            if (data.startsWith("No data")) {
                                alert(data);
                            } else {
                                const blob = new Blob([data], {
                                    type: "application/vnd.ms-excel"
                                });
                                const url = URL.createObjectURL(blob);
                                const a = document.createElement("a");
                                a.href = url;
                                a.download = `orders_made_by_user_${selectedUser.value}.xls`;
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);

                                statusDropdown.style.display = "none";
                            }
                        });
                } else {
                    // Prevent form submission
                    e.preventDefault();
                }
            });


        }
    </script>


</body>

</html>