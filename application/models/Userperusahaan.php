<?php

/*
* This file created by Em Husnan
* Copyright 2015
*/

class Userperusahaan extends CI_MODEL
{

    protected $_tableName = 'userperusahaan';
    protected $_dbMaster;

    function __construct()
    {
        parent::__construct();
    }

    protected function initDbMaster()
    {
        $this->_dbMaster = $this->load->database('master', true);
    }

    public function Create($data)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, $data);
        $count = $query->num_rows();
        if ($count > 0) {
            return FALSE;
        } else {
            $this->initDbMaster();
            $this->_dbMaster->insert($this->_tableName, $data);
            return TRUE;
        }
    }

    public function insertUserperusahaan($params, $rs_allowakses, $rs_allowoutlet)
    {
        $this->initDbMaster();

        $this->_dbMaster->trans_start();
        $whereUserperusahaan = array(
            'Username' => $params['username'],
            'PerusahaanID' => $params['PerusahaanID'],
            'PerusahaanNo' => $params['PerusahaanNo'],
        );
        $query = $this->_dbMaster->get_where($this->_tableName, $whereUserperusahaan);
        $count = $query->num_rows();
        if ($count == 0) {
            $this->_dbMaster->insert($this->_tableName, $params);
        }

        $query = $this->_dbMaster->get_where($this->_tableName, $whereUserperusahaan);
        $Userperusahaan = $query->row();

        // Insert Hak Akses
        $params =  array(
            'PerusahaanID' => $Userperusahaan->PerusahaanID,
            'PerusahaanNo' => $Userperusahaan->PerusahaanNo,
            'Username' => $Userperusahaan->username,
            'UserNew' => 0,
            'UserEdit' => 0,
            'UserDelete' => 0,
            'HapusData' => 0
        );
        $this->_dbMaster->insert('userperusahaanaksescloud', $params);
        $where = $params;

        $queryGetAkses = $this->_dbMaster->get_where('userperusahaanaksescloud', $where);
        $akses = $queryGetAkses->row();
        $paramsUpdateAkses = array();
        foreach ($rs_allowakses as $key => $akses) {
            if ($this->_dbMaster->field_exists($key, 'userperusahaanaksescloud')) {
                $paramsUpdateAkses[$key] = $akses == 'on' ? 1 : 0;
            }
        }

        $this->_dbMaster->update('userperusahaanaksescloud', $paramsUpdateAkses, $where);

        $queryGetAkses = $this->_dbMaster->get_where('userperusahaanaksescloud', $where);
        $akses = $queryGetAkses->row_array();

        // Insert Akses Outlet
        foreach ($rs_allowoutlet as $key => $outlet) {
            if ($outlet == 'on') {
                $this->_dbMaster->insert('userperusahaancabang', array_merge($whereUserperusahaan, array('OutletID' => $key, 'IsAktif' => 1)));
            }
        }

        $this->_dbMaster->trans_complete();
        return $this->_dbMaster->trans_status();
    }

    public function Update($params, $where)
    {
        $this->initDbMaster();
        return $this->_dbMaster->update($this->_tableName, $params, $where);
    }


    public function getListUser($idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        return $query->result();
    }

    public function getTotalUser($idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan));
        return $query->num_rows();
    }

    public function isUserOwner($idperusahaan, $username)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'username' => $username));
        $result = $query->result();
        if (count($result) > 0) {
            return $result[0]->IsOwner == 1;
        } else {
            return FALSE;
        }
    }

    public function changeUserAktif($idperusahaan, $username, $isaktif)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('username' => $username, 'PerusahaanID' => $idperusahaan));
        $this->_dbMaster->update($this->_tableName, array('IsAktif' => $isaktif,));
    }

    public function isUsernameExist($username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'username' => $username));
        $count = $query->num_rows();
        return $count == 1;
    }

    public function isUsernameExistEdit($selectedusername, $username, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'username <> ' => $selectedusername, 'username' => $username));
        $count = $query->num_rows();
        return $count == 1;
    }

    public function isEmailExist($email, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'email' => $email));
        $count = $query->num_rows();
        return $count == 1;
    }

    public function isEmailExistEdit($selectedusername, $email, $idperusahaan)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $idperusahaan, 'username <> ' => $selectedusername, 'email' => $email));
        $count = $query->num_rows();
        return $count == 1;
    }

    public function Delete($username, $idperusahaan)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where(
            $this->_tableName,
            array('PerusahaanID' => $idperusahaan, 'username' => $username)
        );
        $count = $query->num_rows();
        $code = ($count == 1 ? 200 : 404);
        if ($code == 200) {
            $this->_dbMaster->delete(
                $this->_tableName,
                array(
                    'PerusahaanID' => $idperusahaan,
                    'username' => $username,
                    'IsOwner' => 0
                )
            );
        }
        return $code;
    }

    public function changePassword($perusaahanid, $username, $oldpassword, $newpassword)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where(
            $this->_tableName,
            array(
                'PerusahaanID' => $perusaahanid,
                'username' => $username
            )
        );
        $count = $query->num_rows();

        if ($count == 1) {
            $this->_dbMaster->where(
                array(
                    'PerusahaanID' => $perusaahanid,
                    'username' => $username
                )
            );
            $this->_dbMaster->update($this->_tableName, array('password' => $newpassword,));
            return "OK";
        } else {
            return "Password lama salah";
        }
    }

    public function updateImageFoto($path, $perusaahanid, $username)
    {
        $this->initDbMaster();

        $this->_dbMaster->where(array('username' => $username, 'PerusahaanID' => $perusaahanid));
        $this->_dbMaster->update($this->_tableName, array('UrlFoto' => $path, 'TglJamUpdate' => date('Y:m:d H:i:s')));
        return "OK";
    }

    public function getUrlFotoAndEmail($perushaanid, $username)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $perushaanid, 'username' => $username));
        $result = $query->result();
        $foto = $result[0]->UrlFoto;
        $foto .= isNotEmpty($foto) ? '?' . @preg_replace('/[-: ]/i', '', $result[0]->TglJamUpdate) : '';
        return array('UrlFoto' => $foto, 'Email' => $result[0]->email);
    }

    public function getEmailPasswordByPerusahaanUsername($perusahaanid, $username)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $perusahaanid, 'username' => $username));
        $result = $query->row();
        return array('password' => $result->password, 'email' => $result->email);
    }

    public function getUserByEmail($email)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('Email' => $email));
        $result = $query->result();
        return $result;
    }

    public function getUsernamePasswordByPerusahaanEmail($perusahaanid, $email)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $perusahaanid, 'email' => $email));
        $result = $query->row();
        return array('password' => $result->password, 'username' => $result->username);
    }

    public function getUserByUsernameAndPerusahaanID($perusahaanid, $username)
    {
        $this->load->database();
        $query = $this->db->get_where($this->_tableName, array('PerusahaanID' => $perusahaanid, 'username' => $username));
        $result = $query->row();
        return $result;
    }

    public function updateDailyReport($perusahaanid, $username, $allowDailyReport)
    {
        $this->initDbMaster();
        $this->_dbMaster->where(array('PerusahaanID' => $perusahaanid, 'username' => $username));
        $this->_dbMaster->update($this->_tableName, array('SentDailyReport' => $allowDailyReport));
    }

    public function changeEmail($perusaahanid, $username, $oldpassword, $newemail)
    {
        $this->initDbMaster();
        $query = $this->_dbMaster->get_where(
            $this->_tableName,
            array('PerusahaanID' => $perusaahanid, 'username' => $username, 'password' => $oldpassword)
        );
        $count = $query->num_rows();

        if ($count == 1) {

            $queryEmail = $this->_dbMaster->get_where(
                $this->_tableName,
                array('email' => $newemail, 'PerusahaanID' => $perusaahanid)
            );
            $countEmail = $queryEmail->num_rows();
            if ($countEmail > 0) {
                return "Email sudah dipakai";
            } else {
                $this->_dbMaster->where(array('username' => $username, 'password' => $oldpassword));
                $this->_dbMaster->update($this->_tableName, array('email' => $newemail,));
                return "OK";
            }
        } else {
            return "Password lama salah";
        }
    }

    public function getDatatablesUserCloud($params, $visibilityMenu)
    {
        $orderColumn = array(null, 'username', 'email', 'TglJamUpdate', 'IsOwner', 'Jabatan');
        $orderType = array("asc" => "ASC", "ASC" => "ASC", "desc" => "DESC", "DESC" => "DESC");

        // Get Total Data
        $length = $params['length'];
        $start = (int) $params['start'];
        $sql = "SELECT COUNT(*) AS 'recordsTotal' FROM userperusahaan WHERE PerusahaanID = ? ";
        $query = $this->db->query($sql, array($params['PerusahaanID']));
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
        $sql = "SELECT iduserperusahaan, username, email, DATE(TglJamUpdate) AS 'TglUserDibuat', IsOwner, IF(IsOwner = '1', 'Administrator','Biasa') AS 'jenisUser', jabatan
        FROM userperusahaan
        WHERE PerusahaanID = ? AND (username LIKE ? OR email LIKE ? OR IF(IsOwner = 1,'Administrator','Biasa') LIKE ?)
        " . $orderBy . " LIMIT ?,? ";
        $query = $this->db->query($sql, array(
            $params['PerusahaanID'],
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            '%' . $search['value'] . '%',
            $params['start'],
            $length
        ));
        if ($query->num_rows() > 0) {
            $rs_result = $query->result();
            foreach ($rs_result as $i => $result) {
                $row = array();
                $row[] = (($start + 1) + $i);
                $row[] = $result->username;
                $row[] = $result->email;
                $row[] = formatdateindonesia($result->TglUserDibuat);
                if ($result->IsOwner == '1') {
                    $row[] = '<label class="label label-danger label-table">' . $result->jenisUser . '</label>';
                } else {
                    $row[] = $result->jenisUser;
                }

                $row[] = $result->jabatan;

                if ($visibilityMenu['UserEdit'] || $visibilityMenu['UserDelete']) {
                    if ($result->IsOwner == '1' && $visibilityMenu['UserEdit']) {
                        $row[] = '<a href="' . base_url() . 'perusahaan/userclouddetail?user=' . $result->username . '" class="btn btn-md btn-block btn-white">Kelola Akses</a>';
                    } else {
                        $btn = '<div class="dropdown dropdown-inherit">';
                        $btn .= '<button class="btn btn-md btn-block btn-white dropdown-toggle" type="button" data-toggle="dropdown">Detail';
                        $btn .= '<span class="fa fa-chevron-down pull-right mt-3"></span></button>';
                        $btn .= '<ul class="dropdown-menu">';
                        if ($visibilityMenu['UserEdit']) {
                            $btn .= '<li><a href="' . base_url() . 'perusahaan/userclouddetail?user=' . $result->username . '"  class="text-center py-10">Kelola Akses</a></li>';
                        }
                        if ($visibilityMenu['UserDelete']) {
                            $btn .= '<li class="divider my-0"></li>';
                            $btn .= '<li><a href="#modalHapus" class="konfirmasihapus text-center py-10" data-tag="' . $result->username . '">Hapus</a></li>';
                        }
                        $btn .= '</ul>';
                        $btn .= '</div>';
                        $row[] = $btn;
                    }
                }

                $listData[$i] = $row;
            }
        }

        // Get Total Filtered Data
        $sql = "SELECT COUNT(*) AS 'recordsFiltered'
        FROM userperusahaan
        WHERE PerusahaanID = ? AND (username LIKE ? OR email LIKE ? OR IF(IsOwner = 1,'Administrator','Biasa') LIKE ?)";
        $query = $this->db->query($sql, array(
            $params['PerusahaanID'],
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

    public function getAllHakAkses($aksescloud)
    {
        // Array Parent
        $rs_hak_akses = array(
            array(
                'level' => '1', 'label' => 'Dashboard',
                'detail' => array(
                    'Dashboard' => array('akses' => 'Dashboard', 'allow' => !$aksescloud ? '1' : $aksescloud->Dashboard)
                )
            ),
            array(
                'level' => '1', 'label' => 'Outlet',
                'detail' => array(
                    // 'OutletView' => array('akses' => 'Lihat Outlet', 'allow' => !$aksescloud ? '1' : $aksescloud->OutletView),
                    'OutletNew' => array('akses' => 'Tambah Outlet', 'allow' => !$aksescloud ? '1' : $aksescloud->OutletNew),
                    'OutletEdit' => array('akses' => 'Edit Outlet', 'allow' => !$aksescloud ? '1' : $aksescloud->OutletEdit),
                    'OutletDelete' => array('akses' => 'Hapus Outlet', 'allow' => !$aksescloud ? '1' : $aksescloud->OutletDelete),
                )
            ),
            array(
                'level' => '2', 'label' => 'Items',
                'detail' => array(
                    array(
                        'label' => 'Item <br> <small>(Kategori, Produk, Bahan, Pilihan Ekstra)</small>',
                        'detail' => array(
                            // 'ItemView' => array('akses' => 'Lihat Item', 'allow' => !$aksescloud ? '1' : $aksescloud->ItemView),
                            'ItemAdd' => array('akses' => 'Tambah Item', 'allow' => !$aksescloud ? '1' : $aksescloud->ItemAdd),
                            'ItemEdit' => array('akses' => 'Edit Item', 'allow' => !$aksescloud ? '1' : $aksescloud->ItemEdit),
                            'ItemDelete' => array('akses' => 'Hapus Item', 'allow' => !$aksescloud ? '1' : $aksescloud->ItemDelete),
                        )
                    ),
                    array(
                        'label' => 'Diskon',
                        'detail' => array(
                            // 'DiskonView' => array('akses' => 'Lihat Diskon', 'allow' => !$aksescloud ? '1' : $aksescloud->DiskonView),
                            'DiskonNew' => array('akses' => 'Tambah Diskon', 'allow' => !$aksescloud ? '1' : $aksescloud->DiskonNew),
                            'DiskonEdit' => array('akses' => 'Edit Diskon', 'allow' => !$aksescloud ? '1' : $aksescloud->DiskonEdit),
                            'DiskonDelete' => array('akses' => 'Hapus Diskon', 'allow' => !$aksescloud ? '1' : $aksescloud->DiskonDelete),
                        )
                    ),
                    array(
                        'label' => 'Pajak',
                        'detail' => array(
                            // 'PajakView' => array('akses' => 'Lihat Pajak', 'allow' => !$aksescloud ? '1' : $aksescloud->PajakView),
                            'PajakNew' => array('akses' => 'Tambah Pajak', 'allow' => !$aksescloud ? '1' : $aksescloud->PajakNew),
                            'PajakEdit' => array('akses' => 'Edit Pajak', 'allow' => !$aksescloud ? '1' : $aksescloud->PajakEdit),
                            'PajakDelete' => array('akses' => 'Hapus Pajak', 'allow' => !$aksescloud ? '1' : $aksescloud->PajakDelete),
                        )
                    ),
                    array(
                        'label' => 'Tipe Penjualan',
                        'detail' => array(
                            // 'TipePenjualanView' => array('akses' => 'Lihat TipePenjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->TipePenjualanView),
                            'TipePenjualanNew' => array('akses' => 'Tambah TipePenjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->TipePenjualanNew),
                            'TipePenjualanEdit' => array('akses' => 'Edit TipePenjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->TipePenjualanEdit),
                            'TipePenjualanDelete' => array('akses' => 'Hapus TipePenjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->TipePenjualanDelete),
                        )
                    )
                )
            ),
            array(
                'level' => '1', 'label' => 'Pelanggan',
                'detail' => array(
                    // 'CustomerView' => array('akses' => 'Lihat Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->CustomerView),
                    'CustomerAdd' => array('akses' => 'Tambah Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->CustomerAdd),
                    'CustomerEdit' => array('akses' => 'Edit Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->CustomerEdit),
                    'CustomerDelete' => array('akses' => 'Hapus Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->CustomerDelete),
                )
            ),
            array(
                'level' => '1', 'label' => 'Promo',
                'detail' => array(
                    // 'PromoView' => array('akses' => 'Lihat Promo', 'allow' => !$aksescloud ? '1' : $aksescloud->PromoView),
                    'PromoNew' => array('akses' => 'Tambah Promo', 'allow' => !$aksescloud ? '1' : $aksescloud->PromoNew),
                    'PromoEdit' => array('akses' => 'Edit Promo', 'allow' => !$aksescloud ? '1' : $aksescloud->PromoEdit),
                    'PromoDelete' => array('akses' => 'Hapus Promo', 'allow' => !$aksescloud ? '1' : $aksescloud->PromoDelete),
                )
            ),
            array(
                'level' => '1', 'label' => 'User',
                'detail' => array(
                    // 'UserView' => array('akses' => 'Lihat User', 'allow' => !$aksescloud ? '1' : $aksescloud->UserView),
                    'UserNew' => array('akses' => 'Tambah User', 'allow' => !$aksescloud ? '1' : $aksescloud->UserNew),
                    'UserEdit' => array('akses' => 'Edit User', 'allow' => !$aksescloud ? '1' : $aksescloud->UserEdit),
                    'UserDelete' => array('akses' => 'Hapus User', 'allow' => !$aksescloud ? '1' : $aksescloud->UserDelete),
                )
            ),
            array(
                'level' => '2', 'label' => 'Stok',
                'detail' => array(
                    array(
                        'label' => 'Supplier',
                        'detail' => array(
                            // 'SupplierView' => array('akses' => 'Lihat Supplier', 'allow' => !$aksescloud ? '1' : $aksescloud->SupplierView),
                            'SupplierAdd' => array('akses' => 'Tambah Supplier', 'allow' => !$aksescloud ? '1' : $aksescloud->SupplierAdd),
                            'SupplierEdit' => array('akses' => 'Edit Supplier', 'allow' => !$aksescloud ? '1' : $aksescloud->SupplierEdit),
                            'SupplierDelete' => array('akses' => 'Hapus Supplier', 'allow' => !$aksescloud ? '1' : $aksescloud->SupplierDelete),
                        )
                    ),
                    array(
                        'label' => 'Pembelian',
                        'detail' => array(
                            // 'PurchaseView' => array('akses' => 'Lihat Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->PurchaseView),
                            'PurchaseAdd' => array('akses' => 'Tambah Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->PurchaseAdd),
                            'PurchaseEdit' => array('akses' => 'Edit Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->PurchaseEdit),
                            'PurchaseDelete' => array('akses' => 'Hapus Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->PurchaseDelete),
                        )
                    ),
                    array(
                        'label' => 'Stok Masuk',
                        'detail' => array(
                            // 'IncomingStockView' => array('akses' => 'Lihat Stok Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->IncomingStockView),
                            'IncomingStockNew' => array('akses' => 'Tambah Stok Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->IncomingStockNew),
                            'IncomingStockEdit' => array('akses' => 'Edit Stok Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->IncomingStockEdit),
                            'IncomingStockDelete' => array('akses' => 'Hapus Stok Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->IncomingStockDelete),
                        )
                    ),
                    array(
                        'label' => 'Stok Keluar',
                        'detail' => array(
                            // 'OutgoingStockView' => array('akses' => 'Lihat Stok Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->OutgoingStockView),
                            'OutgoingStockNew' => array('akses' => 'Tambah Stok Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->OutgoingStockNew),
                            'OutgoingStockEdit' => array('akses' => 'Edit Stok Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->OutgoingStockEdit),
                            'OutgoingStockDelete' => array('akses' => 'Hapus Stok Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->OutgoingStockDelete),
                        )
                    ),
                    array(
                        'label' => 'Koreksi Stok',
                        'detail' => array(
                            // 'StockView' => array('akses' => 'Lihat Koreksi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->StockView),
                            'StockAdd' => array('akses' => 'Tambah Koreksi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->StockAdd),
                            'StockEdit' => array('akses' => 'Edit Koreksi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->StockEdit),
                            'StockDelete' => array('akses' => 'Hapus Koreksi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->StockDelete),
                        )
                    ),
                    array(
                        'label' => 'Transfer Stok',
                        'detail' => array(
                            // 'TransferStockView' => array('akses' => 'Lihat Transfer Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->TransferStockView),
                            'TransferStockNew' => array('akses' => 'Tambah Transfer Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->TransferStockNew),
                            'TransferStockEdit' => array('akses' => 'Edit Transfer Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->TransferStockEdit),
                            'TransferStockDelete' => array('akses' => 'Hapus Transfer Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->TransferStockDelete),
                        )
                    )
                )
            ),
            // array(
            //     'level' => '1', 'label' => 'Rekening Bank',
            //     'detail' => array(
            //         // 'DataRekeningView' => array('akses' => 'Lihat Rekening Bank', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningView),
            //         'DataRekeningAdd' => array('akses' => 'Tambah Rekening Bank', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningAdd),
            //         'DataRekeningEdit' => array('akses' => 'Edit Rekening Bank', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningEdit),
            //         'DataRekeningDelete' => array('akses' => 'Hapus Rekening Bank', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningDelete),
            //     )
            // ),
            array(
                'level' => '2', 'label' => 'Uang',
                'detail' => array(
                    array(
                        'label' => 'Data Rekening',
                        'detail' => array(
                            // 'DataRekeningView' => array('akses' => 'Lihat Rekening Bank', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningView),
                            'DataRekeningAdd' => array('akses' => 'Tambah Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningAdd),
                            'DataRekeningEdit' => array('akses' => 'Edit Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningEdit),
                            'DataRekeningDelete' => array('akses' => 'Hapus Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->DataRekeningDelete),
                        )
                    ),
                    array(
                        'label' => 'Uang Masuk',
                        'detail' => array(
                            // 'MoneyView' => array('akses' => 'Lihat Uang Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->MoneyView),
                            'MoneyAdd' => array('akses' => 'Tambah Uang Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->MoneyAdd),
                            'MoneyEdit' => array('akses' => 'Edit Uang Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->MoneyEdit),
                            'MoneyDelete' => array('akses' => 'Hapus Uang Masuk', 'allow' => !$aksescloud ? '1' : $aksescloud->MoneyDelete),
                        )
                    ),
                    array(
                        'label' => 'Uang Keluar',
                        'detail' => array(
                            // 'CashBankOutView' => array('akses' => 'Lihat Uang Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->CashBankOutView),
                            'CashBankOutNew' => array('akses' => 'Tambah Uang Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->CashBankOutNew),
                            'CashBankOutEdit' => array('akses' => 'Edit Uang Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->CashBankOutEdit),
                            'CashBankOutDelete' => array('akses' => 'Hapus Uang Keluar', 'allow' => !$aksescloud ? '1' : $aksescloud->CashBankOutDelete),
                        )
                    )
                )
            ),
            array(
                'level' => '2', 'label' => 'Laporan',
                'detail' => array(
                    array(
                        'label' => 'Penjualan',
                        'detail' => array(
                            'LaporanPenjualan' => array('akses' => 'Penjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualan),
                            'LaporanRekapPenjualan' => array('akses' => 'Rekap Penjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPenjualan),
                            'LaporanPenjualanPerJam' => array('akses' => 'Penjualan per Jam', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerJam),
                            'LaporanPenjualanPerKasir' => array('akses' => 'Penjualan per Kasir', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerKasir),
                            'LaporanRekapPenjualanPerKategori' => array('akses' => 'Penjualan per Kategori', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPenjualanPerKategori),
                            'LaporanRataRataBelanjaPelanggan' => array('akses' => 'Rata-rata Belanja per Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRataRataBelanjaPelanggan),
                            'LaporanDiskon' => array('akses' => 'Diskon', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanDiskon),
                            'LaporanPajak' => array('akses' => 'Pajak', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPajak),
                            'LaporanRekapPembayaran' => array('akses' => 'Rekap Pembayaran', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPembayaran),
                            'LaporanPenjualanVarian' => array('akses' => 'Penjualan Varian', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanVarian),
                            'LaporanPenjualanPilihanEkstra' => array('akses' => 'Penjualan Pilhan Ekstra', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPilihanEkstra),
                            'LaporanPembulatan' => array('akses' => 'Pembulatan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPembulatan),
                            'LaporanPesananBelumLunas' => array('akses' => 'Pesanan Belum Lunas', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPesananBelumLunas),
                            'LaporanPenjualanVoid' => array('akses' => 'Penjualan Void', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanVoid),
                            'LaporanPenjualanPerTipe' => array('akses' => 'Penjualan per Tipe', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerTipe),
                            'LaporanPenjualanPerJamItem' => array('akses' => 'Penjualan per Jam per Item', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerJamItem),
                            'LaporanRiwayatBelanjaPelanggan' => array('akses' => 'Riwayat Belanja Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRiwayatBelanjaPelanggan),
                            'LaporanPesananBatal' => array('akses' => 'Pesanan Batal', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPesananBatal),
                            'LaporanPenjualanPerKategoriSemuaItem' => array('akses' => 'Penjualan per Kategori Semua Item', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerKategoriSemuaItem),
                            'LaporanPenjualanPerShift' => array('akses' => 'Penjualan per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerShift),
                            'LaporanRekapPenjualanPerShift' => array('akses' => 'Rekap Penjualan per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPenjualanPerShift),
                            'LaporanRekapShift' => array('akses' => 'Rekap Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapShift),
                            'LaporanPenjualanPerKasirShift' => array('akses' => 'Penjualan per Kasir Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPerKasirShift),
                            'LaporanPenjualanKategoriPerShift' => array('akses' => 'Penjualan Kategori per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanKategoriPerShift),
                            'LaporanPenjualanVarianPerShift' => array('akses' => 'Penjualan Varian per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanVarianPerShift),
                            'LaporanPenjualanPilihanEkstraPerShift' => array('akses' => 'Penjualan Pilihan Ekstra per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualanPilihanEkstraPerShift),
                            'LaporanTipePenjualanPerShift' => array('akses' => 'Tipe Penjualan per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanTipePenjualanPerShift),
                        )
                    ),
                    array(
                        'label' => 'Pembelian',
                        'detail' => array(
                            'LaporanPembelian' => array('akses' => 'Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPembelian),
                            'LaporanRekapPembelian' => array('akses' => 'Rekap Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPembelian),
                        )
                    ),
                    array(
                        'label' => 'Keuangan',
                        'detail' => array(
                            'LaporanSaldoKasRekening' => array('akses' => 'Saldo Kas Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanSaldoKasRekening),
                            'LaporanMutasiKasRekening' => array('akses' => 'Mutasi Kas Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanMutasiKasRekening),
                            'LaporanPengeluaran' => array('akses' => 'Pengeluaran Uang Operasional', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPengeluaran),
                            'LaporanPengeluaranPerDibayarKe' => array('akses' => 'Pengeluaran per Dibayar Ke', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPengeluaranPerDibayarKe),
                        )
                    ),
                    array(
                        'label' => 'Stok',
                        'detail' => array(
                            'LaporanStok' => array('akses' => 'Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanStok),
                            'LaporanKartuStok' => array('akses' => 'Kartu Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanKartuStok),
                            'LaporanRekapMutasiStok' => array('akses' => 'Rekap Mutasi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapMutasiStok),
                        )
                    ),
                    array(
                        'label' => 'Laba',
                        'detail' => array(
                            'LaporanLaba' => array('akses' => 'Laba', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanLaba),
                            'LaporanRincianLaba' => array('akses' => 'Rincian Laba', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRincianLaba),
                            'LaporanLabaPerKategori' => array('akses' => 'Laba per Kategori', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanLabaPerKategori),
                            'LaporanLabaPerShift' => array('akses' => 'Laba per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanLabaPerShift),
                            'LaporanRincianLabaPerShift' => array('akses' => 'Rincian Laba per Shift', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRincianLabaPerShift),
                        )
                    ),
                    array(
                        'label' => 'Feedback Pelanggan',
                        'detail' => array(
                            'FeedbackPelanggan' => array('akses' => 'Feedback Pelanggan', 'allow' => !$aksescloud ? '1' : $aksescloud->FeedbackPelanggan),
                        )
                    ),
                )
                // 'detail' => array(
                //     'LaporanPenjualan' => array('akses' => 'Laporan Penjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPenjualan),
                //     'LaporanRekapPenjualan' => array('akses' => 'Laporan Rekap Penjualan', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPenjualan),
                //     'LaporanRekapPenjualanPerKategori' => array('akses' => 'Laporan Rekap Penjualan Per Kategori', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPenjualanPerKategori),
                //     'LaporanPembelian' => array('akses' => 'Laporan Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanPembelian),
                //     'LaporanRekapPembelian' => array('akses' => 'Laporan Rekap Pembelian', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPembelian),
                //     'LaporanSaldoKasRekening' => array('akses' => 'Laporan Saldo Kas Rekening', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanSaldoKasRekening),
                //     'LaporanStok' => array('akses' => 'Laporan Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanStok),
                //     'LaporanKartuStok' => array('akses' => 'Laporan Kartu Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanKartuStok),
                //     'LaporanRekapMutasiStok' => array('akses' => 'Laporan Rekap Mutasi Stok', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapMutasiStok),
                //     'LaporanLaba' => array('akses' => 'Laporan Laba', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanLaba),
                //     'LaporanRekapPembayaran' => array('akses' => 'Laporan Rekap Pembayaran', 'allow' => !$aksescloud ? '1' : $aksescloud->LaporanRekapPembayaran),
                // )
            ),
            array(
                'level' => '1', 'label' => 'Aktivasi',
                'detail' => array(
                    'Aktivasi' => array('akses' => 'Aktivasi', 'allow' => !$aksescloud ? '1' : $aksescloud->Aktivasi)
                )
            ),
            array(
                'level' => '1', 'label' => 'Hapus Data',
                'detail' => array(
                    'HapusData' => array('akses' => 'Hapus Data', 'allow' => !$aksescloud ? '1' : $aksescloud->HapusData)
                )
            ),
        );

        return $rs_hak_akses;
    }
}
