<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // いいね登録
    public function store(Item $item)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

        if ($like) {
            $like->update(['delete_flag' => 0]);
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'delete_flag' => 0,
            ]);
        }

        return back();
    }

    // いいね解除
    public function destroy(Item $item)
    {
        Like::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->update(['delete_flag' => 1]);

        return back();
    }
}