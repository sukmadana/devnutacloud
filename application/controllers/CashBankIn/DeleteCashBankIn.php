<?php

/**
 * @author <yustiko404@gmail.com> 5/5/2017 2:32 PM
 */
trait DeleteCashBankIn
{
    public function destroy($dateStart, $dateEnd)
    {
        // Method not allowed guard
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        if ($this->input->post('iscloud', TRUE) === NULL) {
            show_error("Invalid Parameter", 400, "Bad Request");
        }

        $iscloud = (int)$this->input->post("iscloud", TRUE);

        // Running validation
        $this->runDeleteValidation();

        // Runnning delete protocol
        $this->load->model('Uangmasukmodel');
        $whr = $this->getWhere();
        $this->Uangmasukmodel->deleteUangMasuk($iscloud, $whr);

        $this->load->model('Firebasemodel');
        $deleted_data = array(
            "table" => $iscloud === 1 ? "deletecloudcashbankin" : 'deletecashbankin',
            "column" => array("TransactionID" => $whr['TransactionID'], 
                "DeviceNo" => $iscloud === 1 ? 0 : $whr['DeviceNo'])
        );
        $this->Firebasemodel->push_firebase($this->input->post('outlet_id'), $deleted_data,
            $whr['TransactionID'], $iscloud === 1 ? 0 : $whr['DeviceNo'], getPerusahaanNo(), 0);

        return redirect(base_url('uangmasuk/?outlet=' . $this->getWhere()['DeviceID'] . '&ds=' . $dateStart . '&de=' . $dateEnd));
    }

    protected function getWhere()
    {
        $id = $this->input->post('id', TRUE);
        $iscloud = (int)$this->input->post('iscloud', TRUE);

        $deviceno = -1;
        if ($iscloud === 0) {
            $tmp = explode(".", $id, 2);
            $id = $tmp[0];
            $deviceno =  $tmp[1];
        }

        $tmp = array(
            'PerusahaanNo' => getPerusahaanNo(),
            'TransactionID' => $id,
            'DeviceID' => $this->input->post('outlet_id', TRUE)
        );

        if ($iscloud === 0) {
            $tmp["DeviceNo"] = $deviceno;
        }

        return $tmp;
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect('/uangmasuk/?outlet=' . $this->input->post('outlet_id'));

        // Running outlet input guard
        if (in_array($this->input->post('deviceid'), $this->data['outlets'])) {
            redirect(base_url('uangmasuk'));
        }
    }
}