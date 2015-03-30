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
	 * Dependency injection
	 *
	 * @param Illuminate\Http\Request $request
	 * @param Bkwld\Upchuck\Storage $storage
	 */
	public function __construct(Request $request, Storage $storage) {
		$this->request = $request;
		$this->storage = $storage;
	}

	/**
	 * A model is saving, check for files being uploaded
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return void
	 */
	public function onSaving(Model $model) {

		// Check that the model supports uploads through Upchuck
		if (!$this->supportsUploads($model)
			|| !($config = $model->getUploadConfig())) return;

		// Loop through the all of the upload attributes ...
		foreach($config as $key => $attribute) {

			// If there is a file in the input, move the upload to the
			// config-ed disk and save the resulting URL on the model.
			if ($this->request->hasFile($key)) {
				$url = $this->storage->moveUpload($this->request->file($key));
				$model->setUploadAttribute($attribute, $url);
			}

			// If the attribute field is dirty, delete the old image
			if ($model->isDirty($attribute) && ($old = $model->getOriginal($attribute))) {
				$this->storage->delete($old);
			}
		}
	}

	/**
	 * A model has been deleted, trash all of it's files
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return void
	 */
	public function onDeleted(Model $model) {

		// Check that the model supports uploads through Upchuck
		if (!$this->supportsUploads($model)
			|| !($config = $model->getUploadConfig())) return;

		// Loop through the all of the upload attributes ...
		foreach($config as $key => $attribute) {
			$this->storage->delete($model->getAttribute($attribute));
		}

	}

	/**
	 * Check that the model supports uploads through Upchuck
	 *
	 * @param Illuminate\Database\Eloquent\Model $model 
	 * @return boolean 
	 */
	public function supportsUploads($model) {
		return in_array('Bkwld\Upchuck\SupportsUploads', class_uses($model));
	}

}