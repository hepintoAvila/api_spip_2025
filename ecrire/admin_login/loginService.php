<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
} 
	
include_spip('ecrire/classes/security');	
class LoginService {
	
	private $apisKey;
	public $data;
	
    public function __construct($data) {
     $this->apisKey = new ApiKeyManager($data);
     $this->data=$data;
	}
	public function addKey(){
		$chartic=array();
	    $id_auteur=$this->data['id_auteur'];
		
	    return $this->apisKey->asignarSecretKey($id_auteur);
		
	}
			
}