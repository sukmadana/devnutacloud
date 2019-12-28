<?php

trait DeletePurchase
{
    public function destroy()
    {
        // Method not allowed guard
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        $this->runDeleteValidation();

        $this->load->model('Purchase');
        $this->load->model('Purchaseitemdetail');

        $this->Purchase->delete($this->getWhere());
        $this->Purchaseitemdetail->delete($this->getWhere());

        redirect(base_url('/pembelian/?outlet=' . $this->input->post('outlet')));
    }

    protected function getWhere()
    {
        return [
            'PerusahaanNo' => getPerusahaanNo(),
            'TransactionID' => $this->input->post('id'),
            'DeviceID' => $this->input->post('outlet'),
        ];
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect(base_url('/pembelian/?outlet=' . $this->input->post('outlet')));

        // Running outlet input guard
        if (!array_key_exists($this->input->post('outlet'), $this->data['outlets'])) {
            redirect(base_url('/pembelian'));
        }
    }
}