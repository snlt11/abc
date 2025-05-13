<?php

namespace App\GraphQL;

use Nuwave\Lighthouse\Execution\ErrorHandler;
use Illuminate\Support\Facades\Response;
use Throwable;
use Closure;

class ErrorFormatter implements ErrorHandler
{
    public function __invoke(?Throwable $error, Closure $next): ?array
    {
        $formattedError = $next($error);

        if (isset($formattedError['extensions']['validation'])) {
            $formattedError['message'] = collect($formattedError['extensions']['validation'])
                ->first()[0] ?? 'Validation failed';
            unset($formattedError['extensions']['validation']);

            if (!app()->runningInConsole()) {
                Response::json([
                    'errors' => [
                        ['message' => $formattedError['message']]
                    ]
                ], 422)->send();
                exit;
            }
        }

        return $formattedError;
    }
}
