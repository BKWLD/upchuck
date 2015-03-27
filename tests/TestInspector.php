<?php

use Bkwld\EloquentUploads\Inspector;
use Mockery;

class TestInspector extends PHPUnit_Framework_TestCase {

	private $inspector;
	public function setUp() {

		// Mock what we need from the model
		$mock = Mockery::mock('Illuminate\Database\Eloquent\Model')
			->shouldReceive('getUploadAttributes')
			->andReturn([

				// The input field and the model field are the same
				'image', 

				// The input is array-like and will be stored like $model->copy = {image:"/file/path"}
				'copy[image]',

				// The input is array-like and will be stored like $model->copy = [{image:"/file/path"}]
				'bullets[][image]', 

				// The input field is "image" and the model field is "background"
				'bkgd' => 'background', 

				// Not so sure if these are reasonable ... though maybe they run via 'saved' or something ...

				// Will be stored via relationship, like $model->banner->image = '/file/path'
				'banner[image]' => 'banner.image',

				// Will be stored via relationship, like $model->features()->save(['image' => '/file/path])
				'features[][image]' => 'features.image',

				])
			->getMock()
		;

		// Instatiate the inspector;
		$this->inspector = new Inspector($mock);
	}

	public function testGetInputKeys() {
		$this->assertEquals([
			'image',
			'copy[image]',
			'bullets[][image]',
			'bkgd',
			'banner[image]',
			'features[][image]',
		], $this->inspector->getInputKeys());
	}

	// public function testGetModelAttributes() {
	// 	$this->assertEquals([

	// 	], $this->inspector->getInputKeys());
	// }

}