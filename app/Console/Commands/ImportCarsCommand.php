<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportCarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cars';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pega os dados de um JSON e manipula ele na aplicação';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $carsJson = Http::get('https://hub.alpes.one/api/v1/integrator/export/1902');

        $cars = json_decode($carsJson);

        foreach ($cars as $car) {
            
        }

    }
}
