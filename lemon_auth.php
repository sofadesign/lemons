<?php
# http://github.com/sofadesign/vincent-helye.com for usage example
# TODO: documentation…



/**
 * Set and returns authentication users/admin_users
 *
 * @param string $conf_file_path 
 * @return void
 */
function lemon_auth($conf_file_path = null)
{
  static $users = null;
  static $admin_users = null;
  if(!is_null($conf_file_path) && is_null($users))
  {
    require $conf_file_path;
  }
  if(is_null($users)) trigger_error("lemon_auth not initialized.", E_USER_ERROR);
  return array('users' => $users, 'admin_users' => $admin_users );
}

function lemon_auth_is_authorized($user, $password)
{
  $auth = lemon_auth();
  if(isset($auth['users'][$user])) return $auth['users'][$user] === $password;
  return false;
}

function lemon_auth_is_admin($user)
{
  $auth = lemon_auth();
  return in_array($user, $auth['admin_users']);
}

function lemon_auth_required($unauthorized_redirect_url)
{
  if(!isset($_SESSION['username'])) redirect_to($unauthorized_redirect_url);
  exit; // to be sure
}

function lemon_auth_logged_in()
{
  if(isset($_SESSION['username']) && lemon_auth_user_exists($_SESSION['username']))
  {
    return (string) $_SESSION['username'];
  }
  return false;
}

function lemon_auth_logout()
{
  unset($_SESSION['username']);
}

function lemon_auth_user_exists($user)
{
  $auth = lemon_auth();
  return isset($auth['users'][$user]);
}


function lemon_auth_filter_form($username, $password)
{
  $errors = array();
  if(!isset( $username, $password))
  {
    $errors[] = 'Identifiant ou mot de passe incorrect';
  }
  elseif(strlen($username) > 30 || strlen($username) < 3)
  {
    $errors[] = 'Longueur de l\'identifiant incorrecte. Il doit être composé de 3 caractères minimum et de 30 caractères maximum.';
  }
  elseif(strlen($password) > 30 || strlen($password) < 3)
  {
    $errors[] = 'Longueur du mot de passe incorrecte. Il doit être composé de 3 caractères minimum et de 30 caractères maximum.';
  }
  elseif(!ctype_alnum($username))
  {
    $errors[] = "L'identifiant ne peut contenir que des caractères alpha-numériques.";
  }
  elseif(!ctype_alnum($password))
  {
    $errors[] = "L'identifiant ne peut contenir que des caractères alpha-numériques.";
  }
  return $errors;
}


function lemon_auth_login($username, $password, $authorized_url)
{
  $auth = lemon_auth();
  if(lemon_auth_logged_in()) halt(SERVER_ERROR, "User is already logged in.");
  
  $errors = lemon_auth_filter_form($username, $password);
  if(lemon_auth_is_authorized($username, $password))
  {
    $_SESSION['username'] = $username;
    redirect_to($authorized_url);
  }
  else
  {
    $errors[] = "Identifiant ou mot de passe incorrect";
  }
  return $errors;
}
