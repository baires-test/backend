<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Image;
use App\Thumbnail;

class ImageTableSeeder extends Seeder
{
	private $imageManager;
	private $thumbSizes = [
		[200,200],
		[400,300],
		[500,500],
	];
	private $acceptedExtensions = ["jpg", "jpeg", "png", "gif", "tif", "tiff", "bmp"];
    /**
     * Run the image seeder.
     *
     * @return void
     */
    public function run()
    {
    	$this->command->info('Generating Images Seeds');

	    $this->imageManager = new \Intervention\Image\ImageManager();

	    /**
	     * Read all files paths inside the 'Images' folder
	     */
	    $allFiles = scandir(dirname(__FILE__)."/Images");

	    /**
	     * Exclude all files that are not images
	     */
	    $files = array_filter($allFiles, function($fileName) {
	    	if($fileName === '.' || $fileName === '..') {
	    		return false;
		    }

		    $pathAsArray = explode(".", $fileName);
		    $extension = end($pathAsArray);
	    	return in_array($extension, $this->acceptedExtensions);
	    });

	    /**
	     *
	     */
	    $images = array_map(function($fileName){
	    	$image = Image::create([
	    		'name' => $fileName,
			    'path' => $fileName,
			    'status' => 'active'
		    ]);
		    $this->command->info("File '{$fileName}' created");

	    	$this->copyToStorage($image);
	    	$this->generateThumbnails($image);
	    	return $image->attributesToArray();
	    }, $files);

	    $this->command->info(sprintf("%s images created.", count($images)));
    }

	/**
	 * Copy the file to the public storage folder.
	 *
	 * @param Image $image
	 */
    private function copyToStorage(Image $image) {
    	$attrs = $image->attributesToArray();
	    $this->command->info("Copying '{$attrs['path']}' to public folder");
    	$filePath = dirname(__FILE__)."/Images/{$attrs['path']}";
    	Storage::put("public/images/{$attrs['id']}/{$attrs['path']}", file_get_contents($filePath));
    }

	/**
	 * Generate thumbs of the image provided.
	 *
	 * @param Image $image
	 */
    private function generateThumbnails(Image $image) {

	    $attrs = $image->attributesToArray();
	    $this->command->info("Generating thumbs for '{$attrs['path']}'");

	    $filePath = dirname(__FILE__)."/Images/{$attrs['path']}";
	    $pathAsArray = explode(".", $attrs['path']);
	    $extension = end($pathAsArray);
	    $baseName = basename($attrs['path'], ".{$extension}");

	    array_map(function($size) use ($filePath, $extension, $attrs, $baseName){

		    $this->command->info("Generating {$size[0]}x{$size[1]} thumb");
		    $thumb = $this->imageManager->make($filePath)->fit($size[0], $size[1])->stream($extension, 80);
		    Storage::put("public/images/{$attrs['id']}/{$baseName}-{$size[0]}x{$size[1]}.{$extension}", $thumb);

		    Thumbnail::create([
			    'path' => "{$baseName}-{$size[0]}x{$size[1]}.{$extension}",
			    'image_id' => $attrs['id'],
			    'width' => $size[0],
			    'height' => $size[1]
		    ]);

	    }, $this->thumbSizes);

    }
}
