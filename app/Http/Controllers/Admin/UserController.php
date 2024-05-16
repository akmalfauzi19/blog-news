<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $_model;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->_model = new User();

        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'store', 'update', 'destroy']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        return view('page.admin.users.index');
    }

    public function list(Request $request): JsonResponse
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
        $filterRoles = $columnName_arr[4]['search']['value']; // role filter

        $response = $model->list($columnName, $columnSortOrder, $searchValue, $start, $rowperpage, $draw, $filterRoles);

        return response()->json($response);
    }

    public function getRole(Request $request): JsonResponse
    {
        $search = $request->search;

        if ($search == '') {
            $roles = Role::select('id', 'name')->get();
        } else {
            $roles = Role::orderby('name', 'asc')->select('id', 'name')->where('name', 'like', '%' . $search . '%')->get();
        }

        $response = [];
        foreach ($roles as $role) {
            $response[] = [
                "id" => $role->id,
                "text" => $role->name
            ];
        }

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
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:password_confirm',
                'roles' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);
            $user->assignRole(intval($data['roles']));

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan user'
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
        $user = User::find($id);
        return view('page.admin.users.show', compact('user'));
    }

    public function getUser(Request $request, $id): JsonResponse
    {
        try {
            if ($id == null || !isset($id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Id user perlu diisi',
                ], 400);
            }

            $user = User::find($id);
            $roles = Role::pluck('name', 'name')->all();
            $userRole = $user->roles->pluck('id', 'name')->all();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data user',
                'data' => [
                    'user' => $user,
                    'roles' => $roles,
                    'userRole' => $userRole
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'same:confirm-password',
                'roles' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                $data = Arr::except($data, array('password'));
            }

            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'errors'  => 'Akun user tidak ditemukan',
                ], 400);
            }

            $user->update($data);
            DB::table('model_has_roles')
                ->where('model_id', $id)
                ->delete();

            if (!is_null($request->input('roles'))) {
                $user->assignRole(intval($request->input('roles')));
            }

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengubah user' . $user->name
            ]);
        } catch (Exception $e) {
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
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $user = User::where('id', $id)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data user tidak ditemukan'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menghapus User'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
}
