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
			|| !($map = $model->getUploadMap())) return;

		// Loop through the all of the upload attributes ...
		foreach($map as $key => $attribute) {

			// If there is a file in the input, move the upload to the
			// config-ed disk and save the resulting URL on the model.
			if ($this->request->hasFile($key)) {
				$url = $this->storage->moveUpload($this->request->file($key));
				$model->setUploadAttribute($attribute, $url);

				// Remove the file from the request object after it's been processed. 
				// This prevents other models that may be touched during the processing 
				// of this request (like because of event handlers) from trying to act 
				// on this upload.
				$this->request->files->remove($key);
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
			|| !($map = $model->getUploadMap())) return;

		// Loop through the all of the upload attributes ...
		foreach($map as $key => $attribute) {
			if (!$path = $model->getAttribute($attribute)) continue;
			$this->storage->delete($path);
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
		return method_exists($model, 'getUploadMap');
	}

}
