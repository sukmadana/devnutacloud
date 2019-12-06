<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>

<script type="text/javascript">
var $ = jQuery.noConflict();

const current_form = $('#form-account');

$(document).ready(function() {

    current_form.validate({
        debug: true,
        rules: {
            account_type: "required",
            account_code: "required",
            account_name: "required",
        },
        messages: {
            account_type: "Tipe akun belum dipilih.",
            account_code: "Kode akun harus diisi.",
            account_name: "Nama akun harus diisi",
        },
        submitHandler: function(form) { 
            form.submit();
        }
    });

    current_form.find('#account_type').on('change', function() {
        const value = $(this).val();
        var code = parseInt(current_form.find('#account_code').val().length);
        
        if (code <= 2 ) {
            current_form.find('#account_code').val(value + '.');
        }
    });
});


</script>