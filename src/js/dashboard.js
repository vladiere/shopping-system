$(document).ready(()=>{
    loginRole()
    var tmpID, curStock

    $(document).on('click', '#logout', () => {
        logout()
    })

    $(document).on('click', '.addProd', function(){
        if (checkProduct()) {
            addProduct()
        } else {
            alert('Please don\'t leave empty')
        }
    })

    $(document).on('click', '.deleteProd', function(e){
        e.preventDefault()
        var prodID = $(this).attr('id')
        console.log(prodID);
        deleteProduct(prodID)
    })

    $(document).on('click', '.addStock', function(e){
        e.preventDefault()

        var stocks = $(this).attr('id')
        var arr = stocks.split('.')
        curStock = arr[0]
        tmpID = arr[1]
    })

    $(document).on('click', '#addStock', () => {
        addStock(tmpID, curStock)
    })


    $(document).on('click', '#addCart', (e) => {
        var tmpQty = $('#stock').val()
        if (tmpQty < curStock || tmpQty != 0) {
            addToMyCart(tmpID, tmpQty)
        } else {
            alert('Cannot add to cart stock is only ' + curStock)
            $('#stock').val('')
        }
    })

})

const addToMyCart = (id, qty) => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'addtocart',
            productID: id,
            qty: qty
        },
        success: function(data){
            if (data == '200') {
                $('#stock').val('')
                $('.modal-header #exampleModalLabel').remove()
                alert('Add to cart successfully')
                $('#products').load(location.href + ' #products')
                loginRole()
            }
        },
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError)}
    })
}

const addStock = (tmpID,curStock) => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'addstock',
            id: tmpID,
            qty: $('#stock').val(),
            stockqty: curStock
        },
        success: function(data) {
            if (data == "200") {
                $('#stock').val('')
                $('.modal-header #exampleModalLabel').remove()
                alert("Stock was successfully added")
                $('#products').load(location.href + ' #products')
                loginRole()
            }
        },
        error: function(xhr, ajaxOptions, thrownError){console.log(thrownError);}
    })
}

const deleteProduct = (prodID) => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'deleteproduct',
            productid: prodID
        },
        success: function(data) {
            console.log(data);
            if (data == "200") {
                alert("Product deleted successfully");
                $('#products').load(location.href + ' #products')
                loginRole()
            }
        },
        error: function(xhr, ajaxOptions, thrownError){console.log(thrownError);}
    })
}

const addProduct = () => {
    var randomNum = Math.floor(Math.random() * 5)
    var prodIMG = '/public/images/' + randomNum + '.jpg'
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'addproduct',
            productname: $('#productname').val(),
            price: $('#price').val(),
            qty: $('#qty').val(),
            img_path: prodIMG
        },
        success: function(data) {
            console.log(data);
            if(data == "200"){
                $('#productname').val('')
                $('#price').val('')
                $('#qty').val('')
                alert('Product add successfully')
                $('#products').load(location.href + ' #products')
                loginRole()
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {console.log(thrownError);}
    })
}

const logout = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'logout'
        },
        success: (data) => {window.location.href = '../'},
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError)}
    })
}

const loginRole = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'getrole'
        },
        success: function(data) {
            if (data == "seller") {
                $('#menu a').remove()
                $('#menu').append('<li class="nav-item"><a class="nav-link" href="../public/mycart.html" id="deliver">Sold Products</a></li>')
                $('#menu').append('<li class="nav-item"><a class="nav-link" href=".#?" id="addproduct" data-bs-toggle="modal" data-bs-target="#addModal">Add Product</a></li>')
                $('#menu').append('<li class="nav-item"><a class="nav-link" href=".#?" id="logout">Logout</a></li>')
                viewProductSeller()
                $('.modal-header').prepend('<h1 class="modal-title fs-5" id="exampleModalLabel">Add Stock</h1>')
            } else if(data == "customer"){
                $('#menu a').remove()
                $('#menu').append('<li class="nav-item"><a class="nav-link" href="../public/mycart.html" id="mycar">My Cart</a></li>')
                $('#menu').append('<li class="nav-item"><a class="nav-link" href=".#?" id="logout">Logout</a></li>')
                $('.btn-addStock').attr('id', 'addCart')
                viewProductCustomer()
                $('.modal-header').prepend('<h1 class="modal-title fs-5" id="exampleModalLabel">Add to cart</h1>')
                
            } else {
                window.location.href = '../'
            }
        },
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError);}
    })
}

const viewProductSeller =()=>{
    $.ajax({
        type: "POST",
        url: "../src/php/router.php",
        data: {
            choice:'viewproduct'
        },
        success: function(data){
            var json = JSON.parse(data);
            var str = ""
            json.forEach(element => {
                str += '<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4">' +
                        '<div class="card shadow">'+
                            '<div class="card-body text-center">'+
                            '<a href="#"><img class="card-img-top" src="..'+element.img+'" alt=""></a>'+
                                '<a class="text-reset" href="#"><h6 class="card-title display-3">'+ element.productname +'</h6></a>'+
                                '<h4><i class="fa-solid fa-peso-sign"></i>'+ element.price +'</h4>'+
                                '<p>Stock: '+ element.quantity +' </p>'+
                                '<a class="btn btn-dark my-2 addStock" href="#" role="button" id="'+ element.quantity +"."+ element.id +'" data-bs-toggle="modal" data-bs-target="#addStockModal">Add Stock</a>'+
                                '<a class="btn btn-danger my-2 ms-2 deleteProd" href="#" role="button" id="'+element.id+'">Remove</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
            });
            $('#products').append(str)
        }, 
        error: function (xhr, ajaxOptions, thrownError) {console.log(thrownError)}
    });
}

const viewProductCustomer = () =>{
    $.ajax({
        type: "POST",
        url: "../src/php/router.php",
        data: {
            choice:'viewproduct'
        },
        success: function(data){
            var json = JSON.parse(data);
            var str = ""
            json.forEach(element => {
                str += '<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4">' +
                        '<div class="card shadow">'+
                            '<div class="card-body text-center">'+
                                '<a href="#"><img class="card-img-top" src="..'+element.img+'" alt=""></a>'+
                                '<a class="text-reset" href="#"><h3 class="card-title display-5">'+ element.productname +'</h3></a>'+
                                '<h4><i class="fa-solid fa-peso-sign"></i> '+ element.price +' each</h4>'+
                                '<p>Stock: '+ element.quantity +' </p>'+
                                '<a class="btn btn-dark my-2 addStock" href="#" role="button" id="'+ element.quantity +"."+ element.id +'" data-bs-toggle="modal" data-bs-target="#addStockModal">Add to Cart</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
            });
            $('#products').append(str)
        }, 
        error: function (xhr, ajaxOptions, thrownError) {console.log(thrownError)}
    });
}


const checkProduct = () => {
    if ($('#product').val() != '' && $('#price').val() != '' && $('#qty').val() != '') {
        return true
    } else {
        return false
    }
}