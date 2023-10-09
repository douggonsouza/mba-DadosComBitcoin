<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Douggonsouza\Coincap\Coincap;
use App\Models\Assets;

class AssetsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importação de ativos do dia para a base de dados.';

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
     * @return int
     */
    public function handle()
    {
        $salvos = 0;

        $coincap = new Coincap('api.coincap.io/v2/assets');
        $collection = $coincap->request();

        if(empty($collection)){
          echo("Não encontrados ativos.");
          return 0;
        }

        DB::beginTransaction();

        try{
          foreach( $collection->data as $item){
            // prepare date
            $ast = (array) $item;
            $ast['code'] = $ast['id'];
            $ast['day'] = Date('Y-m-d');
            unset($ast['id']);

            // import
            $asset = new Assets($ast);
            if(!$asset->save()){
              echo(sprintf("\nNão foi possível salvar o ativo %s no banco.", $item->name));
              DB::rollBack();
              return 0;
            }
            echo(sprintf("\nO ativo %s foi salvo com sucesso no banco.", $item->name));
            $salvos++;
          }

          echo(sprintf("\n\nForam importados %d ativos.", $salvos));
          echo("\nFIM\n");
          DB::commit();
          return 0;
        }catch(\Exception $e){
          echo("\nErro: $e");
          DB::rollBack();
          return 0;
        }
    }
}
