<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Ryan's Market</title>
        <link rel="shortcut icon" href="https://cdn.7tv.app/emote/63371ac69af9b93dad7ba9e5/4x.webp" />
        <style>
            h1 {
                text-align: center; 
                line-height: 50px;
            }

            h2 {
                text-align: center;
            }
            
            a {
                text-decoration: none;
            }

            a:link {
                color: rgb(20, 20, 20);
            }

            a:visited {
                color: rgb(20, 20, 20);
            }

            a:hover {
                color: rgb(50, 50, 50);
            }

            a:active {
                color: rgb(150, 150, 150);
            }
        </style>
    </head>
    <body>
        <h1>
            <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 'On');

                $file = file_get_contents("../config.json");
                $json = json_decode($file, true);
                $cnx = new mysqli($json['Host'], $json['User'], $json['Password'], $json['Database']);
                if ($cnx->connect_error) {
                    die('Connection failed:' . $cnx->connect_error);
                }

                $query = "SELECT * FROM Sales";
                $cursor = $cnx->query($query);

                $orderNum = $cursor->num_rows + 1;
                $name = $_POST['fullname'];
                $address = $_POST['address'];
                $city = $_POST['city'];
                $state = $_POST['state'];
                $zip = $_POST['zip'];
                $cardholder = $_POST['cardholder'];
                $cardnumber = $_POST['cardnumber'];
                $cvv = $_POST['cvv'];
                $expmonth = $_POST['expmonth'];
                $expyear = $_POST['expyear'];
                $item = $_POST['item'];
                $price = $_POST['price'];

                $query = "INSERT INTO Sales(orderNum, customerName, customerAddress, customerCity, customerState, customerZip, cardHolder, cardNumber, cvv, expMonth, expYear, itemPurchased, price) ";
                $query = $query . "VALUES('$orderNum', '$name', '$address', '$city', '$state', '$zip', '$cardholder', '$cardnumber', '$cvv', '$expmonth', '$expyear', '$item', '$price')";
                if ($cnx->query($query) === TRUE) {
                    echo "Order Confirmed";
                } else {
                    echo "Order Unsuccessful";
                }
                $cnx->close();
            ?>
        </h1>
        <h2><a href="./store.php">Back to Store</a></h2>
    </body>
</html>