<?php 
    session_start();
    require './backend.php';

    if (isset($_POST["choice"])) {
        switch ($_POST["choice"]) {
            case 'login':
                $back = new backend();
                echo $back->login($_POST["email"], $_POST["password"]);
                break;
            case 'register':
                $back = new backend();
                echo $back->register($_POST["role"],$_POST["fullname"], $_POST["contact"], $_POST["email"], $_POST["password"]);
                break;
            case 'getrole':
                $back = new backend();
                echo $back->loginRole();
                break;
            case 'addproduct':
                $back = new backend();
                echo $back->addProducts($_POST["productname"], $_POST["price"], $_POST["qty"]);
                break;
            case 'viewproduct':
                $back = new backend();
                echo $back->viewProduct();
                break;
            case 'deleteproduct':
                $back = new backend();
                echo $back->deleteProduct($_POST["productid"]);
                break;
            case 'addstock':
                $back = new backend();
                echo $back->addStock($_POST["id"], $_POST["qty"], $_POST["stockqty"]);
                break;
            case 'addtocart':
                $back = new backend();
                echo $back->addToCart($_POST["productID"], $_POST["qty"]);
                break;
            case 'viewmycart':
                $back = new backend();
                echo $back->viewMyCart();
                break;
            case 'deletefromcart':
                $back = new backend();
                echo $back->deleteFromCart($_POST["productid"], $_POST["qty"]);
                break;
            case 'displaycheckout':
                $back = new backend();
                echo $back->displayCheckOut();
                break;
            case 'checkout':
                $back = new backend();
                echo $back->checkOut($_POST["productID"], $_POST["sellerID"]);
                break;
            case 'delivery':
                $back = new backend();
                echo $back->delivery();
                break;
            case 'logout':
                session_unset();
                session_destroy();
                echo "200";
                break;
            default:
                echo "404";
                break;
        }
    }

?>