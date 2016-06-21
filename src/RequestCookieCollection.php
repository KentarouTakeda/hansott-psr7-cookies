<?php
declare(strict_types=1);

namespace HansOtt\PSR7Cookies;

use Psr\Http\Message\ServerRequestInterface;

final class RequestCookieCollection implements CookieCollectionInterface
{
    private $cookies = [];

    /**
     * CookieCollection constructor.
     *
     * @param Cookie[] $cookies
     */
    public function __construct(array $cookies = [])
    {
        foreach ($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }

    /**
     * Create instance from a server request.
     *
     * @param ServerRequestInterface $request
     *
     * @return RequestCookieCollection
     */
    public static function createFromRequest(ServerRequestInterface $request) : RequestCookieCollection
    {
        $collection = new static();
        $cookies = $request->getCookieParams();
        foreach ($cookies as $name => $value) {
            $collection->addCookie(new Cookie($name, $value));
        }

        return $collection;
    }

    private function addCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    /**
     * Does the collection has a cookie with the given name?
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name) : bool
    {
        return isset($this->cookies[$name]);
    }

    /**
     * Get a cookie from the collection by name.
     *
     * @param string $name
     *
     * @throws CookieNotFound
     *
     * @return Cookie
     */
    public function get(string $name) : Cookie
    {
        if (isset($this->cookies[$name]) === false) {
            throw CookieNotFound::forName($name);
        }

        return $this->cookies[$name];
    }

    public function current() : Cookie
    {
        return current($this->cookies);
    }

    public function key() : string
    {
        return key($this->cookies);
    }

    public function next()
    {
        next($this->cookies);
    }

    public function valid() : bool
    {
        return key($this->cookies) !== null;
    }

    public function rewind()
    {
        reset($this->cookies);
    }
}
