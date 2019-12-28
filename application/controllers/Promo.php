<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo extends MY_Controller{

    var $DevIDAtauIDPerusahaan = '';

    public function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();
        $this->load->library('NutaQuery');
        $this->load->model('Userperusahaancabang');
        $this->load->model('MasterPromo');
        $this->load->model('Perusahaanmodel');
        $devid = getLoggedInUserID();
        $result = $this->Perusahaanmodel->get_perusahaanno_by_devid($devid);
        $noperusahaan = 1;
        if (count($result) >= 0) {
                $noperusahaan = $result[0]->PerusahaanNo;
        }
        $this->nutaquery->setDeviceID(getLoggedInRegisterWithDeviceID());
        $this->DevIDAtauIDPerusahaan = $devid;
        $this->nutaquery->setPerusahaaanID($this->DevIDAtauIDPerusahaan, $noperusahaan);
        $cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->nutaquery->setCabangs($cabangs);
        $this->load->library('CurrencyFormatter');
        $this->load->helper('nuta_helper');
    }

    function listPromo()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        if (count($availableOutlets) > 1) {
                $this->nutaquery->SetOutlet(-999);
        } else {
                $this->setDefaultOutletId($availableOutlets);
        }
        $selected_outlet = $this->input->get('outlet');
        if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->SetOutlet($selected_outlet);
        } else if(count($availableOutlets)==1) {
                $this->nutaquery->SetOutlet($this->default_outlet_id);
        }
        $query_master_promo = $this->nutaquery->get_query_daftar_master_promo(getPerusahaanNo());
        $query = $this->db->query($query_master_promo);
        $result = $query->result();
        $fields = $query->field_data();

        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $this->nutaquery->getOutlet();
        $data['page_part'] = 'promo/promo_master_promo';
        $data['js_part'] = array(
                "features/js/js_form",
                'features/js/js_grid_item',
                'features/js/js_datatable',
                'features/js/js_promo_master_promo',
                'features/js/js_dialog_hapus_promo');
        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();
        $this->load->view('main_part', $data);
    }

    function promoForm()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        $selected_outlet = $this->input->get('outlet');

        if (isNotEmpty($selected_outlet)) {
                $this->nutaquery->setOutlet($selected_outlet);
        }

        $promotitle = $this->input->get('promoid');
        $category = $this->MasterPromo->getCategory($selected_outlet,getPerusahaanNo());
        $item = $this->MasterPromo->getItem($selected_outlet,getPerusahaanNo());
        $mode = isNotEmpty($promotitle) ? 'edit' : 'new';
        $listjenispromo = array('Diskon produk dengan jumlah minimal n' => 1,
                                                        'Diskon order dengan total minimal transaksi n' => 2,
                                                        'Beli A gratis B' => 3);
        $listharipromo = array(
            'Senin' => '0-senin',
            'Selasa' => '1-selasa',
            'Rabu' => '2-rabu',
            'Kamis' => '3-kamis',
            'Jumat' => '4-jumat',
            'Sabtu' => '5-sabtu',
            'Minggu' => '6-minggu'
        );
        $promotype = 0;
        if ($mode == 'new') {
            $promotype = 1;
            $promo_title = '';
            $promofromdate = date('Y-m-d');
            $promotodate = date('Y-m-d');
            $promofromtime = '';
            $promototime = '';
            $termqty = '';
            $termitems = '';
            $termcategories = '';
            $getdiscounttype = '';
            $getdiscountvalue = '';
            $termtotal = '';
            $applymultiply = '';
            $getitemqty = '';
            $getitemid = '';
            $hari = array('');
        } else {
            if (isNotEmpty($selected_outlet)) {
                 $this->nutaquery->setOutlet($selected_outlet);
            } else {
                 $this->setDefaultOutlet($availableOutlets);
            }

            $promo = $this->MasterPromo->getByTitle($promotitle, $this->nutaquery->getOutlet());
            $promo_title = $promo -> PromoTitle;
            $promofromdate = $promo -> PromoFromDate;
            $promotodate = $promo -> PromoToDate;
            $promofromtime = $promo -> PromoFromTime;
            $promototime = $promo -> PromoToTime;
            $applymonday = ($promo -> ApplyMonday == 1) ? '0-senin' : '';
            $applytuesday = ($promo -> ApplyTuesday == 1) ? '1-selasa' : '';
            $applywednesday = ($promo -> ApplyWednesday == 1) ? '2-rabu' : '';
            $applythursday = ($promo -> ApplyThursday == 1) ? '3-kamis' : '';
            $applyfriday = ($promo -> ApplyFriday == 1) ? '4-jumat' : '';
            $applysaturday = ($promo -> ApplySaturday == 1) ? '5-sabtu' : '';
            $applysunday = ($promo -> ApplySunday == 1) ? '6-minggu' : '';
            $promotype = $promo -> PromoType;
            $termqty = $promo -> TermQty;
            $termitems = $promo -> TermItems;
            $termcategories = $promo -> TermCategories;
            $getdiscounttype = $promo -> GetDiscountType;
            $getdiscountvalue = $promo -> GetDiscountValue;
            $termtotal = $promo -> TermTotal;
            $applymultiply = $promo -> ApplyMultiply;
            $getitemqty = $promo -> GetItemQty;
            $getitemid = $promo -> GetItemID;
            $hari = array($applymonday,$applytuesday,$applywednesday,$applythursday,$applyfriday,$applysaturday,$applysunday);
            $hari = array_diff($hari,array(''));
    }

        $data['outlets'] = $availableOutlets;
        $data['selected_outlet'] = $selected_outlet;
        $data['modeform'] = $mode;
        $data['page_part'] = 'promo/promo_form_master_promo';
        $data['js_part'] = array(
                'features/js/js_form',
                'features/js/js_grid_item',
                'features/js/js_datatable',
                'features/filters/filter_date_mulai_sampai_horizontal_js',
                'promo/js/promo_dialog_simpan_beberapa_outlet_js',
                'features/js/js_promo_master_promo',
                'features/js/js_promo_form_master',
                'features/js/js_dialog_hapus_promo');

        $data['jenispromo'] = $listjenispromo;
        $data['hari'] = $listharipromo;
        $data['category'] = $category;
        $data['item'] = $item;

        $data['form']['jenispromo'] = $promotype;
        $data['form']['namapromo'] = $promo_title;
        $data['form']['oldname'] = $promo_title;
        $data['form']['datestart'] = $promofromdate;
        $data['form']['dateend'] = $promotodate;
        $data['form']['jammulai'] = $promofromtime;
        $data['form']['jamend'] = $promototime;
        $data['form']['hari'] = $hari;
        $data['form']['termqty'] = $termqty;
        $data['form']['termitems'] = $termitems;
        $data['form']['termcategories'] = $termcategories;
        $data['form']['discounttype'] = $getdiscounttype;
        $data['form']['discountvalue'] = $getdiscountvalue;
        $data['form']['termtotal'] = $termtotal;
        $data['form']['multiply'] = $applymultiply;
        $data['form']['itemqty'] = $getitemqty;
        $data['form']['itemid'] = $getitemid;

        $data['js_chart'] = array();
        $data['datagrid'] = array('fields' => '', 'result' => '');
        $data['visibilityMenu'] = $this->visibilityMenu;
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanStokVisible'] = $this->IsLaporanStokVisible();
        $data['isLaporanPembelianVisible'] = $this->IsLaporanPembelianVisible();
        $data['isLaporanPriceVarianVisible'] = $this->IsLaporanVarianHargaVisible();

        $this->load->view('main_part', $data);
    }
}
