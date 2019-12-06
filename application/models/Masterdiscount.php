<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Masterdiscount extends CI_Model
{
    var $_tableName = "masterdiscount";
    protected $_dbMaster;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');

        $this->initDbMaster();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function getTotalDiscount($where)
    {
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        return $query->num_rows();
    }

    public function getDatatablesDiscount($params, $visibilityMenu)
    {
        $this->load->library('CurrencyFormatter');

        $orderColumn = array('DiscountName', null, null);
        $orderType = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");

        // Get Total Data
        $length = $params['length'];
        $start = (int) $params['start'];
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'recordsTotal'
            FROM masterdiscount a
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ?  ";
        $query = $this->_dbMaster->query($sql, array($params['PerusahaanNo'], $params['DeviceID']));
        $recordsTotal = (int) $query->row()->recordsTotal;
        $length = $length <= 0 ? $recordsTotal : $length;

        $search = $params['search'];
        $order = $params['order'];
        if (!is_array($order)) {
            $orderBy = ' ';
        } else {
            $orderData = array();
            foreach ($order as $o) {
                array_push($orderData, $orderColumn[$o["column"]] . " " . $orderType[$o["dir"]]);
            }

            $orderBy = 'ORDER BY ' . implode(',', $orderData);
        }

        // Get List Data
        $listData = array();
        $sql = "SELECT a.*
            FROM masterdiscount a
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND ( DiscountName LIKE ? ) " . $orderBy . " LIMIT ?,? ";
        $query = $this->_dbMaster->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
            '%' . $search['value'] . '%',
            $start,
            $length
        ));
        if ($query->num_rows() > 0) {
            $rs_result = $query->result();
            foreach ($rs_result as $i => $result) {
                $row = array();
                $row[] = $result->DiscountName;
                $row[] = $this->__discountFormat($result->Discount);

                if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete']) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<a href="javascript:void(0)" class="dropdown-toggle pull-right blue-sea" type="button" data-toggle="dropdown">';
                    $btn .= '<i class="fa fa-ellipsis-h"></i></a>';
                    $btn .= '<ul class="dropdown-menu dropdown-menu-right">';
                    if ($visibilityMenu['ItemEdit']) {
                        $btn .= '<li><a href="javascript:void(0)" class="btnUpdateDiscount py-10" data-discount-id="' . $result->DiscountID . '" data-device-no="' . $result->DeviceNo . '" data-discount-name="' . $result->DiscountName . '">Edit</a></li>';
                    }
                    if ($visibilityMenu['ItemDelete']) {
                        $btn .= '<li class="divider my-0"></li>
                                <li>
                                    <a href="javascript:void(0)" class="btnDeleteDiscount py-10" data-discount-id="' . $result->DiscountID . '" data-device-no="' . $result->DeviceNo . '" data-discount-name="' . $result->DiscountName . '">Hapus</a>
                                </li>';
                    }
                    $btn .= '</ul>';
                    $btn .= '</div>';
                    $row[] = $btn;
                }

                $listData[$i] = $row;
            }
        }

        // Get Total Filtered Data
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'recordsFiltered'
            FROM masterdiscount a
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND ( DiscountName LIKE ? )";
        $query = $this->_dbMaster->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
            '%' . $search['value'] . '%',
        ));
        $recordsFiltered = (int) $query->row()->recordsFiltered;

        $return = new stdClass;
        $return->draw = $params['draw'];
        $return->recordsTotal = (int) $recordsTotal;
        $return->recordsFiltered = (int) $recordsFiltered;
        $return->data = $listData;

        return $return;
    }

    function __discountFormat($value)
    {
        if (strpos($value, '%')) {
            $value = str_replace('%', '', $value);
            $explode = explode(".", $value);
            $explode1 = $explode[0];
            $explode1 = number_format($explode1, 0, ',', '.');
            $explode2 = $explode[1];

            if (!empty($explode[1])) {
                return $explode1 . "," . $explode2 . "%";
            } else {
                return $explode1 . "%";
            }
        } else {
            $explode = explode(".", $value);
            $explode1 = $explode[0];
            $explode1 = number_format($explode1, 0, ',', '.');
            $explode2 = $explode[1];

            if (!empty($explode[1])) {
                return "Rp " . $explode1 . "," . $explode2;
            } else {
                return "Rp " . $explode1;
            }
        }
    }

    public function getDetailMasterDiscount($where)
    {
        $sql = "SELECT a.*, REPLACE(REPLACE(a.Discount, '%', ''),'.',',') AS 'Discount', IF(Discount REGEXP '[[.percent-sign.]]', 'yes','no' ) AS 'percent'
            FROM masterdiscount a
            WHERE PerusahaanNo = ? AND DeviceID = ? AND DiscountID = ? AND DeviceNo = ?";
        $query = $this->_dbMaster->query($sql, $where);
        return $query->row_array();
    }

    public function insertMasterDiscount($params)
    {

        $insert = $this->_dbMaster->insert($this->_tableName, $params);

        if ($insert) {
            // Push to firebase
            $where = array(
                'PerusahaanNo' => $params['PerusahaanNo'],
                'DeviceID' => $params['DeviceID'],
                'DiscountID' => $params['DiscountID'],
            );
            $query = $this->_dbMaster->get_where($this->_tableName, $where);
            $dataInsert = $query->row_array();
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $dataInsert
            );
            $this->Firebasemodel->push_firebase(
                $dataInsert['DeviceID'],
                $last_insert_data,
                $dataInsert['DiscountID'],
                $dataInsert['DeviceNo'],
                $dataInsert['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function getNewDiscountID($where)
    {
        $query = $this->_dbMaster->order_by('DiscountID', 'desc')->get_where($this->_tableName, $where);
        $lastDiscount = $query->row();
        return (intval($lastDiscount->DiscountID) + 1);
    }

    public function updateMasterDiscount($params, $where)
    {

        $update = $this->_dbMaster->update($this->_tableName, $params, $where);

        if ($update) {
            // Push to firebase
            $query = $this->_dbMaster->get_where($this->_tableName, $where);
            $dataUpdate = $query->row_array();
            $last_update_data = array(
                "table" => $this->_tableName,
                "column" => $dataUpdate
            );
            $this->Firebasemodel->push_firebase(
                $dataUpdate['DeviceID'],
                $last_update_data,
                $dataUpdate['DiscountID'],
                $dataUpdate['DeviceNo'],
                $dataUpdate['PerusahaanNo'],
                0
            );

            return true;
        } else {
            return false;
        }
    }

    public function deleteMasterDiscount($where)
    {

        $query = $this->_dbMaster->get_where('masterdiscountdelete', $where);
        $Discount = $query->row_array();

        if ($query->num_rows() > 0) {
            $this->_dbMaster->delete('masterdiscountdelete', $where);
        }

        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $Discount = $query->row_array();

        if ($query->num_rows() == 0) {
            return false;
        }

        if ($this->_dbMaster->insert('masterdiscountdelete', $Discount)) {
            if ($this->_dbMaster->delete($this->_tableName, $where)) {
                // Push to firebase
                $last_update_data = array(
                    "table" => "delete" . $this->_tableName,
                    "column" => array(
                        "DiscountID" => $Discount['DiscountID'],
                        "DeviceNo" => $Discount['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $Discount['DeviceID'],
                    $last_update_data,
                    $Discount['DiscountID'],
                    $Discount['DeviceNo'],
                    $Discount['PerusahaanNo'],
                    0
                );

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
