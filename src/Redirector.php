<?php

namespace Bolt\Extension\Mimez\Redirect;

use Symfony\Component\HttpFoundation\Request;

class Redirector
{
    /**
     * @var array
     */
    protected $redirects = [];

    /**
     * Primary Domain
     *
     * @var string
     */
    protected $domain;

    /**
     * SSL
     *
     * @var bool
     */
    protected $ssl;

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Redirector
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * @param boolean $ssl
     * @return Redirector
     */
    public function setSsl($ssl)
    {
        $this->ssl = $ssl;
        return $this;
    }

    /**
     * @return array
     */
    public function getRedirects()
    {
        return $this->redirects;
    }

    /**
     * @param array $redirects
     * @return Redirector
     */
    public function setRedirects($redirects)
    {
        $this->redirects = $redirects;
        return $this;
    }
    public function getRedirect(Request $request)
    {
        // check if we need to redirect the domain
        $redirectDomain = $this->getDomainRedirect($request);

        // check if there is a path redirect
        $redirectPath = $this->getPathRedirect($request);

        // check if we have NO redirect
        if ($redirectDomain === false && $redirectPath === false) {
            return false;
        }

        // we have a redirect
        $redirectDomain = $redirectDomain !== false ? $redirectDomain : $this->buildBaseUrl($request);
        $redirectPath = $redirectPath !== false ? $redirectPath : $request->getRequestUri();

        return $redirectDomain . $redirectPath;
        
    }

    /**
     * Checks the Domain if redirect is needed
     *
     * @param Request $request
     * @return bool|string returns the SchemaAndHttpHost if a redirect is needed, otherwise false
     */
    protected function getDomainRedirect(Request $request)
    {
        $baseUrl = $this->buildBaseUrl($request);

        // check if the request matches the leading domain.
        if ($baseUrl != $request->getSchemeAndHttpHost()) {
            return $baseUrl;
        }

        return false;
    }

    /**
     * Checks the Path redirection
     *
     * @param Request $request
     * @return bool|string returns the redirect if we have match otherwise false
     */
    protected function getPathRedirect(Request $request)
    {
        $requestUri = strtolower($request->getRequestUri());
        
        foreach ($this->getRedirects() as $url => $redirect) {

            // sanetize url
            $url = '/' . ltrim($url, '/');

            // check if the current Uri matches the redirect
            if (strpos($requestUri, $url) === 0) {
                return '/' . ltrim($redirect, '/');
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function buildBaseUrl(Request $request)
    {
        // determine the configured domain and ssl
        $domain = $this->getDomain() ? $this->getDomain() : $request->getHost();
        $secure = $this->getSsl() !== null ? $this->getSsl() : $request->isSecure();

        // build the leading domain
        $baseUrl = sprintf('%s://%s', ($secure ? 'https' : 'http'), $domain);

        return $baseUrl;
    }
}