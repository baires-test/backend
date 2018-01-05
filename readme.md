# BairesDev - Fullstack Coding Challenge - API Project

This project was developed as part of a coding challenge for BairesDev. To see the complementary project for the Frontend go to [https://github.com/alexbaron50/bairesdev-frontend-test](https://github.com/alexbaron50/bairesdev-frontend-test)

## About

This project was developed using Laravel 5.5 and it's intended to be used as a RESTful API

## Pre-requisites

- The best way to run this project is by using the [Larabel Homestead](https://laravel.com/docs/5.5/homestead) virtual machine. If you prefer to set up your own server please refer to the [official documentation](https://laravel.com/docs/5.5/installation) for the installation.
- In the `.env` file, modify the needed information to fit your system.
- Be sure to have created a database with the name and proper permission as specified in the `.env` file.
- Have the  `php` command available in the global path.

## Table Migration

Once you have properly set up you system to run the project, it's time to install the needed tables. From your command line, go to location of the project and run `php artisan migrate`

## Tables

This project use mainly two tables to save the information: `images` and `thumbnails`.

The `images` table has information about the name, path, status and creation of record of images.

The `thumbnails` table has information about the additional thumbs generated per each image. It has a foreign key related with the id of the image, and information about the width, height and the file name of the thumb generated.

The thumbnails are generated in the moment of the creation of each image and have a predefined size: `200w x 200h`, `400w x 300h` and `500w x 500h`. Feel free to change those sizes by editing the `database\seeds\ImageTableSeeder.php` and `app\Http\Controllers\InageController.php` files corresponding to the seeder and controller.

The images generated are saved in the `storage\app\public\images` folder. For every new record it will be created a folder with the id of the image, and inside that folder, it will the the full image and its thumbs.

## Seeder

This project as a custom seeder used to pre-fill the information of the tables mentioned before. The location of the seeder is `database\seeds\ImageTableSeeder.php`. The script will read the content of the `database\seeds\Images` folder, and will only read those files that have an image format. The content of the folder is the files given in the specification of the application. Feel free to add more images if you want to seed the database with different information.

To run the seed from your command line on the location of the project type `php artisan db:seed --class=ImageTableSeeder`;

If you run the command multiple times it will create new records on the database and files on the storage folder.

If you want to reset the content of the images and thumbnails tables, and the files created, run from you command line `php artisan images:truncate`. The file corresponding to this command is located in `app\Console\Commands\TruncateImages.php`;

## RESTful API Endpoints

In order to perform all requirement specified, it was created the next endpoints:

* `/api/images --METHOD GET` will return all `active` images.
* `/api/images/deleted --METHOD GET` will return all `deleted` images.
* `/api/images --METHOD POST` will create a new image. The request must contain, the next fields: `image_name`, `image_data` and `image_filename`. The `image_data` field must be in base64 encoded format. Following is an example of how to make a request to this endpoint using Javascript, however it will not work as the image_data is not encoded in base64. You can use [FileReader API](https://developer.mozilla.org/en-US/docs/Web/API/FileReader) to get this information from an input file in Javascript. On top of that you will to change the url for you own domain.

```
var data = new FormData();
data.append("image_name", "name");
data.append("image_data", "data");
data.append("image_filename", "name.ext");

var xhr = new XMLHttpRequest();
xhr.withCredentials = true;

xhr.addEventListener("readystatechange", function () {
  if (this.readyState === 4) {
    console.log(this.responseText);
  }
});

xhr.open("POST", "http://test.bairesdev.com/api/images");

xhr.send(data);
```
* `/api/images/{id} --METHOD PUT` will make the image with the id provided as `active`
* `/api/images/{id} --METHOD DELETE` will make the image with the id provided as `deleted`. As told in the specifications, an image will not be permanently deleted from the database.
* `/api/images/download/{id} --METHOD GET` will force the download of the image. It file to be downloaded will have the original file name.
