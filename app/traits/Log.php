<?php

namespace App\Traits;

use App\Classes\User;

use App\Model\LogModel;

trait Log {
	public function save(): bool
	{
		// Get timestamp
		$date = date('Y-m-d H:i:s');

		// Create log
		if (!_exists([$this->log_id])) {
			$log = new LogModel;
			$log->{self::CREATED_AT} = $date;
			$log->{self::UPDATED_AT} = $date;
			$log->user_id = User::getSession()['id'];
			if (!$log->save()) {
				return false;
			}

			// Add log to new model
			$this->log_id = $log->{$log->primaryKey};
		}
		// Update log
		else {
			$log = LogModel::findOrFail($this->log_id);
			$log->{self::UPDATED_AT} = $date;
		}

		// Save model
		if (!parent::save()) {
			// Clean up the log if model creation failed
			if (!_exists([$this->{$this->primaryKey}])) {
				$log->delete();
			}

			return false;
		}

		// Update table and user ID
		$log->table_name = $this->table;
		$log->table_id = $this->{$this->primaryKey};
		$log->user_id = User::getSession()['id'];
		return $log->save();
	}

	public function delete(): bool
	{
		// Exit if there is no log
		if (!_exists([$this->log_id])) {
			return false;
		}

		$log = $this->log_id;

		// Delete model
		if (!parent::delete()) {
			return false;
		}

		// Delete log
		return LogModel::destroy($log);
	}
}
