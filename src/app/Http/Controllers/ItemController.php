<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $tab = $request->tab;

        // マイリスト
        if ($tab === 'mylist' && Auth::check()) {
            $query = Auth::user()
                ->likedItems()
                ->with('user');
        } else {
            // おすすめ
            $query = Item::with('user');
        }

        // 🔍 商品名 部分一致検索
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->latest()->get();

        return view('items.index', compact('items'));
    }

    // 商品詳細
    public function show($item_id)
    {
        $item = Item::with([
            'categories',
            'likes' => function ($query) {
            $query->where('delete_flag', 0);
            },
            'comments.user.profile',
        ])->findOrFail($item_id);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('items.create', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        // 画像保存
        $path = $request->file('image_path')->store('items', 'public');

        // 商品登録
        $item = Item::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'brand'      => $request->brand,
            'description'=> $request->description,
            'price'      => $request->price,
            'condition'  => $request->condition,
            'image_path' => $path,
        ]);

        // カテゴリ紐付け（多対多）
        $item->categories()->attach($request->categories);

        return redirect()->route('mypage')->with('success', '商品を出品しました');

    }
}