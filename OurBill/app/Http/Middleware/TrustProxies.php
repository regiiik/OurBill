<?php
 
 namespace App\Http\Middleware;
 
 use Closure;
 use Illuminate\Http\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Illuminate\Http\Middleware\TrustProxies as Middleware;
 
 class TrustProxies extends Middleware
 {
     /**
      * The trusted proxies for this application
      *
      * @var array|string|null
      */
     protected $proxies = '*'; // Trust all proxies (Heroku's dynamic IPs)
 
     /**
      * The headers that should be used to detect proxies.
      *
      * @var int
      */
     protected $headers =
         Request::HEADER_X_FORWARDED_FOR |
         Request::HEADER_X_FORWARDED_HOST |
         Request::HEADER_X_FORWARDED_PORT |
         Request::HEADER_X_FORWARDED_PROTO |
         Request::HEADER_X_FORWARDED_AWS_ELB;
     /**
      * Handle an incoming request.
      *
      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
      */
     public function handle(Request $request, Closure $next): Response
     {
         return $next($request);
     }
 }
