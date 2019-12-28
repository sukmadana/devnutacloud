<?php

/**
 * @author <yustiko404@gmail.com> 5/5/2017 2:32 PM
 */
trait DestroyTrait
{
    public function destroy()
    {
        // Method not allowed guard
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        // Running validation
        $this->runDeleteValidation();

        // Runnning delete protocol
        $this->Stockopname->delete($this->getWhere());
        $this->Stockopnamedetail->delete($this->getWhere());

        return redirect(base_url('stokkeluar?outlet='.$this->getWhere()['DeviceID'].'&notify=1&src=Delete'));
    }

    protected function getWhere()
    {
        return [
            'TransactionID' => $this->input->post('id'),
            'DeviceID' => $this->input->post('outlet'),
        ];
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect('/stokkeluar/?outlet='.$this->input->post('outlet'));

        // Running outlet input guard
        if (in_array($this->input->post('deviceid'), $this->data['outlets'])) {
            redirect('/stokkeluar');
        }
    }
}