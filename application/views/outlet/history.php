<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <ul class="list-page-breadcrumb">
                            <li><a href="<?= base_url('perusahaan/outlet')?>">Outlet</a></li>
                            <li><a href="<?= base_url('perusahaan/outlet/outletdetailinfo/');?> <?= $detail_outlet->OutletID;?>"><?= $detail_outlet->NamaOutlet.' '.$detail_outlet->AlamatOutlet; ?></a></li>
                            <li class="active-page"> Lihat Riwayat Berlangganan</li>
                        </ul>
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
                <div class="widget-container">
                    <div class="widget-head is-stack clearfix">
                        <h4>Lihat Riwayat Berlangganan</h4>
                        <p>Total baris : 3</p>
                    </div>

                    <div class="widget-block">
                        <div class="table-responsive">
                            <table class="table table-simple">
                                <thead>
                                    <tr>
                                        <th>Tanggal Invoice</th>
                                        <th>Tanggal Expired Nuta</th>
                                        <th>Deskripsi</th>
                                        <th>Cara Pembayaran</th>
                                        <th>Status</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php for ($i=0; $i < 20; $i++) : ?>
                                    <tr>
                                        <td>
                                            27 Agustus 2019 <br> 
                                            <a href="<?= base_url('perusahaan/dummyinvoice')?>" class="text-blue" target="_blank">Tampilkan Invoice</a>
                                        </td>
                                        <td>30 Agustus 2019</td>
                                        <td>Langganan Bulanan</td>
                                        <td>Transfer Manual</td>
                                        <td>Terbayar</td>
                                        <td class="text-right">Rp 250.000</td>
                                    </tr>
                                <?php endfor;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>