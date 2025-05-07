<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost(); // e.g., one.localhost
        $subdomain = explode('.', $host)[0]; // get 'one'

        $databaseName = 'tenant_' . $subdomain;

        config([
            'database.connections.mysql.database' => $databaseName,
        ]);

        // Reset DB connection
        DB::purge('mysql');
        DB::reconnect('mysql');
        app('db')->setDefaultConnection('mysql');

        return $next($request);
    }
}
