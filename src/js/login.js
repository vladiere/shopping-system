$('#btn-login').click(function () {
    if (checkLogin()) {
        doLoginRequest()
    } else {
        alert('Please enter your email address and password')
    }
})

doLoginRequest = () => {
    $.ajax({
        type: 'POST',
        url: './src/php/router.php',
        data: {
            choice: 'login',
            email: $('#email').val(),
            password: $('#password').val()
        },
        success: function (data) {
            if (data == "200") {
                $('#email').val('')
                $('#password').val('')
                $(location).attr('href', './public/dashboard.html')
            }else{
                alert('Acount not found please register')
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {console.log(thrownError);}
    })
}

var checkLogin = () => {
    if ($('#email').val() !== '' && $('#password').val() !== '') {
        return true
    } else {
        return false
    }
}