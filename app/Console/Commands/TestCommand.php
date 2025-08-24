<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'read:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command que baixa um arquivo JSON, valida e insere os dados no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::post('https://hub.alpes.one/api/v1/integrator/export/1902');

        $variavel = 'asdasdasd';
        $variavel = 'asdasdasd';
        $variavel = 'asdasdasd';
        $variavel = 'asdasdasd';
        $variavel = 'asdasdasd';
    }
}
