<?php namespace Bkwld\Upchuck;

// Deps
use Bkwld\Upchuck\Helpers;
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
     * @var Bkwld\Upchuck\Helpers
     */
    private $helpers;

    /**
     * @var League\Flysystem\MountManager
     */
    private $manager;


    /**
     * Dependency injection
     */
    public function __construct(MountManager $manager, Helpers $helpers) {
        $this->manager = $manager;
        $this->helpers = $helpers;
    }

    /**
     * Move an uploaded file from the /tmp directory of the local filesystem
     * to the configured location
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return string $url A URL to to the file, resolveable in HTML
     */
    public function moveUpload(UploadedFile $file) {

        // Nest the uploaded file into unique sub directory and a unqiue name
        $path = $this->makeNestedAndUniquePath($file->getClientOriginalName());

        // Move the uploaded file to the destination using Flysystem and return
        // the new path
        $this->manager->move('tmp://'.$file->getFilename(), 'disk://'.$path);

        // Return the URL of the upload.
        return $this->helpers->url($path);
    }

    /**
     * Create a unique directory and filename
     *
     * @param string $filename
     * @param League\Flysystem\Filesystem|void $disk
     * @return string New path and filename
     */
    public function makeNestedAndUniquePath($filename, $disk = null) {

        // If no disk defined, get it from the current mount mananger
        if (empty($disk)) $disk = $this->manager->getFilesystem('disk');

        // Remove unsafe characters from the filename
        // https://regex101.com/r/mJ3sI5/1
        $filename = preg_replace('#[^\w\-_\.]#i', '_', $filename);

        // Create nested folders to store the file in
        $dir = '';
        for ($i=0; $i<$this->depth; $i++) {
            $dir .= str_pad(mt_rand(0, $this->length - 1), strlen($this->length), '0', STR_PAD_LEFT).'/';
        }

        // If this file doesn't already exist, return it
        $path = $dir.$filename;
        if (!$disk->has($path)) return $path;

        // Get a unique filename for the file and return it
        $file = pathinfo($filename, PATHINFO_FILENAME);
        $i = 1;
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        while ($disk->has($path = $dir.$file.'-'.$i.'.'.$ext)) { $i++; }
        return $path;

    }

    /**
     * Delete an upload
     *
     * @param string $url A URL like was returned from moveUpload()
     * @return void
     */
    public function delete($url) {

        // Convert to a path
        $path = $this->helpers->path($url);

        // Delete the path if it still exists
        if ($this->manager->has('disk://'.$path)) $this->manager->delete('disk://'.$path);
    }

}
