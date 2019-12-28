<?php

/**
 * @author <yustiko404@gmail.com> 5/5/2017 2:32 PM
 */
trait DeleteKoreksiStok
{
    public function destroy($dateStart, $dateEnd)
    {
        // Method not allowed guard
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        // Running validation
        $this->runDeleteValidation();

        // Runnning delete protocol
        $this->load->model('Stockopname');
        $this->load->model('Stockopnamedetail');
        $this->Stockopname->delete($this->getWhere());
        $this->Stockopnamedetail->delete($this->getWhere());

        return redirect(base_url('koreksistok/?outlet=' . $this->getWhere()['DeviceID'] . '&ds=' . $dateStart . '&de=' . $dateEnd));
    }

    protected function getWhere()
    {
        return [
            'PerusahaanNo' => getPerusahaanNo(),
            'TransactionID' => $this->input->post('id'),
            'DeviceID' => $this->input->post('outlet_id'),
        ];
    }

    protected function runDeleteValidation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('outlet_id', 'Outlet', 'required');
        $this->form_validation->set_rules('id', 'Id', 'required');
        ($this->form_validation->run()) ?: redirect('/koreksistok/?outlet=' . $this->input->post('outlet_id'));

        // Running outlet input guard
        if (in_array($this->input->post('deviceid'), $this->data['outlets'])) {
            redirect(base_url(koreksistok));
        }
    }
}