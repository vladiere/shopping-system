$(document).ready(() => {
    displayRole()

    $(document).on('click', '#logout', () => {
        logout()
    })

})

const checkOut = (prodID, sellerID) => {
    if (confirm('Confirm Check Out')) {
        checkOutMyProduct(prodID, sellerID)
    } else {
        alert('You just canceled your delivery')
    }
}

const deleteOut = (prodID, qty) => {
    if (confirm('Are you sure you do not want this product?')) {
        removeFromCart(prodID, qty)
    } else {
        alert('You kept this in your cart..')
    }
}

const removeFromCart = (productID, qty) => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'deletefromcart',
            productid: productID,
            qty: qty
        },
        success: function(data) {
            if (data == '200'){
                alert('Deleted from the cart')
                $('#mycart').load(location.href + ' #mycart')
                viewMyCart()
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {console.log(throwError);}
    })
}

const checkOutMyProduct = (productID, sellerID) => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'checkout',
            productID: productID,
            sellerID: sellerID
        },
        success: (data) => {
            console.log(data);
            if (data == '200') {
                $('#mycart').load(location.href + ' #mycart')
                viewMyCart()
                alert('Items will be delivered within 10 to 15 days')
            }
        },
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError)}
    })
}

const displayRole = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'getrole'
        },
        success: (data) => {
            if (data == 'customer') {
                viewMyCart()
            } else if(data == 'seller'){
                viewDeliveryOrder()
            } else{
                window.location.href = '../'
            }
        },
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError)}
    })
}

const viewDeliveryOrder = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'delivery'
        },
        success: (data) => {
            var json = JSON.parse(data);
            var str = ""
            json.forEach(element => {
                str += '<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4">' +
                        '<div class="card shadow">'+
                            '<div class="card-body text-center">'+
                            '<a href="#"><img class="card-img-top" src="..'+element.img+'" alt=""></a>'+
                                '<a class="text-reset" href="#"><h6 class="card-title display-3">'+ element.productname +'</h6></a>'+
                                '<h4><i class="fa-solid fa-peso-sign"></i>'+ element.total_price +'</h4>'+
                                '<p>Stock: '+ element.quantity +' </p>'+
                                '<p>Date Sold: '+ element.date_checked +' </p>'+
                                '<p>Delivery Date: '+ element.date_delivered +' </p>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
            });
            $('#mycart').append(str)
        },
        error: (xhr, ajaxOptions, thrownError) => {console.log(thrownError)}
    })
}

const viewMyCart = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'viewmycart'
        },
        success: (data) => {
            var json = JSON.parse(data);
            var str = ""
            json.forEach(element => {
                str += '<div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-4">' +
                        '<div class="card shadow">'+
                            '<div class="card-body text-center">'+
                                '<a href="#"><img class="card-img-top" src="..'+element.img+'" alt=""></a>'+
                                '<a class="text-reset" href="#"><h6 class="card-title display-3">'+ element.productname +'</h6></a>'+
                                '<h4><i class="fa-solid fa-peso-sign"></i>'+ element.total_price +'</h4>'+
                                '<p>Stock: '+ element.quantity +' </p>'+
                                '<a class="btn btn-outline-success my-2 checkOut" href="#" role="button" id="'+ element.productID+"."+element.sellerID +'" onclick="checkOut('+ element.productID+", "+element.sellerID +')">Check Out</a>'+
                                '<a class="btn btn-outline-warning my-2 ms-2 deleteOut" href="#" role="button" id="'+ element.productID+"."+element.quantity +'" onclick="deleteOut('+ element.productID+","+element.quantity +')">Remove</a>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
            });
            $('#mycart').append(str)
        },
        error: (xhr, ajaxOptions, thrownError) =>{console.log(thrownError);}
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