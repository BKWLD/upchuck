# Upchuck

[![Packagist](https://img.shields.io/packagist/v/bkwld/upchuck.svg)](https://packagist.org/packages/bkwld/upchuck)

Upchuck is a simple, automatic handler of file uploads for [Laravel's](http://laravel.com/) [Eloquent](http://laravel.com/docs/eloquent) models using using [Flysystem](http://flysystem.thephpleague.com/).  It does not attempt to do anything besides let the developer treat file uploads like regular input fields.  It does this by listening to Eloquent `saving` events,  checking the request input for files with names that you have whitelisted, pushing those files to "disk" of your choosing, and then storing the publically accessible URL in the model attribute for that input.


## Installation

1. Add to your project: `composer require bkwld/upchuck:~1.0`
2. Add Upchuck as a provider in your app/config/app.php's provider list: `'Bkwld\Upchuck\ServiceProvider',`
3. Publish the config: `php artisan config:publish bkwld/upchuck`


## Usage

Edit the `disk` config setting to supply configuration information for where uploads should be moved.  We are using [Graham Campbell's Flysystem](https://github.com/GrahamCampbell/Laravel-Flysystem) integration for Laravel to instantiate Flysystem instances, so the configruation of the `disk` matches his [configuration options for connections](https://github.com/GrahamCampbell/Laravel-Flysystem/blob/1.0/src/config/config.php#L38).  As the comments in the config file mention, I recommend turning on caching if you are using any disk other than `local`.  For both [caching](https://github.com/thephpleague/flysystem-cached-adapter) and [other disk drivers](https://github.com/thephpleague/flysystem#adapters), you will need to include other packages.

Then, to enable upload support for your models, use the `Bkwld\Upchuck\SupportsUploads` trait on your model and itemize each attribute that should support uploads via the `$upload_attributes` property.  For example:

```php
class Person extends Eloquent {
	
	// Use the trait
	use Bkwld\Upchuck\SupportsUploads;

	// Define the uploadable attributes
	private $upload_attributes = [

		// Both the file input field and the model attribute are named "image"
		'image',

		// The input file field is named "portrait" but the model attribute is
		// named "headshot"
		'portrait' => 'headshot',
	];

	// Since the upload handling happens via model events, it acts like a mass
	// assignment.  As such, Upchuck sets attributes via `fill()` so you can
	// control the setting.
	protected $fillable = ['image', 'headshot'];
}
```


### Resizing images

If your app supports uploading files you are probably also dealing with needing to resize uploaded images.  We (BKWLD) use our [Croppa](https://github.com/BKWLD/croppa) package to resize images using specially formatted URLs.  If you are looking for an model-upload package that also resizes images, you might want to check out [Stapler](https://github.com/CodeSleeve/stapler).


### Deleting images

If you update a model attribute to a false-y value, Upchuck will delete the old referenced file.  Thus, if your app allows users to delete files, consider using markup like:

```html
<input type="file" name="image">
Delete file <input type="checkbox" name="image" value="">
```

### Array-ish inputs

If your field is like this:

```html
<input type="file" name="types[marquee][image]">
```

Setup your `$upload_attributes` like:

```php
private $upload_attributes = [
	'types.stats.image' => 'image',
];
```

