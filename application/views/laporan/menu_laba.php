<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">

                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>

	<?php if ($visibilityMenu['LaporanLaba']){ ?>
    <div class="alert alert-warning">
        Berdasarkan Tanggal Aktual
    </div>
	
    <div class="row">
		<div class="col-md-3 col-sm-6">
			<a href="<?= base_url('laporan/laba'); ?>" class="nuta-link-box-menu">
				<div class="iconic-w-wrap">
					<span class="stat-w-title">Laba</span>
					<div class="ico-cirlce-widget ">
						<span><i class="fa fa-tags"></i></span>
					</div>
					<div class="w-meta-info">
						<span class="w-meta-title">Menampilkan laporan <br/>laba</span>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-3 col-sm-6">
			<a href="<?= base_url('laporan/rincianlaba'); ?>" class="nuta-link-box-menu">
				<div class="iconic-w-wrap">
					<span class="stat-w-title">Rincian Laba</span>
					<div class="ico-cirlce-widget ">
						<span><i class="fa fa-tags"></i></span>
					</div>
					<div class="w-meta-info">
						<span class="w-meta-title">Menampilkan laporan <br/>rincian laba</span>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-3 col-sm-6">
			<a href="<?= base_url('laporan/labaperkategori'); ?>" class="nuta-link-box-menu">
				<div class="iconic-w-wrap">
					<span class="stat-w-title">Laba Per Kategori</span>
					<div class="ico-cirlce-widget ">
						<span><i class="fa fa-tags"></i></span>
					</div>
					<div class="w-meta-info">
						<span class="w-meta-title">Menampilkan laporan <br/>Laba Per Kategori</span>
					</div>
				</div>
			</a>
		</div>		
	</div>
	
	<div class="alert alert-warning">
		Berdasarkan Tanggal Shift
	</div>

    <div class="row">        
		<div class="col-md-3 col-sm-6">
			<a href="<?= base_url('laporan/labapershift'); ?>" class="nuta-link-box-menu">
				<div class="iconic-w-wrap">
					<span class="stat-w-title">Laba Per Shift</span>
					<div class="ico-cirlce-widget ">
						<span><i class="fa fa-tags"></i></span>
					</div>
					<div class="w-meta-info">
						<span class="w-meta-title">Menampilkan laporan <br/>laba Per Shift</span>
					</div>
				</div>
			</a>
		</div>
		<div class="col-md-3 col-sm-6">
			<a href="<?= base_url('laporan/rincianlabapershift'); ?>" class="nuta-link-box-menu">
				<div class="iconic-w-wrap">
					<span class="stat-w-title">Rincian Laba Per Shift</span>
					<div class="ico-cirlce-widget ">
						<span><i class="fa fa-tags"></i></span>
					</div>
					<div class="w-meta-info">
						<span class="w-meta-title">Menampilkan laporan <br/>Rincian laba Per Shift</span>
					</div>
				</div>
			</a>
		</div>
    </div>
	<?php } ?>
</div>
