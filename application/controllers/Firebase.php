<?php
class Firebase extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        // $this->load->database();
    }

    public function register()
    {
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $outlet = $this->input->post('outlet');
            $devno = $this->input->post('deviceno');
            if (!isset($devno) || $devno == null || empty($devno)) {
                $devno = 1;
            }
            $token = $this->input->post('token');
            $this->load->model('FirebaseModel');
            if (!empty($outlet) && !empty($token)) {
                $result = $this->insert_token($outlet, $token, $devno);
                if ($result) {
                    echo json_encode(
                        array(
                            'status' => "ok",
                            "data" => $result
                        ),
                        JSON_PRETTY_PRINT
                    );
                }
            } else {
                echo json_encode(array('status' => "error", "message" => "parameter token atau outlet tidak boleh kosong"), JSON_PRETTY_PRINT);
            }
        } else {
            echo json_encode(array('status' => "404"), JSON_PRETTY_PRINT);
        }
    }

    protected function token_checker($outlet, $token)
    {
        $result = $this->db->get_where("firebase_device", array(
            'outlet' => $outlet,
            'token' => $token
        ));
        return $result->row_array();
    }

    public function insert_token($outlet, $token, $devno)
    {
        if (empty($this->token_checker($outlet, $token))) {
            $this->db->query("UPDATE device_app SET IsActive=1 WHERE OutletID=".$outlet." AND DeviceNo=".$devno);
            
            $tableName = 'firebase_device';

            $query = $this->db->get_where($tableName, array(
                'outlet' => $outlet,
                'DeviceNo' => $devno
            ));
            $count = $query->num_rows();
            if ($count == 0) {
                $this->db->insert($tableName, array(
                    'outlet' => $outlet,
                    'token' => $token,
                    'DeviceNo' => $devno
                ));
            } else {
                $this->db->where(array('outlet' => $outlet, 'DeviceNo' => $devno));
                $this->db->update($tableName, array(
                    'token' => $token
                ));
            }

            // get last inserted data
            $query = $this->db->get_where("firebase_device", array('outlet' => $outlet, 'DeviceNo' 	=> $devno));
            return $query->row_array();
        } else {
            return $this->token_checker($outlet, $token);
        }
    }

}