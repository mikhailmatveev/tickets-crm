<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class TicketRateLimit
{
    // Временной интервал 24 часа
    const int DECAY = 60 * 60 * 24;

    /**
     * Проверяет на превышение лимита кол-ва создаваемых тикетов в сутки с одного и того же email или телефона
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $keyEmail = 'ticket:create:' . md5($request->input('email'));
        $keyPhone = 'ticket:create:' . md5($request->input('phone'));

        if (
            RateLimiter::tooManyAttempts($keyEmail, 1) ||
            RateLimiter::tooManyAttempts($keyPhone, 1)
        ) {
            return response()->json([
                'message' => 'Вы уже создавали заявку. Попробуйте через 24 часа.'
            ], 429);
        }

        $response = $next($request);

        // Фиксируем попытку только при успешном ответе
        if ($response->getStatusCode() === 201) {
            RateLimiter::hit($keyEmail, self::DECAY);
            RateLimiter::hit($keyPhone, self::DECAY);
        }

        return $response;
    }
}
