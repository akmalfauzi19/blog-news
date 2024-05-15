<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    // use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function list($columnName = null, $columnSortOrder = null, $searchValue = null, $start = null, $rowperpage = null, $draw = null)
    {
        $totalRecords = $this->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $this->select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        $records = $this->orderByRaw('@rownum := @rownum + 1')
            ->where('categories.name', 'like', '%' . $searchValue . '%')
            ->select('categories.*', DB::raw('@rownum := 0 AS rownum'))
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $index => $record) {
            $data_arr[] = [
                "empty" => '',
                "rownum" => $start + $index + 1, // Tambahkan nomor data secara urut
                "name" => $record->name,
                "action" => $record->id
            ];
        }

        $result = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return $result;
    }

    public function article()
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }
}
