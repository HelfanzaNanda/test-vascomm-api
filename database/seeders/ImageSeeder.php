<?php

namespace Database\Seeders;

use App\Helpers\FileHelper;
use App\Helpers\StringHelper;
use App\Models\File;
use DateTimeImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $randomImages =[
            'https://images.pexels.com/photos/4144923/pexels-photo-4144923.jpeg',
            'https://images.pexels.com/photos/4158/apple-iphone-smartphone-desk.jpg',
            'https://images.pexels.com/photos/4145190/pexels-photo-4145190.jpeg',
            'https://images.pexels.com/photos/1809340/pexels-photo-1809340.jpeg',
            'https://images.pexels.com/photos/4386466/pexels-photo-4386466.jpeg',
            'https://images.pexels.com/photos/4145153/pexels-photo-4145153.jpeg',
            'https://images.pexels.com/photos/4144294/pexels-photo-4144294.jpeg',
            'https://images.pexels.com/photos/4144222/pexels-photo-4144222.jpeg',
            'https://images.pexels.com/photos/336948/pexels-photo-336948.jpeg',
            'https://images.pexels.com/photos/256262/pexels-photo-256262.jpeg',
            'https://images.pexels.com/photos/4145074/pexels-photo-4145074.jpeg'
       ];

       $res = [];
       foreach ($randomImages as $img) {

            $imageUrl = $img;

            $info = pathinfo($imageUrl);
            $contents = file_get_contents($imageUrl);
            $file = '/tmp/' . $info['basename'];
            file_put_contents($file, $contents);
            $uploaded_file = new UploadedFile($file, $info['basename']);


            $date = new DateTimeImmutable();
            $timestampMs = (int) ($date->getTimestamp() . $date->format('v'));

            $code = StringHelper::getRandomString();
            $originalName = $uploaded_file->getClientOriginalName();
            $name = pathinfo($originalName, PATHINFO_FILENAME);
            $name = Str::slug($name);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            // $originalName = $name .'.'.$extension;
            $filename = $timestampMs . '.' . $extension;
            $mimetype = $uploaded_file->getClientMimeType();
            $filesize = $uploaded_file->getSize();
            $filepath = 'files/';


            $location = $filepath . $filename;

            // $baseurl = env("APP_URL");
            Storage::put($location, file_get_contents($file));
            $fileurl = Storage::url($location);

            $modelFile = File::create([
                'original_name' => $originalName,
                'filecode' => $code,

                'filename' => $filename,
                'filepath' => $filepath,
                'fileurl' => $fileurl,
                'filesize' => $filesize,
                'extension' => $extension,
                'mime_type' => $mimetype,
            ]);
       }
    }
}
