<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
class Authenticator {
  private $login;
  private $password;

  public function __construct($login, $password) {
    $this->login = trim($login);
    $this->password = $password;
  }

  public function authenticate() {
    if (empty($this->login) || empty($this->password)) {
      throw new Exception("Login y contraseña son requeridos");
    }
	include_spip('inc/auth');
    $var_auth = auth_identifier_login($this->login, $this->password);
	
    if (!$var_auth) {
     spip_log("var_auth $var_auth");
    }

    return $var_auth;
  }
}