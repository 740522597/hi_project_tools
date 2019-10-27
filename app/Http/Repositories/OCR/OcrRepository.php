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

    public function ocr($imagePath)
    {
        $this->ocr->image($imagePath);
        return $this->ocr
            ->threadLimit(1)
            ->lang('chi_sim', 'eng')
            ->psm(12)
            ->oem(3)
            ->userPatterns(public_path('user-patterns.txt'))
            ->run();
    }
}