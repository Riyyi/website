<?php

namespace App\Model;

class UserModel extends Model {

	protected $table = 'user';

	//-------------------------------------//

	public function loginAllowed(): bool
	{
		if (property_exists($this, 'failed_login_attempt') && $this->failed_login_attempt < 5) {
			return true;
		}

		return false;
	}

}
