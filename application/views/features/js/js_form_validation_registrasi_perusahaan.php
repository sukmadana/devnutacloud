<script type="text/javascript">
    jQuery(document).ready(function ($) {
        if ($.fn.bootstrapValidator) {
            $('#form-registrasi-perusahaan')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        namaperusahaan: {
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {
                                    message: 'Nama Perusahaan tidak boleh kosong.'
                                },
                                stringLength: {
                                    min: 4,
                                    max: 50,
                                    message: 'Nama Perusahaan minimal 4 huruf dan maksimal 50 huruf.'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9 ,\.-]+$/,
                                    message: 'Nama Perusahaan hanya bisa diisi huruf dan angka.'
                                },
                            }
                        },
                        namapemilik: {
                            validators: {
                                notEmpty: {
                                    message: 'Nama Pemilik tidak boleh kosong.'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z \.]+$/,
                                    message: 'Nama Pemilik hanya bisa diisi huruf, titik, dan spasi.'
                                },
                            }
                        },
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'Email tidak boleh kosong.'
                                },
                                emailAddress: {
                                    message: 'Format email tidak valid.'
                                },
                                "remote": {
                                    url: '<?=base_url().'ajax/validateemailperusahaan';?>',
                                    type: "post",
                                    data: {
                                        email: function () {
                                            return $('#form-register-individual :input[name="username"]').val();
                                        }
                                    },
                                    message: 'Email sudah dipakai.'
                                },
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
                    $('#form-register-individual').bootstrapValidator('defaultSubmit');
                });
        }
    });
</script>