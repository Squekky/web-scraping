<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Ryan's Market</title>
        <link rel="stylesheet" href="../styles/template.css"/>
        <link rel="stylesheet" href="../styles/links.css"/>
        <link rel="shortcut icon" href="https://cdn.7tv.app/emote/63371ac69af9b93dad7ba9e5/4x.webp" />
        <style>
            table.checkout {
                border-collapse: collapse; 
                width: 90%;
            }

            td {
                text-align: center; 
                border: 1px solid rgb(50, 50, 50);
            }

            td.itemimage, td.itemprice {
                padding: 2px;
                width: 100px; 
            }
            td.billingaddress, td.paymentinfo {
                padding: 8px;
                text-align: left; 
                font-size: 20px; 
            }

            td.billingaddress, td.paymentinfo {
                border-right: none;
            }

            td.paymentinfo, td.placeorder {
                border-left: none;
            }

            td.tableheader, td.billingheader {
                padding: 5px 0px 5px 0px; 
            }

            td.placeorder {
                padding-top: 27%;
            }

            tr.tableheaders, tr.billingheader {
                font-size: 25px;
                color: white; 
                background-color: rgb(30, 30, 30); 
            }

            tr.item {
                background-color: rgb(245, 245, 245);
            }

            input {
                padding: 8px; 
                width: 400px;
            }

            button.placeorder {
                height: 70px; 
                width: 120px; 
                font-size: 20px; 
                cursor: pointer;
                color: white; 
                background-color: rgb(0, 181, 115); 
                border: 1px solid rgb(0, 82, 12); 
            }
            
            img {
                height: 50px;
            }
        </style>
    </head>
    <body>
        <div id="topbar">
            <table class="topbar">
                <tr>
                    <td class="home">
                      <a href="./store.php" class="home">Ryan's Market</a>
                    </td>
                </tr>
            </table>
        </div>
        <div id="mainpage">
            <table class="checkout" align="center">
                <tr class="tableheaders">
                    <td class="tableheader"></td>
                    <td class="tableheader" colspan="7">Product</td>
                    <td class="tableheader">Price</td>
                </tr>
                <tr class="item">
                    <?php
                        $file = file_get_contents("../config.json");
                        $json = json_decode($file, true);
                        $cnx = new mysqli($json['Host'], $json['User'], $json['Password'], $json['Database']);
                        if ($cnx->connect_error) {
                            die('Connection failed:' . $cnx->connect_error);
                        }

                        $query = 'SELECT * FROM Products WHERE productId = "' . $_POST['checkoutitem'] . '"';
                        $cursor = $cnx->query($query);
                        while ($row = $cursor->fetch_assoc()) {
                            $price = number_format($row['productPrice'] * 1.10, 2);
                            $itemName = $row['productName'];
                            echo '<td class="itemimage"><img src="' . $row['productImage'] . '"/></td>';
                            echo '<td class="itemname" colspan="7">' . $itemName . '</td>';
                            echo '<td class="itemprice">$' . $price . '</td>';

                        }
                        $cnx->close();
                    ?>
                    </td>
                </tr>
                <tr class="billingheader">
                    <td class="billingheader" colspan="9">
                        Billing Information
                    </td>
                </tr>
                <tr class="billinginfo">
                    <form action="./confirmed.php" method="post">
                        <td class="billingaddress" colspan="4">
                            <h3>Billing Address</h3>
                            <label for="fullname">Full Name</label>
                            <br />
                            <input type="text" name="fullname" placeholder="John A. Doe" maxlength="255" required />
                            <br /><br />
                            <label for="address">Address</label>
                            <br />
                            <input type="text" name="address" placeholder="323 Dr Martin Luther King Jr Blvd" maxlength="255" required />
                            <br /><br />
                            <label for="city">City</label>
                            <br />
                            <input type="text" name="city" placeholder="Newark" maxlength="100" required />
                            <br /><br />
                            <label for="state">State</label>
                            <br />
                            <input type="text" name="state" placeholder="NJ" maxlength="15" required />
                            <br /><br />
                            <label for="zip">Zip</label>
                            <br />
                            <input type="text" name="zip" placeholder="07102" pattern="[0-9]{5}(-[0-9]{4})?" required />
                            <br />
                        </td>
                        <td class="paymentinfo" colspan="3">
                            <h3>Payment Information</h3>
                            <label for="cardholder">Name of Cardholder</label>
                            <br />
                            <input type="text" name="cardholder" placeholder="John A. Doe" maxlength="255" required />
                            <br /><br />
                            <label for="cardnumber">Credit Card Number</label>
                            <br />
                            <input type="text" name="cardnumber" placeholder="1234-1234-1234-1234" pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" required />
                            <br /><br />
                            <label for="cvv">CVV</label>
                            <br />
                            <input type="text" name="cvv" placeholder="837" pattern="[0-9]{3}" required />
                            <br /><br />
                            <label for="expmonth">Expiration Month</label>
                            <br />
                            <input type="text" name="expmonth" placeholder="November"  maxlength="10" required />
                            <br /><br />
                            <label for="expyear">Expiration Year</label>
                            <br />
                            <input type="number" name="expyear" placeholder="2027" min="2023" max="9999" required />
                            <br />
                        </td>
                        <td class="placeorder" colspan="2">
                            <button class="placeorder">Place Order</button>
                        </td>
                        <input type="hidden" name="price" value="<?php echo $price; ?>" />
                        <input type="hidden" name="item" value="<?php echo $itemName; ?>" />
                    </form>
                </tr>
            </table>
        </div>
    </body>
</html>
