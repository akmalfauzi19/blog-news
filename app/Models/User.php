<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function list($columnName = null, $columnSortOrder = null, $searchValue = null, $start = null, $rowperpage = null, $draw = null, $filterRoles = null)
    {
        // DB::statement(DB::raw('set @rownum=0'));
        // DB::statement(DB::raw('set @rownum=0'));
        // Total records
        $totalRecords = $this->select('count(*) as allcount')->count();
        $totalRecordswithFilter = $this->select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        // $records = $this->orderBy($columnName, $columnSortOrder)
        //     ->where('users.name', 'like', '%' . $searchValue . '%')
        //     ->select('users.*', DB::raw('@rownum  := @rownum  + 1 AS rownum'))
        //     ->roles($filterRoles)
        //     ->skip($start)
        //     ->take($rowperpage)
        //     ->get();

        $records = $this->orderByRaw('@rownum := @rownum + 1')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->select('users.*', DB::raw('@rownum := 0 AS rownum'))
            ->roles($filterRoles)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $index => $record) {
            $data_arr[] = [
                "empty" => '',
                "rownum" => $start + $index + 1, // Tambahkan nomor data secara urut
                "name" => $record->name,
                "email" => $record->email,
                "roles" => $record->getRoleNames(),
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

    public function scopeRoles($query, $filterRoles)
    {
        if ($filterRoles) {
            $query->whereHas('roles', function ($q) use ($filterRoles) {
                $q->where('name', $filterRoles);
            });
        }
    }
}
