<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\AssetUsers;
use App\Models\Assets;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:sendemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza o disparo de e-mails.';

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
      $assets     = new Assets();
      $below      = $assets->belowLimit(5.5, 130000);
      $user       = new AssetUsers($_POST);
      $all        = $user::all()->toArray();
      $listassets = [];

      if(count($below) <= 0){
        echo("\nNão foram encontrados ativos nas condições desejadas.\n");
        return 0;
      }

      // list assets
      foreach($below as $asset){
        $listassets[] = $asset->name;
      }

      foreach($all as $email){
        if(!$this->send($email['name'], $email['email'], implode(', ',$listassets))){
          echo(sprintf("\nNão foi enviado e-mail de Notificação para o usuário '%s'.\n", $email['name']));
          return 0;
        };
        echo(sprintf("\nEnviado e-mail de Notificação para o usuário '%s'.\n", $email['name']));
      }

      return 0;
    }

    public function send(string $name, string $email, string $asset)
    {
      try{
        $em = [
          'username' => $name,
          'email' => $email
        ];
        Mail::send('asset',['asset' => $asset], function($e) use ($em){
          $e->to($em['email'], $em['username'])->subject("Assert Notification");
        });
      }catch(\Exception $e){
        return false;
      }

      return true;
    }
}
