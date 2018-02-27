<?php

class Client extends ActiveRecord\Model {

	var $password = FALSE;
	function before_save()
	{
		if($this->password)
			$this->hashed_password = $this->hash_password($this->password);
	}
	
	function set_password($plaintext)
	{

		$this->hashed_password = $this->hash_password($plaintext);
	}
	private function hash_password($password)
	{
		$salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
		$hash = hash('sha256', $salt . $password);
		
		return $salt . $hash;
	}

	function validate_password($password)
	{
		$salt = substr($this->hashed_password, 0, 64);
		$hash = substr($this->hashed_password, 64, 64);
		
		$password_hash = hash('sha256', $salt . $password);
		
		return $password_hash == $hash;
	}


	static $has_many = array(
    array('projects'),
    array('invoices')
    );

    static $belongs_to = array(
    array('company')
    );
}