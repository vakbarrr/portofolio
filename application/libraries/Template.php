<?php
class Template
{
    protected $_ci;

    function __construct()
    {
        $this->_ci = &get_instance();
    }

    function _admin($content, $data = NULL)
    {
        $data['contents'] = $this->_ci->load->layout(false)->view($content, $data, TRUE);
        $this->_ci->load->view('admin/layout/master', $data);
    }

}
