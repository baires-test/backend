<?php

namespace App\Http\Controllers;

use App\Image;
use App\Thumbnail;
use Illuminate\Http\Request;
use DB;

class ImageController extends Controller
{
    public function index() {

	    return Image::where('status', 'active')
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
    	return Image::find($id);
    }

    public function store(Request $request) {
    	return Image::create($request->all());
    }

    public function update(Request $request, $id) {
    	$image = Image::findOrFail($id);
    	$image->update($request->all());

    	return $image;
    }

    public function delete(Request $request, $id) {
    	$image = Image::findOrFail($id);
    	$image->delete();

    	return 204;
    }
}
