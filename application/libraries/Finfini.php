<?php
/**
 * Created by PhpStorm.
 * User: renifal
 * Date: 10/3/2018
 * Time: 10:15 AM
 */

class Finfini
{

    var $constanta =  "https://finfini.com/api/v2/"; // "https://sandbox.finfini.com/api/v2/";
    var $verbose = true;
    var $CI;
    public function __construct($params = array())
    {
        $this->CI =& get_instance();
        $config = is_array($params) ? $params : array();
        $this->CI->load->driver('session', $config);
    }

    function get_key() {
        if ($this->constanta == "https://sandbox.finfini.com/api/v2/") {
            $key = "WVhCcGEyVjVLMUJVTGlCT2RYTmhiblJoY21FZ1FtVnlhMkZvSUVScFoybDBZV3h5WVdodFlYUXVhV2h6WVc0d00wQm5iV0ZwYkM1amIyMHI=";
        } else {
            $key = "WVhCcGEyVjVLMUJVTGlCT2RYTmhiblJoY21FZ1FtVnlhMkZvSUVScFoybDBZV3h5WVdodFlYUXVhV2h6WVc0d00wQm5iV0ZwYkM1amIyMHJNVFV6T0RRME9EazNOQ3M9";
        }
        return $key;
    }

    function get_Origin() {
        $origin = "nutapos.com";
        return $origin;
    }

    function get_signature($method, $url, $requestbody, $accesstoken, $key, $timestamp) {
        $requestbody = strtolower(hash("sha256",$requestbody));
        $data = [$method, $url, $requestbody, $accesstoken, $timestamp];
        $data = implode("|", $data);
        $signature = hash_hmac('sha256', $data, $key);
        return ['signature' => $signature, 'timestamp' => $timestamp];
    }

    function login_user ($requestbody) {
        $timestamp = time();
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $endpoint = $this->constanta ."user/login";
        $method = "POST";
        $accesstoken = 0;
        $getSignature = $this->get_signature($method, $endpoint, $requestbody, $accesstoken, $key, $timestamp);
        $header = ["F-INFINI-Key:".$key,
                    "Origin:".$origin,
                    "F-INFINI-Timestamp:".$timestamp,
                    "F-INFINI-Access-token:".$accesstoken,
                    "F-INFINI-Signature:".$getSignature["signature"],
                    "Content-Type:application/json",
                    "Content-Length:".strlen($requestbody)
                    ];

        $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
        $exsecute = json_decode($exsecute);
        if ($exsecute->status == 'success') {
            $this->CI->session->set_userdata('finfinitoken', $exsecute->data->access_token);
        }
        return $exsecute;
    }

    function create_user () {
        $newUser = ["username" => "rahmat", "email" => "rahmat.ihsan03@gmail.com", "password" => "Lentera1nf"];
        $requestbody = json_encode($newUser);
        $timestamp = time();
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $endpoint = $this->constanta ."user/create";
        $method = "POST";
        $accesstoken = 0;
        $getSignature = $this->get_signature($method, $endpoint, $requestbody, $accesstoken, $key, $timestamp);
        $header = ["F-INFINI-Key:".$key,
            "Origin:".$origin,
            "F-INFINI-Timestamp:".$timestamp,
            "F-INFINI-Access-token:".$accesstoken,
            "F-INFINI-Signature:".$getSignature["signature"],
            "Content-Type:application/json",
            "Content-Length:".strlen($requestbody)
        ];

        $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
        $exsecute = json_decode($exsecute);
        return $exsecute;
    }

    function create_account($requestbody) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."account/create";
        $method = 'POST';
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);

            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"],
                "Content-Type:application/json",
                "Content-Length:".strlen($requestbody)
            ];
            $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"],
                "Content-Type:application/json",
                "Content-Length:".strlen($requestbody)
            ];

            $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function update_account($requestbody, $id) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."account/update/" . $id;
        $method = 'PUT';
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"],
                "Content-Type:application/json",
                "Content-Length:".strlen($requestbody)
            ];
            $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"],
                "Content-Type:application/json",
                "Content-Length:".strlen($requestbody)
            ];

            $exsecute = $this->do_hit_post($header, $requestbody, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function account_list() {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."accounts";
        $method = 'GET';
        $requestbody = json_encode([]);
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function account_sync($id) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."account/sync/" . $id;
        $method = 'GET';
        $requestbody = json_encode([]);
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function delete_account($id) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."account/delete/".$id;
        $method = 'DELETE';
        $requestbody = json_encode([]);
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_delete($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];

            $exsecute = $this->do_hit_delete($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function reconnect() {
        $body = ["username" => "rahmat.ihsan03@gmail.com", "password" => "Lentera1nf"];
        $result = $this->login_user(json_encode($body));
        return $result;
    }

    function get_vendor() {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."vendors";
        $method = 'GET';
        $access_token = 0;
        $requestbody = json_encode([]);
        $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
        $header = ["F-INFINI-Key:".$key,
            "Origin:".$origin,
            "F-INFINI-Timestamp:".$timestamp,
            "F-INFINI-Access-token:".$access_token,
            "F-INFINI-Signature:".$getSignature["signature"]
        ];
        $exsecute = $this->do_hit_get($header, $endpoint);
        $exsecute = json_decode($exsecute);
        return $exsecute;
    }

    function transaction_by_account($id_account) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."transaction/account/".$id_account;
        $method = 'GET';
        $requestbody = json_encode([]);
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function transaction_by_account_perpage($id_account, $page) {
        $key = $this->get_key();
        $origin = $this->get_Origin();
        $timestamp = time();
        $endpoint = $this->constanta ."transaction/account/".$id_account ."?page=" . $page;
        $method = 'GET';
        $requestbody = json_encode(["page" => $page]);
        if ($this->CI->session->has_userdata('finfinitoken')) {
            $access_token = $this->CI->session->userdata('finfinitoken');
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        } else {
            $a = $this->reconnect();
            $access_token = $a->data->access_token;
            $getSignature = $this->get_signature($method, $endpoint, $requestbody, $access_token, $key, $timestamp);
            $header = ["F-INFINI-Key:".$key,
                "Origin:".$origin,
                "F-INFINI-Timestamp:".$timestamp,
                "F-INFINI-Access-token:".$access_token,
                "F-INFINI-Signature:".$getSignature["signature"]
            ];
            $exsecute = $this->do_hit_get($header, $endpoint);
            $exsecute = json_decode($exsecute);
            return $exsecute;
        }
    }

    function do_hit_post($header, $requestbody, $endpoint) {
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST=> "POST",
            CURLOPT_POSTFIELDS => $requestbody,
            CURLOPT_VERBOSE => $this->verbose,
        ));
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (gettype($result) == 'string') {
            $result = json_decode($result);
        }
        $result->request_code = $httpcode;
        return json_encode($result);
    }

    function do_hit_get($header, $endpoint) {
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST=> "GET",
            CURLOPT_VERBOSE => $this->verbose,
        ));
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (gettype($result) == 'string') {
            $result = json_decode($result);
        }
        $result->request_code = $httpcode;
        return json_encode($result);
    }

    function do_hit_delete($header, $endpoint) {
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST=> "DELETE",
            CURLOPT_VERBOSE => $this->verbose,
        ));
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if (gettype($result) == 'string') {
            $result = json_decode($result);
        }
        $result->request_code = $httpcode;
        return json_encode($result);
    }
}