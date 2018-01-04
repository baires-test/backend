<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thumbnail extends Model
{
	protected $fillable = ['path', 'image_id', 'width', 'height'];
}
