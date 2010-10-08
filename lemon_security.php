<?php
# http://github.com/sofadesign/vincent-helye.com to see how to use it
# TODO: add documentation

function lemon_csrf_token($token_name = 'form_token', $token_expiration_time = 300)
{
  static $name = null;
  static $expiration_time = null;
  static $token;

  if(isset($_SESSION[$token_name]))
  {
    $name = $token_name;
    $token = $_SESSION[$token_name];
    $expiration_time = $_SESSION[$token_name.'_expiration_time'];
  }
  else
  {
    if(!is_null($name)) lemon_csrf_unset_token($name); // unset previous token
    $name = $token_name;
    $token = md5(uniqid('auth', true));
    if(is_null($expiration_time)) $expiration_time = $token_expiration_time;
    $_SESSION[$name] = $token;
    $_SESSION[$name.'_time'] = time();
    $_SESSION[$name.'_expiration_time'] = $expiration_time;
  }
  return array('name' => $name, 'value' => $token, 'expiration_time' => $expiration_time);
}

function lemon_csrf_unset_token($token = null)
{
  if(is_null($token)) $token = lemon_csrf_token();
  $token_name = is_array($token) ? $token['name'] : $token;
  if(!is_null($token_name))
  {
    unset($_SESSION[$token_name]);
    unset($_SESSION[$token_name.'_time']);
    unset($_SESSION[$name.'_expiration_time']);
  }
}

function lemon_csrf_token_age($token = null)
{
  if(is_null($token)) $token = lemon_csrf_token();
  $token_name = is_array($token) ? $token['name'] : $token;
  return time() - $_SESSION[$token_name.'_time'];
}

function lemon_csrf_token_expired($token = null)
{
  if(is_null($token)) $token = lemon_csrf_token();
  return lemon_csrf_token_age($token) > $token['expiration_time'];
}

function lemon_csrf_require_valid_token($msg = 'Cross site request forgery detected. Request aborted', $token = null)
{
  if(is_null($token)) $token = lemon_csrf_token();
  $token_name = $token['name'];
  if($_POST[$token_name] != $_SESSION[$token_name]) halt(HTTP_FORBIDDEN, $msg);
  return true;
}

# HELPERS

function html_form_token_field($token = null)
{
  if(is_null($token)) $token = lemon_csrf_token();
  $token_value = is_array($token) ? $token['value'] : $token;
  return '<input type="hidden" name="form_token" value="'.$token_value.'" id="form_token">';
}




?>