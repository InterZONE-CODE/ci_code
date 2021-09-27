<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* MY_Form_validation
*
* note: when update grocerycrud be sure that you extend MY_Form_validation and not 
        extend CI_Form_validation to activate this addtional methods and be sure that 
        you connect this methods with grocerycrud correctly.

        steps to do if you upgrade your grocerycrud:
        --------------------------------------------

        1- search for class grocery_CRUD_Form_validation extends CI_Form_validation
        2- make it class grocery_CRUD_Form_validation extends MY_Form_validation

*/
class MY_Form_validation extends CI_Form_validation
{
	
	protected $CI;

    public function __construct($rules = array())
    {
        parent::__construct($rules);
        $this->CI =& get_instance();
    }

	 /**
    * datetime validation
    * 
    * @param String $date 
    *
    * @return boolean
    */
    public function valid_datetime($date)
    {
        $format = 'd/m/Y H:i:s';
        //echo $format;
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
    * Valid number
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_number($str)
    {
       $this->form_validation->set_message('valid_number','The %s field may only contain number characters.');

       if (preg_match("/[0-9]/", $str)) {
        return true;
       }

       return false;
    }

    /**
    * Regular expression validation
    * 
    * @param String $str 
    * @param String $val 
    *
    * @return boolean
    */
    public function regex($str, $val = null)
    {
       $this->form_validation->set_message('regex','The %s field must be in accordance with the pattern.');

       if ($ret = preg_match("/".$val."/", $str)) {
        return true;
       }

       return false;
    }

    

    /**
    * Date validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_date($str)
    {
       $this->form_validation->set_message('valid_date','The %s field may only contain date.');

       if ($ret = preg_match("[(\d{4})\-(\d{2})\-(\d{2})]", $str)) {
        return true;
       }

       return false;
    }

    /**
    * Group validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_group($str)
    {
       $str = json_decode($str);
       $this->form_validation->set_message('valid_group','The %s field may only contain array.');

       if (is_array($str)) {
         return true;
       }

       return false;
    }

    /**
    * Valid regex validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_regex($str) {
       $this->form_validation->set_message('valid_regex','The %s field pattern "'.$str.'" is not valid.');

       if (@preg_match($str, null) === false) {
        return false;
       }

       return true;
    }
    
    /**
    * Valid regex validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_alpha_numeric_spaces_underscores($str) {
       $this->form_validation->set_message('valid_alpha_numeric_spaces_underscores','The %s field input only alpha numeric spaces and underscores.');

        return (bool) preg_match('/^[A-Z0-9 _]+$/i', $str);
    }

    /**
    * Valid disallowed chars
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_disallowed_chars($str) {
       $this->form_validation->set_message('valid_disallowed_chars','The %s character '.$chars.' dis allowed.');

       if (preg_match('(\')/i', $str)) {
        return false;
       }
       return true;
    }
    
    /**
    * Valid regex validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_json($str) {
       $this->form_validation->set_message('valid_json','The %s field input not valid json format.');

        json_decode($str);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
    * Valid multiple value validation
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_multiple_value($str) {
       $this->form_validation->set_message('valid_multiple_value','The %s field input not valid multiple value ex val1, val2, val3, more.');

        return (count(explode(',', $str)));
    }

    /**
    * Valid table is avaiable
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_table_avaiable($str) {
       $this->form_validation->set_message('valid_table_avaiable','The %s is not valid.');
       $tables = $this->db->list_tables();

       return in_array($str, $tables);
    }

    /**
    * Valid captcha
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_captcha($str) {
       $this->form_validation->set_message('valid_captcha','You must submit %s word that appears in the %s image.');

       $expiration = time() - 7200;

       $sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
       $binds = array($str, $this->input->ip_address(), $expiration);
       $query = $this->db->query($sql, $binds);
       $row = $query->row();

       if ($row->count == 0) {
            return false;
       }

       return true;
    }

    /**
    * Valid extension list
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_extension_list($str) {
       $this->load->helper('file');

       $mimes = get_mimes();
       $mime_arr = [];
       $ret = TRUE;
       $mime_not_valid = [];

       foreach ($mimes as $key => $value) {
           $mime_arr[] = $key;
       }
       if (strpos($str, ',') === FALSE)
        {
            $mime_not_valid[] = $str;
            $ret = in_array(strtolower(trim($str)), $mime_arr);
        }

        foreach (explode(',', $str) as $extension)
        {
            if (trim($extension) !== '' && in_array(strtolower(trim($extension)), $mime_arr) === FALSE)
            {
                $mime_not_valid[] = $extension;
                $ret = FALSE;
            }
        }

        $this->form_validation->set_message('valid_extension_list','The %s extension "'.implode(',', $mime_not_valid).'" is not valid.');

        return $ret;
    }

    /**
    * Validation max selected option
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_max_selected_option($str, $val = 2)
    {

       $field_match = $this->check_field_has_rules('valid_max_selected_option\['.$val.'\]', $str);
       $this->form_validation->set_message('valid_max_selected_option','The %s field selected options maximum is "'.$val.'".');

       if ($field_match) {
           $field = $this->input->post($field_match);

           if (is_array($field)) {
             if (count($field) <= $val) {
                return true;
             }
           }
       }

       return false;
    }

    /**
    * Validation min selected option
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function valid_min_selected_option($str, $val = 2, $additional = 55)
    {
       $field_match = $this->check_field_has_rules('valid_min_selected_option\['.$val.'\]', $str);

       if ($field_match) {
           $field = $this->input->post($field_match);

           $this->form_validation->set_message('valid_min_selected_option','The %s field selected options minimum is "'.$val.'".');

           if (is_array($field)) {
             if (count($field) < $val) {
                return false;
             }
           }
       }

       return true;
    }

    /**
    * Check field has rules
    * 
    * @param String $str 
    *
    * @return boolean
    */
    public function check_field_has_rules($rule_name = null, $post_data = null)
    {
        foreach ($this->form_validation->_field_data as $field_name => $option) {
            if (isset($option['rules'])) {
                foreach ($option['rules'] as $rule) {
                    if (preg_match("/".$rule_name."/", $rule)) {
                        if (is_array($option['postdata'])) {
                            if (in_array($post_data, $option['postdata'])) {
                                return str_replace('[]', '', $field_name);
                            } 
                        }
                    }
                }
            }
        }

        return false;
    }


}
?>