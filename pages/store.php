<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Ryan's Market</title>
        <link rel="stylesheet" href="../styles/template.css"/>
        <link rel="stylesheet" href="../styles/links.css"/>
        <link rel="shortcut icon" href="https://cdn.7tv.app/emote/63371ac69af9b93dad7ba9e5/4x.webp" />
        <style>
            body {
              margin-bottom: 100px;
            }

            table.products {
              table-layout: fixed;
              position: relative; 
              width: 100%; 
              color: black; 
              border: 2px solid black; 
            }

            table.purchase {
              table-layout: fixed;
              width: 99%;
              font-size: 18px;
            }

            td.product {
              text-align: center;
              padding: 5px 5px 2px 5px;
              height: 400px; 
              border: 2px solid black; 
            }

            td.rating, td.price, td.purchase {
              border: 1px solid black;
            }

            td.purchase {
              padding: 0px;
            }

            td.rating::after {
              content: attr(star); 
              color: rgb(255, 210, 28); 
              text-shadow: 0px 0px 2px rgb(54, 54, 54);
            }

            button.purchase {
              width: 100%; 
              font-size: 24px;
              cursor: pointer; 
              color: white;
              background-color: rgb(0, 181, 115); 
              border: 1px solid rgb(0, 82, 12); 
            }

            button.description {
              font-size: 10px;
              cursor: pointer; 
              background-color: white; 
              border: none; 
            }

            img.productimg {
              height: 300px;
            }

            span.productname {
              font-size: 18px;
            }

            div.collapsible {
              height: 20px;
            }

            div.description {
              width: 100%;
              position: relative; 
              margin-top: -135%; 
              font-size: 0px; 
              opacity: 0; 
            }

            label {
              font-size: 14px; 
              display: block; 
              cursor: pointer; 
              color: rgb(87, 87, 87);
            }

            input {
              display: none;
            }

            input:checked ~ div.description {
              position: relative; 
              z-index: 2; 
              margin-top: -135%; 
              height: 400px; 
              width: 308px; 
              padding: 4px; 
              font-size: 16px; 
              text-align: left;
              overflow-y: scroll; 
              background-color: white; 
              border: 1px solid black; 
              opacity: 1; 
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
              $file = file_get_contents("../config.json");
              $json = json_decode($file, true);
              $cnx = new mysqli($json['Host'], $json['User'], $json['Password'], $json['Database']);
              if ($cnx->connect_error) {
                die('Connection failed:' . $cnx->connect_error);
              }

              $query = 'SELECT * FROM Products';
              $cursor = $cnx->query($query);
              $count = 0;
              while ($row = $cursor->fetch_assoc()) {
                if ($count % 5 == 0) {
                  if ($count != 0) {
                    echo '</tr>';
                  } else {
                    echo '<tr>';
                  }
                }
                $item_num = substr($row['productId'], 1);
                echo '<td class="product">';
                echo '<div class="product">';
                echo '<img src="' . $row['productImage'] . '" class="productimg"/>';
                echo '<br /><br />';
                echo '<span class="productname">' . $row['productName'] . '</span>';
                echo '<br /><br />';
                echo '<table class="purchase" align="center"><tr>';
                echo '<td class="rating" star="★">';
                if ($row['productRating'] == -1) {
                  echo 'N/A';
                } else {
                  echo $row['productRating'];
                }
                echo '</td><td class="price" colspan="2">$' . number_format($row['productPrice'] * 1.10, 2) . '</td>';
                echo '<td class="purchase"><form method="post" action="./order.php">';
                echo '<button class="purchase" name="item" value="' . $item_num . '">Buy</button>';
                echo '</form></td></tr></table><br />';
                echo '<div class="collapsible">';
                echo '<input type="checkbox" id="expand' . $row['productId'] . '"/>';
                echo '<label for="expand' . $row['productId'] . '">Description</label>';
                echo '<div class="description" align="center">' . $row['productDescription'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '</td>';
                $count++;
              }
              $cnx->close();
            ?>
          </table>
        </div>
      </body>
</html>
