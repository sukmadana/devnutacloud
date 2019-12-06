<?php

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/12/2015
 * Time: 20:33
 */ ?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        if ($.fn.bootstrapValidator) {
            $('#form-registrasi-user-perusahaan')
                .bootstrapValidator({
                    message: 'This value is not valid',
                    fields: {
                        username: {
                            message: 'The username is not valid',
                            validators: {
                                "remote": {
                                    url: '<?= base_url() . 'ajax/validateusernametablet'; ?>',
                                    type: "post",
                                    data: {
                                        username: function() {
                                            return $('#form-registrasi-user-perusahaan :input[name="username"]').val();
                                        }
                                    },
                                    message: 'Username sudah dipakai.'
                                },
                                notEmpty: {
                                    message: 'Username tidak boleh kosong.'
                                },
                                stringLength: {
                                    min: 4,
                                    max: 50,
                                    message: 'Username minimal 4 huruf dan maksimal 50 huruf.'
                                },
                                regexp: {
                                    regexp: /^[a-zA-Z0-9]+$/,
                                    message: 'Username hanya bisa diisi huruf dan angka.'
                                }
                            },

                        },
                        password: {
                            validators: {
                                notEmpty: {
                                    message: 'Password tidak boleh kosong.'
                                },
                                identical: {
                                    field: 'konfirmasi_password',
                                    message: 'Password dan Konfirmasi Password tidak sama'
                                }
                            }
                        },
                        konfirmasi_password: {
                            validators: {
                                notEmpty: {
                                    message: 'Konfirmasi password tidak boleh kosong'
                                },
                                identical: {
                                    field: 'password',
                                    message: 'Konfirmasi Password dan Password tidak sama'
                                }
                            }
                        },
                        email: {
                            validators: {
                                "remote": {
                                    url: '<?= base_url() . 'ajax/validateemailtablet'; ?>',
                                    type: "post",
                                    data: {
                                        email: function() {
                                            return $('#form-registrasi-user-perusahaan :input[name="email"]').val();
                                        }
                                    },
                                    message: 'Email sudah dipakai.'
                                },
                                notEmpty: {
                                    message: 'Email tidak boleh kosong.'
                                },
                                emailAddress: {
                                    message: 'Format email tidak valid.'
                                }
                            }
                        },
                    }
                }).on('success.form.bv', function(e) {
                    $('#form-registrasi-user-perusahaan').bootstrapValidator('defaultSubmit');
                });
        }
    });
</script>