<?php

    function debug($value){
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

  // debug($_REQUEST);

    class shoppingCart
    {
        public $productList;
        public $shoppingCartList;
        public $products = [
            [ "name" => "Sledgehammer", "price" => 125.75 ],
            [ "name" => "Axe", "price" => 190.50 ],
            [ "name" => "Bandsaw", "price" => 562.131 ],
            [ "name" => "Chisel", "price" => 12.9 ],
            [ "name" => "Hacksaw", "price" => 18.45 ],
        ];
        public $sessionId;
        public $productCount;

        public function __construct($sessionId){
            $this->sessionId = $sessionId;

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
            $this->productCounter();
            if(($product != '') && ($price != '')){
                $this->productList[$this->sessionId][$this->productCount]['name'] = $product;
                $this->productList[$this->sessionId][$this->productCount]['price'] = $price;
            }
        }

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

            if(isset($this->shoppingCartList[$this->sessionId]) ) {

                $totalCost = 0;

                foreach($this->shoppingCartList[$this->sessionId] as $product => $detail){
                    $data = $this->findProductData($product);
                    $qty = $this->totalProductInCart($product);

                    $cost = number_format(($qty * $data['price']),2,'.','');
                    $totalCost += $cost;

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

        public function addToCart($product){
            if(!isset($this->shoppingCartList[$this->sessionId])){
                $this->shoppingCartList[$this->sessionId] = array();
            }
            if(!isset($this->shoppingCartList[$this->sessionId][$product])){
                $this->shoppingCartList[$this->sessionId][$product] = 0;
            }
            $this->shoppingCartList[$this->sessionId][$product] += 1;
        }

        public function removeFromCart($product){
            $this->shoppingCartList[$this->sessionId][$product] -= 1;
            if($this->shoppingCartList[$this->sessionId][$product] < 1){
                unset($this->shoppingCartList[$this->sessionId][$product]);
            }
        }

        public function findProductData($product){
            $key = array_search($product, array_column($this->productList[$this->sessionId], 'name'));
            return $this->productList[$this->sessionId][$key];
        }

        public function totalProductInCart($product){
            $total =0;
            if($product == ''){
                return 'count all the products in the cart';
            }else{
                $total = $this->shoppingCartList[$this->sessionId][$product];
            }

            return $total;
        }

        public function clearCart(){
            unset($this->shoppingCartList[$this->sessionId]);
        }
    }