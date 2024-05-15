<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    // use HasFactory;
    protected $guarded = [];

    public function list($columnName = null, $columnSortOrder = null, $searchValue = null, $start = null, $rowperpage = null, $draw = null)
    {
        $totalRecords = $this->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $this->select('count(*) as allcount')->where('title', 'like', '%' . $searchValue . '%')->count();

        $records = $this->orderByRaw('@rownum := @rownum + 1')
            ->where('articles.title', 'like', '%' . $searchValue . '%')
            ->select('articles.*', DB::raw('@rownum := 0 AS rownum'))
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $index => $record) {
            $data_arr[] = [
                "empty" => '',
                "rownum" => $start + $index + 1, // Tambahkan nomor data secara urut
                "title" => $record->title,
                'thumbnail' => $record->image,
                'status' => $record->status,
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
