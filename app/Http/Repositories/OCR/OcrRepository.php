<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 12:53
 */

namespace App\Http\Repositories\OCR;

use thiagoalessio\TesseractOCR\TesseractOCR;

class OcrRepository {
    protected $ocr = null;

    public function __construct()
    {
        $this->ocr = new TesseractOCR();
    }

    public function ocr()
    {
        $image = public_path('images/WX20191027-144034.png');
        $this->ocr->image($image);
        return $this->ocr
            ->threadLimit(1)
            ->lang('chi_sim')
            ->run();
    }
}