<?php
function Get_Cotd_Settings($name=null) {
  static $settings=null;
  if(is_null($settings)) {
    $settings=get_option('cotd-settings');
    if(!is_array($settings)) $settings=array();
    $defaults=Get_Default_Settings();
    $settings=array_merge($defaults,$settings);
  }
  if(is_null($name)) return $settings;
  if(isset($settings[$name])) return $settings[$name];
  return '';
}
function Update_Cotd_Settings($settings) {
  update_option('cotd-settings', $settings);
  $redirect_url=array_shift(explode('?', $_SERVER['REQUEST_URI']));
  $redirect_url.='?page='.$_REQUEST['page'].'&updated=true';
  wp_redirect($redirect_url);
}
function Update_Cotd_Single_Setting($field, $val) {
  $current_settings=get_option('cotd-settings');
  $current_settings[$field] = $val;
  update_option('cotd-settings', $current_settings);
  $redirect_url=array_shift(explode('?', $_SERVER['REQUEST_URI']));
  $redirect_url.='?page='.$_REQUEST['page'].'&updated=true';
  wp_redirect($redirect_url);
}
