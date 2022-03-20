<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('getAdmin')) {
    function getAdmin()
    {
        $CI = &get_instance();
        $admin = $CI->db->get_where('tb_user', ['id' => $CI->session->userdata('user_id')])->row();
        return $admin;
    }
}

if (!function_exists('slug_url')) {
    function slug_url($char = null)
    {
        if ($char == null) {
            return false;
        }
        $slug_name = preg_replace('/[^a-zA-Z0-9\']/', '-', $char);
        $data = str_replace(" ", '-', $slug_name);
        return $data;
    }
}

if (!function_exists('limit_word')) {
	function limit_word($text, $limit)
	{
		$explode = explode(' ', $text);
		$total_word = count($explode);
		if ($total_word > $limit) :
			$splice = array_splice($explode, 0, $limit);
			$return = implode(' ', $splice) . '...';
		else :
			$return = $text;
		endif;
		return $return;
	}
}
