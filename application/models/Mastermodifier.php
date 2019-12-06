<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mastermodifier extends CI_Model
{
    var $_tableName = "mastermodifier";
    protected $_dbMaster;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Firebasemodel');
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function getModifier($deviceid)
    {
        $this->db->where(array('DeviceID' => $deviceid));
        $query = $this->db->get($this->_tableName);
        $result = $query->result();
        $retval = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                array_push($retval,
                    array('ModifierID' => $row->ModifierID,'DeviceNo' => $row->DeviceNo, 'ChooseOnlyOne' => $row->ChooseOneOnly,
                        'CanAddQuantity' => $row->CanAddQuantity,
                        'ModifierName' => $row->ModifierName, 'OldModifierName' => $row->ModifierName,
                        'PlaceholderPilihan' => $row->ModifierName, 'Selected' => false)
                );
            }
        }
        return $retval;
    }

    public function getModifierByName($name, $deviceid)
    {
        $this->db->where(array('ModifierName' => $name, 'DeviceID' => $deviceid));
        $query = $this->db->get($this->_tableName);
        $result = $query->result();
        $retval = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $this->db->where(array('ModifierID' => $row->ModifierID, 'DeviceID' => $deviceid));
                $detail  = $this->db->get('mastermodifierdetail')->result();
                if (count($detail) > 0) {
                    foreach ($detail as $k => $v) {
                        $this->db->from('mastermodifierdetailingredients');
                        $this->db->join('masteritem', 'masteritem.PerusahaanNo = mastermodifierdetailingredients.PerusahaanNo AND masteritem.DeviceID = mastermodifierdetailingredients.DeviceID AND masteritem.ItemID = mastermodifierdetailingredients.IngredientsID AND masteritem.DeviceNo = mastermodifierdetailingredients.IngredientsDeviceNo');
                        $this->db->where(array('mastermodifierdetailingredients.DeviceID' => $deviceid, 'mastermodifierdetailingredients.DetailID' => $v->DetailID, 'mastermodifierdetailingredients.DetailDeviceNo' => $v->DeviceNo));
                        $bahan  = $this->db->get()->result();
                        $v->bahan = $bahan;
                    }
                }

                array_push(
                    $retval,
                    array(
                        'ModifierID' => $row->ModifierID, 'DeviceNo' => $row->DeviceNo, 'ChooseOnlyOne' => $row->ChooseOneOnly,
                        'CanAddQuantity' => $row->CanAddQuantity,
                        'ModifierName' => $row->ModifierName, 'OldModifierName' => $row->ModifierName,
                        'PlaceholderPilihan' => $row->ModifierName, 'Selected' => false,
                        'detail' => $detail
                    )
                );
            }
        }
        return $retval;
    }

    public function getModifierByID($modifierid, $deviceid, $deviceNo)
    {
        $this->db->where(array('ModifierID' => $modifierid, 'DeviceID' => $deviceid, 'DeviceNo' => $deviceNo));
        $query = $this->db->get($this->_tableName);
        $result = $query->result();
        $retval = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $this->db->where(array('ModifierID' => $row->ModifierID, 'DeviceID' => $deviceid));
                $detail  = $this->db->get('mastermodifierdetail')->result();
                if (count($detail) > 0) {
                    foreach ($detail as $k => $v) {
                        $this->db->from('mastermodifierdetailingredients');
                        $this->db->join('masteritem', 'masteritem.PerusahaanNo = mastermodifierdetailingredients.PerusahaanNo AND masteritem.DeviceID = mastermodifierdetailingredients.DeviceID AND masteritem.ItemID = mastermodifierdetailingredients.IngredientsID AND masteritem.DeviceNo = mastermodifierdetailingredients.IngredientsDeviceNo');
                        $this->db->where(array('mastermodifierdetailingredients.DeviceID' => $deviceid, 'mastermodifierdetailingredients.DetailID' => $v->DetailID, 'mastermodifierdetailingredients.DetailDeviceNo' => $v->DeviceNo));
                        $bahan  = $this->db->get()->result();
                        $v->bahan = $bahan;
                    }
                }

                array_push(
                    $retval,
                    array(
                        'ModifierID' => $row->ModifierID, 'DeviceNo' => $row->DeviceNo, 'ChooseOnlyOne' => $row->ChooseOneOnly,
                        'CanAddQuantity' => $row->CanAddQuantity,
                        'ModifierName' => $row->ModifierName, 'OldModifierName' => $row->ModifierName,
                        'PlaceholderPilihan' => $row->ModifierName, 'Selected' => false,
                        'detail' => $detail
                    )
                );
            }
        }
        return $retval;
    }

    public function getModifierOnlyByID($params)
    {
        $query = $this->db->get_where($this->_tableName, $params);
        return $query->row_array();
    }

    public function getDatatableListModifier($idoutlet)
    {
        $sql = "SELECT a.ModifierID, ModifierName, PilihanEkstra, totalDetail
            FROM mastermodifier a
            INNER JOIN (
                SELECT ModifierID, DeviceID, PerusahaanNO,
                REPLACE(GROUP_CONCAT('(',ChoiceName, ' Rp. ',ChoicePrice,')' ORDER BY ChoicePrice, ChoiceName),',',' ') AS 'PilihanEkstra',
                COALESCE(COUNT(*),0) AS 'totalDetail'
                FROM mastermodifierdetail
                GROUP BY DeviceID, PerusahaanNo, ModifierID
            ) b ON a.DeviceID = b.DeviceID AND a.PerusahaanNo = b.PerusahaanNo AND a.ModifierID = b.ModifierID
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? ORDER BY a.ModifierName ";
        $query = $this->db->query($sql, array(getPerusahaanNo(), $idoutlet));
        return $query->result_array();
    }

    public function getDatatablesModifier($params, $visibilityMenu)
    {
        $orderColumn = array(null, 'ModifierName', null, null);
        $orderType = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");

        // Get Total Data
        $length = $params['length'];
        $start = (int) $params['start'];
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'recordsTotal'
            FROM mastermodifier a
            INNER JOIN (
                SELECT ModifierID, DeviceID, ModifierDeviceNo, PerusahaanNO,
                REPLACE(GROUP_CONCAT('(',ChoiceName, ' Rp. ',ChoicePrice,')' ORDER BY ChoicePrice, ChoiceName),',',' ') AS 'PilihanEkstra',
                COALESCE(COUNT(*),0) AS 'totalDetail'
                FROM mastermodifierdetail
                GROUP BY DeviceID, ModifierDeviceNo, PerusahaanNo, ModifierID
            ) b ON a.DeviceID = b.DeviceID AND a.PerusahaanNo = b.PerusahaanNo AND a.ModifierID = b.ModifierID AND a.DeviceNo = b.ModifierDeviceNo
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ?  ";
        $query = $this->db->query($sql, array($params['PerusahaanNo'], $params['DeviceID']));
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
        $sql = "SELECT a.ModifierID, a.DeviceNo, ModifierName, PilihanEkstra, COALESCE(totalDetail,0) AS 'totalDetail', COALESCE(totalItem,0) AS 'totalItem'
            FROM mastermodifier a
            INNER JOIN (
                SELECT ModifierID, DeviceID, ModifierDeviceNo, PerusahaanNO,
                REPLACE(GROUP_CONCAT('(',ChoiceName, ' Rp. ',ChoicePrice,')' ORDER BY ChoicePrice, ChoiceName),',',' ') AS 'PilihanEkstra',
                COALESCE(COUNT(*),0) AS 'totalDetail'
                FROM mastermodifierdetail
                GROUP BY DeviceID, ModifierDeviceNo, PerusahaanNo, ModifierID
            ) b ON a.DeviceID = b.DeviceID AND a.PerusahaanNo = b.PerusahaanNo AND a.ModifierID = b.ModifierID AND a.DeviceNo = b.ModifierDeviceNo
            LEFT JOIN (
                SELECT PerusahaanNo, DeviceID, ModifierDeviceNo, ModifierID, COALESCE(COUNT(*),0) AS 'totalItem'
                FROM masteritemdetailmodifier
                GROUP BY PerusahaanNo, DeviceID, ModifierDeviceNo, ModifierID
            ) c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.DeviceNo = c.ModifierDeviceNo AND a.ModifierID = c.ModifierID
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND ( ModifierName LIKE ? OR b.PilihanEkstra LIKE ? ) " . $orderBy . " LIMIT ?,? ";
        $query = $this->db->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            $start,
            $length
        ));
        if ($query->num_rows() > 0) {
            $rs_result = $query->result();
            foreach ($rs_result as $i => $result) {
                $row = array();
                if ($result->totalItem > 0) {
                    $row[] = '<a href="javascript:void(0)" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" class="blue-sea modifier-details icon">
                                <i class="fa fa-chevron-right"></i>
                            </a>';
                    $row[] = '<a href="javascript:void(0)" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" class="blue-sea modifier-details">' . $result->ModifierName . '</a>';
                } else {
                    $row[] = '';
                    $row[] = '<a href="javascript:void(0)" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" class="blue-sea">' . $result->ModifierName . '</a>';
                }
                $row[] = $result->PilihanEkstra;

                if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete'] || $visibilityMenu['ItemAdd']) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<a href="javascript:void(0)" class="dropdown-toggle pull-right blue-sea" type="button" data-toggle="dropdown">';
                    $btn .= '<i class="fa fa-ellipsis-h"></i></a>';
                    $btn .= '<ul class="dropdown-menu dropdown-menu-right">';
                    if ($visibilityMenu['ItemEdit']) {
                        $btn .= '<li><a href="javascript:void(0)" class="btnUpdateModifier py-10" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" data-modifier-name="' . $result->ModifierName . '">Edit</a></li>';
                    }
                    if ($visibilityMenu['ItemAdd'] || $visibilityMenu['ItemEdit']) {
                        $btn .= '<li class="divider my-0"></li>
                                <li>
                                    <a href="javascript:void(0)" class="btnTerapkanProduk py-10" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" data-modifier-name="' . $result->ModifierName . '">Terapkan Produk</a>
                                </li>';
                    }
                    if ($visibilityMenu['ItemDelete']) {
                        $btn .= '<li class="divider my-0"></li>
                                <li>
                                    <a href="javascript:void(0)" class="btnDeleteModifier py-10" data-modifier-id="' . $result->ModifierID . '" data-device-no="' . $result->DeviceNo . '" data-modifier-name="' . $result->ModifierName . '">Hapus</a>
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
            FROM mastermodifier a
            INNER JOIN (
                SELECT ModifierID, DeviceID, ModifierDeviceNo, PerusahaanNO,
                REPLACE(GROUP_CONCAT('(',ChoiceName, ' Rp. ',ChoicePrice,')' ORDER BY ChoicePrice, ChoiceName),',',' ') AS 'PilihanEkstra',
                COALESCE(COUNT(*),0) AS 'totalDetail'
                FROM mastermodifierdetail
                GROUP BY DeviceID, ModifierDeviceNo, PerusahaanNo, ModifierID
            ) b ON a.DeviceID = b.DeviceID AND a.PerusahaanNo = b.PerusahaanNo AND a.ModifierID = b.ModifierID AND a.DeviceNo = b.ModifierDeviceNo
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND ( ModifierName LIKE ? OR b.PilihanEkstra LIKE ? )";
        $query = $this->db->query($sql, array(
            $params['PerusahaanNo'],
            $params['DeviceID'],
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

    public function getListDetailByModifier($ModifierID, $DeviceNo, $DeviceID)
    {
        $query = $this->db->order_by('ChoicePrice, ChoiceName')->get_where('mastermodifierdetail', array('PerusahaanNo' => getPerusahaanNo(), 'DeviceID' => $DeviceID, 'ModifierDeviceNo' => $DeviceNo, 'ModifierID' => $ModifierID));
        $result = $query->result_array();
        return $result;
    }

    public function getListItemsByModifier($ModifierID, $DeviceNo, $OutletID)
    {
        $sql = "SELECT a.*
            FROM masteritem a
            INNER JOIN masteritemdetailmodifier b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID AND a.ItemID = b.ItemID AND a.DeviceNo = b.ItemDeviceNo
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND b.ModifierID = ? AND b.ModifierDeviceNo = ?
            ORDER BY a.ItemName";
        $query = $this->db->query($sql, array(getPerusahaanNo(), $OutletID, $ModifierID, $DeviceNo));
        return $query->result_array();
    }

    public function getPilihan($modifierid, $deviceid)
    {
        $realid = explode(".", $modifierid)[0];
        $devno = explode(".", $modifierid)[1];

        $this->db->where(array('DeviceID' => $deviceid, 'ModifierID' => $realid, 'ModifierDeviceNo' => $devno));
        $query_pilihan = $this->db->get('mastermodifierdetail');
        $result_pilihan = $query_pilihan->result();

        $retval = array();
        foreach ($result_pilihan as $pilihan) {
            array_push($retval,
                ['NamaPilihan' => $pilihan->ChoiceName, 'Harga' => $pilihan->ChoicePrice,
                    'operation' => 'exist', 'oldName' => $pilihan->ChoiceName,
                    'QtyDibutuhkan' => $pilihan->QtyNeed,
                    'DetailID' => $pilihan->DetailID,
                    'DeviceNo' => $pilihan->DeviceNo,
                    'Satuan' => 'Gelas', 'PlaceholderPilihan' => $pilihan->ChoiceName]
            );
        }
        return $retval;
    }

    public function isExistModifierName($where)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertModifier($params, $details)
    {
        $this->initDbMaster();
        $this->load->model('Options');
        $options = $this->Options->get_by_devid($params['DeviceID']);
        $cloudDevno = 0;
        if ($options->CreatedVersionCode < 103 && $options->EditedVersionCode < 103) {
            $cloudDevno = 1;
        }
        $modifierID = $this->generateModifierID($params['DeviceID']);
        $paramsAdd = array(
            'ModifierID' => $modifierID,
            'DeviceNo' => $cloudDevno,
            'Varian' => 'Nuta',
            'HasBeenDownloaded' => 0,
            'IsDetailsSaved' => 1
        );
        $params = array_merge($params, $paramsAdd);

        $dataInsertModifier = array();
        $arrInsertModifierDetail = array();

        // Insert
        $this->_dbMaster->trans_start();
        $insert = $this->_dbMaster->insert($this->_tableName, $params); // insert mastermodifier

        if ($insert == true) {
            $Modifier = $this->_dbMaster->get_where($this->_tableName, array("ModifierID" => $modifierID, "DeviceID" => $params['DeviceID'], "PerusahaanNo" => $params['PerusahaanNo']));
            $dataInsertModifier = $Modifier->row_array();
        }


        // insert detail
        $detailid = $this->generatePilihanID($params['DeviceID']);
        foreach ($details as $key => $detail) {
            $ModifierDetail = array_merge($detail, array('DetailID' => $detailid, 'ModifierID' => $modifierID, 'ModifierDeviceNo' => $cloudDevno, 'DeviceNo' => $cloudDevno));
            $insert = $this->_dbMaster->insert('mastermodifierdetail', $ModifierDetail);

            if ($insert == true) {
                $ModifierDetail = $this->_dbMaster->get_where('mastermodifierdetail', array('DetailID' => $detailid, "ModifierID" => $modifierID, "DeviceID" => $params['DeviceID'], "PerusahaanNo" => $params['PerusahaanNo']));
                $arrInsertModifierDetail[$key] = $ModifierDetail->row_array();

                $detailid += 1;
            }
        }

        $this->_dbMaster->trans_complete();
        $transSt = $this->_dbMaster->trans_status();

        // Push to firebase
        if ($transSt == true) {

            // Modifier
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $dataInsertModifier
            );
            $this->Firebasemodel->push_firebase(
                $dataInsertModifier['DeviceID'],
                $last_insert_data,
                $dataInsertModifier['ModifierID'],
                $dataInsertModifier['DeviceNo'],
                $dataInsertModifier['PerusahaanNo'],
                0
            );

            // Modifier Detail
            foreach ($arrInsertModifierDetail as $ModifierDetail) {

                $last_insert_data = array(
                    "table" => 'mastermodifierdetail',
                    "column" => $ModifierDetail
                );
                $this->Firebasemodel->push_firebase(
                    $ModifierDetail['DeviceID'],
                    $last_insert_data,
                    $ModifierDetail['DetailID'],
                    $ModifierDetail['DeviceNo'],
                    $ModifierDetail['PerusahaanNo'],
                    0
                );
            }

            return true;
        } else {
            return false;
        }
    }

    public function updateModifier2($params, $where, $details)
    {

        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $oldModifier = $query->row_array();

        $paramsEdit = array(
            'RowVersion' => ($oldModifier['RowVersion'] + 1)
        );
        $params = array_merge($params, $paramsEdit);

        $dataUpdateModifier = array();
        $arrDeleteModifierDetail = array();
        $arrModifierDetail = array();
        $arrInsertUpdateModifierDetail = array();

        // Update
        $this->_dbMaster->trans_start();
        $update = $this->_dbMaster->update($this->_tableName, $params, $where); // update mastermodifier

        if ($update == true) {
            $Modifier = $this->_dbMaster->get_where($this->_tableName, $where);
            $dataUpdateModifier = $Modifier->row_array();
        }

        $query = $this->_dbMaster->get_where('mastermodifierdetail', $where);
        $rsModifierDetail = $query->result_array();
        foreach ($rsModifierDetail as $ModifierDetail) {
            $found = array_search($ModifierDetail['ChoiceName'], array_column($details, 'ChoiceName'));
            if (strval($found) == '') {
                // delete
                array_push($arrDeleteModifierDetail, $ModifierDetail);
                $where = array('DetailID' => $ModifierDetail['DetailID'], 'ModifierID' => $ModifierDetail['ModifierID'], 'DeviceID' => $ModifierDetail['DeviceID'], 'PerusahaanNo' => $ModifierDetail['PerusahaanNo'], 'ModifierDeviceNo' => $dataUpdateModifier['DeviceNo']);
                $query = $this->_dbMaster->get_where('mastermodifierdetaildelete', $where);
                if ($query->num_rows() > 0) {
                    $this->_dbMaster->delete('mastermodifierdetaildelete', $where);
                }

                $this->_dbMaster->insert('mastermodifierdetaildelete', $ModifierDetail);
                $this->_dbMaster->delete('mastermodifierdetail', $where);
            } else {
                // update
                if ($ModifierDetail['ChoicePrice'] != $details[$found]['ChoicePrice'] || $ModifierDetail['QtyNeed'] != $details[$found]['QtyNeed']) {
                    $where = array('DetailID' => $ModifierDetail['DetailID'], 'ModifierID' => $ModifierDetail['ModifierID'], 'DeviceID' => $ModifierDetail['DeviceID'], 'PerusahaanNo' => $ModifierDetail['PerusahaanNo'], 'ModifierDeviceNo' => $dataUpdateModifier['DeviceNo']);
                    $query = $this->_dbMaster->get_where('mastermodifierdetail', $where);
                    $oldModifierDetail = $query->row_array();
                    $this->_dbMaster->update('mastermodifierdetail', array_merge($details[$found], array('RowVersion' => ($oldModifierDetail['RowVersion'] + 1))), $where);
                    $query = $this->_dbMaster->get_where('mastermodifierdetail', $where);
                    $newModifierDetail = $query->row_array();
                    array_push($arrInsertUpdateModifierDetail, $newModifierDetail);
                    array_push($arrModifierDetail, $newModifierDetail);
                } else {
                    array_push($arrModifierDetail, $ModifierDetail);
                }
            }
        }


        // insert detail
        $detailid = $this->generatePilihanID($params['DeviceID']);
        foreach ($details as $key => $detail) {
            $found = array_search($detail['ChoiceName'], array_column($rsModifierDetail, 'ChoiceName'));
            if (strval($found) == '') {
                $ModifierDetail = array_merge($detail, array('DetailID' => $detailid, 'ModifierID' => $dataUpdateModifier['ModifierID'], 'ModifierDeviceNo' => $dataUpdateModifier['DeviceNo'], 'DeviceNo' => $dataUpdateModifier['DeviceNo']));
                $insert = $this->_dbMaster->insert('mastermodifierdetail', $ModifierDetail);

                if ($insert == true) {
                    $ModifierDetail = $this->_dbMaster->get_where('mastermodifierdetail', array('DetailID' => $detailid, "ModifierID" => $dataUpdateModifier['ModifierID'], "DeviceID" => $dataUpdateModifier['DeviceID'], "PerusahaanNo" => $dataUpdateModifier['PerusahaanNo']));
                    array_push($arrInsertUpdateModifierDetail, $ModifierDetail->row_array());

                    $detailid += 1;
                }
            }
        }

        $this->_dbMaster->trans_complete();
        $transSt = $this->_dbMaster->trans_status();

        // Push to firebase
        if ($transSt == true) {

            // Modifier
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $dataUpdateModifier
            );
            $this->Firebasemodel->push_firebase(
                $dataUpdateModifier['DeviceID'],
                $last_insert_data,
                $dataUpdateModifier['ModifierID'],
                $dataUpdateModifier['DeviceNo'],
                $dataUpdateModifier['PerusahaanNo'],
                0
            );

            // Modifier Detail
            foreach ($arrDeleteModifierDetail as $ModifierDetail) {

                $deleted_data = array(
                    "table" => 'deletemastermodifierdetail',
                    "column" => array(
                        "DetailID" => $ModifierDetail['DetailID'],
                        'DeviceNo' => $ModifierDetail['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $ModifierDetail['DeviceID'],
                    $deleted_data,
                    $ModifierDetail['DetailID'],
                    $ModifierDetail['DeviceNo'],
                    $ModifierDetail['PerusahaanNo'],
                    0
                );
            }

            foreach ($arrInsertUpdateModifierDetail as $ModifierDetail) {

                $last_insert_data = array(
                    "table" => 'mastermodifierdetail',
                    "column" => $ModifierDetail
                );
                $this->Firebasemodel->push_firebase(
                    $ModifierDetail['DeviceID'],
                    $last_insert_data,
                    $ModifierDetail['DetailID'],
                    $ModifierDetail['DeviceNo'],
                    $ModifierDetail['PerusahaanNo'],
                    0
                );
            }

            return true;
        } else {
            return false;
        }
    }

    public function deleteModifier($where)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $oldModifier = $query->row_array();


        $arrModifierDetail = array();
        $arrModifierItems = array();
        $this->_dbMaster->trans_start();
        $query = $this->_dbMaster->get_where('mastermodifierdelete', $where);
        if ($query->num_rows() > 0) {
            $this->_dbMaster->delete('mastermodifierdelete', $where);
        }
        $this->_dbMaster->insert('mastermodifierdelete', $oldModifier);

        $this->_dbMaster->delete($this->_tableName, $where);

        // Get All Modifier Detail
        $query = $this->_dbMaster->get_where('mastermodifierdetail', array(
            'PerusahaanNo' => $oldModifier['PerusahaanNo'],
            'DeviceID' => $oldModifier['DeviceID'],
            'ModifierDeviceNo' => $oldModifier['DeviceNo'],
            'ModifierID' => $oldModifier['ModifierID'],
        ));
        $rsModifierDetail = $query->result_array();
        foreach ($rsModifierDetail as $ModifierDetail) {
            array_push($arrModifierDetail, $ModifierDetail);
            $where = array('DetailID' => $ModifierDetail['DetailID'], 'ModifierID' => $ModifierDetail['ModifierID'], 'DeviceID' => $ModifierDetail['DeviceID'], 'PerusahaanNo' => $ModifierDetail['PerusahaanNo'], 'ModifierDeviceNo' => $oldModifier['DeviceNo']);
            $query = $this->_dbMaster->get_where('mastermodifierdetaildelete', $where);
            if ($query->num_rows() > 0) {
                $this->_dbMaster->delete('mastermodifierdetaildelete', $where);
            }

            $this->_dbMaster->insert('mastermodifierdetaildelete', $ModifierDetail);
            $this->_dbMaster->delete('mastermodifierdetail', $where);
        }

        // Get All Modifier Items
        $query = $this->_dbMaster->get_where('masteritemdetailmodifier', array(
            'PerusahaanNo' => $oldModifier['PerusahaanNo'],
            'DeviceID' => $oldModifier['DeviceID'],
            'ModifierDeviceNo' => $oldModifier['DeviceNo'],
            'ModifierID' => $oldModifier['ModifierID'],
        ));
        $rsModifierItem = $query->result_array();
        foreach ($rsModifierItem as $ModifierItem) {
            array_push($arrModifierItems, $ModifierItem);
            $where = array('DetailID' => $ModifierItem['DetailID'], 'ModifierID' => $ModifierItem['ModifierID'], 'DeviceID' => $ModifierItem['DeviceID'], 'PerusahaanNo' => $ModifierItem['PerusahaanNo'], 'ModifierDeviceNo' => $oldModifier['DeviceNo']);
            $query = $this->_dbMaster->get_where('masteritemdetailmodifierdelete', $where);
            if ($query->num_rows() > 0) {
                $this->_dbMaster->delete('masteritemdetailmodifierdelete', $where);
            }

            $this->_dbMaster->insert('masteritemdetailmodifierdelete', $ModifierItem);
            $this->_dbMaster->delete('masteritemdetailmodifier', $where);
        }

        $this->_dbMaster->trans_complete();
        $transSt = $this->_dbMaster->trans_status();

        // Push to firebase
        if ($transSt == true) {

            // Modifier
            $deleted_data = array(
                "table" => "delete" . $this->_tableName,
                "column" => array(
                    "ModifierID" => $oldModifier['ModifierID'],
                    "DeviceNo" => $oldModifier['DeviceNo']
                )
            );
            $this->Firebasemodel->push_firebase(
                $oldModifier['DeviceID'],
                $deleted_data,
                $oldModifier['ModifierID'],
                $oldModifier['DeviceNo'],
                $oldModifier['PerusahaanNo'],
                0
            );

            // Modifier Detail
            foreach ($arrModifierDetail as $ModifierDetail) {

                $deleted_data = array(
                    "table" => 'deletemastermodifierdetail',
                    "column" => array(
                        "DetailID" => $ModifierDetail['DetailID'],
                        'DeviceNo' => $ModifierDetail['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $ModifierDetail['DeviceID'],
                    $deleted_data,
                    $ModifierDetail['DetailID'],
                    $ModifierDetail['DeviceNo'],
                    $ModifierDetail['PerusahaanNo'],
                    0
                );
            }

            // Modifier Item
            foreach ($arrModifierItems as $ModifierItem) {

                $deleted_data = array(
                    "table" => 'deletemasteritemdetailmodifier',
                    "column" => array(
                        "DetailID" => $ModifierItem['DetailID'],
                        'DeviceNo' => $ModifierItem['DeviceNo']
                    )
                );
                $this->Firebasemodel->push_firebase(
                    $ModifierItem['DeviceID'],
                    $deleted_data,
                    $ModifierItem['DetailID'],
                    $ModifierItem['DeviceNo'],
                    $ModifierItem['PerusahaanNo'],
                    0
                );
            }

            return true;
        } else {
            return false;
        }
    }

    public function createModifier($idoutlet, $modifierName, $chooseOne, $canAddQty, $perusahaanNo)
    {
        $this->initDbMaster();
        $queryCek = $this->_dbMaster->get_where($this->_tableName, array('DeviceID' => $idoutlet, 'ModifierName' => $modifierName));
        $numCek = $queryCek->num_rows();
        $isExist = $numCek > 0;
        if (!$isExist) {

            $this->load->model('Options');
            $options = $this->Options->get_by_devid($idoutlet);
            $cloudDevno = 0;
            if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
                $cloudDevno = 1;
            }
            $modifierID = $this->generateModifierID($idoutlet);

            $attrib_mastermodifier = array(
                'ModifierID' => $modifierID,
                'DeviceNo' => $cloudDevno,
                'DeviceID' => $idoutlet,
                'ModifierName' => $modifierName,
                'ChooseOneOnly' => $chooseOne,
                'CanAddQuantity' => $canAddQty,
                'Varian' => 'Nuta',
                'HasBeenDownloaded' => 0,
                'IsDetailsSaved' => 1,
                'PerusahaanNo' => $perusahaanNo
            );

            $result_insert_mastermodifier = $this->_dbMaster->insert($this->_tableName, $attrib_mastermodifier);


            if ($result_insert_mastermodifier) {
                //push to firebase
                $insert_query = $this->_dbMaster->get_where($this->_tableName, array(
                    'ModifierID' => $modifierID,
                    'DeviceNo' => $cloudDevno,
                    'DeviceID' => $idoutlet,
                    'PerusahaanNo' => $perusahaanNo
                ));
                $last_insert_data = array(
                    "table" => $this->_tableName,
                    "column" => $insert_query->row_array()
                );
                $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                    $modifierID, $cloudDevno, $perusahaanNo, 0);
            }

            return $modifierID . "." . $cloudDevno;
        } else {
            return "-1.0";
        }
    }

    public function updateModifier($idoutlet, $oldModifierName, $namaModifier, $chooseOnlyOne, $canAddQuantity, $perusahaanNo)
    {
        $this->initDbMaster();
        if ($oldModifierName != $namaModifier) {
            $query_cek_nama_modifier = $this->db->get_where($this->_tableName, array('DeviceID' => $idoutlet, 'ModifierName' => $namaModifier));
            $jumlah_nama_modifier = $query_cek_nama_modifier->num_rows();
            $is_nama_modifier_exist = ($jumlah_nama_modifier > 0);
            if ($is_nama_modifier_exist) {
                $query_dataupdated = $this->_dbMaster->get_where($this->_tableName, array(
                    'ModifierName' => $namaModifier,
                    'DeviceID' => $idoutlet,
                    'PerusahaanNo' => $perusahaanNo
                ));
                $rows = $query_dataupdated->row_array();
                return $rows['ModifierID'] . "." . $rows['DeviceNo'];
            }
        }
        /*UPDATE*/
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ModifierName' => $oldModifierName));
        $this->_dbMaster->update($this->_tableName, array('DeviceID' => $idoutlet, 'ModifierName' => $namaModifier,
            'ChooseOneOnly  ' => $chooseOnlyOne, 'CanAddQuantity' => $canAddQuantity, 'HasBeenDownloaded' => 0));
        /*GET UPDATEDID*/
        //push to firebase
        $query_dataupdated = $this->_dbMaster->get_where($this->_tableName, array(
            'ModifierName' => $namaModifier,
            'DeviceID' => $idoutlet,
            'PerusahaanNo' => $perusahaanNo
        ));
        $rows = $query_dataupdated->row_array();
        if (isset($rows) && count($rows) > 0) {
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $rows
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                $rows['ModifierID'], $rows['DeviceNo'], $perusahaanNo, 0);
            return $rows['ModifierID'] . "." . $rows['DeviceNo'];
        } else {
            return $this->createModifier($idoutlet,$namaModifier, $chooseOnlyOne, $canAddQuantity, getPerusahaanNo());
        }
        return "-1.0";
    }

    public function getPilihanbyName($idoutlet, $choiceName, $idmodifier)
    {
        $this->db->where(["DeviceID" => $idoutlet, "ChoiceName" => $choiceName, "ModifierID" => $idmodifier]);
        $return = $this->db->get("mastermodifierdetail")->result();
        return $return;
    }

    public function createPilihan($idoutlet, $idmodifier, $choiceName, $choicePrice, $qtyNeed, $perusahaanNo)
    {
        $realid = explode(".", $idmodifier)[0];
        $devno = explode(".", $idmodifier)[1];
        $this->initDbMaster();
        $detailid = $this->generatePilihanID($idoutlet);

        $this->load->model('Options');
        $options = $this->Options->get_by_devid($idoutlet);
        $cloudDevno = 0;
        if($options->CreatedVersionCode<103 && $options->EditedVersionCode<103) {
            $cloudDevno = 1;
        }

        $attrib_mastermodifierdetail = array(
            'DetailID' => $detailid,
            'DeviceNo' => $cloudDevno,
            'DeviceID' => $idoutlet,
            'ChoiceName' => $choiceName,
            'ChoicePrice' => $choicePrice,
            'QtyNeed' => $qtyNeed,
            'ModifierID' => $realid,
            'ModifierDeviceNo' => $devno,
            'Varian' => 'Nuta',
            'HasBeenDownloaded' => 0,
            'PerusahaanNo' => $perusahaanNo
        );

        $result_mastermodifierdetail = $this->_dbMaster->insert('mastermodifierdetail', $attrib_mastermodifierdetail);

        if ($result_mastermodifierdetail) {
            //push to firebase
            $insert_query = $this->_dbMaster->get_where('mastermodifierdetail', array(
                'DetailID' => $detailid,
                'DeviceNo' => $cloudDevno,
                'DeviceID' => $idoutlet,
                'PerusahaanNo' => $perusahaanNo
            ));
            $last_insert_data = array(
                "table" => 'mastermodifierdetail',
                "column" => $insert_query->row_array()
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                $detailid, $cloudDevno, $perusahaanNo, 0);
        }


    }

    public function updatePilihan($idoutlet, $idModifier, $oldName, $choiceName, $choicePrice, $qtyNeed, $perusahaanNo)
    {
        $realid = explode(".", $idModifier)[0];
        $devno = explode(".", $idModifier)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ChoiceName' => $oldName,
            'ModifierID' => $realid, 'DeviceNo' => $devno));
        $query = $this->_dbMaster->get('mastermodifierdetail');
        $count = $query->num_rows();
//
//        $queryCek = $this->db->get_where('mastermodifierdetail', array('DeviceID' => $idoutlet, 'ChoiceName' => $choiceName, 'ModifierID' => $idModifier));
//        $countCek = $queryCek->num_rows();


        if ($count > 0) {
            $result = $query->row();
            $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ChoiceName' => $oldName,
                'ModifierID' => $realid, 'DeviceNo' => $devno));
            $this->_dbMaster->update('mastermodifierdetail', array(
                'ChoiceName' => $choiceName, 'ChoicePrice' => $choicePrice, 'QtyNeed' => $qtyNeed,
                'HasBeenDownloaded' => 0));

            //push to firebase
            $query_dataupdated = $this->_dbMaster->get_where('mastermodifierdetail', array(
                'DeviceID' => $idoutlet, 'ChoiceName' => $choiceName, 'ModifierID' => $realid, 'DeviceNo' => $devno
            ));
            $last_insert_data = array(
                "table" => 'mastermodifierdetail',
                "column" => $query_dataupdated->row_array()
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                $realid, $devno, $perusahaanNo, 0);
        } else {
            $this->createPilihan($idoutlet, $idModifier, $choiceName, $choicePrice, $qtyNeed, $perusahaanNo);
            return false;
        }

        return $count > 0;
    }

    public function updatePilihan2($idoutlet, $idModifier, $detailid, $choiceName, $choicePrice, $qtyNeed, $perusahaanNo)
    {
        $realid = explode(".", $idModifier)[0];
        $devno = explode(".", $idModifier)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array(
            'DeviceID' => $idoutlet, 'DetailID' => $detailid,
            'ModifierID' => $realid, 'DeviceNo' => $devno
        ));
        $query = $this->_dbMaster->get('mastermodifierdetail');
        $count = $query->num_rows();
        //
        //        $queryCek = $this->db->get_where('mastermodifierdetail', array('DeviceID' => $idoutlet, 'ChoiceName' => $choiceName, 'ModifierID' => $idModifier));
        //        $countCek = $queryCek->num_rows();


        if ($count > 0) {
            $result = $query->row();
            $this->_dbMaster->where(array(
                'DeviceID' => $idoutlet, 'DetailID' => $detailid,
                'ModifierID' => $realid, 'DeviceNo' => $devno
            ));
            $this->_dbMaster->update('mastermodifierdetail', array(
                'ChoiceName' => $choiceName, 'ChoicePrice' => $choicePrice, 'QtyNeed' => $qtyNeed,
                'HasBeenDownloaded' => 0
            ));

            //push to firebase
            $query_dataupdated = $this->_dbMaster->get_where('mastermodifierdetail', array(
                'DeviceID' => $idoutlet, 'ChoiceName' => $choiceName, 'ModifierID' => $realid, 'DeviceNo' => $devno
            ));
            $last_insert_data = array(
                "table" => 'mastermodifierdetail',
                "column" => $query_dataupdated->row_array()
            );
            $this->Firebasemodel->push_firebase(
                $idoutlet,
                $last_insert_data,
                $realid,
                $devno,
                $perusahaanNo,
                0
            );
        } else {
            $this->createPilihan($idoutlet, $idModifier, $choiceName, $choicePrice, $qtyNeed, $perusahaanNo);
            return false;
        }

        return $detailid;
    }


    public function hapusModifier($idoutlet, $namamodifier, $perusahaanNo)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ModifierName' => $namamodifier));
        $query = $this->_dbMaster->get($this->_tableName);
        $rows = $query->result_array();

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ModifierName' => $namamodifier));
        $this->_dbMaster->delete($this->_tableName);

        if (count($rows) > 0) {
            $rows[0]['HasBeenDownloaded'] = 0;
            $this->_dbMaster->insert('mastermodifierdelete', $rows[0]);

            $deleted_data = array(
                "table" => "deletemastermodifier",
                "column" => array(
                    "ModifierID" => $rows[0]['ModifierID'],
                    "DeviceNo" => $rows[0]['DeviceNo']
                )
            );
            $this->Firebasemodel->push_firebase($idoutlet, $deleted_data,
                $rows[0]['ModifierID'], $rows[0]['DeviceNo'], $perusahaanNo, 0);
            return $rows[0]['ModifierID'] . "." . $rows[0]['DeviceNo'];
        }
        return 0;
    }

    public function hapusPilihanByDetailID($deviceid, $detailid, $perusahaanNo)
    {
        $realid = explode(".", $detailid)[0];
        $devno = explode(".", $detailid)[1];
        $this->initDbMaster();
        try {
            $this->_dbMaster->where(array('DetailID' => $realid,
                'DeviceNo' => $devno, 'DeviceID' => $deviceid));
            $query_deleted_bahan_ex = $this->_dbMaster->get('mastermodifierdetaildelete');
            if (count($query_deleted_bahan_ex->result_array()) == 0) {
                $this->_dbMaster->where(array('DetailID' => $realid, 'DeviceID' => $deviceid, 'DeviceNo' => $devno));
                $query_deleted_bahan = $this->_dbMaster->get('mastermodifierdetail');
                $deleted_bahan = $query_deleted_bahan->result_array();

                $deleted_bahan[0]['HasBeenDownloaded'] = 0;
                $this->_dbMaster->insert('mastermodifierdetaildelete', $deleted_bahan[0]);
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }
        $this->_dbMaster->where(array('DetailID' => $realid, 'DeviceNo' => $devno, 'DeviceID' => $deviceid));
        $this->_dbMaster->delete('mastermodifierdetail');

        $deleted_data = array(
            "table" => 'deletemastermodifierdetail',
            "column" => array(
                "DetailID" => $realid,
                'DeviceNo' => $devno
            )
        );
        $this->Firebasemodel->push_firebase($deviceid, $deleted_data,
            $realid, $devno, $perusahaanNo, 0);
    }

    public function hapusPilihan($idoutlet, $namapilihan, $modifierid, $perusahaanNo)
    {
        $realid = explode(".", $modifierid)[0];
        $devno = explode(".", $modifierid)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'DeviceNo' => $devno,
            'ModifierID' => $realid, 'ChoiceName' => $namapilihan));
        $query = $this->_dbMaster->get('mastermodifierdetail');
        $rows = $query->result_array();
        $rows[0]['HasBeenDownloaded'] = 0;
        $this->_dbMaster->insert('mastermodifierdetaildelete', $rows[0]);

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'DeviceNo' => $devno,
            'ModifierID' => $realid, 'ChoiceName' => $namapilihan));
        $this->_dbMaster->delete('mastermodifierdetail');

        $deleted_data = array(
            "table" => "deletemastermodifierdetail",
            "column" => array(
                "DetailID" => $rows[0]['DetailID'],
                "DeviceNo" => $rows[0]['DeviceNo']
            )
        );
        $this->Firebasemodel->push_firebase($idoutlet, $deleted_data,
            $rows[0]['DetailID'], $rows[0]['DeviceNo'], $perusahaanNo, 0);
    }

    public function hapusPilihanByModifierID($idoutlet, $modifierid, $perusahaanNo)
    {
        $realid = explode(".", $modifierid)[0];
        $devno = explode(".", $modifierid)[1];
        $this->initDbMaster();
        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ModifierID' => $realid, 'DeviceNo' => $devno));
        $q = $this->_dbMaster->get('mastermodifierdetail');
        $rows = $q->result_array();


        $arr = array();
        for ($a = 0; $a < count($rows); $a++) {
            $rows[$a]['HasBeenDownloaded'] = 0;
            array_push($arr, $rows[$a]);

            $deleted_data = array(
                "table" => "deletemastermodifierdetail",
                "column" => array(
                    'DetailID' => $rows[$a]['DetailID'],
                    'DeviceNo' => $rows[$a]['DeviceNo']
                )
            );
            $this->Firebasemodel->push_firebase($idoutlet, $deleted_data,
                $rows[$a]['DetailID'], $rows[$a]['DeviceNo'], $perusahaanNo, 0);
        }
        if (count($arr) > 0) {
            $this->_dbMaster->insert_batch('mastermodifierdetaildelete', $arr);
        }

        $this->_dbMaster->where(array('DeviceID' => $idoutlet, 'ModifierID' => $modifierid, 'DeviceNo' => $devno));
        $this->_dbMaster->delete('mastermodifierdetail');

    }

    private function generateModifierID($deviceid)
    {
        $sql = "
        SELECT
          COALESCE (MAX(ModifierID),0) +1 as ID
        FROM
        (SELECT
            ModifierID
        FROM
            mastermodifier
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . " UNION ALL SELECT
            ModifierID
        FROM
            mastermodifierdelete
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . ") a
        ";
        $query = $this->db->query($sql);
        $row = $query->row();
        return $row->ID;
    }

    private function generatePilihanID($deviceid)
    {
        $sql = "
        SELECT
          COALESCE (MAX(DetailID),0) +1 as ID
        FROM
        (SELECT
            DetailID
        FROM
            mastermodifierdetail
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . " UNION ALL SELECT
            DetailID
        FROM
           mastermodifierdetaildelete
        WHERE
            DeviceID = " . $this->db->escape($deviceid) . ") a
        ";

        $query = $this->db->query($sql);
        $row = $query->row();
        return $row->ID;
    }

    public function getModifierIDByName($idoutlet, $modfierName)
    {

        $this->db->where(array('DeviceID' => $idoutlet, 'ModifierName' => $modfierName));
        $q = $this->db->get('mastermodifier');
        $r = $q->row();
        if (isset($r)) {
            return $r->ModifierID . "." . $r->DeviceNo;
        } else {
            return "NO_MODIFIER_CANT_UPDATE";
        }
    }

    public function tesmodifier($namaModifier, $idoutlet)
    {
        $this->initDbMaster();
        $query_dataupdated = $this->_dbMaster->get_where($this->_tableName, array(
            'ModifierName' => $namaModifier,
            'DeviceID' => $idoutlet,
            'PerusahaanNo' => getPerusahaanNo()
        ));
        $rows = $query_dataupdated->row_array();
        if (isset($rows) && count($rows) > 0) {
            $last_insert_data = array(
                "table" => $this->_tableName,
                "column" => $rows
            );
            $this->Firebasemodel->push_firebase($idoutlet, $last_insert_data,
                $rows['ModifierID'], $rows['DeviceNo'], getPerusahaanNo(), 0);
            var_dump($rows);
            //return $rows[0]['ModifierID'];
        }
    }

    public function createModifierbahan($idoutlet, $detailid, $ingredientsid, $perusahaanNo, $devid, $qty, $detailNumber)
    {
        $cloudDevno = 0;

        $attrib_mastermodifierdetail = array(
            'DetailID' => $detailid,
            'DeviceNo' => $cloudDevno,
            'DeviceID' => $idoutlet,
            'IngredientsID' => $ingredientsid,
            'TglJamUpdate' => date('Y-m-d H:i:s'),
            'DeviceID' => $devid,
            'QtyNeed' => $qty,
            'PerusahaanNo' => $perusahaanNo,
            'DetailNumber' => $detailNumber
        );

        $result_mastermodifierdetail = $this->db->insert('mastermodifierdetailingredients', $attrib_mastermodifierdetail);
        $return = ["status" => $result_mastermodifierdetail];
        return json_encode($return);
    }

    public function getModifierBahanByName($idoutlet, $detailid, $ingredientsid, $perusahaanNo, $devid, $qty, $detailNumber)
    {
        $this->db->where(["DeviceID" => $devid, "DetailID" => $detailid, "IngredientsID" => $ingredientsid]);
        $return = $this->db->get('mastermodifierdetailingredients')->result();
        return $return;
    }

    public function updateModifierbahan($idoutlet, $detailid, $ingredientsid, $perusahaanNo, $devid, $qty, $detailNumber, $id)
    {
        $cloudDevno = 0;

        $attrib_mastermodifierdetail = array(
            'DetailID' => $detailid,
            'DeviceNo' => $cloudDevno,
            'DeviceID' => $idoutlet,
            'IngredientsID' => $ingredientsid,
            'TglJamUpdate' => date('Y-m-d H:i:s'),
            'DeviceID' => $devid,
            'QtyNeed' => $qty,
            'PerusahaanNo' => $perusahaanNo,
            'DetailNumber' => $detailNumber
        );

        $this->db->where('ID', $id);
        $result_mastermodifierdetail = $this->db->update('mastermodifierdetailingredients', $attrib_mastermodifierdetail);
        $return = ["status" => $result_mastermodifierdetail];
        return $id;
    }

    public function hapusBahanByDetailID($outlet_ids, $detail)
    {
        $select = $this->db->where(["DeviceID" => $outlet_ids, "DetailID" => $detail])->get('mastermodifierdetailingredients');
        if ($select->num_rows()) {
            $insert = $this->db->insert('mastermodifierdetailingredientsdelete', $select->result_array());
        }
        $this->db->where(["DeviceID" => $outlet_ids, "DetailID" => $detail]);
        $this->db->delete('mastermodifierdetailingredients');
    }

    public function hapusBahanByID($id, $outlet_id)
    {
        $select = $this->db->where(["DeviceID" => $outlet_id, "ID" => $id])->get('mastermodifierdetailingredients');
        if ($select->num_rows()) {
            $insert = $this->db->insert('mastermodifierdetailingredientsdelete', $select->result_array());
        }
        $this->db->where(["DeviceID" => $outlet_id, "ID" => $id]);
        $this->db->delete('mastermodifierdetailingredients');
    }

    public function isInMultiOutlet($namaitem, $perusahaanID)
    {
        $query = $this->db->query("select OutletID from outlet where outletid in (select DeviceID from mastermodifier where ModifierName="
            . $this->db->escape($namaitem) .
            ") AND PerusahaanID=" . $this->db->escape($perusahaanID));
        $result = $query->result();
        $retval = array();
        foreach ($result as $r) {
            array_push($retval, $r->OutletID);
        }
        return $retval;
    }

    public function getListProdukBelumTerapkan($params)
    {
        $this->initDbMaster();
        $sql = "SELECT IF(b.ItemID IS NOT NULL,'checked','unchecked') AS 'status', a.ItemID, a.ItemName, a.CategoryID, c.CategoryName, a.DeviceNo
        FROM masteritem a
        LEFT JOIN masteritemdetailmodifier b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID
            AND a.ItemID = b.ItemID AND b.ModifierID = ? AND b.ModifierDeviceNo = ?
        LEFT JOIN mastercategory c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.CategoryID = c.CategoryID
        WHERE a.IsProduct = 'true' AND a.PerusahaanNo = ? AND a.DeviceID = ?
        ORDER BY c.CategoryName, a.ItemName ";
        $query = $this->_dbMaster->query($sql, $params);
        return $query->result_array();
    }

    public function countItemsModifier($params)
    {
        $this->initDbMaster();
        $sql = "SELECT COALESCE(COUNT(*),0) AS 'total'
        FROM masteritem a
        INNER JOIN masteritemdetailmodifier b ON a.PerusahaanNo = b.PerusahaanNo AND a.DeviceID = b.DeviceID
            AND a.ItemID = b.ItemID AND b.ModifierID = ? AND b.ModifierDeviceNo = ?
        LEFT JOIN mastercategory c ON a.PerusahaanNo = c.PerusahaanNo AND a.DeviceID = c.DeviceID AND a.CategoryID = c.CategoryID
        WHERE a.IsProduct = 'true' AND a.PerusahaanNo = ? AND a.DeviceID = ? ";
        $query = $this->_dbMaster->query($sql, $params);
        $result = $query->row();
        return $result->total;
    }

    public function insertItemModifier($where, $rsItemsModifier)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        $modifier = $query->row_array();

        if (!$modifier) {
            return FALSE;
        }

        $arrDelete = array();
        $arrInsert = array();

        $this->_dbMaster->trans_start();

        // Get modifier produk sekarang
        $sql = "SELECT a.*, CONCAT(a.ItemDeviceNo,'-',a.ItemID) AS 'ItemKey' FROM masteritemdetailmodifier a
            WHERE a.PerusahaanNo = ? AND a.DeviceID = ? AND a.ModifierDeviceNo = ? AND a.ModifierID = ? ";
        $query = $this->_dbMaster->query($sql, array($modifier['PerusahaanNo'], $modifier['DeviceID'], $modifier['DeviceNo'], $modifier['ModifierID']));
        $rsItemModifierExist = $query->result_array();

        // Delete
        if ($rsItemModifierExist) {
            foreach ($rsItemModifierExist as $i => $ItemModifierExist) {
                $found = array_search($ItemModifierExist['ItemKey'], array_column($rsItemsModifier, 'ItemKey'));
                if (strval($found) == '') {
                    // delete
                    $newItemModifierExist = $ItemModifierExist;
                    unset($newItemModifierExist['ItemKey']);
                    array_push($arrDelete, $newItemModifierExist);
                    $where = array('ItemID' => $ItemModifierExist['ItemID'], 'ItemDeviceNo' => $ItemModifierExist['ItemDeviceNo'], 'ModifierID' => $ItemModifierExist['ModifierID'], 'DeviceID' => $ItemModifierExist['DeviceID'], 'ModifierDeviceNo' => $modifier['DeviceNo'], 'PerusahaanNo' => $ItemModifierExist['PerusahaanNo']);
                    $query = $this->_dbMaster->get_where('masteritemdetailmodifierdelete', $where);
                    if ($query->num_rows() > 0) {
                        $this->_dbMaster->delete('masteritemdetailmodifierdelete', $where);
                    }

                    $this->_dbMaster->insert('masteritemdetailmodifierdelete', $newItemModifierExist);
                    $this->_dbMaster->delete('masteritemdetailmodifier', $where);
                }
            }
        }

        // Insert new
        $detail = array('DeviceID' => $modifier['DeviceID'], 'PerusahaanNo' => $modifier['PerusahaanNo'], 'Varian' => 'Nuta');
        $detailid = $this->getMaxModifierDetailID($where['DeviceID']);
        if ($rsItemModifierExist) {
            foreach ($rsItemsModifier as $ItemsModifier) {
                $found = array_search($ItemsModifier['ItemKey'], array_column($rsItemModifierExist, 'ItemKey'));
                if (strval($found) == '') {
                    $itemQuery = $this->_dbMaster->get_where('masteritem', array('ItemID' => $ItemsModifier['ItemID'], 'DeviceID' => $modifier['DeviceID'], 'DeviceNo' => $ItemsModifier['ItemDeviceNo'], 'PerusahaanNo' => $modifier['PerusahaanNo']));
                    $item = $itemQuery->row_array();
                    $ItemModifier = array_merge($detail, array('DetailID' => $detailid, 'ItemID' => $ItemsModifier['ItemID'], 'ModifierID' => $modifier['ModifierID'], 'ItemDeviceNo' => $item['DeviceNo'], 'ModifierDeviceNo' => $modifier['DeviceNo'], 'DeviceNo' => $modifier['DeviceNo']));
                    unset($ItemsModifier['ItemKey']);
                    $insert = $this->_dbMaster->insert('masteritemdetailmodifier', $ItemModifier);

                    if ($insert == true) {
                        $ItemModifier = $this->_dbMaster->get_where('masteritemdetailmodifier', array('DetailID' => $detailid, 'ItemID' => $ItemsModifier['ItemID'], "ModifierID" => $modifier['ModifierID'], "DeviceID" => $modifier['DeviceID'], "PerusahaanNo" => $modifier['PerusahaanNo']));
                        array_push($arrInsert, $ItemModifier->row_array());

                        $detailid += 1;
                    }
                }
            }
        } else {
            foreach ($rsItemsModifier as $ItemsModifier) {
                unset($ItemsModifier['ItemKey']);
                $itemQuery = $this->_dbMaster->get_where('masteritem', array('ItemID' => $ItemsModifier['ItemID'], 'DeviceID' => $modifier['DeviceID'], 'DeviceNo' => $ItemsModifier['ItemDeviceNo'], 'PerusahaanNo' => $modifier['PerusahaanNo']));
                $item = $itemQuery->row_array();
                $ItemModifier = array_merge($detail, array('DetailID' => $detailid, 'ItemID' => $ItemsModifier['ItemID'], 'ModifierID' => $modifier['ModifierID'], 'ItemDeviceNo' => $item['DeviceNo'], 'ModifierDeviceNo' => $modifier['DeviceNo'], 'DeviceNo' => $modifier['DeviceNo']));
                $insert = $this->_dbMaster->insert('masteritemdetailmodifier', $ItemModifier);

                if ($insert == true) {
                    $ItemModifier = $this->_dbMaster->get_where('masteritemdetailmodifier', array('DetailID' => $detailid, 'ItemID' => $ItemsModifier['ItemID'], "ModifierID" => $modifier['ModifierID'], "DeviceID" => $modifier['DeviceID'], "PerusahaanNo" => $modifier['PerusahaanNo']));
                    array_push($arrInsert, $ItemModifier->row_array());

                    $detailid += 1;
                }
            }
        }

        $this->_dbMaster->trans_complete();
        $transSt = $this->_dbMaster->trans_status();

        // Push to firebase
        if ($transSt == true) {
            // $this->load->model('Options');
            // $options = $this->Options->get_by_devid($modifier['DeviceID']);
            // if ($options->CreatedVersionCode < 200 && $options->EditedVersionCode < 200) {
            // Item Modifier
            foreach ($arrDelete as $Delete) {

                $deleted_data = array(
                    "table" => 'deletemasteritemdetailmodifier',
                    "column" => array(
                        "DetailID" => $Delete['DetailID'],
                        'DeviceNo' => $Delete['DeviceNo']
                    )
                );

                $this->Firebasemodel->push_firebase(
                    $Delete['DeviceID'],
                    $deleted_data,
                    $Delete['DetailID'],
                    $Delete['DeviceNo'],
                    $Delete['PerusahaanNo'],
                    0
                );
            }

            foreach ($arrInsert as $Insert) {

                $last_insert_data = array(
                    "table" => 'masteritemdetailmodifier',
                    "column" => $Insert
                );
                $this->Firebasemodel->push_firebase(
                    $Insert['DeviceID'],
                    $last_insert_data,
                    $Insert['DetailID'],
                    $Insert['DeviceNo'],
                    $Insert['PerusahaanNo'],
                    0
                );
            }
            // }
            return true;
        } else {
            return false;
        }
    }

    public function getMaxModifierDetailID($idoutlet)
    {
        $sql = "
        SELECT
          COALESCE (MAX(DetailID),0) +1 as id
        FROM
        (SELECT
            DetailID
        FROM
            masteritemdetailmodifier
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . " UNION ALL SELECT
            DetailID
        FROM
            masteritemdetailmodifierdelete
        WHERE
            DeviceID = " . $this->db->escape($idoutlet) . ") a
        ";

        $queryid = $this->db->query($sql);
        $resultid = $queryid->result();
        $itemid = $resultid[0]->id;
        return $itemid;
    }

    public function isExistField($where)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        return $query->num_rows() > 0 ? true : false;
    }

    public function getDetailModifier($where)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where($this->_tableName, $where);
        return $query->row();
    }
}
