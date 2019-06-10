<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class CategoryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $recordsPerPage = (int)Config::get('settings.record_per_page');
        if (Input::get('perpage')) {
            $recordsPerPage = (int)Input::get('perpage');
        }
        $user = Auth::user();
        if ($user->can('viewList', Category::class)) {
            $categories = Category::sortable(['level0' => 'asc'])
                ->paginate($recordsPerPage);
            return view('templates.settings.categorySetting.category', compact('categories', 'recordsPerPage'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function show($id)
    {
        /* @var Category $category */
        $user = Auth::user();
        if ($user->can('showDetail', Category::class)) {
            $category = Category::whereId($id)->first();
            if (!$category instanceof Category) {
                return abort(404);
            }
            return view('templates.settings.categorySetting.show', compact('category'));
        }
        return abort(401);
    }
}
