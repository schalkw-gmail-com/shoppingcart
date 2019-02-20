<?php

    require_once "shoppingCart.php";

    if (!session_id()) {
        session_start();
        $sessionId = session_id();

        if(!isset($_SESSION[$sessionId]['shop'])){
            $_SESSION[$sessionId]['shop'] = new shoppingCart($sessionId);
        }

        $shop = $_SESSION[$sessionId]['shop'];
    }

    if((isset($_POST['productName'])) && isset($_POST['productPrice'])){
        $shop->addProductToList($_POST['productName'],$_POST['productPrice']);
    }

    if(isset($_POST['action'])){
        if($_POST['action'] == 'add'){
            $shop->addToCart($_POST['product']);
        }elseif($_POST['action'] == 'remove'){
            $shop->removeFromCart($_POST['product']);
        }elseif($_POST['action'] == 'clear'){
            $shop->clearCart();
        }

        header('Location: index.php');
    }
?>

<html>
    <body>
        <form  id="shoppingcart" method="post">
            <?php
                $shop->hiddenElements();
            ?>
            <table>
                <tr>
                    <td>
                        <h3>Shopping Cart Test</h3>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <td style="text-align: center"><h4>Product List</h4></td>
                                <td style="text-align: center"><h4>Shopping Cart</h4></td>
                            </tr>
                            <tr style="vertical-align: top">
                                <td style="text-align: center">
                                    <?php
                                        $shop->listProducts();
                                    ?>
                                </td>
                                <td style="text-align: center">
                                    <?php
                                        $shop->shoppingCart();
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
