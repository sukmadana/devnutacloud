<style>
    #input-title {
        border : 1px solid #eee;
        padding-top: 10px;
        border-radius: 3px;
        background : #f9f9f9;
        margin: 1px 0px;
    }
    #input-title p {
        font-weight: 500;
        color: #454545;
        font-size: 14px !important;
    }
    #input-content label {
        display: none;
    }

    label.error {
        font-size: 11px;
    }

    .select2-results-dept-1.select2-result.select2-result-unselectable.select2-disabled{
        display:none;
    }

    @media screen and (max-width: 780px) {
        #input-title {
            display: none;
        }
        #input-content label {
            display: block;
        }
    }
</style>

<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">
                        <form class="form-horizontal" id="form-outlet">
                            <div class="col-md-6">
                                <a href="<?= base_url() ?>journal/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pull-right col-md-12">
                                    <div class="col-md-3">
                                        <label class="control-label">Outlet</label>
                                    </div>
                                    <div class="col-md-9">
                                    <select class="form-control" name="outlet" id="outlet"
                                            onchange="changeOutlet()" <?= count($journal['journal']) > 0 ? 'disabled' : ''; ?>>
                                        <?php foreach ($outlets as $key => $outlet): ?>
                                            <option value="<?= $key ?>" <?= $_GET['outlet'] == $key ? "selected" : "" ?>>
                                                <?= str_replace('#$%^', ' ', $outlet); ?></option>
                                        <?php endforeach ?>
                                    </select>
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

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-plus"></i></span>
                    <h4><?= $title; ?></h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal" action="<?= base_url().'journal/saveJournal'; ?>" method="post" id="form-journal">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="col-md-4 control-label">Tanggal Jurnal</label>
                                            <div class="col-md-8">
                                                <div style="float:left; display: inline-block; width: 75%;">
                                                    <div class="input-group date" id="journaldate">
                                                        <input type="text" class="form-control" 
                                                            name="journaldate" autocomplete="off"
                                                            value="<?= isNotEmpty($journal['journal']->JournalDate) ? formatdateindonesia($journal['journal']->JournalDate) : '' ?>"/>
                                                        <span class="input-group-addon">
                                                            <i class="glyphicon glyphicon-th"></i>
                                                        </span>
                                                    </div>
                                                    <span id="journaldate-error-area"></span>
                                                </div>
                                                <div style="float:left; display: inline-block; width: 25%;">
                                                    <input type="text" class="form-control timepicker" 
                                                        id="journaltime" placeholder="__:__" name="journaltime" 
                                                        value="<?= isNotEmpty($journal['journal']->JournalTime) ? $journal['journal']->JournalTime : '00:00'; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="journal-date" name="journal_date" value="<?= isNotEmpty($journal['journal']->JournalDate) ? $journal['journal']->JournalDate : ''; ?>"/>
                                        
                                        <div class="form-group col-md-12">
                                            <label class="col-md-4 control-label">Keterangan</label>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <textarea class="form-control" name="note" id="note" rows="3"><?= isNotEmpty($journal['journal']->Note) ? $journal['journal']->Note : ''; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="col-md-4 control-label">Nama Transaksi</label>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input class="form-control" name="transactionname" id="transactionname" 
                                                            value="<?= isNotEmpty($journal['journal']->TransactionName) ? $journal['journal']->TransactionName : '';  ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label class="col-md-4 control-label">Kode Transaksi</label>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input class="form-control" name="transactioncode" id="transactioncode" 
                                                            value="<?= isNotEmpty($journal['journal']->TransactionNumber) ? $journal['journal']->TransactionNumber : '';  ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" id="input-title">
                                <div class="col-md-3">
                                    <p>Akun</p>
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-right: 10px;" class="pull-right">Debet</p>
                                </div>
                                <div class="col-md-2">
                                    <p style="margin-right: 10px;" class="pull-right">Kredit</p>
                                </div>
                                <div class="col-md-5">
                                    <p style="margin-left: 10px;">Keterangan</p>
                                </div>
                            </div>
                            <br>
                            <div id="input-content">
                                <?php if (count($journal['journaldetail']) > 0) { 
                                    $i = 1;
                                    foreach ($journal['journaldetail'] as $val) {
                                        ?>
                                        <div id="input-content-<?= $i; ?>" class="form-input">
                                            <input type="hidden" class="journaldetailid" 
                                                name="journaldetailid[]" value="<?= $val->DetailID; ?>">
                                            <input type="hidden" name="deletedid[]" class="deletedid">

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label class="control-label">Akun :</label>
                                                            <select name="journalaccountid[]" class="form-control journalaccountid">
                                                                <option value=""></option>
                                                                <?php foreach ($account as $row) { ?>
                                                                    <?php if ($row->IsDefault == true) {?>
                                                                        <optgroup label="<?= $row->AccountCode; ?> <?= $row->AccountName; ?>">
                                                                            <option disabled></option>
                                                                        </optgroup>
                                                                    <?php }else{ ?>
                                                                        <option value="<?= $row->JournalAccountID; ?>" <?= $row->JournalAccountID == $val->JournalAccountID ? 'selected' : ''; ?>><?= $row->AccountCode; ?> <?= $row->AccountName; ?></option>
                                                                    <?php } ?>
                                                                    
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        
                                                        <div class="col-md-12">
                                                            <label class="control-label">Debet :</label>
                                                            <input type="text" class="form-control debet" name="debet[]" value="<?= round($val->Debit); ?>" style="text-align: right;" id="debet-<?= $i; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label class="control-label">Kredit :</label>
                                                            <input type="text" class="form-control credit" name="credit[]" value="<?= round($val->Credit); ?>" style="text-align: right;" id="credit-<?= $i; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label class="control-label">Keterangan :</label>
                                                            <input type="text" class="form-control detailnote" name="detailnote[]" value="<?= $val->DetailNote; ?>" id="detailnote-<?= $i; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                        <?php
                                        $i += 1;
                                    }
                                }?>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="btn-append" class="btn btn-primary" data-id="<?= count($journal['journaldetail']); ?>" onclick="newDetail()"> <i class="fa fa-plus"></i> Tambah Akun Lain </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <input type="hidden" name="journalid" id="journalid" 
                                        value="<?= isNotEmpty($journal['journal']->JournalID) ? $journal['journal']->JournalID : ''; ?>">
                                    <input type="hidden" name="outlet" id="outlet" value = "<?= $_GET['outlet'] ?>">
                                    <a href="<?= base_url() ?>journal/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
                                    <button type="submit" class="btn btn-primary" <?= count($journal['journaldetail']) > 0 ? '' : 'disabled'; ?>>Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div id="form-input-template" class="form-input" style="display:none;">
    <input type="hidden" name="journaldetailid[]" class="journaldetailid">
    <input type="hidden" name="deletedid[]" class="deletedid">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Akun :</label>
                    <select name="journalaccountid[]" class="form-control journalaccountid">
                        <option value=""></option>
                        
                        <?php foreach ($account as $row) { ?>
                            <?php if ($row->IsDefault == true) {?>
                                <optgroup label="<?= $row->AccountCode; ?> <?= $row->AccountName; ?>">
                                    <option disabled></option>
                                </optgroup>
                            <?php }else{ ?>
                                <option value="<?= $row->JournalAccountID; ?>"><?= $row->AccountCode; ?> <?= $row->AccountName; ?></option>
                            <?php } ?>
                            
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                
                <div class="col-md-12">
                    <label class="control-label">Debet :</label>
                    <input type="text" class="form-control debet" name="debet[]" value="0" style="text-align: right;" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Kredit :</label>
                    <input type="text" class="form-control credit" name="credit[]" value="0" style="text-align: right;" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Keterangan :</label>
                    <input type="text" class="form-control detailnote" name="detailnote[]" value="" readonly>
                </div>
            </div>
        </div>
    </div>
</div>