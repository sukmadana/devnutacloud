<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Masteritemingredients extends CI_Model
{
    var $_tableName = "masteritemdetailingredients";
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

    public function getTotalIngredients($params)
    {
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'total' FROM masteritem a
            LEFT JOIN mastercategory b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID AND a.CategoryID = b.CategoryID AND a.CategoryDeviceNo = b.DeviceNo
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND a.IsProduct = 'false'";
        $query = $this->_dbMaster->query($sql, array($params['PerusahaanNo'], $params['DeviceID']));
        $result = $query->row();
        return $result->total;
    }

    public function getDatatablesIngredients($params, $visibilityMenu)
    {
        $orderColumn = array(null, 'Bahan', 'CategoryName', 'Unit', 'PurchasePrice', null);
        $orderType = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");

        // Get Total Data
        $length = $params['length'];
        $start = (int) $params['start'];
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'recordsTotal'
            FROM masteritem a
            -- LEFT JOIN masteritemdetailingredients b ON a.PerusahaanID = b.PerusahaanID AND a.DeviceID = b.DeviceID AND a.ItemID = b.IngredientsID
            LEFT JOIN mastercategory c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.CategoryDeviceNo = c.DeviceNo AND a.CategoryID = c.CategoryID
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ?  AND a.IsProduct = ?";
        $query = $this->db->query($sql, array($params['PerusahaanNo'], $params['DeviceID'], $params['IsProduct']));
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
        $sql = "SELECT a.ItemID AS 'IngredientsID', a.DeviceNo AS 'IngredientsDeviceNo', a.ItemName AS 'Bahan', a.Unit, a.PurchasePrice, c.CategoryName, COALESCE(b.totalProduk,0) AS 'totalProduk'
            FROM masteritem a
            LEFT JOIN (
                SELECT PerusahaanNo, DeviceID, IngredientsID, IngredientsDeviceNo, COUNT(*) AS 'totalProduk'
                FROM masteritemdetailingredients
                GROUP BY PerusahaanNo, DeviceID, IngredientsID, IngredientsDeviceNo
            ) b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID AND a.ItemID = b.IngredientsID AND a.DeviceNo = b.IngredientsDeviceNo
            LEFT JOIN mastercategory c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.CategoryDeviceNo = c.DeviceNo AND a.CategoryID = c.CategoryID
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND a.IsProduct = ? AND ( a.ItemName LIKE ? OR a.Unit LIKE ? OR c.CategoryName LIKE ? OR a.PurchasePrice LIKE ?) " . $orderBy . " LIMIT ?,? ";
        $query = $this->db->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
            $params['IsProduct'],
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            $start,
            $length
        ));
        if ($query->num_rows() > 0) {
            $rs_result = $query->result();
            foreach ($rs_result as $i => $result) {
                $row = array();
                if ($result->totalProduk > 0) {
                    $row[] = '<a href="javascript:void(0)" data-ingredients-id="' . $result->IngredientsID . '" data-device-no="' . $result->IngredientsDeviceNo . '" class="blue-sea ingredients-details icon">
                                <i class="fa fa-chevron-right"></i>
                            </a>';
                    $row[] = '<a href="javascript:void(0)" data-ingredients-id="' . $result->IngredientsID . '" data-device-no="' . $result->IngredientsDeviceNo . '" class="blue-sea ingredients-details">' . $result->Bahan . '</a>';
                } else {
                    $row[] = '';
                    $row[] = '<a href="javascript:void(0)" data-ingredients-id="' . $result->IngredientsID . '" data-device-no="' . $result->IngredientsDeviceNo . '" class="blue-sea">' . $result->Bahan . '</a>';
                }

                $row[] = $result->CategoryName;
                $row[] = $result->Unit;
                $row[] = "Rp " . (number_format($result->PurchasePrice, 0, ',', '.'));

                if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete'] || $visibilityMenu['ItemAdd']) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<a href="javascript:void(0)" class="dropdown-toggle pull-right blue-sea" type="button" data-toggle="dropdown">';
                    $btn .= '<i class="fa fa-ellipsis-h"></i></a>';
                    $btn .= '<ul class="dropdown-menu dropdown-menu-right">';
                    if ($visibilityMenu['ItemEdit']) {
                        $btn .= '<li><a href="javascript:void(0)" class="btnUpdateBahan py-10" data-ingredients-id="' . $result->IngredientsID . '" data-device-no="' . $result->IngredientsDeviceNo . '" data-ingredients-name="' . $result->Bahan . '">Edit</a></li>';
                    }
                    if ($visibilityMenu['ItemDelete']) {
                        $btn .= '<li class="divider my-0"></li>
                                <li>
                                    <a href="javascript:void(0)" class="btnDeleteBahan py-10" data-ingredients-id="' . $result->IngredientsID . '" data-device-no="' . $result->IngredientsDeviceNo . '" data-ingredients-name="' . $result->Bahan . '">Hapus</a>
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
            FROM masteritem a
            LEFT JOIN (
                SELECT PerusahaanNo, DeviceID, IngredientsID, IngredientsDeviceNo, COUNT(*) AS 'totalProduk'
                FROM masteritemdetailingredients
                GROUP BY PerusahaanNo, DeviceID, IngredientsID, IngredientsDeviceNo
            ) b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID AND a.ItemID = b.IngredientsID AND a.DeviceNo = b.IngredientsDeviceNo
            LEFT JOIN mastercategory c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.CategoryDeviceNo = c.DeviceNo AND a.CategoryID = c.CategoryID
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND a.IsProduct = ? AND ( a.ItemName LIKE ? OR a.Unit LIKE ? OR c.CategoryName LIKE ? OR a.PurchasePrice LIKE ? )";
        $query = $this->db->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
            $params['IsProduct'],
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
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

    public function getItemsIngredient($where)
    {
        $sql = "SELECT b.ItemName AS 'Produk', CONCAT(SUM(a.QtyNeed),' ',c.Unit) AS 'KebutuhanBahan'
        FROM masteritemdetailingredients a
        INNER JOIN masteritem b ON a.PerusahaanID = b.PerusahaanID AND a.DeviceID = b.DeviceID AND a.ItemID = b.ItemID AND a.ItemDeviceNo = b.DeviceNo
        INNER JOIN masteritem c ON a.PerusahaanID = c.PerusahaanID AND a.DeviceID = c.DeviceID AND a.IngredientsID = c.ItemID AND a.IngredientsDeviceNo = c.DeviceNo
        WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND a.IngredientsID = ? AND a.IngredientsDeviceNo = ?
        GROUP BY a.PerusahaanNo, a.deviceID, a.ItemID, a.ItemDeviceNo
        ORDER BY b.ItemName";
        $query = $this->_dbMaster->query($sql, $where);
        return $query->result_array();
    }

    public function getMasterCategory($params)
    {
        $query = $this->_dbMaster->get_where('mastercategory', $params);
        return $query->result_array();
    }

    public function getSearchCategory($params)
    {
        $sql = "SELECT CategoryID AS 'id', CategoryName AS 'text' FROM mastercategory WHERE PerusahaanNo = ? AND DeviceID = ? AND CategoryName LIKE ? ";
        $query = $this->_dbMaster->query($sql, array($params['PerusahaanNo'], $params['DeviceID'], '%' . $params['CategoryName'] . '%'));
        return $query->result_array();
    }

    public function isExistField($where)
    {
        $query = $this->_dbMaster->get_where('masteritem', $where);
        return $query->num_rows() > 0 ? true : false;
    }

    public function insertBahan($params)
    {
        $insert = $this->_dbMaster->insert('masteritem', $params);

        if ($insert) {
            // Push to firebase
            $where = array(
                'PerusahaanNo' => $params['PerusahaanNo'],
                'DeviceID' => $params['DeviceID'],
                'ItemID' => $params['ItemID'],
            );
            $query = $this->_dbMaster->get_where('masteritem', $where);
            $dataInsert = $query->row_array();
            $last_insert_data = array(
                "table" => 'masteritem',
                "column" => $dataInsert
            );

            $this->load->model('Options');
            $options = $this->Options->get_by_devid($params['DeviceID']);
            if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
                $this->Firebasemodel->push_firebase(
                    $dataInsert['DeviceID'],
                    $last_insert_data,
                    $dataInsert['ItemID'],
                    $dataInsert['DeviceNo'],
                    $dataInsert['PerusahaanNo'],
                    0
                );
            } else {
                $this->Firebasemodel->push_firebase(
                    $dataInsert['DeviceID'],
                    array(
                        'table' => 'pleaseUpdateMasterItem',
                        'column' => array('ItemID' => $dataInsert['ItemID'], 'DeviceNo' => $dataInsert['DeviceNo'])
                    ),
                    $dataInsert['ItemID'],
                    $dataInsert['DeviceNo'],
                    $dataInsert['PerusahaanNo'],
                    0
                );
            }



            return true;
        } else {
            return false;
        }
    }

    public function updateBahan($params, $where)
    {
        $update = $this->_dbMaster->update('masteritem', $params, $where);

        if ($update) {
            // Push to firebase
            $query = $this->_dbMaster->get_where('masteritem', $where);
            $dataUpdate = $query->row_array();
            $last_update_data = array(
                "table" => 'masteritem',
                "column" => $dataUpdate
            );

            $this->load->model('Options');
            $options = $this->Options->get_by_devid($params['DeviceID']);
            if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
                $this->Firebasemodel->push_firebase(
                    $dataUpdate['DeviceID'],
                    $last_update_data,
                    $dataUpdate['ItemID'],
                    $dataUpdate['DeviceNo'],
                    $dataUpdate['PerusahaanNo'],
                    0
                );
            } else {
                $this->Firebasemodel->push_firebase(
                    $dataUpdate['DeviceID'],
                    array(
                        'table' => 'pleaseUpdateMasterItem',
                        'column' => array('ItemID' => $dataUpdate['ItemID'], 'DeviceNo' => $dataUpdate['DeviceNo'])
                    ),
                    $dataUpdate['ItemID'],
                    $dataUpdate['DeviceNo'],
                    $dataUpdate['PerusahaanNo'],
                    0
                );
            }

            return true;
        } else {
            return false;
        }
    }

    public function deleteBahan($where)
    {

        $query = $this->_dbMaster->get_where('masteritem', $where);
        $Bahan = $query->row_array();

        if ($query->num_rows() == 0) {
            return false;
        }


        $arrDetail = array();
        $this->_dbMaster->trans_start();
        $query = $this->_dbMaster->get_where('masteritemdelete', $where);
        if ($query->num_rows() > 0) {
            $this->_dbMaster->delete('masteritemdelete', $where);
        }

        if ($this->_dbMaster->insert('masteritemdelete', $Bahan)) {
            if ($this->_dbMaster->delete('masteritem', $where)) {
                // Delete Masteritemdetailingredients

                $query = $this->_dbMaster->get_where('masteritemdetailingredients', array(
                    'PerusahaanNo' => $where['PerusahaanNo'],
                    'DeviceID' => $where['DeviceID'],
                    'IngredientsID' => $where['ItemID'],
                    'IngredientsDeviceNo' => $where['DeviceNo']
                ));

                $rs_detail = $query->result_array();
                if ($rs_detail) {
                    foreach ($rs_detail as $detail) {

                        $where = array(
                            'PerusahaanNo' => $detail['PerusahaanNo'],
                            'DeviceID' => $detail['DeviceID'],
                            'DeviceNo' => $detail['DeviceNo'],
                            'DetailID' => $detail['DetailID']
                        );
                        $query = $this->_dbMaster->get_where('masteritemdetailingredientsdelete', $where);

                        if ($query->num_rows() > 0) {
                            $this->_dbMaster->delete('masteritemdetailingredientsdelete', $where);
                        }

                        if ($this->_dbMaster->delete('masteritemdetailingredients', $where)) {
                            array_push($arrDetail, $detail);

                            $this->_dbMaster->insert('masteritemdetailingredientsdelete', $detail);
                        }
                    }
                }
            }
        }

        $this->_dbMaster->trans_complete();
        $transSt = $this->_dbMaster->trans_status();
        if ($transSt == true) {
            // Push to firebase

            // Master item / bahan
            $delete_data = array(
                "table" => "delete" . 'masteritem',
                "column" => array(
                    "ItemID" => $Bahan['ItemID'],
                    "DeviceNo" => $Bahan['DeviceNo']
                )
            );
            $this->Firebasemodel->push_firebase(
                $Bahan['DeviceID'],
                $delete_data,
                $Bahan['ItemID'],
                $Bahan['DeviceNo'],
                $Bahan['PerusahaanNo'],
                0
            );

            // Detail ingredients
            if ($arrDetail) {
                foreach ($arrDetail as $detail) {
                    $delete_data = array(
                        "table" => "delete" . 'masteritemdetailingredients',
                        "column" => array(
                            "DetailID" => $detail['DetailID'],
                            "DeviceNo" => $detail['DeviceNo']
                        )
                    );
                    $this->Firebasemodel->push_firebase(
                        $detail['DeviceID'],
                        $delete_data,
                        $detail['DetailID'],
                        $detail['DeviceNo'],
                        $detail['PerusahaanNo'],
                        0
                    );
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getNewItemID($where)
    {
        $query = $this->_dbMaster->order_by('ItemID', 'DESC')->get_where('masteritem', $where);
        $result = $query->row();
        if ($query->num_rows() > 0) {
            return (intval($result->ItemID) + 1);
        }
        return 1;
        $sql = "SELECT MAX(ItemID)
            FROM masteritem ";
    }

    public function getRowNumber($where)
    {
        $query = $this->_dbMaster->order_by('RowNumber', 'DESC')->get_where('masteritem', $where);
        $result = $query->row();
        return (intval($result->RowNumber) + 1);
    }

    public function getDetailBahan($where)
    {
        $query = $this->_dbMaster->get_where('masteritem', $where);
        return $query->row_array();
    }
}
