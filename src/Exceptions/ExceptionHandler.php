<?php

namespace Core\Exceptions;

use Core\Exceptions\TenantNotFoundException;
use Throwable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Configuration\Exceptions;

final class ExceptionHandler
{
    /** 
     * Handle the exceptions.
     *
     * @param  \Illuminate\Foundation\Configuration\Exceptions  $exceptions
     * @return void
     */
    public function handle(Exceptions $exceptions)
    {
        $exceptions->renderable(
            function (TenantNotFoundException $exception) {
                return $this->renderer('Error', [
                    'status' => 404,
                    'message' => $exception->getMessage(),
                    'backUrl' => '/'
                ])->toResponse(request());
            }
        );

        $exceptions->respond(
            fn(
                $response,
                $exception,
                $request
            ) => $this->respond(
                $response,
                $exception,
                $request
            )
        );
    }

    /**
     * Respond to the exception
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  \Throwable  $exception
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function respond(
        Response $response,
        Throwable $exception,
        Request $request
    ) {

        // if API request
        if ($request->is('api/*')) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $response->getStatusCode());
        }

        // Show the default error page when debug is enabled and not in production environment
        if (
            config('app.debug') &&
            !app()->environment('production', 'prod') &&
            $response->getStatusCode() >= 500
        ) {
            return $response;
        }

        // Handle Inertia responses
        if (
            $response->getStatusCode() >= 500 ||
            in_array($response->getStatusCode(), [401, 403, 404])
        ) {
            return $this->renderer('Error', [
                'status' => $response->getStatusCode(),
                'message' => $exception->getMessage(),
                'backUrl' => url()->previous()
            ])
                ->toResponse($request)
                ->setStatusCode($response->getStatusCode());
        } elseif (
            in_array($response->getStatusCode(), [419,  401])
        ) {
            $title = match ($response->getStatusCode()) {
                401 => 'Unauthorized',
                419 => 'Page Expired',
                default => 'Error',
            };

            flashMessage($title, $exception->getMessage(), 'error');

            return back();
        }

        return $response;
    }

    private function renderer($view = null, $data = [])
    {
        if (function_exists('inertia')) {
            return inertia($view, $data);
        }

        return view($view, $data);
    }
}
