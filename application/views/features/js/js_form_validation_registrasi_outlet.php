<script type="text/javascript">
    jQuery(document).ready(function ($) {
        if ($.fn.bootstrapValidator) {
            $('#form-registrasi-outlet')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        namaoutlet: {
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {
                                    message: 'Nama Outlet tidak boleh kosong.'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9 ,\.-]+$/,
                                    message: 'Nama Outlet hanya bisa diisi huruf dan angka.'
                                },
                                "remote": {
                                    url: '<?=base_url().'ajax/validatenamaoutlet';?>',
                                    type: "post",
                                    data: {
                                        namaoutlet: function () {
                                            return $('#form-registrasi-outlet :input[name="namaoutlet"]').val();
                                        }
                                    },
                                    message: 'Nama Outlet sudah dipakai.'
                                },
                            }
                        },
                        alamatoutlet: {
                            validators: {
                                notEmpty: {
                                    message: 'Alamat Outlet tidak boleh kosong.'
                                },
                            }
                        },
                        kotaoutlet: {
                            validators: {
                                notEmpty: {
                                    message: 'Kota Outlet tidak boleh kosong.'
                                },
                            }
                        },
                        provinsioutlet: {
                            validators: {
                                notEmpty: {
                                    message: 'Provinsi Outlet tidak boleh kosong.'
                                },
                            }
                        },
                        notelpoutlet: {
                            validators: {
                                notEmpty: {
                                    message: 'No telepon Outlet tidak boleh kosong.'
                                },
                            }
                        },
                        pemilikoutlet: {
                            validators: {
                                notEmpty: {
                                    message: 'Pemilik Outlet tidak boleh kosong.'
                                },
                            }
                        },
                    }
                }).on('success.form.bv', function (e) {
                    $('#form-registrasi-outlet').bootstrapValidator('defaultSubmit');
                });
        }
    });
</script>