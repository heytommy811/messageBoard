<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#エラー
Route::get('error', 'ErrorController@index');
#ログイン
Route::get('/', 'LoginController@index');   // ログイン画面を表示
Route::get('login', 'LoginController@index');   // ログイン画面を表示
Route::post('login', 'LoginController@store');  // ログインを実行
Route::get('logout', 'LoginController@logout'); // ログイン画面を表示
Route::get('home', 'LoginController@home', 'home')->name('home'); // ホーム画面表示
Route::post('home', 'LoginController@home', 'home')->name('home'); // ホーム画面表示

#アカウント
Route::post('account', 'AccountController@store');  // アカウント仮登録処理
Route::get('account/complete/{cert_id}', 'AccountController@complete'); // アカウント作成完了画面を表示
Route::put('account/profile', 'AccountController@updateProfile');   // アカウント情報変更
Route::put('account/password', 'AccountController@updatePassword'); // パスワード変更
Route::post('account/password/reset/send', 'AccountController@sendPasswordResetMail');  // パスワードリセット用URLのメール送信
Route::get('account/password/reset/{cert_id}', 'AccountController@resetPasswordPage');  // パスワードリセットページを表示
Route::post('account/password/reset', 'AccountController@resetPasssword');              // パスワードリセットを実行

#検索
Route::get('search/board', 'SearchController@selectBoard'); // 伝言板検索

#伝言板
Route::get('board/all', 'BoardController@all'); // トップ画面に伝言板の一覧を表示
Route::get('board', 'BoardController@index');   // 伝言板を表示
Route::post('board', 'BoardController@store');  // 伝言板を作成
Route::get('board/join/{dgb_id}', 'BoardController@join');  // 伝言板参加画面を表示

#伝言（メッセージ）
Route::get('message', 'MessageController@index');   // メッセージを表示
Route::post('message', 'MessageController@store');  // メッセージを追加
Route::delete('message', 'MessageController@destroy');   // メッセージ削除
Route::put('message', 'MessageController@update');   // メッセージを編集

// コメント
Route::post('comment', 'CommentController@store');  // コメントを登録
Route::delete('comment', 'CommentController@destory');  // コメントを削除
// LIKE
Route::post('like', 'LikeController@store');    // LIKE送信

#伝言メンバー
Route::get('member', 'MemberController@index'); // 伝言板メンバー一覧取得
Route::put('member', 'MemberController@update');    // 伝言板メンバーの権限更新
Route::post('member', 'MemberController@store');    // 参加初回のメンバー名の登録
Route::post('member/request', 'MemberController@request');  // 参加申請
Route::post('member/answer', 'MemberController@answer');    // 参加申請を回答

#共有
Route::post('share', 'BoardShareController@store'); // 共有URL取得
Route::get('share/{invite_id}', 'BoardShareController@index');  // 共有URLから訪問

#伝言管理
Route::get('manage', 'BoardManageController@index');    // 管理メニューを表示
Route::put('manage', 'BoardManageController@update');   // 管理状態を更新




#管理画面
Route::get('admin', 'Manage_Controller@admin');
Route::post('admin/systemSetting', 'Manage_Controller@systemSetting');
Route::post('admin/registryAccountRequest', 'Manage_Controller@registryAccountRequest');
Route::post('admin/searchUser', 'Manage_Controller@searchUser');
Route::post('admin/resetAccountLock', 'Manage_Controller@resetAccountLock');