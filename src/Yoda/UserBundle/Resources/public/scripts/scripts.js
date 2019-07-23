$(document).ready(function () {
    var email;
    var pwd;

    $(".ProfilePic").on('change', function (e) {

        $(".ProfilePic").submit();
    });

    $(".ProfilePic").on('submit', function (e) {
        e.preventDefault();
        formData = new FormData(this);
        $.ajax({
            url: 'updateProfilePic',
            context: $(this),
            contentType: false,
            cache: false,
            processData: false,
            type: "POST",
            beforeSend: function () {
                console.log('hey');
            },
            data: formData,
            success: function (data) {
                location.href = 'profile';
            }
        })
    });


    //reset password section

    $(".reset-pwd-label").on('click', function () {
        console.log('asd');

        $(".registerForm").hide();
        $(".register-heading").html('Forget Password');
        $(".forgetPasswordDiv").show();
    });

    $(".resetPassword").on('click', function () {
        email = $("#resetEamil").val();
        pwd = $("#resetPwd").val();

        var profile = '';
        profile = $(this).attr('profile');

        $.ajax({
            url: passwordRestPath,
            type: "POST",
            data: {"email": email, 'password': pwd},
            success: function (data) {
                if (data.success == "yes") {

                    if( profile !== 'yii') {
                        $("#ResetPwdMsg").html("<p class='alert alert-success'>" + data.message + "<i class='loginClick'> <b>Click here to login </b></i>  </p>");
                    } else
                    {
                        $("#resetPwd").val('');
                        $("#ResetPwdMsg").html("<p class='alert alert-success'>" + data.message + "</p>");
                    }

                } else {
                    $("#ResetPwdMsg").html("<p class='alert alert-danger'>" + data.message + "</p>");
                }
            }
        })
    });

    //loginClick
    $(document).on('click', '.loginClick', function () {
        $("#resetEamil").val('');
        $("#resetPwd").val('');
        $("#ResetPwdMsg").html('');

        $(".forgetPasswordDiv").hide();
        $(".registerForm").show();
        $(".register-heading").html('Enter Your Credentials');
        $(".registerForm")[0].reset();

        $("#username").val(email);

    })
})

$(document).on('click', '.js-attend-toggle', function( e) {
    e.preventDefault();
    var linkButton = $(this).attr('href');
    url =   linkButton + ".json";

    $.ajax({
        url:url,
        context:$(this),
        success: function (data) {
            if(data.attending) {
                var msg = 'See you there';
            }
            else
                var msg = 'We miss you';
            $(this).after('<span class="label label-default">'+msg+'</span>');
            $(this).hide();
        }
    })
});