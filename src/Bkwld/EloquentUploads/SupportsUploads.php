<? namespace Bkwld\EloquentUploads;


trait SupportsUploads {

	/**
	 * Return the raw upload_attributes configuration
	 *
	 * @return array 
	 */
	public function getUploadAttributes() {
		if (!isset($this->upload_attributes)) return [];
		return $this->upload_attributes;
	}

	/**
	 * Massage the attribute configuration so that all keys represent input fields 
	 * and all values represent model attributes.
	 *
	 * @return array  
	 */
	public function getUploadConfig() {
		$attributes = $this->getUploadAttributes();
		return array_map(function($val, $key) {
			if (is_numeric($key)) return $val;
			return $key;
		}, $attributes, array_keys($attributes));
	}

	/**
	 * Set the URL to an uploaded file on the model
	 *
	 * @param string $attribute 
	 * @param string $url 
	 */
	public function setUploadAttribute($attribute, $url) {
		$this->setAttribute($attribute, $url);
	}

}