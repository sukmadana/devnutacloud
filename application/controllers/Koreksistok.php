<?php
include_once "KoreksiStok/AddKoreksiStok.php";
include_once "KoreksiStok/ListKoreksiStok.php";
include_once "KoreksiStok/ViewKoreksiStok.php";
include_once "KoreksiStok/EditKoreksiStok.php";
include_once "KoreksiStok/DeleteKoreksiStok.php";

class Koreksistok extends MY_Controller

{
    use AddKoreksiStok, ListKoreksiStok, ViewKoreksiStok, EditKoreksiStok, DeleteKoreksiStok;
    protected $perusahaanno=0;

    function __construct()
    {
        parent::__construct();
        ifNotAuthenticatedRedirectToLogin();

        $this->data = [
            'page_part' => 'koreksi-stok/index',
            'js_chart' => [],
            'js_part' => array('features/js/js_form'),
            'outlets' => $this->GetOutletTanpaSemua(),
            'selected_outlet' => $this->getOutletId(),
            'visibilityMenu' => $this->visibilityMenu,
            'isLaporanStokVisible' => $this->IsLaporanStokVisible(),
            'isLaporanPembelianVisible' => $this->IsLaporanPembelianVisible(),
            'isLaporanPriceVarianVisible' => $this->IsLaporanVarianHargaVisible(),
            'menu' => "stok",
        ];
    }

    protected function getOutletId()
    {
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0;
        }

