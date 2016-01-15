<?php namespace Bkwld\Upchuck;

// Deps
use Bkwld\Upchuck\Storage;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
	 * @param Bkwld\Upchuck\Storage $storage
	 */
	public function __construct(Storage $storage) {
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
			|| !($attributes = $model->getUploadAttributes())) return;

		// Loop through the all of the upload attributes ...
		foreach($attributes as $attribute) {

			// Check if there is an uploaded file in the upload attribute
			if (($file = $model->getAttribute($attribute))
				&& is_a($file, UploadedFile::class)) {

				// Move the upload and get the new URL
				$url = $this->storage->moveUpload($file);
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
			|| !($attributes = $model->getUploadAttributes())) return;

		// Loop through the all of the upload attributes ...
		foreach($attributes as $attribute) {
			if (!$url = $model->getAttribute($attribute)) continue;
			$this->storage->delete($url);
		}

	}

	/**
	 * Check that the model supports uploads through Upchuck.  Not detecting the
	 * trait because it doesn't report to subclasses.
	 *
	 * @param Illuminate\Database\Eloquent\Model $model
	 * @return boolean
	 */
	public function supportsUploads($model) {
		return method_exists($model, 'getUploadAttributes');
	}

}
