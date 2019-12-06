<?php

/**
* Created by PhpStorm.
* User: Husnan
* Date: 30/11/2015
* Time: 18:03
*/
class User extends CI_Model
{

  var $_tableName = "user";
  protected $_dbMaster;


  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  protected function initDbMaster()
  {
    $this->_dbMaster = $this->load->database('master', true);
  }

  public function IsTerdaftar($devID)
  {
    $query = $this->db->get_where($this->_tableName, array('DeviceID' => $devID));
    $result = $query->result();
    return count($result) > 0;
  }

  public function TidakPunyaPerusahaanID($devid)
  {
    $query = $this->db->get_where($this->_tableName, array('Username' => $devid, 'PerusahaanID' => NULL, 'IsAktif' => 1, 'SudahKonfirmasi' => 1));
    $result = $query->num_rows();
    return $result > 0;
  }

  public function Create($data)
  {
    $this->initDbMaster();
    $this->_dbMaster->insert($this->_tableName, $data);
    $insert_id = $this->_dbMaster->insert_id();
    return $insert_id;
  }

  public function UpdatePerusahaanID($iduser, $idperusahaan)
  {
    $this->initDbMaster();
    $this->_dbMaster->where(array('Username' => $iduser, 'IsAktif' => 1, 'SudahKonfirmasi' => 1));
    $this->_dbMaster->update($this->_tableName, array('PerusahaanID' => $idperusahaan));
  }

  public function IsUsernameExist($username)
  {
    $query = $this->db->get_where($this->_tableName, array('Username' => $username));
    $result = $query->result();
    return count($result) > 0;
  }

  public function IsEmailExist($email)
  {
    $query = $this->db->get_where($this->_tableName, array('Email' => $email));
    $result = $query->result();
    return count($result) > 0;
  }

  public function getEmailByUsername($username)
  {
    $query = $this->db->get_where($this->_tableName, array('Username' => $username));
    $result = $query->result();

    if (count($result) == 1) {
      return $result[0]->Email;
    } else {
      return NULL;
    }
  }

  public function SudahKonfirmasi($iduser)
  {
    $this->initDbMaster();
    $this->_dbMaster->get_where($this->_tableName, array('userid' => $iduser));
    $this->_dbMaster->update($this->_tableName, array('SudahKonfirmasi' => 1));

  }

  public function authIndividual($user, $pass)
  {
    $isUsernameExist = $this->IsUsernameExist($user);
    if (!$isUsernameExist) {
      return array('isAuth' => FALSE, 'id' => NULL);
    }
    $strwhere = "Username = " . $this->db->escape($user) . " AND BINARY Password = BINARY " . $this->db->escape($pass) . " AND SudahKonfirmasi=1 AND IsAktif=1";
    $this->db->where($strwhere);
    $query = $this->db->query("SELECT Username FROM " . $this->_tableName . " WHERE " . $strwhere);
    $count = $query->num_rows();
    if ($count == 1) {
      $result = $query->result();
      return array('isAuth' => TRUE, 'id' => $result[0]->Username);
    } else {
      return array('isAuth' => FALSE, 'id' => NULL);
    }
  }

  public function authPerusahaan($idperusahaan, $user, $pass)
  {
    $sql = "
    select
    a.PerusahaanID,
    a.username,
    a.password,
    b.registerwithdeviceid
    from
    userperusahaan a
    inner join
    perusahaan b
    where
    a.PerusahaanID = b.PerusahaanID
    and a.IsAktif = 1
    and a.Username = " . $this->db->escape($user) . "
    and a.Password = " . $this->db->escape($pass) . "
    and a.PerusahaanID = " . $this->db->escape($idperusahaan);
    $query = $this->db->query($sql);
    $result = $query->result();
    if (count($result) == 1) {
      return array('isAuth' => TRUE, 'id' => $result[0]->PerusahaanID, 'regid' => $result[0]->registerwithdeviceid);
    } else {
      return array('isAuth' => FALSE, 'id' => NULL, 'regid' => NULL);
    }
  }

  public function authIndividualByDeviceID($deviceid)
  {
    $query = $this->db->get_where($this->_tableName, array('DeviceID' => $deviceid));
    $result = $query->result();
    if (count($result) == 1) {
      return array('isAuth' => TRUE, 'id' => $result[0]->DeviceID, 'username' => $result[0]->Username);
    } else {
      return array('isAuth' => FALSE, 'id' => NULL);
    }
  }

  public function getUsernamePassword($devID)
  {
    $query = $this->db->get_where($this->_tableName, array('Username' => $devID, 'SudahKonfirmasi' => 1, 'IsAktif' => 1));
    $result = $query->result();
    $retval = array(
      'username' => $result[0]->Username,
      'password' => $result[0]->Password,
      'email' => $result[0]->Email
    );
    return $retval;
  }

  public function isUserConfirmed($username)
  {
    $query = $this->db->get_where($this->_tableName, array('Username' => $username));
    $result = $query->result();
    return $result[0]->SudahKonfirmasi == 1;

  }

  public function isUserPerusahaanExist($idperusahaan, $username)
  {
    $sql = "SELECT username FROM userperusahaan WHERE PerusahaanID = " . $this->db->escape($idperusahaan) . " AND username=" . $this->db->escape($username);
    $query = $this->db->query($sql);
    $count = $query->num_rows();
    return $count > 0;
  }

  public function isUserPerusahaanAktif($idperusahaan, $username)
  {
    $sql = "SELECT username FROM userperusahaan WHERE PerusahaanID = " . $this->db->escape($idperusahaan) . " AND username=" . $this->db->escape($username) . " AND IsAktif = 1";
    $query = $this->db->query($sql);
    $count = $query->num_rows();
    return $count > 0;
  }

  public function getUsernamePasswordByUserID($userid)
  {
    $query = $this->db->get_where($this->_tableName, array('UserID' => $userid));
    $result = $query->result();
    $retval = array(
      'username' => $result[0]->Username,
      'password' => $result[0]->Password,
    );
    return $retval;
  }
}
