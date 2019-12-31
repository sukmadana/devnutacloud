<?php

class Laporan extends Laporan_Controller
{

    var $DevIDAtauIDPerusahaan = '';

    function __construct()
    {
        parent::__construct();

        $this->load->model('Userperusahaancabang');
        $devid = getLoggedInUserID();
        $this->nutaquery->setDeviceID(getLoggedInRegisterWithDeviceID());
        $this->DevIDAtauIDPerusahaan = $devid;

        $this->nutaquery->setPerusahaaanID($this->DevIDAtauIDPerusahaan);
        $this->nutaquery->setNomorPerusahaan(getPerusahaanNo());
        $this->nutaquery->setTableSale(getTableSale());
        $this->nutaquery->setTableSaleDetail(getTableSaleDetail());
        $this->nutaquery->setTableSaleDetailIngredients(getTableSaleDetailIngredients());
        $cabangs = $this->Userperusahaancabang->getListCabang(getLoggedInUsername(), $this->DevIDAtauIDPerusahaan);
        $this->nutaquery->setCabangs($cabangs);
    }

    function Penjualan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);


        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        // if ($outlet==4998) {
        //     log_message('error', $strQuery);
        // }
        list($result, $fields) = $this->execute_query($strQuery);

        unset($fields[0]);
        unset($fields[1]);
        $htmlTable = $this->generateMergeCellLaporanPenjualan2($result, $modules, $is_ekstra);
        $htmlTable_hidden = $this->generateMergeCellLaporanPenjualan2($result, $modules, $is_ekstra, TRUE);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporan';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_penjualan';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data['tbody_hidden'] = $htmlTable_hidden;
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Penjualan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    // Laporan Rekap Penjualan Done
    function RekapPenjualan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rekap_penjualan($modules['IsUseTaxModule'], $rekapper);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan';
        $data['filter_webpart'] = 'features/filters/filter_form_rekap_penjualan';

        $data['selected_rekapper'] = $rekapper;


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array(
            'fields' => $fields, 'result' => $result,
            'fields_charge' => $fieldsCharge, 'result_charge' => $resultCharge
        );

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);

        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];
        // var_dump($data['title']);
        // exit;
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function SaldoKasRekening()
    {

        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        $dateStart = $this->input->get('date_start');
        $dateEnd = $dateStart;
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $no = getPerusahaanNo();
        $before = false;
        if ($dateStart != null) {
            $before = date('Y-m') . '-01';
        }
        $checkpoint_uang = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint_uang
                                where PerusahaanNo=" . $no . " and DeviceID=" . $outlet . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month' from checkpoint_uang where PerusahaanNo="
            . $no . " and DeviceID=" . $outlet;
        if ($before != false) {
            $checkpoint_uang .= " and InsertedDate < '" . $dateStart . "'";
        }
        $checkpoint_uang .= " )";
        $query_checkpoint = $this->db->query($checkpoint_uang);
        $result_checkpoint = $query_checkpoint->result();
        $rcheckpoint = array();
        foreach ($result_checkpoint as $row => $value) {
            $key = $value->AccountID . "." . $value->DeviceNo;
            $rcheckpoint[$key] = $value;
        }
        $result_checkpoints = json_decode(json_encode($result_checkpoint), true);
        if (sizeof($result_checkpoints) > 0) {
            $minDate = $result_checkpoints[0]['InsertedDate'];
        } else {
            $minDate = false;
        }

        $strQuery = $this->nutaquery->get_query_saldo_uang($minDate);
        list($result, $fieldsEx) = $this->execute_query($strQuery);
        $transtok = array();
        foreach ($result as $value) {
            $key = $value->AccountID . "." . $value->DeviceNo;
            $transtok[$key] = $value;
        }
        $strQueryMI = "SELECT AccountID,DeviceNo,"
            . "CASE WHEN AccountType=2 THEN CONCAT(m.BankName,' ',m.AccountNumber,' ',m.AccountName) "
            . "ELSE m.AccountName END KasRekening, 0 AS Saldo FROM mastercashbankaccount m WHERE PerusahaanNo="
            . $no . " AND DeviceID=" . $outlet . " ORDER BY KasRekening";
        list($result_mi, $fields) = $this->execute_query($strQueryMI);
        //log_message('error', 'fields before unset ' . var_export($fields,true));
        unset($fields[0]);
        unset($fields[1]);
        //        foreach ($fields as $f) {
        //            unset($f->AccountID);
        //            unset($f->DeviceNo);
        //        }
        //log_message('error', 'fields after unset ' . var_export($fields,true));
        $res = json_decode(json_encode($result), true);
        $resultfinal = array();
        foreach ($result_mi as $v) {
            $key = $v->AccountID . "." . $v->DeviceNo;
            //            if(is_null($rcheckpoint[$key]) && is_null($transtok[$key])) {
            //                continue;
            //            }
            $qty = 0.0;
            if (!is_null($rcheckpoint[$key])) {
                $qty += $rcheckpoint[$key]->Saldo;
            }
            if (!is_null($transtok[$key])) {
                $qty += $transtok[$key]->Saldo;
                //                $qty += $transtok[$key]['Qty'];
            }
            $v->Saldo = $qty;
            unset($v->AccountID);
            unset($v->DeviceNo);
            array_push($resultfinal, $v);
        }
        //log_message('error', 'resultfinal kas rekening ' . var_export($resultfinal,true));
        //        $result = array_merge($result_checkpoint,$result);

        $data['page_part'] = 'laporan/laporan_saldo_kas_rekening';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        //        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['datagrid'] = array('fields' => $fields, 'result' => $resultfinal);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Saldo Kas / Rekening ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Laba()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();

        $strQuery = $this->nutaquery->get_query_laba($modules['IsUseTaxModule']);

        list($result, $fields) = $this->execute_query($strQuery);

        $links = array(
            'Total Penjualan' => 'laporan/rekappenjualan',
            'Total HPP' => 'laporan/rincianlaba',
            'Total Pendapatan Lain' => 'laporan/pendapatanselainpenjualan',
            'Total Pengeluaran' => 'laporan/pengeluaran',
            'Total Pajak' => 'laporan/pajak',
            'Total Share Revenue' => 'laporan/rekappenjualanpertipepenjualan',
        );


        $data['page_part'] = 'laporan/laporan_laba';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Laba ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Labapershift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();

        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);
        $resultdata = array();
        if ($result)
            for ($i = 0; $i < count($result); $i++) {
                $r = $result[$i];
                $q2 = $this->nutaquery->get_query_laba_per_shift($modules['IsUseTaxModule'], $r->OpenID, $r->DeviceNo);
                list($details, $f2) = $this->execute_query($q2);
                $r->Details = $details;
            }

        $links = array(
            'Total Penjualan' => 'laporan/rekappenjualan',
            'Total HPP' => 'laporan/rincianlaba',
            'Total Pendapatan Lain' => 'laporan/pendapatanselainpenjualan',
            'Total Pengeluaran' => 'laporan/pengeluaran',
            'Total Pajak' => 'laporan/pajak'
        );

        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;
        
        $data['title'] = 'Laporan Laba';
        $data['page_part'] = 'laporan/laporan_labapershift';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPembayaran()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPembayaran']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rekap_pembayaran();

        list($result, $fields) = $this->execute_query($strQuery);

        $links = array(
            'Penjualan Tunai' => 'laporan/rekappenjualantunai',
            'Penjualan Non Tunai' => 'laporan/rekappenjualannontunai',
        );




        $data['page_part'] = 'laporan/laporan_rekap_pembayaran';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Pembayaran ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function isNotEmpty($value)
    {
        return isset($value) && trim($value) != "";
    }

    protected function GroupResultByField($result, $field)
    {

        $GroupBySaleNumber = array();
        foreach ($result as $row) {
            $key = $row->$field;
            $isKeyExist = array_key_exists($row->$field, $GroupBySaleNumber);
            if ($isKeyExist) {
                array_push($GroupBySaleNumber[$key], $row);
            } else {
                $GroupBySaleNumber[$key] = array();
                array_push($GroupBySaleNumber[$key], $row);
            }
        }
        return $GroupBySaleNumber;
    }

    protected function GroupResultByField2($result, $field, $field2)
    {

        $GroupBySaleNumber = array();
        foreach ($result as $row) {
            $key = $row->$field . $row->$field2;
            $isKeyExist = array_key_exists($key, $GroupBySaleNumber);
            if ($isKeyExist) {
                array_push($GroupBySaleNumber[$key], $row);
            } else {
                $GroupBySaleNumber[$key] = array();
                array_push($GroupBySaleNumber[$key], $row);
            }
        }
        return $GroupBySaleNumber;
    }

    protected function RenderMergeCell($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice, $excel_export = FALSE)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            //if ($sale->UrutanTampil != 2) {
            if (substr($sale->Item, 0, 12) !== 'Diskon Final') {
                $jumlah += $sale->SubTotal;
            } else {
                $jumlah += $sale->SubTotal - $sale->HargaSatuan;
            }
        }

        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Qty, $excel_export);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = ($sale->Qty * $sale->HargaSatuan);

            if (substr($itemname, 0, 12) === 'Diskon Final') {
                //if ($sale->UrutanTampil == 2) {
                $Qty = "";
                $UnitPrice = "";
                $SubTotal = $sale->SubTotal - $sale->HargaSatuan;
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . " " . $sale->Diskon; // Diskon Final 10%
                }
            } else if ($sale->Diskon !== "" && substr($sale->Diskon, 0, 1) !== "0") {
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . "<br><i>" . "Diskon " . $sale->Diskon . "</i>"; // Diskon 10 %
                } else {
                    $itemname = $itemname . "<br> <i>" . "Diskon " . "</i>";
                }

                $strSubtotal = $sale->SubTotal - ($Qty * $UnitPrice);
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                $SubTotal .= "<br/> <i>" . $this->idrToUsd($strSubtotal, $excel_export) . "</i>";
            } else {
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
            }

            //if(!itemname.equals(getActivity().getResources().getString(R.string.FinalDiscount)))
            //gt += d.SubTotal;
            //$Qty = $this->idrToUsd($Qty);
            $UnitPrice = $this->idrToUsd($UnitPrice, $excel_export);
            $jumlah = $this->idrToUsd($jumlah, $excel_export);
            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);

                $tablecontent .= "<tr>";
                if (isset($sale->Outlet)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Outlet . "</td>";
                }
                $linkapi = "";
                if (isset($sale->TransactionID)) {
                    $paramapi = new \stdClass();
                    $paramapi->i = $this->get_outlet_param();
                    $paramapi->s = $sale->TransactionID . "." . $sale->DeviceNo;
                    //$paramlinkapi = base64_encode(json_encode(array(["i"=>$this->get_outlet_param(), "s"=>$sale->TransactionID.".".$sale->DeviceNo])));
                    $paramlinkapi = base64_encode(json_encode($paramapi));
                    //log_message('error', $this->get_outlet_param());
                    if ($excel_export === FALSE) {
                        $linkapi = "<br><a target=\"_blank\" rel=\"noopener noreferrer\" href=\"" . $this->config->item('api_base_url') . "receipt/viewstruk?param=$paramlinkapi&paper=A4\">Lihat Struk</a>";
                    }
                    
                }
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Nomor . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Tanggal . $linkapi . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pelanggan . "</td>";
                if ($IsDiningTable)
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pax . "</td>";

                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $jumlah . "</td>";
                if ($is_ekstra || isset($sale->DibuatOleh)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DibuatOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglBuat . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DiubahOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglUbah . "</td>";
                }
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    protected function RenderMergeCellPerShift($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            //if ($sale->UrutanTampil != 2) {
            if (substr($sale->Item, 0, 12) !== 'Diskon Final') {
                //$jumlah += $sale->SubTotal + $sale->Markup;
                $jumlah += $sale->SubTotal;
            } else {
                $jumlah += $sale->SubTotal - $sale->HargaSatuan;
            }
        }

        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->currencyformatter->format($sale->Qty);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = ($sale->Qty * $sale->HargaSatuan) + $sale->Markup;

            if (substr($itemname, 0, 12) === 'Diskon Final') {
                //if ($sale->UrutanTampil == 2) {
                $Qty = "";
                $UnitPrice = "";
                $SubTotal = $sale->SubTotal - $sale->HargaSatuan;
                $SubTotal = $this->currencyformatter->format($SubTotal);
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . " " . $sale->Diskon; // Diskon Final 10%
                }
            } else if ($sale->Diskon !== "" && substr($sale->Diskon, 0, 1) !== "0") {
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . "<br><i>" . "Diskon " . $sale->Diskon . "</i>"; // Diskon 10 %
                } else {
                    $itemname = $itemname . "<br> <i>" . "Diskon " . "</i>";
                }

                $strSubtotal = $sale->SubTotal - ($Qty * $UnitPrice);
                $SubTotal = $this->currencyformatter->format($SubTotal);
                $SubTotal .= "<br/> <i>" . $this->currencyformatter->format($strSubtotal) . "</i>";
            } else {
                $SubTotal = $this->currencyformatter->format($SubTotal);
            }

            //if(!itemname.equals(getActivity().getResources().getString(R.string.FinalDiscount)))
            //gt += d.SubTotal;
            //$Qty = $this->currencyformatter->format($Qty);
            $UnitPrice = $this->currencyformatter->format($UnitPrice);
            $jumlah = $this->currencyformatter->format($jumlah);
            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);

                $tablecontent .= "<tr>";
                if (isset($sale->Outlet)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Outlet . "</td>";
                }
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Nomor . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Tanggal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pelanggan . "</td>";
                if ($IsDiningTable)
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pax . "</td>";

                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . $sale->Markup . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $jumlah . "</td>";
                if ($is_ekstra || isset($sale->DibuatOleh)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DibuatOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglBuat . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DiubahOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglUbah . "</td>";
                }
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . $sale->Markup . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    protected function RenderMergeCellVoid($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice, $excel_export = FALSE)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            //if ($sale->UrutanTampil != 2) {
            if (substr($sale->Item, 0, 12) !== 'Diskon Final') {
                $jumlah += $sale->SubTotal;
            } else {
                $jumlah += $sale->SubTotal - $sale->HargaSatuan;
            }
        }

        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Qty, $excel_export);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = $sale->Qty * $sale->HargaSatuan;

            if (substr($itemname, 0, 12) === 'Diskon Final') {
                //if ($sale->UrutanTampil == 2) {
                $Qty = "";
                $UnitPrice = "";
                $SubTotal = $sale->SubTotal - $sale->HargaSatuan;
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . " " . $sale->Diskon; // Diskon Final 10%
                }
            } else if ($sale->Diskon !== "" && substr($sale->Diskon, 0, 1) !== "0") {
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . "<br><i>" . "Diskon " . $sale->Diskon . "</i>"; // Diskon 10 %
                } else {
                    $itemname = $itemname . "<br> <i>" . "Diskon " . "</i>";
                }

                $strSubtotal = $sale->SubTotal - ($Qty * $UnitPrice);
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                $SubTotal .= "<br/> <i>" . $this->idrToUsd($strSubtotal, $excel_export) . "</i>";
            } else {
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
            }

            //if(!itemname.equals(getActivity().getResources().getString(R.string.FinalDiscount)))
            //gt += d.SubTotal;
            //$Qty = $this->idrToUsd($Qty);
            $UnitPrice = $this->idrToUsd($UnitPrice, $excel_export);
            $jumlah = $this->idrToUsd($jumlah, $excel_export);
            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);

                $tablecontent .= "<tr>";
                if (isset($sale->Outlet)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Outlet . "</td>";
                }
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Nomor . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Tanggal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pelanggan . "</td>";
                if ($IsDiningTable)
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pax . "</td>";

                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $jumlah . "</td>";
                if ($is_ekstra || isset($sale->DivoidOleh)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DivoidOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglVoid . "</td>";
                }
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    protected function RenderMergeCellPesananBatal($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice, $excel_export = FALSE)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            //if ($sale->UrutanTampil != 2) {
            if (substr($sale->Item, 0, 12) !== 'Diskon Final') {
                $jumlah += $sale->SubTotal * -1;
            } else {
                $jumlah += ($sale->SubTotal * -1) - $sale->HargaSatuan;
            }
        }

        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Qty, $excel_export);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = ($sale->Qty * -1 * $sale->HargaSatuan);

            if (substr($itemname, 0, 12) === 'Diskon Final') {
                //if ($sale->UrutanTampil == 2) {
                $Qty = "";
                $UnitPrice = "";
                $SubTotal = $sale->SubTotal - $sale->HargaSatuan;
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . " " . $sale->Diskon * -1; // Diskon Final 10%
                }
            } else if ($sale->Diskon !== "" && substr($sale->Diskon, 0, 1) !== "0") {
                if (strpos($sale->Diskon, "%") !== false) {
                    $itemname = $itemname . "<br><i>" . "Diskon " . $sale->Diskon * -1 . "</i>"; // Diskon 10 %
                } else {
                    $itemname = $itemname . "<br> <i>" . "Diskon " . "</i>";
                }

                $strSubtotal = ($sale->SubTotal - ($Qty * $UnitPrice)) * -1;
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
                $SubTotal .= "<br/> <i>" . $this->idrToUsd($strSubtotal, $excel_export) . "</i>";
            } else {
                $SubTotal = $this->idrToUsd($SubTotal, $excel_export);
            }

            //if(!itemname.equals(getActivity().getResources().getString(R.string.FinalDiscount)))
            //gt += d.SubTotal;
            //$Qty = $this->currencyformatter->format($Qty);
            $UnitPrice = $this->idrToUsd($UnitPrice, $excel_export);
            $jumlah = $this->idrToUsd($jumlah, $excel_export);
            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);

                $tablecontent .= "<tr>";
                if (isset($sale->Outlet)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Outlet . "</td>";
                }
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Nomor . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Tanggal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pelanggan . "</td>";
                if ($IsDiningTable)
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $sale->Pax . "</td>";

                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty * -1 . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $jumlah . "</td>";
                if ($is_ekstra || isset($sale->DibuatOleh)) {
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DibuatOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglBuat . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->DiubahOleh . "</td>";
                    $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\">" . $sale->TglUbah . "</td>";
                }
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                if ($is_ekstra) {
                    $tablecontent .= "<td>" . $sale->Kategori . "</td>";
                    if ($isVarianAndPrice) {
                        $tablecontent .= "<td>" . $sale->Varian . "</td>";
                        $tablecontent .= "<td>" . $sale->PilihanExtra . "</td>";
                    }
                }
                $tablecontent .= "<td align=\"center\">" . $Qty * -1 . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $UnitPrice . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    function RekapPembelian()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();

        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }


        $strQuery = $this->nutaquery->get_query_rekap_pembelian($rekapper);
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/laporan_rekap_pembelian';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Pembelian ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Pembelian()
    {
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();

        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pembelian();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pembelian';
        $data['page_part'] = 'laporan/laporan_pembelian';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $a = $this->GroupResultByField($result, 'Nomor');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCell($a[$key], false, false, false);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];

            $processrenderMergeCellExport = $this->RenderMergeCell($a[$key], false, false, false, TRUE);
            $b_export .= $processrenderMergeCellExport['content'];
        }

        $b .= "<tr>";
        $b .= "<td colspan='8'>Grand Total</td>";
        $b .= "<td colspan='5'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='8'>Grand Total</td>";
        $b_export .= "<td colspan='5'>" . $grandTotalFooter . "</td>";
        $b_export .= "<tr>";


        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function stok()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        $availableOutlets = $this->setup_outlets();

        $dateStart = $this->input->get('date_start');
        $dateEnd = $dateStart;
        $outlet = $this->get_outlet_param();
        $before = false;
        if ($dateStart != '') {
            $month = date('Y-m', strtotime($dateStart));
            $before = $month . '-01';
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $perusahaannomor = getPerusahaanNo();
        $checkpoint = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint
                                where PerusahaanNo=" . $perusahaannomor . " and DeviceID=" . $outlet . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month' from checkpoint where PerusahaanNo=" . $perusahaannomor . " and DeviceID=" . $outlet;
        if ($before != false) {
            $checkpoint .= " and InsertedDate < '" . $dateStart . "'";
        }
        $checkpoint .= ")";
        $query_checkpoint = $this->db->query($checkpoint);
        $result_checkpoint = $query_checkpoint->result();
        $rcheckpoint = array();
        foreach ($result_checkpoint as $row => $value) {
            $key = $value->ItemID . "." . $value->ItemDeviceNo;
            $rcheckpoint[$key] = $value;
        }
        $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
        if (sizeof($result_checkpoint) > 0) {
            $minDate = $result_checkpoint[0]['InsertedDate'];
        } else {
            $minDate = false;
        }
        $strQuery = $this->nutaquery->get_query_stok2($minDate);
        list($result, $fields) = $this->execute_query($strQuery);
        foreach ($result as $keys => $value) {
            //            $key = $value->ItemID . "." . $value->DeviceNo;
            //            //$key = array_search($value->ItemId. '', array_column($result_checkpoint, 'ItemID'));
            //            if(!is_null($rcheckpoint[$key])) {
            //                $value->Qty = $rcheckpoint[$key]->Qty + $value->Qty;
            //            }
            unset($value->ItemId);
            unset($value->DeviceNo);
        }
        unset($fields[0]);
        unset($fields[1]);
        $fields = array_values($fields);

        $data['page_part'] = 'laporan/laporan_stok';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Stok ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function nilaistok()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        $availableOutlets = $this->setup_outlets();

        $dateStart = $this->input->get('date_start');
        $dateEnd = $dateStart;
        $outlet = $this->get_outlet_param();
        $before = false;
        if ($dateStart != '') {
            $month = date('Y-m', strtotime($dateStart));
            $before = $month . '-01';
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $perusahaannomor = getPerusahaanNo();
        $checkpoint = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint
                                where PerusahaanNo=" . $perusahaannomor . " and DeviceID=" . $outlet . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month' from checkpoint where PerusahaanNo=" . $perusahaannomor . " and DeviceID=" . $outlet;
        if ($before != false) {
            $checkpoint .= " and InsertedDate < '" . $dateStart . "'";
        }
        $checkpoint .= ")";
        $query_checkpoint = $this->db->query($checkpoint);
        $result_checkpoint = $query_checkpoint->result();
        $rcheckpoint = array();
        foreach ($result_checkpoint as $row => $value) {
            $key = $value->ItemID . "." . $value->ItemDeviceNo;
            $rcheckpoint[$key] = $value;
        }
        $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
        if (sizeof($result_checkpoint) > 0) {
            $minDate = $result_checkpoint[0]['InsertedDate'];
        } else {
            $minDate = false;
        }
        $strQuery = $this->nutaquery->get_query_nilai_stok($minDate);
        list($result, $fields) = $this->execute_query($strQuery);
        foreach ($result as $keys => &$value) {
            $value->HargaBeli = $this->currencyformatter->format($value->HargaBeli);
            $value->NilaiRupiah = $this->currencyformatter->format($value->NilaiRupiah);
            unset($value->ItemId);
            unset($value->DeviceNo);
        }
        unset($fields[0]);
        unset($fields[1]);
        $fields = array_values($fields);

        $data['page_part'] = 'laporan/laporan_nilai_stok';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js',
            'laporan/js/laporan_nilai_stok'

        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Stok ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function samakanstokkeapp()
    {
        $outlet = $this->input->get_post('o');
        $this->load->model("outlet");
        $perusahaanNo = $this->outlet->getPerusahaanNoByOutlet($outlet);

        $this->load->library('NutaQuery');
        $querystok = $this->nutaquery->get_query_stok_byoutlet($perusahaanNo, $outlet);

        $stocks = $this->db->query($querystok)->result();
        //            print_r(json_encode($stocks));
        print_r(json_encode(array('data' => $stocks)));
        //print_r(array('data' => $stocks));
    }

    public function kartustok()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = "1.1";
        }

        $realitemid = explode(".", $selecteditem)[0];
        $devno = explode(".", $selecteditem)[1];
        $checkpoint = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint
                                where DeviceID=" . $outlet . "
                                and ItemID=" . $realitemid . " and ItemDeviceNo=" . $devno . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month' from checkpoint
                    where DeviceID=" . $outlet . " and ItemID=" . $realitemid . " and ItemDeviceNo=" . $devno .
            " AND InsertedDate<'" . $dateStart . "')";
        $query_checkpoint = $this->db->query($checkpoint);
        $result_checkpoint = $query_checkpoint->result();
        $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
        if (sizeof($result_checkpoint) > 0) {
            $minDate = $result_checkpoint[0]['InsertedDate'];
        } else {
            $minDate = '2015-01-01';
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $availableItems = $this->GetAvailableItemsByOutlet($this->nutaquery->getOutlet());
        $strQuery = $this->nutaquery->get_query_kartu_stok2($selecteditem, $minDate);
        list($result, $fields) = $this->execute_query($strQuery);
        //        if (count ($result_checkpoint) > 0) {
        //            $result[0]->Saldo = (float)$result_checkpoint[0]['Qty'] + (float)$result[0]->Saldo;
        //        }
        $data['items'] = $availableItems;
        $data['selected_item'] = $selecteditem;

        $data['page_part'] = 'laporan/laporan_kartu_stok';
        $data['js_part'] = array(
            'features/js/js_form', 'features/js/js_datatable',
            'features/js/js_ajax_item_by_outlet',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Laporan Kartu Stok ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function rekapmutasistok()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = 1;
        }
        $checkpoint = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint
                                where DeviceID=" . $outlet . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month'
                                        from checkpoint where DeviceID=" . $outlet;
        if ($dateStart != null) {
            $checkpoint .= " and InsertedDate < '" . $dateStart . "'";
        }
        $checkpoint .= " )";
        $query_checkpoint = $this->db->query($checkpoint);
        $result_checkpoint = $query_checkpoint->result();
        $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
        if (sizeof($result_checkpoint) > 0) {
            $minDate = $result_checkpoint[0]['InsertedDate'];
        } else {
            $minDate = '2015-01-01';
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_rekap_mutasi_stok2($minDate);
        list($result, $fields) = $this->execute_query($strQuery);
        $resultArray = json_decode(json_encode($result), true);
        $result = json_decode(json_encode($resultArray));

        $data['selected_item'] = $selecteditem;

        $data['page_part'] = 'laporan/laporan_mutasi_stok';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Mutasi Stok ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function hapusdata()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();
        $dateEnd = $this->input->get('date_start');
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = 1;
        }

        if ($this->isNotEmpty($dateEnd)) {
            $this->nutaquery->setDate($dateEnd, $dateEnd);
        }
        if ($this->isNotEmpty($outlet)) {
            $this->nutaquery->SetOutlet($outlet);
        }
        if ($this->input->get_post('yesdelete') === 'delete') {
            $o = $this->input->get_post('outlet');
            $end = $this->input->get_post('date');
            $this->nutaquery->setDate($end, $end);
            $this->nutaquery->SetOutlet($o);

            $strQueryd = $this->nutaquery->get_query_delete_data();
            $dbmaster = $this->load->database('master', true);


            $queryd = $dbmaster->query($strQueryd);
        }
        $data['title'] = 'Hapus Data';
        $data['js_chart'] = array();
        $data['page_part'] = 'laporan/konfirmasi_hapus_data';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/filters/filter_date_mulai_sampai_js',
            'features/dialogs/dialog_hapus_data',
        );
        $strQuery = $this->nutaquery->get_query_konfirmasi_delete_data();
        $query = $this->db->query($strQuery);
        $result = $query->result();
        $this->load->model("outlet");
        $data['result'] = $result;
        $data = $this->setup_view_params($availableOutlets, $data);
        $data['OutletInfo'] = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function mutasikas()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = "1.1";
        }
        $realitemid = explode(".", $selecteditem)[0];
        $devno = explode(".", $selecteditem)[1];
        $checkpoint = "select * from (select distinct *, DATE_FORMAT(`InsertedDate`,'%Y%m%d') as 'month'
                                from checkpoint_uang
                                where DeviceID=" . $outlet . "
                                and AccountID=" . $realitemid . " and DeviceNo=" . $devno . "
                                ) p
                    where p.month = (select MAX(DATE_FORMAT(`InsertedDate`,'%Y%m%d')) as 'month' from checkpoint_uang
                    where DeviceID=" . $outlet . " and AccountID=" . $realitemid . " and DeviceNo=" . $devno .
            " AND InsertedDate<'" . $dateStart . "')";
        $query_checkpoint = $this->db->query($checkpoint);
        $result_checkpoint = $query_checkpoint->result();
        $result_checkpoint = json_decode(json_encode($result_checkpoint), true);
        //log_message('error',$checkpoint);
        if (sizeof($result_checkpoint) > 0) {
            $minDate = $result_checkpoint[0]['InsertedDate'];
        } else {
            $minDate = '2000-01-01';
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $availableItems = $this->GetAvailableKasRekeningByOutlet($this->nutaquery->getOutlet());
        $strQuery = $this->nutaquery->get_query_mutasi_kas($selecteditem, $minDate);
        //log_message('error',$strQuery);
        list($result, $fields) = $this->execute_query($strQuery);

        $data['items'] = $availableItems;
        $data['selected_item'] = $selecteditem;

        $data['page_part'] = 'laporan/laporan_mutasi_kas_rekening';
        $data['js_part'] = array(
            'features/js/js_form', 'features/js/js_datatable',
            'features/js/js_ajax_kasrekening_by_outlet',
            'features/filters/filter_date_mulai_sampai_js',
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Pembayaran ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function feedback()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = 1;
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);


        $strQuery = $this->nutaquery->get_query_feedback();
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryRekap = $this->nutaquery->get_query_rekap_feedback();
        $queryRekap = $this->db->query($strQueryRekap);
        $resultRekap = $queryRekap->result();
        $rekapFeedback = array();
        foreach ($resultRekap as $rekap) {
            $rekapFeedback[$rekap->Subject][$rekap->Response] = $rekap->Total;
        }


        $data['selected_item'] = $selecteditem;
        $data['page_part'] = 'laporan/laporan_feedback';
        $data['js_part'] = array(
            'features/js/js_form', 'features/js/js_datatable',
            'features/js/js_ajax_item_by_outlet',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['rekap'] = $rekapFeedback;
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Feedback Pelanggan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Penjualanvarian()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rekap_penjualan_varian();
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Varian');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerVarian($a[$key]);
            $processrenderMergeCellExport = $this->RenderMergeCellPerVarian($a[$key], TRUE);
            $b_export .= $processrenderMergeCellExport['content'];
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalFooterExport += $processrenderMergeCellExport['total'];
        }
        $grandTotalColspan = 4;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>" . $grandTotalFooterExport . "</td>";
        $b_export .= "<tr>";



        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_varian';
        $data['filter_webpart'] = 'features/filters/filter_form';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Penjualan per Varian ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Penjualanekstra()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $strQuery = $this->nutaquery->get_query_rekap_penjualan_modifier();
        list($result, $fields) = $this->execute_query($strQuery);




        $data['page_part'] = 'laporan/laporan_penjualan_pilihan_ekstra';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Penjualan Pilihan Ekstra ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Rincianlaba()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanLaba']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rincian_laba();
        list($result, $fields) = $this->execute_query($strQuery);




        $data['page_part'] = 'laporan/laporan_rincian_laba';
        $data['outlet'] = $outlet;
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rincian Laba ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Rincianlabapershift()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanLaba']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();



        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);
        $resultdata = array();
        if ($result)
            for ($i = 0; $i < count($result); $i++) {
                $r = $result[$i];
                $q2 = $this->nutaquery->get_query_rincian_laba_per_shift($r->OpenID, $r->DeviceNo);
                list($details, $fields) = $this->execute_query($q2);
                $r->Details = $details;
                $r->Fields = $fields;
            }

        $data['result'] = $result;

        $data['page_part'] = 'laporan/laporan_rincian_laba_per_shift';
        $data['outlet'] = $outlet;
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_item"]);
        $data['title'] = 'Nutacloud - Laporan Rincian Laba ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function rincianhpp()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $detailid = $this->input->get('detailid');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $devno = 1;

        $realdetailid = explode(".", $detailid)[0];
        $devno = explode(".", $detailid)[1];

        $strQuery = $this->nutaquery->get_query_rincian_hpp($outlet, $realdetailid, getPerusahaanNo(), $devno);
        list($result, $fields) = $this->execute_query($strQuery);

        $data['detailid'] = $detailid;
        $data['title'] = 'Rincian HPP';
        $data['page_part'] = 'laporan/laporan_rincian_hpp';
        $data['js_part'] = array(
            'features/js/js_form', 'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function GetAvailableItems()
    {
        $query = $this->db->get_where('masteritem', array('deviceid' => $this->DevIDAtauIDPerusahaan, 'Stock' => 'true'));
        $result = $query->result();
        //        $retval = array();
        //        foreach ($result as $row) {
        //            array_push($retval, $row->ItemName);
        //        }
        return $result;
    }

    protected function GetAvailableItemsByOutlet($outlet)
    {
        $query = $this->db->get_where('masteritem', array('deviceid' => strval($outlet), 'Stock' => 'true'));
        $result = $query->result();
        $items = array();
        foreach ($result as $row) {
            $items[$row->ItemID . "." . $row->DeviceNo] = $row->ItemName;
        }
        return $items;
    }

    protected function GetAvailableKasRekeningByOutlet($outlet)
    {
        $query = $this->db->get_where('mastercashbankaccount', array('deviceid' => strval($outlet)));
        $result = $query->result();
        $items = array();
        foreach ($result as $row) {
            $items[$row->AccountID . "." . $row->DeviceNo] = $row->AccountName;
        }
        return $items;
    }

    private function dump_table_sales()
    {
        $this->hiddenmsg('table sale: ' . $this->nutaquery->tabelsale);
        $this->hiddenmsg('table sale detail: ' . $this->nutaquery->tabelsaleitemdetail);
        $this->hiddenmsg('table sale detail ingredients: ' . $this->nutaquery->tabelsaleitemdetailingredients);
    }

    private function hiddenmsg($msg)
    {
        //        echo '<p style="display:none">' . $msg . '</p>';
    }

    function Pengeluaran()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pengeluaran();
        list($result, $fields) = $this->execute_query($strQuery);



        $data['page_part'] = 'laporan/laporan_pengeluaran';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Pengeluaran ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Pengeluarangrub()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pengeluaran_groupby();
        list($result, $fields) = $this->execute_query($strQuery);



        $data['page_part'] = 'laporan/laporan_pengeluaran_group_by_dipayar_ke';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Pengeluaran ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PendapatanSelainPenjualan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pendapatan_selain_penjualan();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pendapatan Selain Penjualan';

        $data['page_part'] = 'laporan/laporan_pendapatan_selain_pengeluaran';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerJam()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_jam();
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'features/filters/filter_form';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Laporan Rekap Penjualan per Jam' ;
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerJamPerItem()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_jam_per_item();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Nutacloud -Laporan Rekap Penjualan per Jam per Item';

        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_kasir';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerkasir()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_kasir();
        list($result, $fields) = $this->execute_query($strQuery);



        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_kasir';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);
        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);

        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan per Kasir' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RatarataBelanjaPerPelanggan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rata2_belanja_per_pelanggan();
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rata_pembelian_per_pelanggan';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rata-Rata Belanja per Pelanggan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerKategori()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanRekapPenjualanPerKategori']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_kategori();
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Kategori');

        $keys = array_keys($a);
        $b = '';
        $b_export ='';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerKategori($a[$key]);
            $processrenderMergeCellExport = $this->RenderMergeCellPerKategori($a[$key], TRUE);
            $b .= $processrenderMergeCell['content'];
            $b_export .= $processrenderMergeCellExport['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalFooterExport += $processrenderMergeCellExport['total'];
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";
        
        $b_export .= "<tr>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>" . $grandTotalFooterExport . "</td>";
        $b_export .= "<tr>";



        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_kategori';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan per Kategori ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerKategoriSemuaItem()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_kategori_semua_item();
        log_message('error', $strQuery);
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Kategori');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerKategori($a[$key]);
            $processrenderMergeCellExport = $this->RenderMergeCellPerKategori($a[$key], TRUE);

            $b .= $processrenderMergeCell['content'];
            $b_export .= $processrenderMergeCellExport['content'];

            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalFooterExport += $processrenderMergeCellExport['total'];
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>" . $grandTotalFooterExport . "</td>";
        $b_export .= "<tr>";



        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_kategori';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan per Kategori' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function labaperkategori()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');

        if (!$this->visibilityMenu['LaporanLabaPerKategori']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_laba_per_kategori(); // get_query_rekap_penjualan_per_kategori
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Kategori');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = $grandTotalRpFooter = $grandTotalHppFooter = $grandTotalLabaFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellLabaPerKategori($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalRpFooter += $processrenderMergeCell['totalRp'];
            $grandTotalHppFooter += $processrenderMergeCell['totalHpp'];
            $grandTotalLabaFooter += $processrenderMergeCell['totalLaba'];

            $processrenderMergeCellExport = $this->RenderMergeCellLabaPerKategori($a[$key], TRUE);
            $b_export .= $processrenderMergeCellExport['content'];
            $grandTotalFooterExport += $processrenderMergeCellExport['total'];
            $grandTotalMarkupFooterExport += $processrenderMergeCellExport['totalRp'];
            $grandTotalHppFooterExport += $processrenderMergeCellExport['totalHpp'];
            $grandTotalLabaFooterExport += $processrenderMergeCellExport['totalLaba'];
        }
        $grandTotalColspan = 2;

        $b .= "<tr>";
        $b .= "<td colspan='5'>Grand Total</td>";
        $b .= "<td align='right'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<td align='right'>&nbsp;</td>";
        $b .= "<td align='right'>" . $this->currencyformatter->format($grandTotalRpFooter) . "</td>";
        $b .= "<td align='right'>" . $this->currencyformatter->format($grandTotalHppFooter) . "</td>";
        $b .= "<td align='right'>" . $this->currencyformatter->format($grandTotalLabaFooter) . "</td>";
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='5'>Grand Total</td>";
        $b_export .= "<td align='right'>" . $grandTotalFooterExport . "</td>";
        $b_export .= "<td align='right'>&nbsp;</td>";
        $b_export .= "<td align='right'>" . $grandTotalMarkupFooterExport . "</td>";
        $b_export .= "<td align='right'>" . $grandTotalHppFooterExport . "</td>";
        $b_export .= "<td align='right'>" . $grandTotalLabaFooterExport . "</td>";
        $b_export .= "<tr>";


        $data['title'] = 'Nutacloud - Laporan Laba per Kategori';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_laba_per_kategori'; //grid_rekap_penjualan_per_kategori
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerTipePenjualan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($outlet);

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $data['markup_or_share'] = 'markup';
        if ($options->CreatedVersionCode >= 319 || $options->EditedVersionCode >= 319) {
            $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_tipe_with_share();
            $data['markup_or_share'] = 'share';
        } else {
            $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_opsimakan();
        }
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'TipePenjualan');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = 0;
        $grandTotalQtyFooter = 0;
        $grandTotalMarkupFooter = 0.0;
        $grandTotalShareFooter = 0.0;
        if ($options->CreatedVersionCode >= 319 || $options->EditedVersionCode >= 319) {
            foreach ($keys as $key) {
                $processrenderMergeCell = $this->RenderMergeCellPerTipeWithShare($a[$key]);
                $b .= $processrenderMergeCell['content'];
                $grandTotalFooter += $processrenderMergeCell['total'];
                $grandTotalQtyFooter += $processrenderMergeCell['totalQty'];
                $grandTotalShareFooter += $processrenderMergeCell['totalShare'];

                $processrenderMergeCellExport = $this->RenderMergeCellPerTipeWithShare($a[$key], TRUE);
                $b_export .= $processrenderMergeCellExport['content'];
                $grandTotalFooterExport += $processrenderMergeCellExport['total'];
                $grandTotalQtyFooterExport += $processrenderMergeCellExport['totalQty'];
                $grandTotalShareFooterExport += $processrenderMergeCellExport['totalShare'];
            }
        } else {
            foreach ($keys as $key) {
                $processrenderMergeCell = $this->RenderMergeCellPerOpsiMakan($a[$key]);
                $b .= $processrenderMergeCell['content'];
                $grandTotalFooter += $processrenderMergeCell['total'];
                $grandTotalQtyFooter += $processrenderMergeCell['totalQty'];
                $grandTotalMarkupFooter += $processrenderMergeCell['totalMarkup'];

                $processrenderMergeCellExport = $this->RenderMergeCellPerOpsiMakan($a[$key], TRUE);
                $b_export .= $processrenderMergeCellExport['content'];
                $grandTotalFooterExport += $processrenderMergeCellExport['total'];
                $grandTotalQtyFooterExport += $processrenderMergeCellExport['totalQty'];
                $grandTotalMarkupFooterExport += $processrenderMergeCellExport['totalMarkup'];
            }
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalQtyFooter) . "</td>";
        $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        if ($options->CreatedVersionCode >= 319 || $options->EditedVersionCode >= 319) {
            $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalShareFooter) . "</td>";
        } else {
            $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalMarkupFooter) . "</td>";
        }
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b_export .= "<td align=\"right\">" . $grandTotalQtyFooterExport . "</td>";
        $b_export .= "<td align=\"right\">" . $grandTotalFooter . "</td>";
        if ($options->CreatedVersionCode >= 319 || $options->EditedVersionCode >= 319) {
            $b_export .= "<td align=\"right\">" . $grandTotalShareFooterExport . "</td>";
        } else {
            $b_export .= "<td align=\"right\">" . $grandTotalMarkupFooterExport . "</td>";
        }
        $b_export .= "<tr>";



        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_opsimakan';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['tbody_export'] = $b_export;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan per Tipe Penjualan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PenjualanVoid()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);


        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan_void($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualanVoid($result, $modules, $is_ekstra);

        $htmlTable_hidden = $this->generateMergeCellLaporanPenjualanVoid($result, $modules, $is_ekstra, TRUE);

        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporan';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_penjualan';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data['tbody_hidden'] = $htmlTable_hidden;
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Penjualan Void ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PenjualanPerKategoriByCashBank()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $accounts = $this->input->get('accounts');
        $AccountIds = explode('.', $accounts);
        $accountid = $AccountIds[0];
        $accountdevno = $AccountIds[1];
        $this->load->model("Datarekeningmodel");
        $cashbank = $this->Datarekeningmodel->getByAccountID($accountid, $accountdevno, $outlet);
        $accountname = $cashbank->AccountName;
        if ($cashbank->AccountType != 1) {
            $accountname = $cashbank->BankName . ' ' . $cashbank->AccountNumber . ' ' . $cashbank->AccountName;
        }
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_penjualan_per_kategori_by_cashbank($accountid, $accountdevno);
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Kategori');

        $keys = array_keys($a);
        $b = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerKategori($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";


        $data['title'] = 'Laporan Penjualan per Kategori dari ' . $accountname;
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_non_tunai';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanTunai()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_tunai();
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'Kategori');

        $keys = array_keys($a);
        $b = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerKategori($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";


        $data['title'] = 'Laporan Rekap Penjualan Tunai per Kategori';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_non_tunai';
        $data['filter_webpart'] = 'features/filters/filter_form';


        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function RenderMergeCellPerKategori($groupBySaleNumber, $excel_export = FALSE)
    {
        $indexD = 0;
        $totalQty = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            $jumlah += $sale->TotalPerItem;
            $totalQty += $sale->Quantity;
        }
        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Quantity, $excel_export);
            $kategori = $sale->Kategori;
            $SubTotal = $this->idrToUsd($sale->TotalPerItem, $excel_export);

            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $kategori . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalQty, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    protected function RenderMergeCellLabaPerKategori($groupBySaleNumber, $excel_export = FALSE)
    {
        $indexD = 0;
        $totalQty = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $grandTotalRp = 0;
        $grandTotalHpp = 0;
        $grandTotalLaba = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            $jumlah += $sale->TotalPerItem;
            $totalQty += $sale->Quantity;
            $jumlahHPP += $sale->TotalHppPerItem;
            $jumlahLaba += ($sale->TotalPerItem - $sale->TotalHppPerItem);
        }
        $grandTotal += $jumlah;
        $grandTotalRp += $jumlah;
        $grandTotalHpp += $jumlahHPP;
        $grandTotalLaba += $jumlahLaba;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Quantity, $excel_export);
            $kategori = $sale->Kategori;
            $SubTotal = $this->idrToUsd($sale->TotalPerItem, $excel_export);
            $HppPerItem = $this->idrToUsd($sale->TotalHppPerItem, $excel_export);
            $TotalLabaKotorPerItem = $this->idrToUsd($sale->TotalPerItem - $sale->TotalHppPerItem, $excel_export);


            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $kategori . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $HppPerItem . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $TotalLabaKotorPerItem . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalQty, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlahHPP, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd(($jumlah - $jumlahHPP), $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $HppPerItem . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $TotalLabaKotorPerItem . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'totalRp' => $grandTotalRp, 'totalHpp' => $grandTotalHpp, 'totalLaba' => $grandTotalLaba, 'content' => $tablecontent);
    }

    protected function RenderMergeCellPerOpsiMakan($groupBySaleNumber, $excel_export = FALSE)
    {
        $indexD = 0;
        $totalQty = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $totalMarkup = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            $jumlah += $sale->TotalPerItem;
            $totalQty += $sale->Quantity;
            $totalMarkup += $sale->MarkupPerItem;
        }
        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Quantity, $excel_export);
            $opsimakan = $sale->TipePenjualan;
            $SubTotal = $this->idrToUsd($sale->TotalPerItem, $excel_export);
            $Markup = $this->idrToUsd($sale->MarkupPerItem, $excel_export);

            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $opsimakan . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $Markup . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalQty, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalMarkup, $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $Markup . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent, 'totalQty' => $totalQty, 'totalMarkup' => $totalMarkup);
    }

    protected function RenderMergeCellPerTipeWithShare($groupBySaleNumber, $excel_export = FALSE) {
        $indexD = 0;
        $totalQty = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $totalShare = 0.0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            $jumlah += $sale->TotalPerItem;
            $totalQty += $sale->Quantity;
            $totalShare += (double)$sale->ShareRevenuePerItem;
        }
        $grandTotal += $jumlah;
        foreach ($groupBySaleNumber as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Quantity, $excel_export);
            $opsimakan = $sale->TipePenjualan;
            $SubTotal = $this->idrToUsd($sale->TotalPerItem, $excel_export);
            $Share = $this->idrToUsd($sale->ShareRevenuePerItem, $excel_export);

            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $opsimakan . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $Share . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalQty, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($totalShare, $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $Share . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent, 'totalQty' => $totalQty, 'totalShare' => $totalShare);
    }

    function Diskon()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_laporan_diskon();
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'features/filters/filter_form';
        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Diskon ' . $getOutlet->NamaOutlet . " " . $getOutlet->alamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Pajak()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_laporan_pajak_penjualan();
        list($result, $fields) = $this->execute_query($strQuery);
        $a = $this->GroupResultByField($result, 'NamaPajak');

        $keys = array_keys($a);
        $b = '';
        $b_export = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerNamaPajak($a[$key]);
            $processrenderMergeCellExport = $this->RenderMergeCellPerNamaPajak($a[$key], TRUE);
            $b .= $processrenderMergeCell['content'];
            $b_export .= $processrenderMergeCellExport['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 4;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";

        $b_export .= "<tr>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b_export .= "<td colspan='" . $grandTotalColspan . "'>" . $grandTotalFooter . "</td>";
        $b_export .= "<tr>";

        $data['tbody'] = $b;
        $data['tbody_hidden'] = $b_export;


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_penjualan';
        $data['filter_webpart'] = 'features/filters/filter_form';
        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Pajak ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function menupenjualan()
    {
        $data['page_part'] = 'laporan/menu_penjualan';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menupembelian()
    {
        $data['page_part'] = 'laporan/menu_pembelian';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menukeuangan()
    {
        $data['page_part'] = 'laporan/menu_keuangan';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menustok()
    {
        $data['page_part'] = 'laporan/menu_stok';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menulaba()
    {
        $data['page_part'] = 'laporan/menu_laba';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function Pembulatan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_laporan_pembulatan();
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'features/filters/filter_form';
        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Pembulatan ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function RenderMergeCellPerVarian($groupByVarian, $excel_export = FALSE)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupByVarian as $sale) {
            $jumlah += $sale->TotalPerItem;
        }
        $grandTotal += $jumlah;
        foreach ($groupByVarian as $sale) {
            $itemname = $sale->Item;
            $Qty = $this->idrToUsd($sale->Qty, $excel_export);
            $varian = $sale->Varian;
            $SubTotal = $this->idrToUsd($sale->TotalPerItem, $excel_export);

            if ($indexD == 0) {
                $rowspan = count($groupByVarian);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $varian . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    protected function RenderMergeCellPerNamaPajak($groupByNamaPajak, $excel_export = FALSE)
    {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupByNamaPajak as $pajak) {
            $jumlah += $pajak->Pajak;
        }
        $grandTotal += $jumlah;
        foreach ($groupByNamaPajak as $pajak) {
            $pajakName = $pajak->NamaPajak;
            $nomer = $pajak->Nomor;
            $tanggal = $pajak->Tanggal;
            $pajak = $this->idrToUsd($pajak->Pajak, $excel_export);

            if ($indexD == 0) {
                $rowspan = count($groupByNamaPajak);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $pajakName . "</td>";
                $tablecontent .= "<td>" . $nomer . "</td>";
                $tablecontent .= "<td>" . $tanggal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $pajak . "</td>";
                $tablecontent .= "<td  rowspan=\"" . $rowspan . "align=\"right\">" . $this->idrToUsd($jumlah, $excel_export) . "</td>";
                $tablecontent .= "</tr>";
            } else {
                $tablecontent .= "<tr>";
                $tablecontent .= "<td>" . $nomer . "</td>";
                $tablecontent .= "<td>" . $tanggal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $pajak . "</td>";
                $tablecontent .= "</tr>";
            }


            $indexD++;
        }

        return array('total' => $grandTotal, 'content' => $tablecontent);
    }

    /**
     * @param $result
     * @param $modules
     * @param $is_ekstra
     * @return string
     */
    private function generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra, $excel_export)
    {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCell($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $excel_export);
            $htmlTable .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 7;
        if ($is_ekstra) {
            $grandTotalColspan += 1;
            if ($modules['IsUseVarianAndPrice'])
                $grandTotalColspan += 2;
        }
        $htmlTable .= "<tr>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->idrToUsd($grandTotalFooter, $excel_export) . "</td>";
        $htmlTable .= "<tr>";
        return $htmlTable;
    }

    /**
     * @param $result
     * @param $modules
     * @param $is_ekstra
     * @return string
     */
    private function generateMergeCellLaporanPenjualan2($result, $modules, $is_ekstra, $excel_export = FALSE)
    {
        $gropByNomor = $this->GroupResultByField2($result, 'Nomor', 'Tanggal');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCell($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $excel_export);
            $htmlTable .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 7;
        if ($is_ekstra) {
            $grandTotalColspan += 1;
            if ($modules['IsUseVarianAndPrice'])
                $grandTotalColspan += 2;
        }
        $htmlTable .= "<tr>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $htmlTable .= "<tr>";
        return $htmlTable;
    }

    private function generateMergeCellLaporanPesananBatal($result, $modules, $is_ekstra, $excel_export = FALSE)
    {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCellPesananBatal($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $excel_export);
            $htmlTable .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 7;
        if ($is_ekstra) {
            $grandTotalColspan += 1;
            if ($modules['IsUseVarianAndPrice'])
                $grandTotalColspan += 2;
        }
        $htmlTable .= "<tr>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $htmlTable .= "<td style='text-align:right' colspan='2'>" . $this->idrToUsd($grandTotalFooter, $excel_export) . "</td>";
        $htmlTable .= "<tr>";
        return $htmlTable;
    }

    private function generateMergeCellLaporanPenjualanPerShift($result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd)
    {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalShift = 0;
        $grandTotalFooter = 0;
        $openid = -1;
        $opendate = "";
        $opentime = "";
        $closedate = "";
        $closetime = "";
        $grandTotalColspan = 9;
        $strtable = "";
        if ($is_ekstra) {
            $grandTotalColspan += 1;
            if ($modules['IsUseVarianAndPrice'])
                $grandTotalColspan += 2;
        }
        $strShift = "";
        foreach ($nomorPenjualans as $nomorPenjualan) {
            foreach ($gropByNomor[$nomorPenjualan] as $sale) {
                if ($sale->OpenID != $openid) {
                    if ($openid != -1) {
                        $htmlTable .= "<tr>";
                        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total Shift "
                            . $strShift . "</td>";
                        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalShift) . "</td>";
                        $htmlTable .= '</tr>';
                        $htmlTable .= '</tbody>
</table>';
                        $htmlTable .= '
                        </div>
                    </div>
                </div>
                    ';
                    }
                    $grandTotalShift = 0;
                    $openid = $sale->OpenID;
                    $opendate = $sale->OpenDate;
                    $opentime = $sale->OpenTime;
                    $closedate = $sale->CloseDate;
                    $closetime = $sale->CloseTime;

                    if ($dateStart == $dateEnd) {
                        if (empty($closedate) || $closedate == "") {
                            $strShift = $opentime;
                        } else {
                            $strShift = $opentime . " - " . $closetime;
                        }
                    } else {
                        if (substr($dateStart, 0, 4) == substr($dateEnd, 0, 4)) {
                            if (empty($closedate) || $closedate == "") {
                                $strShift = str_replace(" " . substr($opendate, 0, 4), "", formatdateindonesia($opendate)) . ", " . $opentime;
                            } else if ($opendate == $closedate) {
                                $strShift = str_replace(" " . substr($opendate, 0, 4), "", formatdateindonesia($opendate)) . ", " . $opentime . " - " . $closetime;
                            } else {
                                $strShift = str_replace(" " . substr($opendate, 0, 4), "", formatdateindonesia($opendate)) . ", " . $opentime . " - " .
                                    str_replace(" " . substr($closedate, 0, 4), "", formatdateindonesia($closedate)) . ", " . $closetime;
                            }
                        } else {
                            if (empty($closedate) || $closedate == "") {
                                $strShift = formatdateindonesia($opendate) . " " . $opentime;
                            } else if ($opendate == $closedate) {
                                $strShift = formatdateindonesia($opendate) . " " . $opentime . " - " . $closetime;
                            } else {
                                $strShift = formatdateindonesia($opendate) . " " . $opentime . " - " .
                                    formatdateindonesia($closedate) . " " . $closetime;
                            }
                        }
                    }
                    if ($sale->DeviceNo != 1) {
                        $strShift .= " Perangkat ke-" . $sale->DeviceNo;
                    }
                    $htmlTable .= '
                                        <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <form method="post" action="' . base_url() . 'test" class="margin-bottom10">
                                <input type="hidden" name="table"/>
                                <button class="btn btn-default" type="submit">Export Excel</button>
                            </form>
                        </div>
                    </div>';
                    $htmlTable .= '
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-table"></i></span>
                    <h4>Shift ' . $strShift . '</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span>
                        </li>
                    </ul>
                </div>

                <div class="widget-container">
                    <div class=" widget-block">

<div class="table-responsive">
                ';

                    $str = '<table class="table table-bordered table-striped ">
<thead>
    <tr>';
                    foreach ($fields as $field) {
                        if (
                            $field->name === 'OpenID' || $field->name === 'OpenDate' || $field->name === 'OpenTime' || $field->name === 'CloseDate' || $field->name === 'CloseTime' || $field->name === 'Jam' || $field->name === 'DeviceNo'
                        ) {
                            continue;
                        }
                        $str .= '<th>';
                        if ($field->name === 'Diskon') {
                            $str .= "Jumlah";
                        } else if ($field->name === 'SubTotal') {
                            $str .= "Total";
                        } else {
                            $str .= CamelToWords($field->name);
                        }
                        $str .= '</th>';
                    }
                    $str .= '</tr>
</thead>
<tbody>';
                    $htmlTable .= $str;
                }
                break;
            }
            $processrenderMergeCell = $this->RenderMergeCellPerShift($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
            $htmlTable .= $processrenderMergeCell['content'];
            $grandTotalShift += $processrenderMergeCell['total'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        if ($openid != -1) {
            $htmlTable .= "<tr>";
            $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total Shift "
                . $strShift . "</td>";
            $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalShift) . "</td>";
            $htmlTable .= '</tr>';
            $htmlTable .= "<tr>";
            $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total Penjualan "
                . "</td>";
            $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
            $htmlTable .= '</tr>';
            $htmlTable .= '</tbody>
</table>';
            $htmlTable .= '
                        </div>
                    </div>
                </div>
                    ';
        }
        //        $htmlTable .= "<tr>";
        //        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        //        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        //        $htmlTable .= "</tr>";
        return $htmlTable;
    }

    private function generateMergeCellLaporanPenjualanVoid($result, $modules, $is_ekstra, $excel_export = FALSE)
    {
        $gropByNomor = $this->GroupResultByField2($result, 'Nomor', 'Tanggal');
        //log_message('error', var_export($gropByNomor,true));
        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCellVoid($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $excel_export);
            $htmlTable .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 7;
        if ($is_ekstra) {
            $grandTotalColspan += 1;
            if ($modules['IsUseVarianAndPrice'])
                $grandTotalColspan += 2;
        }
        $htmlTable .= "<tr>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $htmlTable .= "<td colspan='" . $grandTotalColspan . "'>" . $this->idrToUsd($grandTotalFooter, $excel_export) . "</td>";
        $htmlTable .= "<tr>";
        return $htmlTable;
    }

    /**
     * @return array
     */
    private function setup_outlets()
    {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        return $availableOutlets;
    }

    /**
     * @return mixed
     */
    private function get_module()
    {
        $this->load->model('Perusahaanmodel');
        $modules = $this->Perusahaanmodel->getModulPerusahaan(getLoggedInUserID());
        return $modules;
    }

    /**
     * @param $strQuery
     * @return array
     */
    private function execute_query($strQuery)
    {
        $query = $this->db->query($strQuery);
        $result = $query->result();
        $fields = $query->field_data();
        return array($result, $fields);
    }

    function Penjualanpershift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);


        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualanpershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualanPerShift(
            $result,
            $fields,
            $modules,
            $is_ekstra,
            $dateStart,
            $dateEnd
        );


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_penjualanpershift';
        $data['title'] = 'Laporan Penjualan';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapShift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rekapshift();
        //log_message('error',$strQuery);
        list($result, $fields) = $this->execute_query($strQuery);


        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['grid_webpart'] = 'features/grids/grid_rekap_shift';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';

        $data['selected_rekapper'] = $rekapper;
        $data['title'] = 'Laporan Rekap Shift';
        $data['dateStart'] = $dateStart;
        $data['dateEnd'] = $dateEnd;

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerShift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_rekap_penjualanpershift($modules['IsUseTaxModule'], $rekapper);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan';
        $data['filter_webpart'] = 'features/filters/filter_form_rekap_penjualan';

        $data['selected_rekapper'] = $rekapper;


        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array(
            'fields' => $fields, 'result' => $result,
            'fields_charge' => $fieldsCharge, 'result_charge' => $resultCharge
        );

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan Berdasarkan Tanggal Shift / Tanggal Buka Outlet ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PesananBelumLunas()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);

        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan_belum_lunas($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualan2($result, $modules, $is_ekstra);

        $htmlTable_hidden = $this->generateMergeCellLaporanPenjualan2($result, $modules, $is_ekstra, TRUE);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporan';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan_belum_lunas';
        $data['grid_webpart'] = 'features/grids/grid_penjualan';


        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data['tbody_hidden'] = $htmlTable_hidden;
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Pesanan Belum Lunas ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet;

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function opsimakanpershift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);

        $this->nutaquery->SetOutlet($outlet);

        $modules = $this->get_module();
        
        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($outlet);

        for ($i = 0; $i < count($result); $i++) {
            $r = $result[$i];
            //$this->nutaquery->setDate($r->OpenDate, $r->CloseDate);

            $data['markup_or_share'] = 'markup';
            if ($options->CreatedVersionCode >= 319 || $options->EditedVersionCode >= 319) {
                $q2 = $this->nutaquery->get_query_tipe_with_share_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
                
                $data['markup_or_share'] = 'share';
            } else {
                $q2 = $this->nutaquery->get_query_opsimakan_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
            }

            
            list($details, $f2) = $this->execute_query($q2);
            $r->Details = $details;
        }


        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet); // reset

        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_opsimakanpershift';
        $data['title'] = 'Laporan Tipe Penjualan per Shift';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        //$data['tbody'] = $htmlTable;

        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;

        $data = $this->setup_view_params($availableOutlets, $data);


        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function outletpershift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        if (count($availableOutlets) >= 2){
            $outlet = '';
        } else {
            $outlet = array_keys($availableOutlets);
        }
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $a = NULL);
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan_per_outlet_per_shift($outlet);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet); // reset

        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['filter_webpart'] = 'features/filters/filter_form_per_outlet_per_hari';
        $data['grid_webpart'] = 'features/grids/grid_outlet_per_shift';
        $data['title'] = 'Laporan Penjualan per Outlet per Shift ';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;

        $data = $this->setup_view_params($availableOutlets, $data);


        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function penjualankategoripershift()
    {

        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }
        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);

        $this->nutaquery->SetOutlet($outlet);

        $modules = $this->get_module();
        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);

        for ($i = 0; $i < count($result); $i++) {
            $r = $result[$i];
            $q2 = $this->nutaquery->get_query_kategori_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
            list($details, $f2) = $this->execute_query($q2);
            $r->Details = $details;
        }

        /* $htmlTable = $this->generateMergeCellLaporanOpsiMakanPerShift(
          $result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd); */


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['filter_webpart'] = 'features/filters/filter_penjualan_kategori_shift';
        $data['grid_webpart'] = 'features/grids/grid_kategoripershift';
        $data['title'] = 'Laporan Kategori per Shift';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        //$data['tbody'] = $htmlTable;

        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;

        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    /* laporan penjualan variant per shift
     * date create : 16/07/2018
     * create by : fach
     */

    function penjualanvarianpershift()
    {

        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }
        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);

        $this->nutaquery->SetOutlet($outlet);

        $modules = $this->get_module();
        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);

        for ($i = 0; $i < count($result); $i++) {
            $r = $result[$i];
            $q2 = $this->nutaquery->get_query_variant_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
            list($details, $f2) = $this->execute_query($q2);
            $r->Details = $details;
        }

        /* $htmlTable = $this->generateMergeCellLaporanOpsiMakanPerShift(
          $result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd); */


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporanpershift';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_variantpershift';
        $data['title'] = 'Laporan Variant per Shift';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        //$data['tbody'] = $htmlTable;

        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;

        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    /* laporan penjualan pilihan ekstra per shift
     * date create : 16/07/2018
     * create by : fach
     */

    function penjualanpilihanekstrapershift()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualanPilihanekstraperShift']) {
            show_404();
        }
        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $this->nutaquery->SetOutlet($outlet);
        $modules = $this->get_module();

        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);

        $resultdata = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = $result[$i];
            $q2 = $this->nutaquery->get_query_pilihanekstra_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
            list($details, $f2) = $this->execute_query($q2);
            $r->Details = $details;

            $resultdata[$i] = $result[$i];
            $resultdata[$i]->Details = $details;
        }
        $data['selected_isekstra'] = $is_ekstra;
        $data['data'] = $resultdata;
        $data['page_part'] = 'laporan/laporan_pilihan_ekstra_per_shift';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['title'] = 'Laporan Pilihan Ekstra per Shift';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        //$data['tbody'] = $htmlTable;

        $data['result'] = $result;
        $data['fields'] = $fields;
        $data['outlet'] = $outlet;

        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }
    
    public function penjualanperoutletperhari()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanPenjualanPerOutletPerHari']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        if (count($availableOutlets) >= 2){
            $outlet = '';
        } else {
            $outlet = array_keys($availableOutlets);
        }
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $a = NULL);
        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan_per_outlet_per_hari($outlet);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_penjualan_per_outlet_per_hari';
        $data['filter_webpart'] = 'features/filters/filter_outlet_pershift';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array(
            'fields' => $fields, 'result' => $result,
            'fields_charge' => $fieldsCharge, 'result_charge' => $resultCharge
        );

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $data['title'] = 'Nutacloud - Laporan Penjualan Per Outlet Per Hari '. $data["date_start"] . " - " . $data["date_end"];

        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function riwayatpelanggan()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();

        $t = $this->input->get('usedate');
        if (!isset($t)) {
            $dateStart = "1900-01-01";
            $dateEnd = "2099-12-31";
        }

        if ((int) $this->input->get('usedate') == 0) {
            $dateStart = "1900-01-01";
            $dateEnd = "2099-12-31";
        }

        $outlet = $this->get_outlet_param();

        $customers = $this->GetCustomers($outlet);
        $customer = $this->get_customer_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet, $customer);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);


        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_penjualan_by_customer($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra);

        $htmlTable_hidden = $this->generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra, TRUE);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporan';
        $data['filter_webpart'] = 'features/filters/filter_form_riwayat_pelanggan';
        $data['grid_webpart'] = 'features/grids/grid_penjualan';
        $data['title'] = 'Laporan Riwayat Pelanggan';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data['tbody_hidden'] = $htmlTable_hidden;

        $data = $this->setupViewListCustomer($customers, $data);
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function pesananbatal()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $customers = $this->GetCustomers($outlet);
        $customer = $this->get_customer_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet, $customer);

        $is_ekstra_param = $this->input->get('isekstra');
        $is_ekstra = $this->isNotEmpty($is_ekstra_param);


        $modules = $this->get_module();
        $strQuery = $this->nutaquery->get_query_pesanan_batal($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPesananBatal($result, $modules, $is_ekstra);
        $htmlTableExport = $this->generateMergeCellLaporanPesananBatal($result, $modules, $is_ekstra, TRUE);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'laporan/widget_laporan';
        $data['filter_webpart'] = 'features/filters/filter_form_penjualan';
        $data['grid_webpart'] = 'features/grids/grid_pesanan_batal';
        $data['title'] = 'Laporan Pesanan batal';

        $data['js_part'] = array('features/js/js_form', 'features/js/js_datatable', 'features/filters/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data['tbody_export'] = $htmlTableExport;

        // print_r($htmlTableExport);
        // exit();

        $data = $this->setupViewListCustomer($customers, $data);
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function menuakuntansi()
    {
        $data['page_part'] = 'laporan/menu_akuntansi';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function bukubesar()
    {
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $selected_outlet = $this->get_outlet_param();

        if (DateTime::createFromFormat('Y-m-d', $dateStart) === false) $dateStart = date('Y-m-d');
        if (DateTime::createFromFormat('Y-m-d', $dateEnd) === false) $dateEnd = date('Y-m-d');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $selected_outlet);

        $this->load->model('journalmodel');
        $this->load->model('masteraccount');
        $journal = $this->journalmodel->generateGeneralLedger(getPerusahaanNo(), trim(strtolower($selected_outlet)), $dateStart, $dateEnd);
        $account = $this->masteraccount->AccountBalance(getPerusahaanNo(), trim(strtolower($selected_outlet)), 'balance', $dateStart);
        $values = array();

        foreach ($account as $key => $val) {
            if ($val->IsDefault == true) continue;
            $code = $val->AccountCode;

            $new_array = array_filter($journal, function ($obj) use ($code) {
                if ($code == $obj->AccountCode) {
                    return true;
                }
            });

            $values[$key]->AccountCode = $val->AccountCode;
            $values[$key]->AccountName = $val->AccountName;
            $values[$key]->AccountType = $val->AccountType;
            $values[$key]->SaldoAwal = isNotEmpty($val->Saldo) ? $val->Saldo : 0;
            $values[$key]->Journal = $new_array;
        }

        $data['journal'] = $values;
        $data['date_start'] =  $dateStart;
        $data['date_end'] = $dateEnd;
        $data['title'] = 'Laporan Buku Besar';
        $data['page_part'] = 'laporan/laporan_bukubesar';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function rekapbukubesar()
    {
        $this->benchmark->mark('code_start');

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $selected_outlet = $this->get_outlet_param();

        if (DateTime::createFromFormat('Y-m-d', $dateStart) === false) $dateStart = date('Y-m-d');
        if (DateTime::createFromFormat('Y-m-d', $dateEnd) === false) $dateEnd = date('Y-m-d');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $selected_outlet);

        $this->load->model('journalmodel');
        $this->load->model('masteraccount');

        $account_type = $this->masteraccount->AccountType(getPerusahaanNo());
        $account_default = $this->masteraccount->getAllAccount(getPerusahaanNo(), true, array('IsDefault' => true));
        $account_balance = $this->masteraccount->AccountBalance(getPerusahaanNo(), trim(strtolower($selected_outlet)), 'balance', $dateStart);
        $journal = $this->journalmodel->generateLedger(getPerusahaanNo(), trim(strtolower($selected_outlet)), $dateStart, $dateEnd);

        $rebuild_balance =  array();
        $values = array();

        foreach ($journal as $key => $val) {
            if ($val->IsDefault == '1') continue;
            $rebuild_balance[$key] = $val;
            $key_bal = array_search($val->JournalAccountID, array_column($account_balance, 'JournalAccountID'));
            $rebuild_balance[$key]->Balance = isNotEmpty($account_balance[$key_bal]->Saldo) ? (float) $account_balance[$key_bal]->Saldo : 0;
        }

        foreach ($account_type as $key => $val) {
            $values[$key]->TypeCode = $key;
            $values[$key]->TypeName = $val;

            $new_array =  array_filter($account_default, function ($def) use ($key, $rebuild_balance) {
                if ($def->AccountType == $key) {
                    $new_balance = array_filter($rebuild_balance, function ($obj) use ($def) {
                        if ($def->AccountType == $obj->AccountType && substr($obj->AccountCode, 0, strlen($def->AccountCode)) == $def->AccountCode) {
                            $obj->Debit = empty($obj->Debit) ? 0 : $obj->Debit;
                            $obj->Credit = empty($obj->Credit) ? 0 : $obj->Credit;
                            return true;
                        }
                    });
                    $def->Account = $new_balance;
                    return true;
                }
            });

            $values[$key]->Account = $new_array;
        }

        $data['journal'] = $values;
        $data['date_start'] =  $dateStart;
        $data['date_end'] = $dateEnd;
        $data['title'] = 'Laporan Rekap Buku Besar';
        $data['page_part'] = 'laporan/laporan_bukubesarrekap';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);

        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function neraca()
    {
        $this->benchmark->mark('code_start');

        $availableOutlets = $this->setup_outlets();
        list($dateStart, $dateEnd) = $this->get_periode_param();
        $selected_outlet = $this->get_outlet_param();

        if (DateTime::createFromFormat('Y-m-d', $dateStart) === false) $dateStart = date('Y-m-d');
        if (DateTime::createFromFormat('Y-m-d', $dateEnd) === false) $dateEnd = date('Y-m-d');

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $selected_outlet);

        $this->load->model('masteraccount');
        $this->load->model('journalmodel');

        $account_default = $this->masteraccount->getAllAccount(getPerusahaanNo(), true, array('IsDefault' => true, 'AccountType <' => 5));
        $account_balance = $this->masteraccount->AccountBalance(getPerusahaanNo(), trim(strtolower($selected_outlet)), 'real', $dateStart);
        $account_type = $this->masteraccount->accountType(getPerusahaanNo());
        $journal = $this->journalmodel->generateLedger(getPerusahaanNo(), trim(strtolower($selected_outlet)), $dateStart, $dateEnd);
        $values = array();

        foreach ($account_type as $key => $val) {
            if ($key > 4) continue;

            $values[$key]->TypeCode = $key;
            $values[$key]->TypeName = $val;

            $new_account = array_filter($account_default, function ($obj) use ($key, $account_balance) {
                if ($obj->AccountType == $key) {
                    $new_balance = array_filter($account_balance, function ($bal) use ($obj) {
                        $code = substr($bal->AccountCode, 0, strlen($obj->AccountCode));
                        if ($bal->IsDefault == '0' && $bal->AccountType == $obj->AccountType && $code == $obj->AccountCode) {
                            $bal->Code = $code;
                            $bal->Code2 = $obj->AccountCode;
                            return true;
                        }
                    });
                    $obj->Balance = $new_balance;
                    return true;
                }
            });

            $values[$key]->Account = $new_account;
        }

        $data['account'] = $values;
        $data['date_start'] =  $dateStart;
        $data['date_end'] = $dateEnd;
        $data['title'] = 'Laporan Neraca';
        $data['page_part'] = 'laporan/laporan_neraca';
        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);

        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function idrToUsd($number, $excel_export){
        if ($excel_export) {
            return $number;
        } else {
            return $this->currencyformatter->format($number, 'IDR');
        }
    }

    function RekapPenjualanPerHari()
    {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $this->load->model("outlet");

        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $rekapper = $this->input->get('rekapper');
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        if ($this->isNotEmpty($rekapper) == FALSE) {
            $rekapper = 'item';
        }

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_hari();
        list($result, $fields) = $this->execute_query($strQuery);


        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'laporan/widget_laporan';
        $data['grid_webpart'] = 'features/grids/grid_rekap_penjualan_per_hari';
        $data['filter_webpart'] = 'features/filters/filter_form';

        $data['js_part'] = array(
            'features/js/js_form',
            'features/js/js_datatable',
            'features/filters/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $getOutlet = $this->outlet->getByName(getPerusahaanNo(), $data["selected_outlet"]);
        $data['title'] = 'Nutacloud - Laporan Rekap Penjualan per Hari ' . $getOutlet->NamaOutlet . " " . $getOutlet->AlamatOutlet . " " . $data["date_start"] . " - " . $data["date_end"];
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }
}
