<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        Log::error('Handler:'.$exception);
        if ($exception instanceof TimeoutException) {
            return response()->json([
                'status' => 'timeout',
                'message' => 'セッションがタイムアウトしました。'
            ]);
        } else if ($exception instanceof MessageException) {
            if ($exception->hasPage()) {
                if ($exception->hasParam()) {
                    return redirect()->route($exception->getPage(), $exception->getParam())->with('message', $exception->getMessage());
                } else {
                    return redirect($exception->getPage())->with('message', $exception->getMessage());
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ]);
        }
        return parent::render($request, $exception);
    }
}
