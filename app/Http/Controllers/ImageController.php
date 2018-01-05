<?php

namespace App\Http\Controllers;

use App\Image;
use App\Thumbnail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use DB;

class ImageController extends Controller
{
	private $thumbSizes = [
		[200,200],
		[400,300],
		[500,500],
	];

    public function index() {

	    $response = Image::where('status', 'active')
	                     ->orderBy('id', 'desc')
	                     ->get()
	                     ->map(function($image){
		                     $attributes = $image->attributesToArray();

		                     $attributes['thumbnails'] = Thumbnail::where('image_id', $attributes['id'])
		                                                          ->get()
		                                                          ->map(function($thumb) {
			                                                          return $thumb->attributesToArray();
		                                                          });

		                     return $attributes;
	                     });

	    return response()->json($response);
    }

    public function deleted() {
	    return Image::where('status', 'deleted')
	                ->orderBy('id', 'desc')
	                ->get()
	                ->map(function($image){
		                $attributes = $image->attributesToArray();
		                $attributes['thumbnails'] = Thumbnail::where('image_id', $attributes['id'])
		                                                     ->get()
		                                                     ->map(function($thumb) {
			                                                     return $thumb->attributesToArray();
		                                                     });

		                return $attributes;
	                });
    }

    public function show($id) {
	    $image = Image::findOrFail($id);
	    $attributes = $image->attributesToArray();
	    $file = sprintf('%s/%s/%s/%s', storage_path('app/public'), 'images', $attributes['id'], $attributes['path']);

	    return response()->download($file, $attributes['path']);
    	//return Image::find($id);
    }

    public function store(Request $request) {

	    $imageManager = new \Intervention\Image\ImageManager();

	    $image = $imageManager->make($request['image_data'])->stream();

	    $imageData = Image::create([
		    'name' => $request['image_name'],
		    'path' => $request['image_filename'],
		    'status' => 'active'
	    ]);

	    $imageAttrs = $imageData->attributesToArray();

	    Storage::put("public/images/{$imageAttrs['id']}/".$request['image_filename'], $image);

	    $pathAsArray = explode(".", $imageAttrs['path']);
	    $extension = end($pathAsArray);
	    $baseName = basename($imageAttrs['path'], ".{$extension}");

	    array_map(function($size) use ($image, $extension, $imageAttrs, $baseName, $imageManager){


		    $thumb = $imageManager->make($image)->fit($size[0], $size[1])->stream($extension, 80);

		    Storage::put("public/images/{$imageAttrs['id']}/{$baseName}-{$size[0]}x{$size[1]}.{$extension}", $thumb);

		    Thumbnail::create([
			    'path' => "{$baseName}-{$size[0]}x{$size[1]}.{$extension}",
			    'image_id' => $imageAttrs['id'],
			    'width' => $size[0],
			    'height' => $size[1]
		    ]);

	    }, $this->thumbSizes);

    	return response()->json([
    		'success' => true,
		    'data' => $imageData
	    ]);
    }

    public function update(Request $request, $id) {
    	$image = Image::findOrFail($id);
	    $image->status = 'active';
	    $image->save();
    	return $image;
    }

    public function delete(Request $request, $id) {
    	$image = Image::findOrFail($id);
	    $image->status = 'deleted';
	    $image->save();
    	return $image;
    }
}
