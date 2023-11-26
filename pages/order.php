<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Ryan's Market</title>
        <link rel="stylesheet" href="../styles/template.css" />
        <link rel="stylesheet" href="../styles/links.css" />
        <link rel="shortcut icon" href="https://cdn.7tv.app/emote/63371ac69af9b93dad7ba9e5/4x.webp" />
        <style>
            table.products {
                table-layout: fixed;
                position: relative; 
                width: 80%;
                color: black; 
                border: 2px solid black; 
            }

            table.purchase {
                table-layout: fixed;
                padding: 0px;
                width: 100%; 
                font-size: 28px; 
                text-align: center; 
            }

            td.product {
                padding: 0px 2px 0px 2px; 
                height: 500px; 
                text-align: center; 
                border: 2px solid black;
            }
            div.product1, div.product2 {
                padding: 2px;
            }

            td.button {
                text-align: left; 
                padding: 0px;
            }

            td.purchase {
                border: 1px solid black;
            }

            td.rating::after {
                content: attr(star); 
                color: rgb(255, 210, 28); 
                text-shadow: 0px 0px 2px rgb(54, 54, 54);
            }

            button.checkout {
                height: 50px; 
                width: 100%; 
                font-size: 28px;
                cursor: pointer; 
                color: white;  
                background-color: rgb(0, 181, 115); 
                border: 1px solid rgb(0, 82, 12); 
            }

            div.description {
                position: relative; 
                height: 250px; 
                width: 100%; 
                overflow-y: scroll;
                font-size: 20px; 
                text-align: left; 
            }

            span.description {
                text-align: center; 
                text-decoration: underline;
                font-size: 16px; 
                color: rgb(57, 57, 57); 
            }
            
            span.productname {
                line-height: 30px;
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
            <table class="products" align="center">
                <?php 
                    $item_id = $_POST['item'];
                    $item_one_price = 0.0;
                    $item_two_price = 0.0;

                    $file = file_get_contents("../config.json");
                    $json = json_decode($file, true);
                    $cnx = new mysqli($json['Host'], $json['User'], $json['Password'], $json['Database']);
                    if ($cnx->connect_error) {
                        die('Connection failed:' . $cnx->connect_error);
                    }

                    $query = 'SELECT * FROM Products WHERE productId LIKE "_' . $item_id . '"';
                    $cursor = $cnx->query($query);
                    $count = 1;
                    while ($row = $cursor->fetch_assoc()) {
                        if ($count == 1) {
                            $item_one_price = $row['productPrice'];
                        } else {
                            $item_two_price = $row['productPrice'];
                        }
                        echo '<td class="product">';
                        echo '<div class="product' . $count . '">';
                        echo '<img src="' . $row['productImage'] . '" width="200px" class="image" />';
                        echo '<br />';
                        echo $row['productName'];
                        echo '<br /><br />';
                        echo '<table class="purchase" align="center"><tr><td class="rating purchase" star="★">';
                        if ($row['productRating'] == -1) {
                            echo 'N/A';
                        } else {
                            echo $row['productRating'];
                        }
                        echo '<td colspan="3" class="purchase"><form method="post" action="./checkout.php">$' . number_format($row['productPrice'] * 1.10, 2) . '</td>';
                        echo '</td><td class="button purchase">';
                        echo '<button class="checkout" name="checkoutitem" value="' . $row['productId'] . '">Buy</button>';
                        echo '</form></td></tr></table><br /><br />';
                        echo '<span class="description">Product Description</span>';
                        echo '<div class="description" align="center">' . $row['productDescription'] . '</div>';
                        echo '</div>';
                        echo '</td>';
                        $count++;
                    }
                    echo '<style>';
                    if ($item_two_price < $item_one_price) {
                        echo 'div.product2 {margin: 1px; padding: 5px; border: 5px dotted red;}';
                    } else if ($item_one_price < $item_two_price) {
                        echo 'div.product1 {margin: 1px; padding: 5px; border: 5px dotted red;}';
                    }
                    echo '</style>';
                    $cnx->close();
                ?>
            </table>
        </div>
    </body>
</html>
