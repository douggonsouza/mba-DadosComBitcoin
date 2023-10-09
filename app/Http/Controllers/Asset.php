<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\Assets;
use App\Models\AssetUsers;

class Asset extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function lineChartDate()
    {
      $dtChart       = [];
      $dtChart['ln'] = [];
      $line          = [];
      $g             = 34;
      $r             = 255;
      $b             = 0;
      $erro          = null;

      // retister
      if(isset($_POST) && isset($_POST['_token'])){
        $user = new AssetUsers($_POST);
        if(!$user->save()){
          $erro = 'O usuário não pode ser salvo.';
        }
      }

      // max assets
      $assets = new Assets();
      foreach($assets->maxPriceUsd() as $ast){
        $dtChart['ln'][$ast->code] = [];
        $dtChart['ln'][$ast->code]['code'] = $ast->code;
        $dtChart['ln'][$ast->code]['name'] = $ast->name;
        $dtChart['ln'][$ast->code]['day'] = [];
        $dtChart['ln'][$ast->code]['flPriceUsd'] = [];
        $dtChart['ln'][$ast->code]['backgroundColor'] = sprintf('rgba(%d, %d, %d, 0.05)', ($r -= 5), ($g += 5), ($b += 5));

        // get output the values
        preg_match_all("/([\\d.]+)/", $dtChart['ln'][$ast->code]['backgroundColor'], $matches);
        $hex = sprintf(
            "#%02X%02X%02X",
            $matches[1][2], // blue
            $matches[1][1], // green
            $matches[1][0], // red
        );
        $dtChart['ln'][$ast->code]['border'] = $hex;
      }

      // dates for line Chart
      foreach($dtChart['ln'] as $code => $ln){
        $dt = $assets->lineChartByCode($code);

        foreach(array_reverse($dt) as $byDay){
          $dtChart['ln'][$code]['day'][] = $byDay->day;
          $dtChart['ln'][$code]['flPriceUsd'][] = (float) $byDay->flPriceUsd;
        }

        if(!isset($dtChart['label'])){
          $dtChart['label'] = $dtChart['ln'][$code]['day'];
        }
      }

      return view('welcome',[
        'dtChart' => $dtChart,
        'erro'    => $erro
      ]);
    }
}
