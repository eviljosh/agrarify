<?php

namespace Agrarify\Lib;

use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class ImageStorageAdapter {

    /**
     * @param string $guid
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public static function storeVeggieImage($guid, $file)
    {
        $s3_client = S3Client::factory([
            'key'    => Config::get('agrarify.s3_key'),
            'secret' => Config::get('agrarify.s3_secret'),
            'region' => Config::get('agrarify.s3_region'),
        ]);

        $result = $s3_client->putObject([
            'Bucket' => Config::get('agrarify.s3_user_bucket_name'),
            'Key' => Config::get('agrarify.s3_veggie_images_folder_name') . '/' . $guid,
            'SourceFile' => $file->getRealPath(),
            'ContentType' => $file->getMimeType(),
            'StorageClass' => 'REDUCED_REDUNDANCY',
            'ACL' => 'public-read',
            'Expires' => Carbon::now()->addDays(90)->getTimestamp(),
        ]);
    }

    /**
     * @param string $guid
     * @return string
     */
    public static function getUrlForVeggieImage($guid)
    {
        return Config::get('agrarify.s3_user_images_root_url') . Config::get('agrarify.s3_veggie_images_folder_name') . '/' . $guid;
    }


}