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
    $('.stlms-select2-multi').select2();
});

jQuery(function ($) { 

    // Set New Password
    $(document).on('click', '.stlms-form-group .wp-generate-pw', function(){
        var $wrap  = $(this).closest('.stlms-form-group').find('.stlms-password-field.wp-pwd');
        var $input = $wrap.find('.stlms-form-control');

        $wrap.show();
        $(this).attr('aria-expanded','true');
        $input.prop('disabled', false);

        // Get password from data-pw or generate a fallback
        var newPass = $input.data('pw') || '';
        $input.val(newPass).trigger('keyup');

        // Initially hide weak password checkbox; strength meter JS will handle showing if needed
        $('.pw-weak').hide().find('input.pw-checkbox').prop('checked', false);
    });

    // Cancel button
    $(document).on('click', '.stlms-form-group .wp-cancel-pw', function(){
        var $wrap  = $(this).closest('.stlms-password-field.wp-pwd');
        var $input = $wrap.find('.stlms-form-control');

        $input.val('').prop('disabled', true).attr('type','password');
        $wrap.hide();
        $('.wp-generate-pw').attr('aria-expanded','false');

        // Hide weak password checkbox
        $('.pw-weak').hide().find('input.pw-checkbox').prop('checked', false);
    });

    // Show/Hide toggle
    $(document).on('click', '.stlms-form-group .wp-hide-pw', function(){
        var $input  = $(this).closest('.stlms-password-field.wp-pwd').find('.stlms-form-control');
        var $eyeOn  = $(this).find('.eye-on');
        var $eyeOff = $(this).find('.eye-off');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $eyeOn.hide();
            $eyeOff.show();
            $(this).attr('aria-label','Hide password');
        } else {
            $input.attr('type', 'password');
            $eyeOn.show();
            $eyeOff.hide();
            $(this).attr('aria-label','Show password');
        }
    });

    // Password strength meter
    $(document).on('keyup paste', '.stlms-form-group .stlms-form-control', function(){
        var $input  = $(this);
        var pass    = $input.val();
        var $result = $('#pass-strength-result');
        var $pwWeak = $('.pw-weak');

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
    });
});

