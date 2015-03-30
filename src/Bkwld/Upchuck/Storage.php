<?php namespace Bkwld\Upchuck;

// Deps
use League\Flysystem\MountManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interact with disks via Flysystem
 */
class Storage {

	/**
	 * How deep to store files
	 *
	 * @var integer
	 */
	private $depth = 2;
	
	/**
	 * How many folders are in each depth
	 *
	 * @var integer
	 */
	private $length = 16;

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
	 * @return string $path The path of the file on the disk, relateive to the disk
	 */
	public function moveUpload(UploadedFile $file) {

		// Nest the uploaded file into unique sub directory and a unqiue name
		$path = $this->makeNestedAndUniquePath($file->getClientOriginalName());

		// Move the uploaded file to the destination using Flysystem and return
		// the new path
		$this->manager->move('tmp://'.$file->getFilename(), 'disk://'.$path);
		return $path;
	}

	/**
	 * Create a unique directory and filename
	 *
	 * @param string $filename 
	 * @return string New path and filename
	 */
	public function makeNestedAndUniquePath($filename) {

		// Create nested folders to store the file in
		$dir = '';
		for ($i=0; $i<$this->depth; $i++) {
			$dir .= str_pad(mt_rand(0, $this->length - 1), strlen($this->length), '0', STR_PAD_LEFT).'/';
		}

		// If this file doesn't already exist, return it
		$path = $dir.$filename;
		if (!$this->manager->has('disk://'.$path)) return $path;

		// Get a unique filename for the file and return it
		$file = pathinfo($filename, PATHINFO_FILENAME);
		$i = 1;
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		while ($this->manager->has('disk://'.($path = $dir.$file.'-'.$i.'.'.$ext))) { $i++; }
		return $path;

	}

}