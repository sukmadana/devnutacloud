<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <a href="<?= base_url(); ?>perusahaan/user" class="btn btn-default" ><i
                                class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Staf</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <form class="form-horizontal" action="<?= base_url(); ?>perusahaan/userform" method="post"
                              id="form-registrasi-user-perusahaan">
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="username"><span
                                        class="required">Username</span>
                                </label>

                                <div class="col-md-8">
                                    <input name="username" class="form-control required"
                                           type="text">
                                    <label for="username" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="password"><span
                                        class="required">Password</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="password" name="password" class="form-control required"
                                           type="text">
                                    <label for="password" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="email"><span class="required">Email</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="email" name="email" class="form-control required email" type="text"
                                        >
                                    <label for="email" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                </label>

                                <div class="col-md-8">
                                    <input type="submit" class="btn btn-primary" value="Daftar" name="submitbutton">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

