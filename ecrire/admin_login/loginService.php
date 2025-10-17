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

    if (!isset($this->data)) {
		$records['data'] = array('status'=>402,'type' =>'error','message'=>'Error:: existen registros para el usuario'); 
		$var = var2js($records);
		echo $var;   
   }
    
        $id_auteur = intval($this->data['id_auteur']);

	    return $this->apisKey->asignarSecretKey($id_auteur);
		
	}
			
}