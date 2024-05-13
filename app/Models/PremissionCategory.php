<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PremissionCategory extends Model
{
    // use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public static function getPermission()
    {
        $permission = DB::table('permissions')
            ->select('permissions.*', 'premission_categories.name as category')
            ->join('premission_categories', 'permissions.permission_category_id', '=', 'premission_categories.id')
            ->get();

        return $permission;
    }
}
