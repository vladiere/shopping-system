<?php
    require("database.php");
    class backend
    {
        public function login($email,$Password){
            return self::loginAccounts($email,$Password);
        }
        
        public function register($role, $fullname, $contact, $email, $password){
            return self::registerAccount($role, $fullname, $contact, $email, $password);
        }
    
        public function addProducts($productname, $price, $qty){
            return self::addProduct($productname, $price, $qty);
        }
        public function viewProduct(){
            return self::getProduct();
        }

        public function deleteProduct($prodID)
        {
            return self::deletedProduct($prodID);
        }

        public function addStock($id, $qty, $stockQty)
        {
            $newQty = $qty + $stockQty;
            return self::updateStocks($id, $newQty);
        }

        public function loginRole()
        {
            return self::getRole();
        }

        public function addToCart($productID, $qty)
        {
            return self::addThisProduct($productID, $qty);
        }

        public function viewMyCart()
        {
            return self::viewCart();
        }

        public function deleteFromCart($prod_id, $qty)
        {
            return self::deleteCartProd($prod_id, $qty);
        }

        public function displayCheckOut()
        {
            return self::checkOutDisplay();
        }

        public function checkOut($prodID, $sellerid)
        {
            return self::insertToReceipt($prodID, $sellerid);
        }

        public function delivery()
        {
            return self::viewDelivery();
        }

        private function viewDelivery()
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->viewDeliveryQuery());
                        $stmt->execute(array($this->getId()));
                        $res = $stmt->fetchAll();
                        
                        $db->closeConn();
                        return json_encode($res);
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function insertToReceipt($prodID, $sellerid)
        {
            try {
                if ($prodID != '') {
                    $db = new database();
                    $var = $this->getCustomerIDandItem($sellerid, $prodID);
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->insertToReceiptQuery());
                        $stmt->execute(array($sellerid, $this->getId(), $var[0], $var[1], $var[2], $this->getCurrentDate(), $this->getSevenDaysDate()));
                        $res = $stmt->fetch();

                        if (!$res) {
                            $db->closeConn();
                            return self::removeCheckOut($prodID);
                        } else {
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function removeCheckOut($prodID)
        {
            try {
                if ($prodID != '') {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->deleteFromBuyerQuery());
                        $stmt->execute(array($prodID, $this->getId()));
                        $res = $stmt->fetch();
                        if (!$res) {
                            $db->closeConn();
                            return "200";
                        } else {
                            $db->closeConn();
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function checkOutDisplay()
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->getReceiptItem());
                        $stmt->execute(array($this->getId()));
                        $res = $stmt->fetchAll();

                        $db->closeConn();
                        return json_encode($res);
                    } else {
                        $db->closeConn();
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function deleteCartProd($prod_id, $qty)
        {
            try {
                if ($prod_id != '' && $qty != '') {
                    $db = new database();
                    $var = $this->getSingleProduct($prod_id);
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->deleteFromBuyerQuery());
                        $stmt->execute(array($prod_id, $this->getId()));
                        $res = $stmt->fetch();
                        if (!$res) {
                            $db->closeConn();
                            return self::updateStocks($prod_id, $var[4] + $qty);
                        } else {
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        private function viewCart()
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->getQueryForBuyer());
                        $stmt->execute(array($this->getId()));
                        $view = $stmt->fetchall();
                        return json_encode($view);
                    } else {
                        return "404";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function addThisProduct($productID, $qty)
        {
            try {
                if ($productID != '') {
                    $var = $this->getSingleProduct($productID);
                    if ($this->getSingleCustomerIDandItem($productID)) {
                        $db = new database();
                        if ($db->getStatus()) {
                            $stmt = $db->getConn()->prepare($this->addToCartQuery());
                            $stmt->execute(array($var[0], $var[1], $this->getId(), $var[2], $var[3] * $qty, $qty, $this->getCurrentDate()));
    
                            $result = $stmt->fetch();
                            if (!$result) {
                                $db->closeConn();
                                return $this->updateStocks($productID, $var[4] - $qty);
                            } else {
                                return "404";
                            }
                        } else {
                            return "403";
                        }    
                    } else {
                        return $this->updateBuyer($productID, $qty, $var[4] - $qty);
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function updateStocks($id, $newQty)
        {
            try {
                if ($id != '' && $newQty != '') {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->addStockQuery());
                        $stmt->execute(array($newQty,$id));
                        $res = $stmt->fetch();
                        if (!$res) {
                            $db->closeConn();
                            return "200";
                        } else {
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        private function deletedProduct($prodID)
        {
            try {
                if ($prodID != '') {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->dropProductQuery());
                        $stmt->execute(array($prodID));
                        $res = $stmt->fetch();
                        if (!$res) {
                            $db->closeConn();
                            return "200";
                        } else {
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }

        private function loginAccounts($email, $Password)
        {
            try {
                if ($this->checkLogin($email, $Password)) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $tmp = md5($Password);
                        $stmt = $db->getConn()->prepare($this->loginQuery());
                        $stmt->execute(array($email,$tmp));
                        $result = $stmt->fetch();
                        if ($result) {
                            $_SESSION['email'] =$email;
                            $_SESSION['password'] = $tmp;
                            $db->closeConn();
                            return "200";
                        }else{
                            return "404";
                        }
                    }else{
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $th) {
                return "501";
            }
        }


        private function registerAccount($role, $fullname, $contact, $email, $password){
            try {
                if ($this->checkRegister($role, $fullname, $contact, $email, $password)) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->registerQuery());
                        $stmt->execute(array($role, $fullname, $contact, $email, md5($password), $this->getCurrentDate()));
                        $result = $stmt->fetch();
                        if (!$result) {
                            $db->closeConn();
                            return "200";
                        }else{
                            $db->closeConn();
                            return "404";
                        }
                    }else{
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $th) {
                // return "501";
                return $th;
            }
        }

        private function addProduct($productname, $price, $quantity){
            try {
                try {
                    if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                        $db = new database();
                        if ($db->getStatus()) {
                            $stmt = $db->getConn()->prepare($this->addProductQuery());
                            $stmt->execute(array($this->getId(),$productname, $price, $quantity,$this->getCurrentDate()));
                            $result = $stmt->fetch();
                            if (!$result) {
                                $db->closeConn();
                                return "200";
                            }else{
                                $db->closeConn();
                                return "404";
                            }
                        }else{
                            return "403";
                        }
                    } else {
                        return "403";
                    }
                } catch (PDOException $th) {
                    return "501";
                }
            } catch (PDOException $th) {
                return "501";
            }
        }

        private function getProduct(){
            try {
                if ($this->checkLogin($_SESSION['email'], $_SESSION['password'])) {
                    $db = new database();
                    if ($this->getRole() == "seller") {
                        if ($db->getStatus()) {
                            $stmt = $db->getConn()->prepare($this->getProductQuery());
                            $stmt->execute(array($this->getId()));
                            $result = $stmt->fetchAll();
                            $db->closeConn();
                            return json_encode($result);
                        }else{
                            $db->closeConn();
                            return "403";
                        }
                    } else if($this->getRole() == "customer") {
                        if ($db->getStatus()) {
                            $stmt = $db->getConn()->prepare($this->getProductBuyerQuery());
                            $stmt->execute();
                            $result = $stmt->fetchAll();
                            $db->closeConn();
                            return json_encode($result);
                        }else{
                            $db->closeConn();
                            return "403";
                        }
                    } else {
                        return "404";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $th) {
                return "501";
            }
        }

        private function getId(){
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->loginQuery());
                        $stmt->execute(array($_SESSION['email'],$_SESSION['password']));
                        $tmp = null;
                        while ($row = $stmt->fetch()) {
                            $tmp = $row['id'];
                        }
                        $db->closeConn();
                        return $tmp;
                    }
                    else{
                        return "404";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $th) {
                echo $th;
            }        
        }

        private function getRole()
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->loginQuery());
                        $stmt->execute(array($_SESSION["email"], $_SESSION["password"]));
                        $tmp = null;
                        while ($row = $stmt->fetch()) {
                            $tmp = $row['role'];
                        }
                        $db->closeConn();
                        return $tmp;
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return "501";
            }
        }

        private function getSingleProduct($productID)
        {
            try {
                if ($productID != '') {
                    $db = new database();
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->getSingleProductQuery());
                        $stmt->execute(array($productID));
                        $product = array();
                        while ($var = $stmt->fetch()) {
                            array_push(
                                $product,
                                $var['id'],         //0
                                $var['sellerID'],   //1
                                $var['productname'],//2
                                $var['price'],      //3
                                $var['quantity']    //4
                            );
                        }
                        return $product;
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return "501";
            }
        }

        private function updateBuyer($prod_id, $qty, $currentStock)
        {
            try {
                if ($prod_id != '') {
                    $db = new database();
                    $varQty = $this->getCartQuantity($prod_id) + $qty;
                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->updateBuyerQtyQuery());
                        $stmt->execute(array($varQty, $prod_id, $this->getId()));
                        $res = $stmt->fetch();
                        if (!$res) {
                            $db->closeConn();
                            return self::updateStocks($prod_id, $currentStock);
                        } else {
                            return "404";
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function getCartQuantity($prodID)
        {
            try {
                $db = new database();
                if ($db->getStatus()) {
                    $stmt = $db->getConn()->prepare($this->getBuyerQuery());
                    $stmt->execute(array($this->getId(), $prodID));
                    $varQty = null;
                    while($res = $stmt->fetch()){
                        $varQty = $res['quantity'];
                    }
                    $db->closeConn();
                    return $varQty;
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function getCustomerIDandItem($sellerID, $prodID)
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();

                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->getBuyerQuery());
                        $stmt->execute(array($sellerID, $prodID));
                        $var = array();
                        while ($row = $stmt->fetch()) {
                            array_push(
                                $var,
                                $row['productname'],//0
                                $row['quantity'],   //1
                                $row['total_price'],//2
                            );
                        }
                        $db->closeConn();
                        return $var;
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function getSingleCustomerIDandItem($prod_id)
        {
            try {
                if ($this->checkLogin($_SESSION["email"], $_SESSION["password"])) {
                    $db = new database();

                    if ($db->getStatus()) {
                        $stmt = $db->getConn()->prepare($this->getBuyerQuery());
                        $stmt->execute(array($this->getId(), $prod_id));
                        $res = $stmt->fetch();

                        if (!$res) {
                            $db->closeConn();
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return "403";
                    }
                } else {
                    return "403";
                }
            } catch (PDOException $e) {
                return $e;
            }
        }

        private function getCurrentDate(){
            return date("Y/m/d");
        }

        private function getSevenDaysDate()
        {
            return date('Y/m/d', strtotime('+7 days'));
        }

        private function checkRegister($role, $fullname, $contact, $email, $password)
        {
            if ($role != '' && $fullname != '' && $contact != '' && $email != '' && $password != ''){
                return true;
            }
            else{
                return false;
            }
        }

        private function checkLogin($email, $password)
        {
            if($email != '' && $password != '')
                return true;
            else
                return false;
        }

        private function dropProductQuery()
        {
            return "DELETE FROM `products` WHERE `id` = ?";
        }

        private function addStockQuery()
        {
            return "UPDATE `products` SET `quantity` = ? WHERE `products`.`id` = ?";
        }

        private function loginQuery()
        {
            return "SELECT * FROM `accounts` WHERE `email` = ? AND `password` = ?";
        }

        private function registerQuery(){
            return "INSERT INTO `accounts` (`role`,`fullname`,`number`,`email`,`password`,`created`) VALUES (?,?,?,?,?,?)";
        }

        private function addToCartQuery()
        {
            return "INSERT INTO `checked_products` (`productID`, `sellerID`, `customerID`, `productname`, `total_price`, `quantity`, `date_checked`) VALUES (?,?,?,?,?,?,?)";
        }

        private function addProductQuery(){
            return "INSERT INTO `products` (`sellerID`, `productname`, `price`, `quantity`, `date_added`) 
                    VALUES (?,?,?,?,?)";
        }

        private function deleteFromBuyerQuery()
        {
            return "DELETE FROM `checked_products` WHERE `productID` = ? AND `customerID` = ?";
        }

        private function getBuyerQuery()
        {
            return "SELECT * FROM `checked_products` WHERE `sellerID` = ? AND `productID` = ?";
        }

        private function getQueryForBuyer()
        {
            return "SELECT * FROM `checked_products` WHERE `customerID` = ?";
        }

        private function updateBuyerQtyQuery()
        {
            return "UPDATE `checked_products` SET `quantity` = ? WHERE `productID` = ? AND `customerID` = ?";
        }

        private function getProductQuery()
        {
            return "SELECT * FROM `products` WHERE `sellerID` = ?";
        }

        private function getProductBuyerQuery()
        {
            return "SELECT * FROM `products`";
        }

        private function getSingleProductQuery()
        {
            return "SELECT * FROM `products` WHERE `id` = ?";
        }

        private function insertToReceiptQuery()
        {
            return "INSERT INTO `delivery` (`sellerID`, `customerID`, `productname`, `quantity`, `total_price`, `date_checked`, `date_delivered`) VALUES (?,?,?,?,?,?,?)";
        }

        private function getReceiptItem()
        {
            return "SELECT * FROM `delivery` WHERE `customerID` = ?";
        }

        private function viewDeliveryQuery()
        {
            return "SELECT * FROM `delivery` WHERE `sellerID` = ?";
        }
    }
?>
