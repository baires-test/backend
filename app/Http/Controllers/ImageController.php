<?php

namespace App\Http\Controllers;

use App\Image;
use App\Thumbnail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class ImageController extends Controller
{
    public function index() {

	    $response = Image::where('status', 'active')
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
    	return Image::create($request->all());
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
