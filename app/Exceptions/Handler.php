<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable  $exception)
    {   
        $response = parent::render($request, $exception);

        if ($request->header('X-Inertia')) {
            if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->back()->with('error', 'Sesi Anda telah berakhir. Silakan muat ulang halaman atau login kembali.');
            }

            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return \Inertia\Inertia::location(route('login'));
            }
            
            // For other errors like 403, 404, 500 you can also return Inertia pages if needed,
            // but for now we'll just fix the session/auth redirect issue.
        }

        return $response;
    }
}
