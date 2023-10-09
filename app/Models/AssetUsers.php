<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetUsers extends Model
{
    use HasFactory;

    protected $table = 'asset_users';

    protected $visible = [
      'id',
      'name',
      'e-mail',
      'active',
      'updated_at',
      'created_at',
      'removed_at'
    ];

    protected $fillable = [
      'name',
      'e-mail',
      'updated_at',
      'created_at'
    ];

    protected $guarded = [
      'id',
      'active',
      'updated_at',
      'created_at',
      'removed_at'
    ];
}
