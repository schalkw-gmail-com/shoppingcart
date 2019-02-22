<?php

    /* debug function to allow for easy viewing of any variable. It is not placed in the class as to make it accessibe everywhere */
    function debug($value){
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

  // debug($_REQUEST);

    class shoppingCart
    {
        public $productList; // default list of products + the added products for this session */
        public $shoppingCartList; // list of items currenty in the shopping cart */

        /* default list of products as provided */
        public $products = [
            [ "name" => "Sledgehammer", "price" => 125.75 ],
            [ "name" => "Axe", "price" => 190.50 ],
            [ "name" => "Bandsaw", "price" => 562.131 ],
            [ "name" => "Chisel", "price" => 12.9 ],
            [ "name" => "Hacksaw", "price" => 18.45 ],
        ];
        public $sessionId;
        public $productCount; //counter to provide count the number of available products in the list

        public function __construct($sessionId){
            $this->sessionId = $sessionId;

            //ensure that the shopping carts starts with the default list of products on startip
            //this is only done once per session
            if(!isset($this->productList[$this->sessionId])){
                $this->productList[$this->sessionId] = $this->products;
            }
        }

        public function listProducts(){
            $productList = <<<HTML
                <table>                              
                  <tr>
                    <td><b>Name</b></td>
                    <td><b>Price</b></td>
                    <td></td>
                  </tr>
HTML;

            foreach($this->productList[$this->sessionId] as $product => $detail){
                $cost = number_format($detail['price'], 2, '.','');
                $productList .= <<<HTML
                  <tr>
                    <td>{$detail['name']}</td>
                    <td>{$cost}</td>
                    <td><a href="javascript:{}" onclick="document.getElementById('action').value='add';document.getElementById('product').value='{$detail['name']}';document.getElementById('shoppingcart').submit();">Add to cart</a></td>
                  </tr>
HTML;
            }

            $productList .= <<<HTML
                <tr>
                    <td><input type="text" id="name" name="productName"/></td>
                    <td><input type="text" id="price" name="productPrice"/></td>
                    <td><a href="javascript:{}" onclick="document.getElementById('shoppingcart').submit();">Add to product list</a></td>
                </tr>
                
                </table>
HTML;
            echo $productList;
        }

        public function productCounter(){
            $this->productCount = count($this->productList[$this->sessionId]);
        }

        public function addProductToList($product,$price){
            //count the number of items in the product list
            $this->productCounter();
            if(($product != '') && ($price != '')){//ensure that both the name and price has a value before adding it to the list. this is to ensure that you do not add blank values.
                $this->productList[$this->sessionId][$this->productCount]['name'] = $product;
                $this->productList[$this->sessionId][$this->productCount]['price'] = $price;
            }
        }

        // this function display the shopping cart.
        public function shoppingCart(){
            $shoppingCartList = <<<HTML
                <table>                              
                  <tr>
                    <td><b>Name</b></td>
                    <td><b>Price</b></td>
                    <td><b>Quantity</b></td>
                    <td><b>Total</b></td>
                    <td><b>Remove</b></td> 
                    <td><a href="javascript:{}" onclick="document.getElementById('action').value='clear';document.getElementById('shoppingcart').submit();">Clear Cart</a></td>
                  </tr>
HTML;

            //check if there is a shopping cart for this session
            if(isset($this->shoppingCartList[$this->sessionId]) ) {

                $totalCost = 0;

                // loop through the list of products in the shopping cart
                foreach($this->shoppingCartList[$this->sessionId] as $product => $detail){
                    // gather all the data for the product
                    $data = $this->findProductData($product);
                    // get the total qty for this product
                    $qty = $this->totalProductInCart($product);

                    // calculate the total cost for the this product
                    // while we are busy with the calculation apply the numbering format
                    $cost = number_format(($qty * $data['price']),2,'.','');
                    $totalCost += $cost; //increase the total cost of the cart

                    $shoppingCartList .= <<<HTML
                      <tr>
                        <td>{$data['name']}</td>
                        <td>{$data['price']}</td>
                        <td>{$qty}</td>
                        <td>{$cost}</td>
                        <td><a href="javascript:{}" onclick="document.getElementById('action').value='remove';document.getElementById('product').value='{$data['name']}';document.getElementById('shoppingcart').submit();">Remove</a></td>                       
                      </tr>
HTML;
                }
                // ensure that the total cost is display in the correct format
                $totalCost = number_format($totalCost, 2, '.','');
                $shoppingCartList .= <<<HTML
                      <tr>
                        <td><b>Total</b></td>
                        <td></td>
                        <td></td>
                        <td><b>{$totalCost}</b></td>
                        <td></td>                       
                      </tr>
HTML;

            }else{
                // if nothing is in the shopping cart display a message to that effect
                $shoppingCartList .= <<<HTML
                      <tr>
                        <td colspan="6">Nothing has been added to your cart yet.</td>
                      </tr>
HTML;
            }

            echo $shoppingCartList;
        }

        public function hiddenElements(){
            $elements = <<<HTML
              <input type="hidden" name="product" id="product" value="" />
              <input type="hidden" name="action" id="action" value="" />
HTML;
            echo $elements;
        }

        // add an item to the shopping cart.
        public function addToCart($product){
            //ensure that we have a shopping cart for this session
            if(!isset($this->shoppingCartList[$this->sessionId])){
                $this->shoppingCartList[$this->sessionId] = array();
            }
            //make sure the product has been added to the shopping cart for this session
            if(!isset($this->shoppingCartList[$this->sessionId][$product])){
                $this->shoppingCartList[$this->sessionId][$product] = 0;
            }
            //increase the qty for the specific selection
            $this->shoppingCartList[$this->sessionId][$product] += 1;
        }

        // remove a specific item from the cart. Star by decreasing the qty by 1 and if the qty reaches 0 remove the product from the lit
        // thereby not showing it in the shopping as an item with 0 qty
        public function removeFromCart($product){
            $this->shoppingCartList[$this->sessionId][$product] -= 1;
            if($this->shoppingCartList[$this->sessionId][$product] < 1){
                unset($this->shoppingCartList[$this->sessionId][$product]);
            }
        }

        //search through the product list array for the data (name and price) of the selected product and return the key
        public function findProductData($product){
            //array_column return the values of a given array where the key matches the key you are searching for
            $key = array_search($product, array_column($this->productList[$this->sessionId], 'name'));
            return $this->productList[$this->sessionId][$key];
        }

        //count all the products in the shopping cart
        public function totalProductInCart($product){
            $total =0;
            if($product == ''){
                return 'count all the products in the cart';
            }else{
                $total = $this->shoppingCartList[$this->sessionId][$product];
            }

            return $total;
        }

        // clear everything out of the shopping cart
        public function clearCart(){
            unset($this->shoppingCartList[$this->sessionId]);
        }
    }