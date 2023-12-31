<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use MiladRahimi\Jwt\Cryptography\Keys\HmacKey;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Validator\DefaultValidator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Exceptions\InvalidTokenException;
use MiladRahimi\Jwt\Exceptions\ValidationException;


class ValidateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = trim(trim($request->header('authorization'), 'Bearer'));

        if (empty($jwt)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $secret = env('JWT_SECRET');
        $key = new HmacKey($secret);
        $signer = new HS256($key);
        $validator = new DefaultValidator();
        $parser = new Parser($signer, $validator);

        try {
            $parser->parse($jwt);
        } catch (InvalidTokenException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
