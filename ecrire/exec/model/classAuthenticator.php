<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 

/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       febrero-2025
 * @Version:    1.0
 * @Developer:  Hosmmer Eduardo Pinto Rojas
 * @email: holmespinto@unicesar.edu.co
 **/ 

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
      throw new Exception("Credenciales inválidas");
    }

    return $var_auth;
  }
}