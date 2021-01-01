<?php

namespace Package;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Redirect
{
    private $url;
    private $redirects;

    function __construct()
    {
        $this->url = url()->full();
        $this->redirects = [];
    }

    /**
     * URL to https protocol
     * @param url string
     * @return object RedirectHttp
     */
    public function toHttps()
    {
        if (!preg_match('/https/', $this->url)) {
            $this->url = secure_url($this->url);
            $this->redirects[] = 'https';
        }
        return $this;
    }

    /**
     * URL to lowercase
     * @param  url string
     * @return object RedirectHttp
     */
    public function toLower()
    {
        if (Str::lower($this->url) !== $this->url) {
            $this->url = Str::lower($this->url);
            $this->redirects[] = 'lower';
        }
        return $this;
    }

    /**
     * URL to without /index.php
     * @param  url string
     * @return object RedirectHttp
     */
    public function phpFile()
    {
        if (Str::contains($this->url, '/index.php')) {
            $this->url = Str::replaceFirst('/index.php', '', $this->url);
            $this->redirects[] = 'php';
        }
        return $this;
    }

    /**
     * URL to without /index.html
     * @param  url string
     * @return object RedirectHttp
     */
    public function htmlFile()
    {
        if (Str::contains($this->url, '/index.html')) {
            $this->url = Str::replaceFirst('/index.html', '', $this->url);
            $this->redirects[] = 'html';
        }
        return $this;
    }

    /**
     * URL to without www.
     * @param url string
     * @return object RedirecHttp
     */
    public function withoutWww()
    {
        if (Str::contains($this->url, 'www.')) {
            $this->url = Str::replaceFirst('www.', '', $this->url);
            $this->redirects[] = 'www';
        }
        return $this;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $redirectHttp = new Redirect();
        $newUrl = $redirectHttp->toHttps()->withoutWww()->toLower()->phpFile()->htmlFile();
        if (count($redirectHttp->redirects) > 0) {
            return redirect($newUrl->url, 301);
        }
        return $next($request);
    }
}
