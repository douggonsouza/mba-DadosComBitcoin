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
      'ranking',
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
      'ranking',
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
          assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              af.id
            FROM
              assets af
            WHERE
              af.active = 1
          ) af ON af.id = a.id
        WHERE
          a.active = 1
          AND a.day = (
            SELECT
              ad.day
            FROM
              assets ad
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
          assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              af.id
            FROM
              assets af
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

    public function belowLimit($cota, $limit, $code = null)
    {
      // DB::enableQueryLog();
      $sd = DB::select(sprintf(
        'SELECT
          af.flPriceUsd,
          af.ctPriceUsd,
          a.*
        FROM
          assets a
          JOIN (
            SELECT
              CAST(af.priceUsd AS DECIMAL(16, 6)) flPriceUsd,
              (CAST(af.priceUsd AS DECIMAL(16, 6)) * %d) ctPriceUsd,
              af.id
            FROM
              assets af
            WHERE
              af.active = 1
          ) af ON af.id = a.id
        WHERE
          a.active = 1
          AND af.ctPriceUsd < %d %s
          AND a.day = (
            SELECT
              ad.day
            FROM
              assets ad
            WHERE
              ad.active = 1
            ORDER BY
              ad.day DESC
            LIMIT
              1
          )
        ORDER BY a.day DESC, af.flPriceUsd DESC, a.code
        LIMIT 5;',
        $cota,
        $limit,
        !is_null($code)? "AND a.code = '$code'": null
      ));
      // die(var_dump(DB::getQueryLog()));
      return $sd;
    }
}
