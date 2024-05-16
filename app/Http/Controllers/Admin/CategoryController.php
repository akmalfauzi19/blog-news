<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $_model;

    function __construct()
    {
        $this->_model = new Category();
        $this->middleware('permission:category-list|category-create|category-edit|category-delete')
            ->only(['index', 'list', 'store', 'update', 'destroy']);
        $this->middleware('permission:category-create')->only(['create', 'store']);
        $this->middleware('permission:category-edit')->only(['edit', 'update']);
        $this->middleware('permission:category-delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        return view('page.admin.category.index');
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

        $response = $model->list($columnName, $columnSortOrder, $searchValue, $start, $rowperpage, $draw);

        return response()->json($response);
    }

    public function getCategory(Request $request, $id): JsonResponse
    {
        try {
            $model = $this->_model;
            if ($id == null || !isset($id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Id user perlu diisi',
                ], 400);
            }

            $category = $model::find($id);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data user',
                'data' => $category
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:categories,name'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            $model::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan category'
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
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:users,name,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            $category = $model::find($id);
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'errors'  => 'Category tidak ditemukan',
                ], 404);
            }

            $category->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengubah category ' . $category->name
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $category = $model::where('id', $id)->first();

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'category tidak ditemukan'
                ], 404);
            }

            $category->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menghapus category'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }
}
