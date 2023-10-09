<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Assets extends Model
{
    use HasFactory;

    protected $table = 'assets';

    protected $visible = [
      'id',
      'code',
      'rank',
      'symbol',
      'name',
      'supply',
      'maxSupply',
      'marketCapUsd',
      'volumeUsd24Hr',
      'priceUsd',
      'changePercent24Hr',
      'vwap24Hr',
      'explorer',
      'day',
      'active',
      'updated_at',
      'created_at',
      'removed_at'
    ];

    protected $fillable = [
      'code',
      'rank',
      'symbol',
      'name',
      'supply',
      'maxSupply',
      'marketCapUsd',
      'volumeUsd24Hr',
      'priceUsd',
      'changePercent24Hr',
      'vwap24Hr',
      'explorer',
      'day',
      'created_at',
      'updated_at'
    ];

    protected $guarded = [
      'id',
      'active',
      'updated_at',
      'created_at',
      'removed_at'
    ];

    public function maxPriceUsd()
    {
      return DB::select(
        'SELECT
          af.flPriceUsd,
          a.*
        FROM
          dev_xavier.assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              af.id
            FROM
              dev_xavier.assets af
            WHERE
              af.active = 1
          ) af ON af.id = a.id
        WHERE
          a.active = 1
          AND a.day = (
            SELECT
              ad.day
            FROM
              dev_xavier.assets ad
            WHERE
              ad.active = 1
            ORDER BY
              ad.day DESC
            LIMIT
              1
          )
        ORDER BY a.day DESC, af.flPriceUsd DESC, a.code
        LIMIT 10;'
      );
    }

    public function lineChartByCode(string $code)
    {
      return DB::select(sprintf(
        "SELECT
          af.flPriceUsd,
          a.code,
          a.name,
          a.day
        FROM
          dev_xavier.assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              af.id
            FROM
              dev_xavier.assets af
            WHERE
              af.active = 1
          ) af ON af.id = a.id
        WHERE
          a.active = 1
          AND a.code = '%s'
        ORDER BY
          a.day DESC
        LIMIT 5;",
        $code
      ));
    }

    public function higherPrice($cota, $limit)
    {
      // DB::enableQueryLog();
      $sd = DB::select(sprintf(
        'SELECT
          af.flPriceUsd,
          af.ctPriceUsd,
          a.*
        FROM
          dev_xavier.assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              (CAST(af.priceUsd AS DECIMAL(16, 6)) * %d) ctPriceUsd,
              af.id
            FROM
              dev_xavier.assets af
            WHERE
              af.active = 1
          ) af ON af.id = a.id
        WHERE
          a.active = 1
          AND af.ctPriceUsd >= %d
          AND a.day = (
            SELECT
              ad.day
            FROM
              dev_xavier.assets ad
            WHERE
              ad.active = 1
            ORDER BY
              ad.day DESC
            LIMIT
              1
          )
        ORDER BY a.day DESC, af.flPriceUsd DESC, a.code
        LIMIT 10;',
        $cota,
        $limit
      ));
      // die(var_dump(DB::getQueryLog()));
      return $sd;
    }
}
