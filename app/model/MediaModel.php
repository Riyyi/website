<?php

namespace App\Model;

use App\Traits\Log;

class MediaModel extends Model {

	use Log;

	protected $table = 'media';
	protected $sort = 'filename';

}
