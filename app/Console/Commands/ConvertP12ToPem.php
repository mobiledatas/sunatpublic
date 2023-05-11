<?php

namespace App\Console\Commands;

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertP12ToPem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:pem {--start}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $p12 = file_get_contents(storage_path('certificate/p12/certificado.p12'));
        $pass = 'REYNALDO377';
        $certificate = new X509Certificate($p12,$pass);
        $pem = $certificate->export(X509ContentType::PEM);
        Storage::put('certificate/certificado_mds.pem',$pem);
        $this->info("Certificate generated");
        // return Command::SUCCESS;
    }
}
