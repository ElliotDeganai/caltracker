<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserSwitchController extends Controller
{
    public function switch(Request $request)
    {
        abort_unless($request->user()->can('manage-users'), 403);

        $data = $request->validate(['user_id' => 'required|exists:users,id']);
        session(['viewing_user_id' => $data['user_id']]);

        return back();
    }

    public function clear(Request $request)
    {
        session()->forget('viewing_user_id');

        return back();
    }
}
