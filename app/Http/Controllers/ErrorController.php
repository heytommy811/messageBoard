<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * エラー画面へ遷移する
     */
    function index(Request $request) {
        // セッションを削除する
        $request->session()->flush();
        return view('error');
    }
}
