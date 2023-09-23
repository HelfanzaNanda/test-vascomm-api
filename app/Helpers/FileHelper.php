<?php

namespace App\Helpers;

use App\Models\File;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileHelper
{

	public static function upload($file)
	{

        if (!$file->isValid()) {
            throw new Exception("file is not valid");
        }

		$date = new DateTimeImmutable();
		$timestampMs = (int) ($date->getTimestamp() . $date->format('v'));

		$code = StringHelper::getRandomString();
		$originalName = $file->getClientOriginalName();
		$name = pathinfo($originalName, PATHINFO_FILENAME);
		$name = Str::slug($name);
		$extension = pathinfo($originalName, PATHINFO_EXTENSION);
		// $originalName = $name .'.'.$extension;
		$filename = $timestampMs . '.' . $extension;
		$mimetype = $file->getClientMimeType();
		$filesize = $file->getSize();
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

        return $modelFile->filecode;
	}

//     public static function upload($file)
//     {
//         $name = $file->hashName();

//         $upload = Storage::put("files/{$name}", $file);

//         File::query()->create([
//             'original_name' => $file->getClientOriginalName(),
//             'name' => "{$name}",
//             "filename" => $name,
//             'mime_type' => $file->getClientMimeType(),
//             'path' => "avatars/{$name}"
// ,
//             'disk' => config('app.uploads.disk'),
//             'file_hash' => hash_file( config('app.uploads.hash'), storage_path( path: "avatars/{$name}", ), ),
//             'collection' => $request->get('collection'),
//             'size' => $file->getSize(),
//         ]);
//     }

    public static function searchFilecode($filecode)
    {
        $file = File::where('filecode', $filecode)->first();
        if ($file) {
            return $file->id;
        }
        return null;
    }



	public static function download($filecode)
	{
        $file = File::where('filecode', $filecode)->firstOrFail();
		$filename = basename($file->filename);
		$path = $file->filepath;



		header('Content-Description: File Transfer');
            // header('Content-Type: application/octet-stream');
		// header('Content-Type: '.$this->mime_type);
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($path));
		readfile($path);
		exit(200);
	}
}
