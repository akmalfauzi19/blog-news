<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremissionCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    protected $_model;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->_model = new Role();
        $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $roles = PremissionCategory::getPermission()->groupBy('category');
        return view('page.admin.roles.index', compact('roles'));
    }

    public function list(Request $request)
    {
        $model = $this->_model;
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Role::select('count(*) as allcount')->count();
        $totalRecordswithFilter = Role::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

        // Fetch records
        $records = Role::orderBy($columnName, $columnSortOrder)
            ->with(['permissions' => function ($query) {
                $query->select('id', 'name', 'permission_category_id');
            }])->where('roles.name', 'like', '%' . $searchValue . '%')
            ->select('roles.*')
            ->skip($start)
            ->take($rowperpage)
            ->get();


        $records = $records->map(function ($record) {
            $categories = collect();
            foreach ($record->permissions->makeHidden('pivot')->groupBy('permission_category_id') as $id => $value) {
                $category = PremissionCategory::where('id', $id)->first();
                $categories->push($category->name);
            }
            $record->roles_category =  $categories;
            return $record;
        });

        $data_arr = [];

        $number = 0;
        foreach ($records as $record) {
            $number++;
            $data_arr[] = [
                "empty" => '',
                "id" => $record->id,
                "name" => $record->name,
                "role_category" => $record->roles_category,
                "created_at" => \Carbon\Carbon::parse($record->created_at)->format('d-M-Y H:i:s'),
                "action" => $record->id
            ];
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $role = Role::create(['name' => $request->input('name')]);

            $permissionArr = [];
            foreach ($request->input('permission') as $permission) {
                $permissionArr[] = intval($permission);
            }

            $role->syncPermissions($permissionArr);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menambahkan Role',
                'url' => route('roles.index')
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('id');

        $permissionsByCategory = PremissionCategory::getPermission()->groupBy('category');
        $data = $permissionsByCategory->map(function ($permissions) use ($rolePermissions) {
            return $permissions->map(function ($permission) use ($rolePermissions) {
                $permission->status = in_array($permission->id, $rolePermissions->toArray());
                return $permission;
            });
        });

        return view('page.admin.roles.show', compact('role', 'data'));
    }


    public function getDetailRole(Request $request)
    {
        $id = $request->id;
        if ($id == null || !isset($id)) {
            return response()->json([
                'status' => false,
                'message' => 'Id user perlu diisi',
            ], 400);
        }

        $role = Role::with(['permissions' => function ($query) {
            $query->select('id', 'name', 'permission_category_id');
        }])
            ->select('roles.*')
            ->find($request->id);

        if (!$role) {
            return response()->json([
                'status' => false,
                'message' => 'Data role tidak ditemukan',
            ], 404);
        }


        $categories = $role->permissions->pluck('permission_category_id')->unique()->map(function ($categoryId) {
            $category = PremissionCategory::find($categoryId);
            return $category ? $category->name : null;
        });

        $role->roles_category = $categories->toArray();

        return response()->json([
            'status' => true,
            'data' => $role,
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'permission' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 400);
            }

            $role->name = $request->input('name');
            $role->save();

            $permissionArr = [];
            foreach ($request->input('permission') as $permission) {
                $permissionArr[] = intval($permission);
            }

            $role->syncPermissions($permissionArr);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Mengubah Role',
                'url' => route('roles.index')
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $role = Role::where('id', $id)->first();

            if (!$role) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $role->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menghapus Role'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
}
