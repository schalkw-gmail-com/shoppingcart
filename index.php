<?php

    require_once "shoppingCart.php";

    // check if the session has been started.
    if (!session_id()) {
        session_start();
        $sessionId = session_id();

        //check to see if we have a shopping cart for this session. if not start the shopping cart with the current session id
        if(!isset($_SESSION[$sessionId]['shop'])){
            $_SESSION[$sessionId]['shop'] = new shoppingCart($sessionId);
        }
        // assign the current shopping to the shop variable in order to use the functionality
        $shop = $_SESSION[$sessionId]['shop'];
    }

    // check if the product name and price text boxes has been submitted, if both have been submitted add the product to the list
    if((isset($_POST['productName'])) && isset($_POST['productPrice'])){
        $shop->addProductToList($_POST['productName'],$_POST['productPrice']);
    }

    //check what action has been clicked on the links and call the appropriate shopping cart function
    if(isset($_POST['action'])){
        if($_POST['action'] == 'add'){
            $shop->addToCart($_POST['product']);
        }elseif($_POST['action'] == 'remove'){
            $shop->removeFromCart($_POST['product']);
        }elseif($_POST['action'] == 'clear'){
            $shop->clearCart();
        }
        // redirect the page to the it self in order to avoid executing the last action when the page is refreshed
        header('Location: index.php');
    }
?>

<html>
    <body>
        <form  id="shoppingcart" method="post">
            <?php
                //print the hidden elements for the shop control
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
                                        // list the product available for selection
                                        $shop->listProducts();
                                    ?>
                                </td>
                                <td style="text-align: center">
                                    <?php
                                        //show the current state of the shopping cart
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
