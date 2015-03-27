<?php namespace Bkwld\EloquentUploads;

// Deps
use Illuminate\Database\Eloquent\Model;

/**
 * Inspect models for `upload_attributes` and expose methods
 * for dealing with it. 
 */
class Inspector {

	/**
	 * The model being inspected
	 *
	 * @var Illuminate\Database\Eloquent\Model 
	 */
	private $model;

	/**
	 * Constructor
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 */
	public function __construct(Model $model) {
		$this->model = $model;
	}

	/**
	 * Return all the input key names which may be in the key
	 * or the value.
	 * 
	 * @return array
	 */
	public function getInputKeys() {
		$attributes = $this->model->getUploadAttributes();
		return array_map(function($val, $key) {
			if (is_numeric($key)) return $val;
			return $key;
		}, $attributes, array_keys($attributes));
	}

}