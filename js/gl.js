var storageData;
var helper = (function() {
    return {
        onSignInCallback: function(authResult) {
            gapi.client.load('oauth2', 'v2').then(function() {
                if (authResult['access_token']) {
                    helper.email();
                } else if (authResult['error']) {
                    console.log('Произошла ошибка: ' + authResult['error']);
                    helper.err();
                }
            });
        },
        email: function() {
            gapi.client.load('oauth2', 'v2').then(function() {
                gapi.client.oauth2.userinfo.get().execute(function(resp) {
                    getJsonData(resp.email);
                });
            });
        },
        err: function() {
            $('#gConnect').show();
        }
    };
})();

function onSignInCallback(authResult) {
    helper.onSignInCallback(authResult);
}

function getJsonData(userMail) {
    $.post( 
        "productForm.php",
        {
            "email": userMail
        }, 
        function( data ) {
            if(data === "ok")
            {
                $('#pForm').removeAttr('hidden');
            };
    }, "html" );
    $('#gConnect').hide();
}