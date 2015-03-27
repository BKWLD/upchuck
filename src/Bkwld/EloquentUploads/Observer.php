<?php namespace Bkwld\EloquentUploads;

// Deps
use Bkwld\EloquentUploads\Storage;
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
	 * @var Bkwld\EloquentUploads\Storage
	 */
	private $storage;

	/**
	 * Dependency injection
	 *
	 * @param Illuminate\Http\Request $request
	 * @param Bkwld\EloquentUploads\Storage $storage
	 */
	public function __construct(Request $request, Storage $storage) {
		$this->request = $request;
		$this->storage = $storage;
	}

	/**
	 * A model is saving
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return void
	 */
	public function onSaving(Model $model) {

		// Check that the model supports uploads.
		if (!in_array('Bkwld\EloquentUploads\SupportsUploads', class_uses($model))
			|| !($config = $model->getUploadConfig())) return;

		// Loop through the attributes and see if the input contains files keyed as such
		foreach($config as $key => $attribute) {
			if ($this->request->hasFile($key)) {
				$dst = $this->storage->moveUpload($this->request->file($key));
				\Log::info($dst);
				// Convert $dst to a url
				// $model->setUploadAttribute($attribute, $url);
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