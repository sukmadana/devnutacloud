<?php

/**
 * @author <yustiko404gmail.com>
 * User: Yustiko
 * Date: 4/21/2017
 * Time: 2:20 PM
 */
trait ListTrait
{
    public function index()
    {
        $this->addJsPart('webparts/parts/js_socket.php');
        $this->addJsPart('webparts/parts/js_datatable.php');
        $this->addJsPart('webparts/outgoing-stock/parts/js_index.php');
        $this->setData('items', $this->generateItems($this->getOutletId()));
        $this->setData('page_part', 'webparts/outgoing-stock/list');
        $notify = $this->input->get('notify');
        $this->setData('notify', isset($notify));
        if ($notify) {
            $src = $this->input->get('src');
            $this->setData('src', $src);
        }
        $this->load->view('main_part', $this->data);
    }

    protected function getOutletId()
    {
        $selected_outlet = $this->input->get('outlet');
        if (!$selected_outlet) {
            $selected_outlet = 0 ;
        }
        return $selected_outlet;
    }

    protected function generateItems($outlet_id)
    {
        return $this->Stockopname->get_all_outgoing_stock($outlet_id);
    }
}