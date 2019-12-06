<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <h2 class="breadcrumb-titles">Hapus Data</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-table"></i></span>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php $this->load->view('features/filters/filter_form_hapus_data');?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="w-info-chart-meta">
                                    <h2>Fitur ini akan menghapus data berikut selamanya. Lanjutkan ?</h2>
                                    <?php foreach ($result as $row) { ?>
                                    <div class="progress-wrap">
                                        <div class="clearfix progress-meta">
                                            <span class="pull-left progress-label" style="font-size:14px;"><?=$row->tablename;?></span><span class="pull-right progress-percent label label-danger"><?=$row->jumlah;?> data</span>
                                        </div>
                                        <div class="progress">
                                            <div  class="progress-bar progress-bar-danger" style="width: 100%;">
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <form class="form-horizontal" method="post" action="<?=base_url();?>laporan/hapusdata">
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">&nbsp;</label>
                                            <div class="col-md-11">
                                                <input type="hidden" name="date" value="<?=$date_end;?>"/>
                                                <input type="hidden" name="outlet" value="<?=$selected_outlet;?>"/>
                                                <input type="hidden" name="yesdelete" value="delete"/>
                                                <div class="form-actions">
                                                    <a class="btn btn-primary" href="<?=base_url();?>cloud/main">Tidak , kembali ke Dashboard</a>
                                                    <button class="btn btn-danger" type="submit" name="yesdelete" onclick="deleteDataFunction()" value="delete">Ya, Hapus Semua Data</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
