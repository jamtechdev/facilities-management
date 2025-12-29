<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Display error page for 401 Unauthorized
     */
    public function error401()
    {
        return response()->view('errors.401', [], 401);
    }

    /**
     * Display error page for 402 Payment Required
     */
    public function error402()
    {
        return response()->view('errors.402', [], 402);
    }

    /**
     * Display error page for 403 Forbidden
     */
    public function error403(Request $request)
    {
        $message = $request->get('message', 'You do not have permission to access this page. Please contact your administrator if you need this access.');
        return response()->view('errors.403', ['exception' => (object)['getMessage' => fn() => $message]], 403);
    }

    /**
     * Display error page for 404 Not Found
     */
    public function error404()
    {
        return response()->view('errors.404', [], 404);
    }

    /**
     * Display error page for 419 Page Expired
     */
    public function error419()
    {
        return response()->view('errors.419', [], 419);
    }

    /**
     * Display error page for 429 Too Many Requests
     */
    public function error429()
    {
        return response()->view('errors.429', [], 429);
    }

    /**
     * Display error page for 500 Server Error
     */
    public function error500()
    {
        return response()->view('errors.500', [], 500);
    }

    /**
     * Display error page for 503 Service Unavailable
     */
    public function error503()
    {
        return response()->view('errors.503', [], 503);
    }
}
