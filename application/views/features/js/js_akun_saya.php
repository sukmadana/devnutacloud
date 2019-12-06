<script type="text/javascript">
    var state_form = 'read';
    jQuery(document).ready(function ($) {
        var triggerFile = $('#trigger-file'),
            photoField = $('#photo-field'),
            extPhoto = $('#extPhoto'),
            profilePhoto = $('#profile-photo'),
            togglePassword = $('.js-password'),
            formChangePassword = $('#formChangePassword'),
            formChangeEmail = $('#formChangeEmail');
            btnHapusFoto = $('#btn-hapusfoto');
            hapusFotoStatus = $('.status_hapus_foto')

        if ($.fn.filestyle) {
            $(":file").filestyle('destroy');
        }

        setTimeout(function() { 
            $('body').find('.alert-fixed .alert').addClass('fadeout');
        }, 3000);

        btnHapusFoto.on('click', function(e){
            e.preventDefault();
            hapusFotoStatus.prop('checked', true);
            profilePhoto.removeClass('fill').css('background-image', 'url(../images/user.png)');
        })

        triggerFile.on('click', function (){
            photoField.click();
        });

        photoField.change(function() {
            var path = $(this).val();
            if (path.lastIndexOf(".") > 0) {
                var ext = path.substring(path.lastIndexOf(".") + 1, path.length);
                extPhoto.val(ext);
            } else {
                extPhoto.val('');
            }

            if (typeof (FileReader) != "undefined") {
                var reader = new FileReader();
                reader.onload = function (e) {
                    profilePhoto.addClass('fill').css('background-image', 'url(' + e.target.result, + ')');
                }
                reader.readAsDataURL($(this)[0].files[0]);
                hapusFotoStatus.prop('checked', false);
            }
        })

        togglePassword.click(function() {
            var parent = $(this).parents('.form-relative'),
                input = parent.find('input'),
                icon = parent.find('i');

            if (input.attr('type') == 'password') {
                input.prop('type', 'text');
                icon.prop('class', 'fa fa-eye-slash');
            } else {
                input.prop('type', 'password');
                icon.prop('class', 'fa fa-eye');
            }
        })

        formChangePassword.bootstrapValidator({
            fields: {
                oldpassword: {
                    validators: {
                        notEmpty: {
                            message: 'Password Tidak Boleh Kosong'
                        }
                    }
                },
                newpassword: {
                    validators: {
                        notEmpty: {
                            message: 'Password Baru Tidak Boleh Kosong'
                        }
                    }
                },
                confirmpassword: {
                    validators: {
                        notEmpty: {
                            message: 'Konfirmasi Password Baru Tidak Boleh Kosong'
                        },
                        identical: {
                            field: 'newpassword',
                            message: 'Konfirmasi Password Baru Tidak Sama'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            $('form-group').removeClass('has-success');

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            $.post(
               '<?= base_url("cloud/changepassword")?>',
               $form.serialize()
            ).done(function(response) {
                var result = JSON.parse(response);
                if(result.status === 'success') {
                    window.location = result.message
                } else {
                    bv.updateStatus(result.field, 'INVALID');
                    bv.updateMessage(result.field, 'notEmpty', result.message);
                    console.log(result.message);
                }
            })
        });

        formChangeEmail.bootstrapValidator({
            fields: {
                oldpassword: {
                    validators: {
                        notEmpty: {
                            message: 'Password Lama Tidak Boleh Kosong'
                        }
                    }
                },
                oldemail: {
                    validators: {
                        notEmpty: {
                            message: 'Email Saat Tidak Boleh Kosong'
                        }
                    }
                },
                newemail: {
                    validators: {
                        notEmpty: {
                            message: 'Email ini sudah digunakan'
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
            e.preventDefault();
            $('form-group').removeClass('has-success');

            var $form = $(e.target);
            var bv = $form.data('bootstrapValidator');

            $.post(
               '<?= base_url("cloud/changeemail")?>',
               $form.serialize()
            ).done(function(response) {
                var result = JSON.parse(response);
                if(result.status === 'success') {
                    window.location = result.message
                } else {
                    bv.updateStatus(result.field, 'INVALID');
                    bv.updateMessage(result.field, result.field, result.message);
                }
            })
        });

        // function setFormstate() {
        //     if (state_form == 'read') {
        //         $('#btnSimpan').hide();
        //     } else if (state_form == 'input') {
        //         $('#btnSimpan').show();
        //     }
        // }

        // function changeimg(str) {
        //     if (typeof str === "object") {
        //         str = str.target.result;
        //     }
        //     $('#prevImage').attr('src', str);
        //     $('#btnSimpan').prop('disabled', false);
        // }

        // $('#passwordlama').hide();
        // $('#password').hide();
        // $('#ulangipassword').hide();
        // $('#emailbaru').hide();
        // $('#btnSimpan').prop('disabled', true);
        // $("#foto").change(function () {
        //     var fileObj = this,
        //         file;
        //     var path = $(this).val();
        //     if (path.lastIndexOf(".") > 0) {
        //         var ext = path.substring(path.lastIndexOf(".") + 1, path.length);
        //         $('#extfoto').val(ext);
        //     } else {
        //         $('#extfoto').val('');
        //     }
        //     if (fileObj.files) {
        //         file = fileObj.files[0];
        //         var fr = new FileReader;
        //         fr.onloadend = changeimg;
        //         fr.readAsDataURL(file)
        //     } else {
        //         file = fileObj.value;
        //         changeimg(file);
        //     }
        //     $('#btnSimpan').show();
        // });
        // $('#ubahpassword').click(function () {
        //     state_form = 'input';
        //     $('#passwordlama').show();
        //     $('#password').show();
        //     $('#ulangipassword').show();
        //     $('#pwd').hide();
        //     $('#ubahemail').hide();
        //     $('#btnSimpan').prop('disabled', false);
        //     $('#batalpwd').show();
        //     $('#ubahpassword').hide();
        //     $('#ubah-container').hide();
        //     setFormstate();
        // });
        // $('#ubahemail').click(function () {
        //     state_form = 'input';
        //     $('#passwordlama').show();
        //     $('#emailbaru').show();
        //     $('#ubahpassword').hide();
        //     $('#btnSimpan').prop('disabled', false);
        //     $('#batalemail').show();
        //     $('#ubahemail').hide();
        //     $('#ubah-container').hide();
        //     setFormstate();
        // });
        // $('#batalpwd').click(function () {
        //     state_form = 'read';
        //     $('#passwordlama').hide();
        //     $('#password').hide();
        //     $('#ulangipassword').hide();
        //     $('#pwd').show();
        //     $('#ubahemail').show();
        //     $('#btnSimpan').prop('disabled', true);
        //     $('#batalpwd').hide();
        //     $('#ubah-container').show();
        //     $('#ubahpassword').show();
        //     setFormstate();
        // });

        // $('#batalemail').click(function () {
        //     state_form = 'read';
        //     $('#passwordlama').hide();
        //     $('#emailbaru').hide();
        //     $('#ubahpassword').show();
        //     $('#btnSimpan').prop('disabled', true);
        //     $('#batalemail').hide();
        //     $('#ubah-container').show();
        //     $('#ubahemail').show();
        //     setFormstate();
        // });
        // $('#formakunsaya')
        //     .bootstrapValidator({
        //         message: 'This value is not valid',
        //         fields: {
        //             passwordlama: {
        //                 message: 'The username is not valid',
        //                 validators: {
        //                     notEmpty: {
        //                         message: 'Password lama tidak boleh kosong.'
        //                     },
        //                     regexp: {
        //                         regexp: /^[a-zA-Z0-9 ,$&+,:;=?@#|'<>.^*()%!-]+$/,
        //                         message: 'Password.'
        //                     },
        //                 }
        //             },
        //             passwordbaru: {
        //                 validators: {
        //                     notEmpty: {
        //                         message: 'Password baru tidak boleh kosong.'
        //                     },

        //                 }
        //             }, confirmpassword: {
        //                 validators: {
        //                     notEmpty: {
        //                         message: 'Ulangi password baru tidak boleh kosong.'
        //                     },
        //                     identical: {
        //                         field: 'passwordbaru',
        //                         message: 'Harusa sama dengan password baru'
        //                     }
        //                 }
        //             },
        //         }
        //     }).on('success.form.bv', function (e) {
        //     $('#formakunsaya').bootstrapValidator('defaultSubmit');
        // });
        // setFormstate();
    });
</script>
