<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-08
 * Time: 00:20
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;

class ImageRoute
{
    static public function imageStorageRoute(){
        $realpath = str_replace('api/hi-project/task-file','',Request::path());
        $path = storage_path() . $realpath;
        if(!file_exists($path)){
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }
        header('Content-type: image/jpg/doc/docx/pdf/xls/xlsx/csv');
        echo file_get_contents($path);
        exit;
    }
}