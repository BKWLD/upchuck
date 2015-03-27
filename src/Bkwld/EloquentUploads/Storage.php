<?php namespace Bkwld\EloquentUploads;

// Deps
use League\Flysystem\MountManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interact with disks via Flysystem
 */
class Storage {

	/**
	 * @var League\Flysystem\MountManager
	 */
	private $manager;

	/**
	 * Dependency injection
	 */
	public function __construct(MountManager $manager) {
		$this->manager = $manager;
	}

	/**
	 * Move an uploaded file from the /tmp directory of the local filesystem
	 * to the configured location
	 *
	 * @param Symfony\Component\HttpFoundation\File\UploadedFile $file 
	 * @return string $dst, Absolute path to the file
	 */
	public function moveUpload(UploadedFile $file) {
		$src = 'tmp://'.$file->getFilename();
		$dst = 'dst://'.$file->getClientOriginalName();
		$this->manager->move($src, $dst);
		// dd('Dude');
	}

}