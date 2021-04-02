<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Log;
use App\Exceptions\TimeoutException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getUserSession($request) {
        $users = $request->session()->get('users');
        if ($users == null) {
            Log::debug('sesstion timeout.');
            throw new TimeoutException();
        }
        return $users;
    }
}
