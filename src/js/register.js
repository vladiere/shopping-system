$('#register').click(function(){
    if (checkValid()) {
        doRequest()
    } else {
        alert('Fill all input fields')
    }
})

var doRequest = () => {
    $.ajax({
        type: 'POST',
        url: '../src/php/router.php',
        data: {
            choice: 'register',
            fullname: $('#fullname').val(),
            email: $('#email').val(),
            contact: $('#contact').val(),
            password: $('#password').val(),
            role: 'customer'
        },
        success: function(data) {
            if (data == "200") {
                $('#fullname').val('')
                $('#email').val('')
                $('#contact').val('')
                $('#password').val('')
                $('#password2').val('')
                alert('Registration successful')
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {console.log(thrownError);}
    })
}

var checkValid = () => {
    if ($('#fullname').val() != '' && $('#email').val() != '' && $('#contact').val() != '' && $('#password').val() != '' && $('#password2').val() != '' ){
        if ($('#password').val() == $('#password2').val()) {
            return true
        } else {
            return false
        }
    } else {
        return false
    }
}