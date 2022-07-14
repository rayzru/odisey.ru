<?php

namespace odissey;

use UploadHandler;

class uploadController extends UploadHandler
{
	protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $range = null) {
		$file = parent::handle_file_upload($uploaded_file, $name, $size, $type, $error, $index, $range);
		return $file;
	}

	protected function set_additional_file_properties($file) {
		parent::set_additional_file_properties($file);
	}

	public function delete($print_response = true) {
		$response = parent::delete(false);
		foreach ($response as $name => $deleted) {
			if ($deleted) {
				//
			}
		}
		return $this->generate_response($response, $print_response);
	}
}
