<?php

namespace Bolt\Extension\Mimez\Redirect;

use Bolt\Extension\SimpleExtension;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class RedirectExtension extends SimpleExtension
{
    public function boot(Application $app)
    {
        // get config
        $config = $this->getConfig();

        // we have a separate redirector service
        $app['mimez.redirector'] = $app->share(function() use ($config) {

            $redirector = new Redirector();

            // set domain if present
            if (isset($config['domain']) && strlen($config['domain'])) {
                $redirector->setDomain($config['domain']);
            }

            // set ssl if present
            if (isset($config['ssl'])) {
                $redirector->setSsl((bool)$config['ssl']);
            }

            // set redirects
            if (isset($config['redirects']) && count($config['redirects'])) {
                $redirector->setRedirects($config['redirects']);
            }

            return $redirector;
        });

        $app->before(function (Request $request) use($app) {

            // try to determine a redirection for this request
            $redirect = $app['mimez.redirector']->getRedirect($request);

            if ($redirect !== false) {
                return $app->redirect($redirect, 301);
            }
        });
    }
}
