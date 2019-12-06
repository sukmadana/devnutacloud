<?php

/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 13/02/2016
 * Time: 13:02
 */
class Postdata extends CI_Model
{
    var $arrayofparams = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function set_post_data($arrayPostDataFromClient)
    {
        $this->arrayofparams = $arrayPostDataFromClient;
    }

    public function validate()
    {
        $arrayOfErrorMessage = $this->is_params_has_empty_value();
        return $arrayOfErrorMessage;
    }

    public function validate_format()
    {
        $arrayOfErrorMessage = array();
        $validMailMessage = $this->validate_email_address();
        if (!$validMailMessage) {
            array_push($arrayOfErrorMessage, 'Email tidak valid');
        }
        return $arrayOfErrorMessage;
    }


    private function is_params_has_empty_value()
    {
        $retval = array();
        foreach ($this->arrayofparams as $k => $v) {
            if (isset($v) == FALSE || trim($v) === '') {
                array_push($retval, $k . ' tidak boleh kosong');
            }
        }
        return $retval;
    }

    private function validate_email_address()
    {
        $email = $this->arrayofparams['Email'];
        $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $validEmail;
    }
}