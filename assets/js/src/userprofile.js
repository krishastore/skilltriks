jQuery(function ($) { 
    const $fileInput = $('#fileInput');
    const $preview = $('#preview');
    const $uploadBtn = $('#uploadBtn');
    const $deleteBtn = $('#deleteBtn');

    $uploadBtn.on('click', function () {
        $fileInput.trigger('click');
    });

    $fileInput.on('change', function () {
        const file = this.files[0];
        if (file) {
            if (file.size > 4 * 1024 * 1024) {
                alert("File size exceeds 4 MB.");
                $(this).val("");
                return;
            }
            if (!["image/jpeg", "image/png", "image/gif"].includes(file.type)) {
                alert("Only JPG, PNG & GIF files are allowed.");
                $(this).val("");
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                $preview.attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $deleteBtn.on('click', function () {
        $preview.attr('src', "");
        $preview.attr('alt', "No Profile Picture Available");
        $fileInput.val("");
    });
});

jQuery(function ($) { 

    // Set New Password
    $(document).on('click', '.stlms-form-group .wp-generate-pw', function() {
        var $wrap   = $(this).closest('.stlms-form-group').find('.stlms-password-field.wp-pwd');
        var $input  = $wrap.find('.stlms-form-control');
        var $toggle = $wrap.find('.wp-hide-pw');
        var $eyeOn  = $toggle.find('.eye-on');
        var $eyeOff = $toggle.find('.eye-off');
        var $text   = $toggle.find('.text');

        $wrap.show();
        $(this).attr('aria-expanded', 'true');
        $input.prop('disabled', false);

        // Get password from data-pw or generate a fallback
        var newPass = $input.data('pw') || '';
        $input.val(newPass).trigger('keyup');
        $input.attr('type', 'text');
        $eyeOn.hide();
        $eyeOff.show();
        $text.text('Hide');
        $toggle.attr('aria-label', 'Hide password');

        // Initially hide weak password checkbox; strength meter JS will handle showing if needed
        $('.pw-weak').hide().find('input.pw-checkbox').prop('checked', false);
    });

    // Cancel button
    $(document).on('click', '.stlms-form-group .wp-cancel-pw', function(){
        var $wrap  = $(this).closest('.stlms-password-field.wp-pwd');
        var $input = $wrap.find('.stlms-form-control');

        $input.val('').prop('disabled', true).attr('type','text');
        $wrap.hide();
        $('.wp-generate-pw').attr('aria-expanded','false');

        // Hide weak password checkbox
        $('.pw-weak').hide().find('input.pw-checkbox').prop('checked', false);
        $('.save-profile').prop('disabled', false);
    });

    // Show/Hide toggle
    $(document).on('click', '.stlms-form-group .wp-hide-pw', function(){
        var $input  = $(this).closest('.stlms-password-field.wp-pwd').find('.stlms-form-control');
        var $eyeOn  = $(this).find('.eye-on');
        var $eyeOff = $(this).find('.eye-off');
        var $text   = $(this).find('.text');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $eyeOn.hide();
            $eyeOff.show();
            $text.text('Hide');$text.text('Hide');
            $(this).attr('aria-label','Hide password');
        } else {
            $input.attr('type', 'password');
            $eyeOn.show();
            $eyeOff.hide();
            $text.text('Show');
            $(this).attr('aria-label','Show password');
        }
    });

    // Password strength meter
    $(document).on('keyup paste', '.stlms-form-group .stlms-form-control#pass1', function(){
        var $input    = $(this);
        var pass      = $input.val();
        var $result   = $('#pass-strength-result');
        var $pwWeak   = $('.pw-weak');
        var $checkbox = $pwWeak.find('input.pw-checkbox');
        var $submit   = $('.save-profile');

        if (typeof wp !== 'undefined' && wp.passwordStrength) {
            var strength = wp.passwordStrength.meter(pass, wp.passwordStrength.userInputDisallowedList(), pass);
            var msg = '', cls = '';

            switch(strength){
                case 1: msg='Very Weak'; cls='short'; break;
                case 2:  msg='Weak'; cls='bad'; break;
                case 3:  msg='Medium'; cls='good'; break;
                case 4:  msg='Strong'; cls='strong'; break;
                default: msg='Too short'; cls='short';
            }

            $result.removeClass().addClass('stlms-pass-strength '+cls).text(msg);
            if(strength <= 2 && pass.length > 0 ){
                $pwWeak.show();
            } else {
                $pwWeak.hide().find('input.pw-checkbox').prop('checked', false);
            }
        }

        if (
            pass.length === 0 ||
            (strength <= 2 && !$checkbox.is(':checked'))
        ) {
            $submit.prop('disabled', true);
        } else {
            $submit.prop('disabled', false);
        }
    });
});

jQuery(function ($) { 
    $('.save-profile').on('click', function(e){
        e.preventDefault();

        let firstName = $('#first-name').val();
        let lastName = $('#last-name').val();
        let password = $('#pass1').val();
        let fileInput = $('#fileInput')[0].files[0];

        function updateUserProfile(avatarUrl = null){
            let userData = {
                first_name: firstName,
                last_name: lastName
            };

            if(password){
                userData.password = password;
            }

            if(avatarUrl){
                userData.meta = {
                    avatar_url: avatarUrl
                };
            }

            $.ajax({
                url: StlmsRestObj.restUserUrl,
                method: 'POST',
                data: JSON.stringify(userData),
                contentType: 'application/json',
                beforeSend: function(xhr){
                    xhr.setRequestHeader('X-WP-Nonce', StlmsRestObj.nonce);
                },
                success: function(response){
                    showSnackbar('snackbar-success');
                },
                error: function(err){
                    showSnackbar('snackbar-error');
                }
            });
        }

        if(fileInput){
            let formData = new FormData();
            formData.append('file', fileInput);

            $.ajax({
                url: StlmsRestObj.restMediaUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function(xhr){
                    xhr.setRequestHeader('X-WP-Nonce', StlmsRestObj.nonce);
                },
                success: function(mediaResponse){
                    let avatarUrl = mediaResponse.source_url;
                    updateUserProfile(avatarUrl);
                },
                error: function(err){
                    showSnackbar('snackbar-error');
                }
            });
        } else {
            // No file selected, just update user
            updateUserProfile();
        }

    });
});

let snackbarTimeout;

function showSnackbar(snackbarId) {
    const $snackbar = jQuery('#' + snackbarId);
    $snackbar.addClass('show');

    clearTimeout(snackbarTimeout);

    snackbarTimeout = setTimeout(() => {
        $snackbar.removeClass('show');
    }, 3000);
}

// Hide snackbar on close button click
jQuery(document).on('click', '.hideSnackbar', function (e) {
    e.preventDefault();
    jQuery(this).closest('.stlms-snackbar').removeClass('show');
    jQuery(this).closest('.stlms-snackbar').addClass('hide');
});