        return $selected_outlet;
    }

    protected function addJsPart($location)
    {
        if ($location) {
            array_push($this->data['js_part'], $location);
        }
    }

    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    protected function getItem($outlet)
    {
        $this->load->model('masterItem');
        return $this->masterItem->getMasterItemByOutlet($outlet);
    }

    protected function get_list_koreksi_by_id($outlet, $id, $devno)
    {
        $query = "
        select * from stockopname 
            where DeviceID='$outlet' 
                and PerusahaanNo='" . getPerusahaanNo() . "'
                and TransactionID='$id'
                and DeviceNo='$devno'
        ";
        $query = $this->db->query($query);
        return $query->row();
    }

    protected function get_list_koreksi_by_id_rowarray($outlet, $id, $devno)
    {
        $query = "
        select * from stockopname 
            where DeviceID='$outlet' 
                and PerusahaanNo='" . getPerusahaanNo() . "' 
                and TransactionID='$id'
                and DeviceNo='$devno'

        ";
        $query = $this->db->query($query);
        return $query->row_array();
    }

    public function simpan()
    {
        $this->load->model('Stockopname');
        $this->load->model('Stockopnamedetail');
        $this->perusahaanno = getPerusahaanNo();

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($this->input->post('outlet'));
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        if ($this->input->post('mode') == "new") {
            $this->attributes = $this->generateAttribute($cloudDevno);
            $storing = $this->Stockopname->create($this->attributes);
        } else if ($this->input->post('mode') == "edit") {
            $this->attributes = $this->generateAttribute($cloudDevno);
            $clause = array(
                "PerusahaanNo" => $this->perusahaanno,
                "DeviceID" => $this->input->post('outlet'),
                "TransactionID" => $this->input->post('transaction_id'),
                "DeviceNo" => $this->input->post('devno')
            );
            $storing = $this->Stockopname->update($clause, $this->attributes);
        }

        if (!$storing)
            return redirect(base_url('koreksistok/add?outlet=' . $this->input->post('outlet')));

        // push data to firebase
        if ($this->input->post('mode') == "new") {
            $tr_id = $this->attributes['TransactionID'];
            $devno = $this->attributes['DeviceNo'];
        } else if ($this->input->post('mode') == "edit") {
            $tr_id = $this->input->post('transaction_id');
            $devno = $this->input->post('devno');
        }
        $query_dataupdated = $this->get_list_koreksi_by_id_rowarray($this->input->post('outlet'), $tr_id, $devno);
        $last_update_data = array(
            "table" => 'stockopname',
            "column" => $query_dataupdated
        );
        $this->load->model('Firebasemodel');

        $firebase_push = false;
        $firebase_push = (($options->CreatedVersionCode<220) && ($options->EditedVersionCode<220));
        if ($this->storeDetail($cloudDevno, $firebase_push)) {
            $this->savedDetail();
            $this->Firebasemodel->push_firebase($this->input->post('outlet'), $last_update_data,
                $tr_id, $devno, $this->perusahaanno, 0);
            $this->session->set_flashdata('notif', 'Simpan Berhasil');

            if ($this->input->post('mode') == "new") {
                return redirect($this->input->server('HTTP_REFERER'));
            } else if ($this->input->post('mode') == "edit") {
//                return redirect($this->input->server('HTTP_REFERER'));
                redirect(base_url('koreksistok/?outlet=' . $this->input->post('outlet') .
                    '&ds=' . $this->input->post('date_start') . '&de=' . $this->input->post('date_end')));
//              redirect(base_url('koreksistok/view/' . $this->input->post('outlet') . '/' . $this->input->post('transaction_id') ) );
            }
        }
    }

    protected function storeDetail($cloudDevno, $firebase=true)
    {
        foreach ($this->input->post('item') as $key => $value) {
            if ($value === "")
                continue;

            $storing = null;
            $atributdetil = $this->generateAttributeDetail($key, $cloudDevno);
            if ($this->input->post('mode') == "new") {
                $storing = $this->Stockopnamedetail->create($atributdetil);
            } else if ($this->input->post('mode') == "edit") {
                $clause = array(
                    "DeviceID" => $this->input->post('outlet'),
                    "DetailID" => $this->input->post('detail_id')[$key],
                    "DeviceNo" => $this->input->post('detail_devno')[$key],
                );
                
                $added = (int)$this->input->post('detail_added')[$key];
                if ($added <= 0) {
                    $deleted = (int)$this->input->post('detail_deleted')[$key];
                    if ($deleted <= 0) {  
                        $storing = $this->Stockopnamedetail->update_item($atributdetil, $clause);
                    } else {
                        $this->Stockopnamedetail->delete($clause);
                        continue;
                    }
                } else {
                    $atributdetil = $this->addNewDetail($key, $cloudDevno);
                    $storing = $this->Stockopnamedetail->create($atributdetil);
                }
            }
            if (!$storing)
                return redirect(base_url('koreksistok/add?outlet=' . $this->input->post('outlet')));

            // push data to firebase
            if ($this->input->post('mode') == "new") {
                $detail_id = $atributdetil['DetailID'];
                $detail_devno = $cloudDevno;
            } else if ($this->input->post('mode') == "edit") {
                $detail_id = $this->input->post('detail_id')[$key];
                if ($detail_id === "") {
                     $detail_id = $atributdetil['DetailID'];
                }

                $detail_devno = $this->input->post('detail_devno')[$key];
                if ($detail_devno === "") {
                    $detail_devno = $cloudDevno;
                }
            }
            if ($firebase) {
                $insert_query = $this->Stockopnamedetail->get_stockopnamedetail_rowarray(
                    $this->input->post('outlet'),
                    $detail_id,
                    $detail_devno
                );
                $last_insert_data = array(
                    "table" => 'stockopnamedetail',
                    "column" => $insert_query
                );
                $this->load->model('Firebasemodel');
                $this->Firebasemodel->push_firebase(
                    $this->input->post('outlet'),
                    $last_insert_data,
                    $detail_id,
                    $detail_devno,
                    $this->perusahaanno,
                    0
                );
            }
        }
        return true;
    }

    protected function savedDetail()
    {
        if ($this->input->post('mode') == "new") {
            $this->Stockopname->update($this->attributes, [
                'IsDetailsSaved' => 1
            ]);
        }
    }

    protected function generateAttribute($cloudDevno)
    {
        if ($this->input->post('mode') == "edit") {
            return [
                'EditedBy' => getLoggedInUsername(),
                'EditedDate' => date('Y-m-d'),
                'EditedTime' => date('H:i')
            ];
        }
        $id = $this->db->query($this->Stockopname->get_transaction_id($this->input->post('outlet')))->first_row();
        $code = $this->db->query($this->Stockopname->get_generate_nomorkoreksistok(
            $this->getDateTime()['date'], $this->input->post('outlet')))->first_row();
        return [
            'TransactionID' => $id->result,
            'DeviceNo' => $cloudDevno,
            'StockOpnameNumber' => $code->result,
            'StockOpnameDate' => $this->getDateTime()['date'],
            'StockOpnameTime' => $this->getDateTime()['time'],
            'DeviceID' => $this->input->post('outlet'),
            'PerusahaanID' => getLoggedInUserID(),
            'PerusahaanNo' => getPerusahaanNo(),
            'CreatedBy' => getLoggedInUsername(),
            'isDetailsSaved' => 0,
            'CreatedDate' => date('Y-m-d'),
            'CreatedTime' => date('H:i'),
            'Varian' => 'Nuta',
            'HasBeenDownloaded' => 0,
            'EditedBy' => '',
            'EditedDate' => '',
            'EditedTime' => ''
        ];
    }

    protected function getDateTime()
    {
        return [
            'date' => date("Y-m-d"),
            'time' => date("H:i"),
        ];
    }

    protected function generateAttributeDetail($key, $cloudDevno)
    {
        $realitemid = explode(".", $this->input->post('item')[$key])[0];
        $devno = explode(".", $this->input->post('item')[$key])[1];
        $atribut = array(
            'ItemID' => $realitemid,
            'ItemDeviceNo' => $devno,
            'StockByApp' => $this->input->post('qty-sistem')[$key],
            'RealStock' => $this->input->post('qty-aktual')[$key],
            'Note' => $this->input->post('keterangan')[$key],
            'detailnumber' => $key + 1,
        );

        if ($this->input->post('mode') == "new") {

            $id = $this->db->query($this->Stockopnamedetail->get_sk_detail_id($this->attributes['DeviceID']));

            $atribut['DetailID'] = $id->first_row()->result;
            $atribut['DeviceNo'] = $cloudDevno;
            $atribut['TransactionID'] = $this->attributes['TransactionID'];
            $atribut['TransactionDeviceNo'] = $this->attributes['DeviceNo'];
            $atribut['Varian'] = 'Nuta';
            $atribut['DeviceID'] = $this->attributes['DeviceID'];
            $atribut['PerusahaanID'] = getLoggedInUserID();
            $atribut['PerusahaanNo'] = getPerusahaanNo();
            $atribut['HasBeenDownloaded'] = 0;

        }

        return $atribut;
    }

    protected function addNewDetail($key, $cloudDevno)
    {
        $realitemid = explode(".", $this->input->post('item')[$key])[0];
        $devno = explode(".", $this->input->post('item')[$key])[1];
        $atribut = array(
            'ItemID' => $realitemid,
            'ItemDeviceNo' => $devno,
            'StockByApp' => $this->input->post('qty-sistem')[$key],
            'RealStock' => $this->input->post('qty-aktual')[$key],
            'Note' => $this->input->post('keterangan')[$key],
            'detailnumber' => $key + 1,
        );

        if ($this->input->post('mode') == "edit") {
            $outlet = (int)$this->input->post("outlet", TRUE);
            $transactionID = (int)$this->input->post("transaction_id", TRUE);

            $id = $this->db->query($this->Stockopnamedetail->get_sk_detail_id($outlet));

            $atribut['DetailID'] = $id->first_row()->result;
            $atribut['DeviceNo'] = $cloudDevno;
            $atribut['TransactionID'] = $transactionID;
            $atribut['TransactionDeviceNo'] = $this->input->post('devno');
            $atribut['Varian'] = 'Nuta';
            $atribut['DeviceID'] = $outlet;
            $atribut['PerusahaanID'] = getLoggedInUserID();
            $atribut['PerusahaanNo'] = getPerusahaanNo();
            $atribut['HasBeenDownloaded'] = 0;

        }

        return $atribut;
    }
}
