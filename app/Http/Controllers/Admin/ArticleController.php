<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ArticleController extends Controller
{
    protected $_model;
    function __construct()
    {
        $this->_model = new Article();
        $this->middleware('permission:article-list|article-create|article-edit|article-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:article-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:article-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:article-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('page.admin.articles.index');
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('page.admin.articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'content' => 'required',
                'category' => 'required|exists:categories,id',
                'image' => 'required|image|mimes:jpeg,bmp,png,gif,svg',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            $slug = implode('-', explode(' ', $data['title']));

            // upload image
            $originName = $request->file('image')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('image')->storeAs('uploads', $fileName, 'public');

            $url = Storage::url('uploads/' . $fileName);

            $model::create([
                'author_id' => Auth::user()->id,
                'category_id' => $data['category'],
                'title' => $data['title'],
                'slug' => $slug,
                'content' => $data['content'],
                'image' => $url,
                'status' => 'draft'
            ]);

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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $articles
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id): View
    {
        $article = Article::findOrFail($id);
        return view('page.admin.articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $articles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:articles,title,' . $id,
                'content' => 'required',
                'category_id' => 'required|exists:categories,id',
                'image' => 'image|mimes:jpeg,bmp,png,gif,svg',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            if ($request->file('image')) {
                $originName = $request->file('image')->getClientOriginalName();
                $fileName = pathinfo($originName, PATHINFO_FILENAME);
                $extension = $request->file('image')->getClientOriginalExtension();
                $fileName = $fileName . '_' . time() . '.' . $extension;

                $request->file('image')->storeAs('uploads', $fileName, 'public');

                $url = Storage::url('uploads/' . $fileName);

                unset($data['image']);
                $data['image'] = $url;
            }

            $data['slug'] = implode('-', explode(' ', $data['title']));

            $article = $model::find($id);
            if (!$article) {
                return response()->json([
                    'status' => false,
                    'errors'  => 'Article tidak ditemukan',
                ], 404);
            }


            $article->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengubah article ' . $article->title
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required|in:true,false'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $data = $request->all();

            $article = $model::find($id);

            if (!$article) {
                return response()->json([
                    'status' => false,
                    'errors'  => 'article tidak ditemukan',
                ], 404);
            }

            $article->update([
                'status' => $data['status'] == 'true' ? 'publish' : 'draft'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil merubah status article',
                'data' => $article
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
     * @param  \App\Article  $articles
     * @return \Illuminate\Http\Response
     */
    public function  destroy(Request $request, $id): JsonResponse
    {
        try {
            $model = $this->_model;
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $article = $model::where('id', $id)->first();

            if (!$article) {
                return response()->json([
                    'status' => false,
                    'message' => 'artcile tidak ditemukan'
                ], 404);
            }

            $article->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Menghapus article'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'errors'  => $e->getMessage(),
            ], 400);
        }
    }

    public function upload(Request $request): JsonResponse
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->storeAs('uploads', $fileName, 'public');

            $url = Storage::url('uploads/' . $fileName);
            return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url]);
        }
    }

    public function getCategory(Request $request): JsonResponse
    {
        $search = $request->search;

        if ($search == '') {
            $categories = Category::select('id', 'name')->get();
        } else {
            $categories = Category::orderby('name', 'asc')->select('id', 'name')->where('name', 'like', '%' . $search . '%')->get();
        }

        $response = [];
        foreach ($categories as $category) {
            $response[] = [
                "id" => $category->id,
                "text" => $category->name
            ];
        }

        return response()->json($response);
    }
}
