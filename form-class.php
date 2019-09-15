<?php
class Cotd_Form {
  var $inputs=array();
  var $settings = array();
  function Cotd_Form($settings = array()) {
    if (is_array($settings)) $this->settings = $settings;
  }
  function add_used_inputs() {
    $value=implode(',',array_unique($this->inputs));
    $this->add_hidden('___msp_form_used_inputs', $value);
  }
  function get_post_data() {
    if(!isset($_POST['___msp_form_used_inputs'])) {
      return $_POST;
    }
    $data=array();
    $inputs=explode(',', $_POST['___msp_form_used_inputs']);
    foreach((array)$inputs as $var) {
      $real_var=str_replace('[]','',$var);
      if(isset($_POST[$real_var])) {
        $data[$real_var]=stripslashes_deep($_POST[$real_var]);
      }
      else if($var!=$real_var) {
        $data[$real_var]=array();
      }
      else {
        $data[$real_var]='';
      }
    }
    return $data;
  }

  function add_text($name,$options=array()) {
    if(!isset($options['class'])) $options['class']='regular-text';
    $this->_add_input('text',$name,$options);
  }
  function add_textarea($name,$options=array()) {
    $this->_add_input('textarea',$name,$options);
  }
  function add_numeric($name,$options=array()) {
    if(!isset($options['class'])) $options['class']='regular-text';
    $this->_add_input('number',$name,$options);
  }
  //function add_checkbox($name, $options=array()) {
  //  if ($this->_get_setting($name)) $options['checked'] = 'checked';
  //  $this->_add_input('checkbox', $name, $options);
  //}
  function add_file($name, $options=array()){
    $this->_add_input('file', $name, $options);
  }
  //function add_password($name, $options=array()){
  //  $this->add_input('password', $name, $options);
  //}
  function add_select($name, $values, $options=array()) {
    $options['values'] = $values;
    $this->_add_input('select', $name, $options);
  }
  //function add_radio($name, $value, $options=array()) {
  //  if ($this->_get_setting($name) == $value) $options['checked'] = 'checked';
  //  $options['value'] = $value;
  //  $this->_add_input('radio', $name, $options);
  //}
  //function add_multicheckbox($name, $value, $options=array()) {
  //  $settings = $this->_get_setting($name);
  //  if (is_array($setting) && in_array($value, $setting)) $options['checked'] = 'checked';
  //  $options['value'] = $value;
  //  $this->_add_input('checkbox', "{$name}[]", $options);
  //}
  function add_hidden($name, $value, $options=array()) {
    $options['value'] = $value;
    $this->_add_input('hidden', $name, $options);
  }
  function add_submit($name,$description,$options=array()) {
    $options['value']=$description;
    $this->_add_input('submit',$name,$options);
  }
  function add_button($name,$description,$options=array()) {
    $options['value']=$description;
    $this->_add_input('button',$name,$options);
  }
  function add_reset($name,$description,$options=array()) {
    $options['value']=$description;
    $this->_add_input('reset',$name,$options);
  }
  function add_image($name, $image_url, $options=array()) {
    $options['src'] = $image_url;
    $this->_add_input('image', $name, $options);
  }

  // This function should not be called directly.
  function _add_input($type,$name,$options=array()) {
    $this->inputs[]=$name;
    $settings_var = str_replace('[]', '', $name);
    $css_var = str_replace('[', '-', str_replace(']', '', $settings_var));
    if(!is_array($options)) $options=array();
    $options['type']=$type;
    $options['name']=$name;
    if (!isset($options['value']) && 'checkbox' != $type) {
      $options['value'] = $this->_get_setting($settings_var);
    }
    if ('radio' == $type || $settings_var != $name) {
      if (empty($options['class'])) $options['class'] = $css_var;
    } else {
      if (empty($options['id'])) $options['id'] = $css_var;
    }
    $scrublist=array($type=>array());
    $scrublist['textarea']=array('value','type');
    $scrublist['file'] = array('value');
    $scrublist['dropdown'] = array('value', 'values', 'type');
    $attributes = '';
    foreach($options as $var => $val) {
      if(!is_array($val) && !in_array($var,$scrublist[$type]))
      $attributes.="$var='".esc_attr($val)."' ";
    }
    if ('textarea'==$options['type']) {
      echo "<textarea $attributes>";
      echo format_to_edit($options['value']);
      echo "</textarea>\n";
    } else if ('select' == $options['type']) {
      echo "<select $attributes>\n";
      if (is_array($options['values'])) {
        foreach ($options['values'] as $val=>$name) {
          $selected = ($options['values']==$val)?' selected ="selected"' : '';
          echo "<option value=\"$val\"$selected>$name</option>\n";
        }
      }
      echo "</select>\n";
    } else {
      echo "<input $attributes />\n";
    }
  }

  //This function should not be called directly
  function _get_setting($name) {
    if (isset($this->settings[$name])) {
      return $this->settings[$name];
    }
    return '';
  }
}
