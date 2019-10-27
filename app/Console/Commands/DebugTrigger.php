<?php

namespace App\Console\Commands;

use App\Http\Repositories\OCR\OcrRepository;
use Illuminate\Console\Command;

class DebugTrigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:trigger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug trigger.';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ocr = new OcrRepository();
        $text = $ocr->ocr();

        $this->line($text);
    }
}
