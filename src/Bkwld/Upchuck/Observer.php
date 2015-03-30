<?php namespace Bkwld\Upchuck;

// Deps
use Bkwld\Upchuck\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Respond to model saving and deleting events
 */
class Observer {

	/**
	 * @var Illuminate\Http\Request
	 */
	private $request;

	/**
	 * @var Bkwld\Upchuck\Storage
	 */
	private $storage;

	/**
	 * @var callable
	 */
	private $url_generator;

	/**
	 * Dependency injection
	 *
	 * @param Illuminate\Http\Request $request
	 * @param Bkwld\Upchuck\Storage $storage
	 * @param callable $url_generator 
	 */
	public function __construct(Request $request, Storage $storage, callable $url_generator) {
		$this->request = $request;
		$this->storage = $storage;
		$this->url_generator = $url_generator;
	}

	/**
	 * A model is saving
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return void
	 */
	public function onSaving(Model $model) {

		// Check that the model supports uploads.
		if (!in_array('Bkwld\Upchuck\SupportsUploads', class_uses($model))
			|| !($config = $model->getUploadConfig())) return;

		// Loop through the attributes and see if the input contains files with those keys
		foreach($config as $key => $attribute) {
			if ($this->request->hasFile($key)) {

				// Move the upload to the config-ed disk, convert the new path
				// to a URL, and save it on the model.
				$path = $this->storage->moveUpload($this->request->file($key));
				$url = call_user_func($this->url_generator, $path);
				$model->setUploadAttribute($attribute, $url);
			}
		}
		
		// Check if 

	}

	/**
	 * A model is deleting
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return void
	 */
	public function onDeleting(Model $model) {

	}

}