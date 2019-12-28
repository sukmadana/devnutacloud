<?php

/**
 * @author <yustiko404@gmail.com> 5/8/2017 1:58 PM
 */
trait UpdateTrait
{
    public function edit($DeviceID, $id)
    {
        $attributes = [
            'DeviceID' => $DeviceID,
            'TransactionID' => $id
        ];

        $this->addJsPart('webparts/outgoing-stock/parts/js_add');
        $this->setData('page_part', 'webparts/outgoing-stock/edit');
        $this->setData('DeviceID', $DeviceID);
        $this->setData('items', $this->getItem($DeviceID));
        $this->setData('stock', $this->Stockopname->where($attributes)->first_row());
        $this->setData('stock_detail', $this->Stockopnamedetail->where($attributes)->result());

        $this->load->view('main_part', $this->data);
    }

    public function update($DeviceID, $id)
    {
        if ($this->input->server('REQUEST_METHOD') != 'POST')
            throw new Exception("Method Not Allowed", 405);

        $this->attributes = [
            'DeviceID' => $DeviceID,
            'TransactionID' => $id
        ];

        $this->load->library('session');

        $this->Stockopname->update($this->attributes, $this->generateAttributeUpdate($id));

        if ($this->Stockopnamedetail->force_delete([
            'TransactionID' => $this->attributes['TransactionID'],
            'DeviceID' => $this->attributes['DeviceID']
        ])) {
            if ($this->storeDetail()) {
                $this->savedDetail();
                $this->session->set_flashdata('notif', 'Simpan Berhasil');

                return redirect($this->input->server('HTTP_REFERER'));
            }
        }

    }

    protected function generateAttributeUpdate($id)
    {
        $number = $this->db->query($this->Stockopname->get_sk_update(
            $this->attributes['TransactionID'], $this->attributes['DeviceID'], $this->getDateTime()['date']
        ))->first_row();

        return [
//            'TransactionID' => $id,
//            'StockOpnameNumber' => $number->result,
//            'StockOpnameDate' => $this->getDateTime()['date'],
//            'StockOpnameTime' => $this->getDateTime()['time'],
//            'PerusahaanID' => getLoggedInUserID(),
            'isDetailsSaved' => 0,
            'EditedBy' => getLoggedInUsername(),
            'EditedDate' => date("Y-m-d"),
            'EditedTime' => date("H:i"),
            'HasBeenDownloaded' => 0
        ];
    }
}