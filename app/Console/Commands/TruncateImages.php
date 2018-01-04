<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Image;
use App\Thumbnail;

class TruncateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete images files and database records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Deletes images, thumbnails and files.
     *
     * @return mixed
     */
    public function handle()
    {
	    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	    Image::truncate();
	    Thumbnail::truncate();
	    Storage::deleteDirectory('public/images');
	    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
