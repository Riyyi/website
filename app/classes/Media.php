<?php

namespace App\Classes;

use App\Model\MediaModel;

class Media {

	public static $pagination = 20;
	public static $directory = 'media';

	public static function errorMessage(int $errorCode): string
	{
		// https://www.php.net/manual/en/features.file-upload.errors.php
		$errorMessages = [
			0  => 'Uploaded with success.',
			1  => 'Uploaded file exceeds the maximum upload size. (1)', // php.ini
			2  => 'Uploaded file exceeds the maximum upload size. (2)', // HTML Form
			3  => 'Uploaded file was only partially uploaded.',
			4  => 'No file was uploaded.',
			6  => 'Missing a temporary folder.',
			7  => 'Failed to write file to disk.',
			8  => 'A PHP extension stopped the file upload.',
			9  => 'User was not logged in.',
			10 => 'Missing media folder.',
			11 => 'Uploaded file has invalid MIME type.',
			12 => 'Uploaded file exceeds the maximum upload size. (3)', // Media.php
			13 => 'Uploaded file already exists.',
			14 => 'DB entry creation failed.',
			15 => 'Moving file from temporary location failed.',
		];

		return $errorMessages[$errorCode];
	}

	public static function deleteMedia(int $id): bool
	{
		if (!User::check()) {
			return false;
		}

		$media = MediaModel::find($id);

		if (!$media->exists()) {
			return false;
		}

		// Delete file
		$file = self::$directory . '/' . $media->filename . '.' . $media->extension;
		if (file_exists($file)) {
			unlink($file);
		}

		return $media->delete();
	}

	public static function uploadMedia(bool $overwrite = false): int
	{
		// Check if User is logged in
		if (!User::check()) {
			return 9;
		}

		// Check if "media" directory exists
		if (!is_dir(self::$directory) && !mkdir(self::$directory, 0755)) {
			return 10;
		}

		$files = $_FILES['file'];

		// Check for file errors
		foreach ($files['error'] as $error) {
			if ($error != 0) {
				return $error;
			}
		}

		if (!Media::checkMimeType($files['type'], $files['tmp_name'])) {
			return 11;
		}

		if (!Media::checkSize($files['size'])) {
			return 12;
		}

		// Append random string to filename that already exists
		$nameExt = Media::duplicateName($files['name'], $overwrite);

		// Check if the file already exists
		$hash = Media::hashExists($files['tmp_name']);
		if (!$hash[0]) {
			return 13;
		}

		$count = count($files['name']);
		for ($i = 0; $i < $count; $i++) {
			$filename = $nameExt[0][$i];
			$extension = $nameExt[1][$i];
			$md5 = $hash[1][$i];
			$tmpName = $files['tmp_name'][$i];

			// Store record
			$media = MediaModel::create([
				'filename' => $filename,
				'extension' => $extension,
				'md5' => $md5,
			]);

			if (!$media->exists()) {
				return 14;
			}

			// Store image
			$name = self::$directory . '/'. $filename . '.' . $extension;
			if (!move_uploaded_file($tmpName, $name)) {
				return 15;
			}

			// After storing successfully, remove old entries with duplicate names
			if ($overwrite) {
				Media::destroyDuplicates($filename, $extension);
			}
		}

		return 0;
	}

	//-------------------------------------//

	private static function checkMimeType(array $fileTypes, array $fileTmpNames): bool
	{
		$allowedMimeType = [
			// .tar.gz
			'application/gzip',
			'application/json',
			'application/octet-stream',
			'application/pdf',
			'application/sql',
			// .tar.xz
			'application/x-xz',
			'application/xml',
			'application/zip',
			'audio/mp3',
			'audio/mpeg',
			'audio/ogg',
			'audio/wav',
			'audio/webm',
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/gif',
			'text/plain',
			'text/csv',
			'text/xml',
			'video/mp4',
			'video/webm',
			// .doc
			'application/msword',
			// .xls
			'application/vnd.ms-excel',
			// .ppt
			'application/vnd.ms-powerpoint',
			// .docx
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			// .xlsx
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			// .pptx
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		];

		// Files type check
		$count = count($fileTypes);
		for ($i = 0; $i < $count; $i++) {
			if ($fileTypes[$i] != mime_content_type($fileTmpNames[$i]) ||
				!in_array($fileTypes[$i], $allowedMimeType)) {
				return false;
			}
		}

		return true;
	}

	private static function checkSize(array $fileSizes): bool
	{
		// Files should not exceed 10MiB
		foreach ($fileSizes as $fileSize) {
			if ($fileSize > 10485760) {
				return false;
			}
		}

		return true;
	}

	private static function duplicateName(array $filenames, bool $overwrite): array
	{
		// Split name from extension
		$names = [];
		$extensions = [];
		foreach ($filenames as $name) {
			$dotPos = strrpos($name, '.');
			$names[] = substr($name, 0, $dotPos);
			$extensions[] = substr($name, $dotPos + 1);
		}

		// Early return if names are specified to be overwritten
		if ($overwrite) {
			return [$names, $extensions];
		}

		// Get duplicate filenames
		$in1 = str_repeat('?, ', count($names) - 1) . '?';
		$in2 = str_repeat('?, ', count($extensions) - 1) . '?';
		$data = array_merge($names, $extensions);
		$duplicates = MediaModel::selectAll(
			'*', "WHERE filename IN ($in1) AND extension IN ($in2)", $data, '?'
		);

		foreach ($filenames as $key => $filename) {
			$hasDuplicate = false;
			foreach ($duplicates as $duplicate) {
				if ($filename == $duplicate['filename'] . '.' . $duplicate['extension']) {
					$hasDuplicate = true;
					break;
				}
			}

			// Append to filename if there are duplicates
			if ($hasDuplicate) {
				$names[$key] = $names[$key] . '-' . _randomStr(5);
			}
		}

		return [$names, $extensions];
	}

	private static function hashExists(array $fileTmpNames): array
	{
		$md5 = [];
		foreach ($fileTmpNames as $tmpName) {
			$md5[] = md5_file($tmpName);
		}

		// If exact file already exists
		$in = str_repeat('?, ', count($md5) - 1) . '?';
		$exists = MediaModel::selectAll(
			'*', "WHERE md5 IN ($in)", $md5, '?'
		);

		if (!empty($exists)) {
			return [false];
		}

		return [true, $md5];
	}

	private static function destroyDuplicates(string $filename, string $extension): void
	{
		$media = MediaModel::selectAll(
			'*', 'WHERE filename = ? AND extension = ? ORDER BY id ASC',
			[$filename, $extension], '?'
		);

		if (!_exists($media)) {
			return;
		}

		foreach ($media as $key => $value) {

			// Dont delete the new entry
			if ($key === array_key_last($media)) {
				return;
			}

			MediaModel::destroy($value['id']);
		}
	}

}

// @Todo
// - If a file fails to store in the loop, destruct all files of that request, by tracking all IDs
