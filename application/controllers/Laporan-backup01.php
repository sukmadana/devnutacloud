<?php

class Laporan extends Laporan_Controller {

    var $DevIDAtauIDPerusahaan = '';

    function __construct() {
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

    function Penjualan() {
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
        $strQuery = $this->nutaquery->get_query_penjualan($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualan';
        $data['title'] = 'Laporan Penjualan';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualan() {
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
        $strQuery = $this->nutaquery->get_query_rekap_penjualan($modules['IsUseTaxModule'], $rekapper);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_rekap_penjualan';

        $data['selected_rekapper'] = $rekapper;
        $data['title'] = 'Laporan Rekap Penjualan';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result,
            'fields_charge' => $fieldsCharge, 'result_charge' => $resultCharge);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function SaldoKasRekening() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        $dateStart = $this->input->get('date_start');
        $dateEnd = $dateStart;
        $outlet = $this->get_outlet_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_saldo_uang();
        list($result, $fields) = $this->execute_query($strQuery);
        $data['title'] = 'Laporan Saldo Kas / Rekening';
        $data['page_part'] = 'webparts/laporan_saldo_kas_rekening';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Laba() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');

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
        );

        $data['title'] = 'Laporan Laba';
        $data['page_part'] = 'webparts/laporan_laba';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Labapershift() {
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
        $data['page_part'] = 'webparts/laporan_labapershift';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPembayaran() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_rekap_pembayaran();

        list($result, $fields) = $this->execute_query($strQuery);

        $links = array(
            'Penjualan Tunai' => 'laporan/rekappenjualantunai',
            'Penjualan Non Tunai' => 'laporan/rekappenjualannontunai',
        );

        $data['title'] = 'Laporan Rekap Pembayaran';


        $data['page_part'] = 'webparts/laporan_rekap_pembayaran';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result, 'links' => $links);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function isNotEmpty($value) {
        return isset($value) && trim($value) != "";
    }

    protected function GroupResultByField($result, $field) {

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

    protected function RenderMergeCell($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice) {
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
            $Qty = $this->currencyformatter->format($sale->Qty);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = ($sale->Qty * $sale->HargaSatuan);

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

    protected function RenderMergeCellPerShift($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice) {
        $indexD = 0;
        $jumlah = 0;
        $grandTotal = 0;
        $tablecontent = '';
        foreach ($groupBySaleNumber as $sale) {
            //if ($sale->UrutanTampil != 2) {
            if (substr($sale->Item, 0, 12) !== 'Diskon Final') {
                $jumlah += $sale->SubTotal + $sale->Markup;
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

    protected function RenderMergeCellVoid($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice) {
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
            $Qty = $this->currencyformatter->format($sale->Qty);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = $sale->Qty * $sale->HargaSatuan;

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

    protected function RenderMergeCellPesananBatal($groupBySaleNumber, $IsDiningTable, $is_ekstra, $isVarianAndPrice) {
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
            $Qty = $this->currencyformatter->format($sale->Qty);
            $UnitPrice = $sale->HargaSatuan;
            $SubTotal = ($sale->Qty * -1 * $sale->HargaSatuan);

            if (substr($itemname, 0, 12) === 'Diskon Final') {
                //if ($sale->UrutanTampil == 2) {
                $Qty = "";
                $UnitPrice = "";
                $SubTotal = $sale->SubTotal - $sale->HargaSatuan;
                $SubTotal = $this->currencyformatter->format($SubTotal);
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

    function RekapPembelian() {
        $this->benchmark->mark('code_start');
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

        $data['title'] = 'Laporan Rekap Pembelian';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/laporan_rekap_pembelian';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Pembelian() {
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();

        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pembelian();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pembelian';
        $data['page_part'] = 'webparts/laporan_pembelian';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $a = $this->GroupResultByField($result, 'Nomor');

        $keys = array_keys($a);
        $b = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCell($a[$key], false, false, false);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $b .= "<tr>";
        $b .= "<td colspan='8'>Grand Total</td>";
        $b .= "<td colspan='5'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";


        $data['tbody'] = $b;
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function stok() {
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        $dateStart = $this->input->get('date_start');
        $dateEnd = $dateStart;
        $outlet = $this->get_outlet_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_stok();

        list($result, $fields) = $this->execute_query($strQuery);
        $data['title'] = 'Laporan Stok';
        $data['page_part'] = 'webparts/laporan_stok';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function samakanstokkeapp() {
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

    public function kartustok() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = "1.1";
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $availableItems = $this->GetAvailableItemsByOutlet($this->nutaquery->getOutlet());
        $strQuery = $this->nutaquery->get_query_kartu_stok($selecteditem);
        list($result, $fields) = $this->execute_query($strQuery);

        $data['items'] = $availableItems;
        $data['selected_item'] = $selecteditem;
        $data['title'] = 'Laporan Rekap Pembayaran';
        $data['page_part'] = 'webparts/laporan_kartu_stok';
        $data['js_part'] = array(
            'webparts/parts/js_form', 'webparts/parts/js_datatable',
            'webparts/parts/js_ajax_item_by_outlet',
            'webparts/parts/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function rekapmutasistok() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = 1;
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_rekap_mutasi_stok();

        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Mutasi Stok';
        $data['selected_item'] = $selecteditem;

        $data['page_part'] = 'webparts/laporan_mutasi_stok';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function hapusdata() {
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
            // $this->output->set_header('Pragma: no-cache');
            // $this->output->set_header('Cache-Control: no-cache, must-revalidate');
            // $this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            // redirect(base_url() . 'cloud/main');
        }
        $data['title'] = 'Hapus Data';
        $data['js_chart'] = array();
        $data['page_part'] = 'webparts/konfirmasi_hapus_data';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/filter_date_mulai_sampai_js');
        $strQuery = $this->nutaquery->get_query_konfirmasi_delete_data();
        $query = $this->db->query($strQuery);
        $result = $query->result();
        $data['result'] = $result;
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function mutasikas() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $selecteditem = $this->input->get('item');
        if (!isset($selecteditem)) {
            $selecteditem = "1.1";
        }

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $availableItems = $this->GetAvailableKasRekeningByOutlet($this->nutaquery->getOutlet());
        $strQuery = $this->nutaquery->get_query_mutasi_kas($selecteditem);
        list($result, $fields) = $this->execute_query($strQuery);

        $data['items'] = $availableItems;
        $data['selected_item'] = $selecteditem;
        $data['title'] = 'Laporan Rekap Pembayaran';
        $data['page_part'] = 'webparts/laporan_mutasi_kas_rekening';
        $data['js_part'] = array(
            'webparts/parts/js_form', 'webparts/parts/js_datatable',
            'webparts/parts/js_ajax_kasrekening_by_outlet', 'webparts/parts/filter_date_mulai_sampai_js',
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function feedback() {
        $this->benchmark->mark('code_start');
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

        $data['title'] = 'Laporan Feedback Pelanggan';
        $data['selected_item'] = $selecteditem;
        $data['page_part'] = 'webparts/laporan_feedback';
        $data['js_part'] = array(
            'webparts/parts/js_form', 'webparts/parts/js_datatable',
            'webparts/parts/js_ajax_item_by_outlet',
            'webparts/parts/filter_date_mulai_sampai_js'
        );
        $data['rekap'] = $rekapFeedback;
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Penjualanvarian() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
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
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerVarian($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 4;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";


        $data['title'] = 'Laporan Penjualan per Varian';
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_varian';
        $data['filter_webpart'] = 'webparts/parts/filter_form';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $b;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Penjualanekstra() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $strQuery = $this->nutaquery->get_query_rekap_penjualan_modifier();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Penjualan Pilihan Ekstra';


        $data['page_part'] = 'webparts/laporan_penjualan_pilihan_ekstra';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Rincianlaba() {
        $this->benchmark->mark('code_start');
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

        $data['title'] = 'Laporan Rincian Laba';


        $data['page_part'] = 'webparts/laporan_rincian_laba';
        $data['outlet'] = $outlet;
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Rincianlabapershift() {
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanLaba']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);
        $modules = $this->get_module();

        $data['title'] = 'Laporan Rincian Laba';

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

        $data['page_part'] = 'webparts/laporan_rincian_laba_per_shift';
        $data['outlet'] = $outlet;
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function rincianhpp() {
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
        $data['page_part'] = 'webparts/laporan_rincian_hpp';
        $data['js_part'] = array(
            'webparts/parts/js_form', 'webparts/parts/js_datatable',
            'webparts/parts/filter_date_mulai_sampai_js'
        );
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function GetAvailableItems() {
        $query = $this->db->get_where('masteritem', array('deviceid' => $this->DevIDAtauIDPerusahaan, 'Stock' => 'true'));
        $result = $query->result();
//        $retval = array();
//        foreach ($result as $row) {
        //            array_push($retval, $row->ItemName);
        //        }
        return $result;
    }

    protected function GetAvailableItemsByOutlet($outlet) {
        $query = $this->db->get_where('masteritem', array('deviceid' => strval($outlet), 'Stock' => 'true'));
        $result = $query->result();
        $items = array();
        foreach ($result as $row) {
            $items[$row->ItemID . "." . $row->DeviceNo] = $row->ItemName;
        }
        return $items;
    }

    protected function GetAvailableKasRekeningByOutlet($outlet) {
        $query = $this->db->get_where('mastercashbankaccount', array('deviceid' => strval($outlet)));
        $result = $query->result();
        $items = array();
        foreach ($result as $row) {
            $items[$row->AccountID . "." . $row->DeviceNo] = $row->AccountName;
        }
        return $items;
    }

    private function dump_table_sales() {
        $this->hiddenmsg('table sale: ' . $this->nutaquery->tabelsale);
        $this->hiddenmsg('table sale detail: ' . $this->nutaquery->tabelsaleitemdetail);
        $this->hiddenmsg('table sale detail ingredients: ' . $this->nutaquery->tabelsaleitemdetailingredients);
    }

    private function hiddenmsg($msg) {
//        echo '<p style="display:none">' . $msg . '</p>';
    }

    function Pengeluaran() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pengeluaran();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pengeluaran';

        $data['page_part'] = 'webparts/laporan_pengeluaran';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PendapatanSelainPenjualan() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();
        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_pendapatan_selain_penjualan();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pendapatan Selain Penjualan';

        $data['page_part'] = 'webparts/laporan_pendapatan_selain_pengeluaran';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerJam() {
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

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_jam();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Rekap Penjualan per Jam';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'webparts/parts/filter_form';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerJamPerItem() {
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

        $data['title'] = 'Laporan Rekap Penjualan per Jam per Item';

        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_kasir';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerkasir() {
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

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_kasir();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Rekap Penjualan per Kasir';

        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_kasir';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RatarataBelanjaPerPelanggan() {
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

        $strQuery = $this->nutaquery->get_query_rata2_belanja_per_pelanggan();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Rata-Rata Belanja per Pelanggan';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rata_pembelian_per_pelanggan';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();

        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerKategori() {
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

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_kategori();
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


        $data['title'] = 'Laporan Rekap Penjualan per Kategori';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_kategori';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function labaperkategori() {
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
        $grandTotalFooter = $grandTotalRpFooter = $grandTotalHppFooter = $grandTotalLabaFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellLabaPerKategori($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalRpFooter += $processrenderMergeCell['totalRp'];
            $grandTotalHppFooter += $processrenderMergeCell['totalHpp'];
            $grandTotalLabaFooter += $processrenderMergeCell['totalLaba'];
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


        $data['title'] = 'Laporan Laba per Kategori';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_laba_per_kategori'; //grid_rekap_penjualan_per_kategori
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerTipePenjualan() {
        $this->dump_table_sales();
        $this->benchmark->mark('code_start');
        if (!$this->visibilityMenu['LaporanRekapPenjualan']) {
            show_404();
        }

        $availableOutlets = $this->setup_outlets();

        list($dateStart, $dateEnd) = $this->get_periode_param();
        $outlet = $this->get_outlet_param();

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet);

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_per_opsimakan();
        list($result, $fields) = $this->execute_query($strQuery);

        $a = $this->GroupResultByField($result, 'TipePenjualan');

        $keys = array_keys($a);
        $b = '';
        $grandTotalFooter = 0;
        $grandTotalQtyFooter = 0;
        $grandTotalMarkupFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerOpsiMakan($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
            $grandTotalQtyFooter += $processrenderMergeCell['totalQty'];
            $grandTotalMarkupFooter += $processrenderMergeCell['totalMarkup'];
        }
        $grandTotalColspan = 5;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalQtyFooter) . "</td>";
        $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<td align=\"right\">" . $this->currencyformatter->format($grandTotalMarkupFooter) . "</td>";
        $b .= "<tr>";


        $data['title'] = 'Laporan Rekap Penjualan per Tipe Penjualan';
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_opsimakan';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PenjualanVoid() {
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
        $strQuery = $this->nutaquery->get_query_penjualan_void($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualanVoid($result, $modules, $is_ekstra);

        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualan';
        $data['title'] = 'Laporan Penjualan Void';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanNonTunai() {
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

        $strQuery = $this->nutaquery->get_query_rekap_penjualan_non_tunai();
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


        $data['title'] = 'Laporan Rekap Penjualan Non Tunai per Kategori';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_non_tunai';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanTunai() {
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
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_non_tunai';
        $data['filter_webpart'] = 'webparts/parts/filter_form';


        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['tbody'] = $b;
        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function RenderMergeCellPerKategori($groupBySaleNumber) {
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
            $Qty = $this->currencyformatter->format($sale->Quantity);
            $kategori = $sale->Kategori;
            $SubTotal = $this->currencyformatter->format($sale->TotalPerItem);

            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $kategori . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($totalQty) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlah) . "</td>";
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

    protected function RenderMergeCellLabaPerKategori($groupBySaleNumber) {
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
            $Qty = $this->currencyformatter->format($sale->Quantity);
            $kategori = $sale->Kategori;
            $SubTotal = $this->currencyformatter->format($sale->TotalPerItem);
            $HppPerItem = $this->currencyformatter->format($sale->TotalHppPerItem);
            $TotalLabaKotorPerItem = $this->currencyformatter->format($sale->TotalPerItem - $sale->TotalHppPerItem);


            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $kategori . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $HppPerItem . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $TotalLabaKotorPerItem . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($totalQty) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlah) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlahHPP) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlah - $jumlahHPP) . "</td>";
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

    protected function RenderMergeCellPerOpsiMakan($groupBySaleNumber) {
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
            $Qty = $this->currencyformatter->format($sale->Quantity);
            $opsimakan = $sale->TipePenjualan;
            $SubTotal = $this->currencyformatter->format($sale->TotalPerItem);
            $Markup = $this->currencyformatter->format($sale->MarkupPerItem);

            if ($indexD == 0) {
                $rowspan = count($groupBySaleNumber);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $opsimakan . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $Markup . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($totalQty) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlah) . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($totalMarkup) . "</td>";
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

    function Diskon() {
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

        $strQuery = $this->nutaquery->get_query_laporan_diskon();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Diskon';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'webparts/parts/filter_form';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function Pajak() {
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

        $strQuery = $this->nutaquery->get_query_laporan_pajak_penjualan();
        list($result, $fields) = $this->execute_query($strQuery);
        $a = $this->GroupResultByField($result, 'NamaPajak');

        $keys = array_keys($a);
        $b = '';
        $grandTotalFooter = 0;
        foreach ($keys as $key) {
            $processrenderMergeCell = $this->RenderMergeCellPerNamaPajak($a[$key]);
            $b .= $processrenderMergeCell['content'];
            $grandTotalFooter += $processrenderMergeCell['total'];
        }
        $grandTotalColspan = 4;

        $b .= "<tr>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>Grand Total</td>";
        $b .= "<td colspan='" . $grandTotalColspan . "'>" . $this->currencyformatter->format($grandTotalFooter) . "</td>";
        $b .= "<tr>";
        $data['tbody'] = $b;

        $data['title'] = 'Laporan Pajak';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualan';
        $data['filter_webpart'] = 'webparts/parts/filter_form';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function menupenjualan() {
        $data['page_part'] = 'webparts/menu_penjualan';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menupembelian() {
        $data['page_part'] = 'webparts/menu_pembelian';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menukeuangan() {
        $data['page_part'] = 'webparts/menu_keuangan';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menustok() {
        $data['page_part'] = 'webparts/menu_stok';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function menulaba() {
        $data['page_part'] = 'webparts/menu_laba';

        $data['js_chart'] = array();
        $data['js_part'] = array();
        $data = $this->setupViewVisibilityMenu($data);
        $data = $this->setupViewVisibilityLaporan($data);
        $this->load->view('main_part_menu_box', $data);
    }

    function Pembulatan() {
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

        $strQuery = $this->nutaquery->get_query_laporan_pembulatan();
        list($result, $fields) = $this->execute_query($strQuery);

        $data['title'] = 'Laporan Pembulatan';
        $data['selected_rekapper'] = $rekapper;
        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan_per_jam';
        $data['filter_webpart'] = 'webparts/parts/filter_form';
        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    protected function RenderMergeCellPerVarian($groupByVarian) {
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
            $Qty = $this->currencyformatter->format($sale->Qty);
            $varian = $sale->Varian;
            $SubTotal = $this->currencyformatter->format($sale->TotalPerItem);

            if ($indexD == 0) {
                $rowspan = count($groupByVarian);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $varian . "</td>";
                $tablecontent .= "<td>" . $itemname . "</td>";
                $tablecontent .= "<td align=\"center\">" . $Qty . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $SubTotal . "</td>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\"   style=\"text-align:right\"> " . $this->currencyformatter->format($jumlah) . "</td>";
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

    protected function RenderMergeCellPerNamaPajak($groupByNamaPajak) {
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
            $pajak = $this->currencyformatter->format($pajak->Pajak);

            if ($indexD == 0) {
                $rowspan = count($groupByNamaPajak);
                $tablecontent .= "<tr>";
                $tablecontent .= "<td rowspan=\"" . $rowspan . "\">" . $pajakName . "</td>";
                $tablecontent .= "<td>" . $nomer . "</td>";
                $tablecontent .= "<td>" . $tanggal . "</td>";
                $tablecontent .= "<td align=\"right\">" . "" . $pajak . "</td>";
                $tablecontent .= "<td  rowspan=\"" . $rowspan . "align=\"right\">" . $this->currencyformatter->format($jumlah) . "</td>";
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
    private function generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra) {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCell($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
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

    private function generateMergeCellLaporanPesananBatal($result, $modules, $is_ekstra) {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCellPesananBatal($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
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

    private function generateMergeCellLaporanPenjualanPerShift($result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd) {
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
                        if ($field->name === 'OpenID' || $field->name === 'OpenDate' || $field->name === 'OpenTime' || $field->name === 'CloseDate' || $field->name === 'CloseTime' || $field->name === 'Jam' || $field->name === 'DeviceNo'
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

    private function generateMergeCellLaporanPenjualanVoid($result, $modules, $is_ekstra) {
        $gropByNomor = $this->GroupResultByField($result, 'Nomor');

        $nomorPenjualans = array_keys($gropByNomor);
        $htmlTable = '';
        $grandTotalFooter = 0;
        foreach ($nomorPenjualans as $nomorPenjualan) {
            $processrenderMergeCell = $this->RenderMergeCellVoid($gropByNomor[$nomorPenjualan], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
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

    /**
     * @return array
     */
    private function setup_outlets() {
        $availableOutlets = $this->GetOutletTanpaSemua();
        $this->setDefaultOutletId($availableOutlets);
        return $availableOutlets;
    }

    /**
     * @return mixed
     */
    private function get_module() {
        $this->load->model('Perusahaanmodel');
        $modules = $this->Perusahaanmodel->getModulPerusahaan(getLoggedInUserID());
        return $modules;
    }

    /**
     * @param $strQuery
     * @return array
     */
    private function execute_query($strQuery) {
        $query = $this->db->query($strQuery);
        $result = $query->result();
        $fields = $query->field_data();
        return array($result, $fields);
    }

    function Penjualanpershift() {
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
                $result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporanpershift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualanpershift';
        $data['title'] = 'Laporan Penjualan';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapShift() {
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
        list($result, $fields) = $this->execute_query($strQuery);


        $data['page_part'] = 'webparts/widget_laporanpershift';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_shift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';

        $data['selected_rekapper'] = $rekapper;
        $data['title'] = 'Laporan Rekap Shift';
        $data['dateStart'] = $dateStart;
        $data['dateEnd'] = $dateEnd;

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function RekapPenjualanPerShift() {
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
        $strQuery = $this->nutaquery->get_query_rekap_penjualanpershift($modules['IsUseTaxModule'], $rekapper);
        list($result, $fields) = $this->execute_query($strQuery);
        $strQueryCharge = $this->nutaquery->get_query_charge_edc();
        $queryCharge = $this->db->query($strQueryCharge);
        $resultCharge = $queryCharge->result();
        $fieldsCharge = $queryCharge->field_data();


        $data['page_part'] = 'webparts/widget_laporan';
        $data['grid_webpart'] = 'webparts/parts/grid_rekap_penjualan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_rekap_penjualan';

        $data['selected_rekapper'] = $rekapper;
        $data['title'] = 'Laporan Rekap Penjualan Berdasarkan Tanggal Shift / Tanggal Buka Outlet';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result,
            'fields_charge' => $fieldsCharge, 'result_charge' => $resultCharge);

        $data['js_chart'] = array();
        $data = $this->setup_view_params($availableOutlets, $data);
        $this->load->view('main_part', $data);
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function PesananBelumLunas() {
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
        $strQuery = $this->nutaquery->get_query_penjualan_belum_lunas($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        list($result, $fields) = $this->execute_query($strQuery);

        $htmlTable = $this->generateMergeCellLaporanPenjualan($result, $modules, $is_ekstra);


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan_belum_lunas';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualan';
        $data['title'] = 'Laporan Pesanan Belum Lunas';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    function opsimakanpershift() {
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
        //$strQuery = $this->nutaquery->get_query_opsimakan_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice']);
        //list($result, $fields) = $this->execute_query($strQuery);
        $q = $this->nutaquery->get_list_shift($dateStart, $dateEnd);
        list($result, $fields) = $this->execute_query($q);

        for ($i = 0; $i < count($result); $i++) {
            $r = $result[$i];
            //$this->nutaquery->setDate($r->OpenDate, $r->CloseDate);
            $q2 = $this->nutaquery->get_query_opsimakan_pershift($modules['IsUseTaxModule'], $modules['IsDiningTable'], $is_ekstra, $modules['IsUseVarianAndPrice'], $r->OpenID, $r->DeviceNo);
            list($details, $f2) = $this->execute_query($q2);
            $r->Details = $details;
        }

        /* $htmlTable = $this->generateMergeCellLaporanOpsiMakanPerShift(
          $result, $fields, $modules, $is_ekstra, $dateStart, $dateEnd); */

        $this->bind_params_to_NutaQuery($dateStart, $dateEnd, $outlet); // reset

        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporanpershift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_opsimakanpershift';
        $data['title'] = 'Laporan Tipe Penjualan per Shift';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
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

    function penjualankategoripershift() {

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

        $data['page_part'] = 'webparts/widget_laporanpershift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_kategoripershift';
        $data['title'] = 'Laporan Kategori per Shift';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
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

    function penjualanvarianpershift() {

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

        $data['page_part'] = 'webparts/widget_laporanpershift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_variantpershift';
        $data['title'] = 'Laporan Variant per Shift';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
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

    function penjualanpilihanekstrapershift() {
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
        $data['page_part'] = 'webparts/laporan_pilihan_ekstra_per_shift';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['title'] = 'Laporan Pilihan Ekstra per Shift';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
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

    public function riwayatpelanggan() {
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


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_riwayat_pelanggan';
        $data['grid_webpart'] = 'webparts/parts/grid_penjualan';
        $data['title'] = 'Laporan Riwayat Pelanggan';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;

        $data = $this->setupViewListCustomer($customers, $data);
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

    public function pesananbatal() {
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


        $data['selected_isekstra'] = $is_ekstra;

        $data['page_part'] = 'webparts/widget_laporan';
        $data['filter_webpart'] = 'webparts/parts/filter_form_penjualan';
        $data['grid_webpart'] = 'webparts/parts/grid_pesanan_batal';
        $data['title'] = 'Laporan Pesanan batal';

        $data['js_part'] = array('webparts/parts/js_form', 'webparts/parts/js_datatable', 'webparts/parts/filter_date_mulai_sampai_js');
        $data['datagrid'] = array('fields' => $fields, 'result' => $result);
        $data['js_chart'] = array();
        $data['tbody'] = $htmlTable;

        $data = $this->setupViewListCustomer($customers, $data);
        $data = $this->setup_view_params($availableOutlets, $data);

        $this->load->view('main_part', $data);
        $this->benchmark->mark('code_end');
        $this->hiddenmsg('bc : ' . $this->benchmark->elapsed_time('code_start', 'code_end'));
    }

}
