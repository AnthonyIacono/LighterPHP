<?php

/**
 * =============================================================================
 * LighterPHP
 * Copyright (C) 2014 ASDF LLC.  All rights reserved.
 * =============================================================================
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, version 3.0, as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once('Globals.php');
include_once('Functions.php');
include_once('Configuration.php');
include_once('Autoloader.php');
include_once('ViewRenderer.php');
include_once('Resource.php');
include_once('Request.php');
include_once('Route.php');
include_once('Router.php');
include_once('HTTPUtils.php');
include_once('models/Model.php');
include_once('models/MySQLModel.php');
include_once('responses/Response.php');
include_once('responses/ViewResponse.php');
include_once('responses/NotFoundResponse.php');

/**
 * Class Lighter
 * The core class of the LighterPHP framework.
 * You can get this object within your application by calling lighter()
 */
class Lighter {
    /**
     * Incoming HTTP request.
     * @var Request|null $request
     */
    private $request = null;

    /**
     * Route matched during routing phase.
     * @var Route|null $route
     */
    private $route = null;

    /**
     * Route parameters outputted during routing phase.
     * @var array
     */
    private $route_params;

    /**
     * Instance of resource being executed.
     * @var Resource|null $resource
     */
    private $resource = null;

    /**
     * Instance of file autoloader.
     * @var Autoloader|null
     */
    private $autoloader = null;

    /**
     * Call this function to run LighterPHP using server environment variables and application configuration files.
     */
    public function run() {
        /**
         * Create our autoloader, which will be used to automatically include different PHP files.
         */
        $this->autoloader = new Autoloader();

        /**
         * Load the configuration for the autoloader, because it might be disabled.
         */
        Configuration::load('Autoloader');

        /**
         * Add the default rules to the autoloader.
         */
        $this->autoloader->add_default_rules();

        /**
         * Check if the autoloader is enabled, and if it is create it then install it.
         */
        if(Configuration::get('Autoloader', 'enabled')) {
            $this->autoloader->register();
        }

        /**
         * Every application has an Autorun.php where they can place code to run during startup.
         */
        lighter_include($this->get_application_path() . 'Autorun.php');

        /**
         * Create the request object containing information from server environment variables and PHP globals
         */
        $this->request = new Request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING'],
            $_POST, $_FILES, getenv('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');

        /**
         * Load the routes from the configuration file
         */
        Configuration::load('Routes');

        /**
         * Create the router object using routes from the config
         * @var Router $router
         */
        $router = new Router(Configuration::get('Routes', 'routes'), Configuration::get('Routes', 'ignore_extensions'));

        /**
         * Use the request to determine the appropriate route.
         */
        $this->route = $router->route($this->request, $this->route_params);

        if(null === $this->route) {
            /**
             * Since we can't find a route that matches the request URI, we should return the default NotFoundResponse.
             */
            $notFoundResponseName = Configuration::get('Routes', 'not_found_response');
            $response = new $notFoundResponseName();
        }
        else {
            /**
             * Now that we have a route, we need to begin to execute our resource.
             */
            $resourceName = $this->route->resource;

            /**
             * Create our resource object based on the route info.
             */
            $this->resource = new $resourceName();

            /**
             * Run the pre_execute function on the resource.
             */
            $preExecuteResult = $this->resource->pre_execute();

            /**
             * @var Response $response
             */
            $response = null;

            if(is_object($preExecuteResult)) {
                /**
                 * Since the pre_execute function returned an object, it must be the response.
                 */
                $response = $preExecuteResult;
            }
            else {
                /**
                 * The pre_execute function did not return an object, so we should run the execute method and store the response.
                 *
                 */
                $response = $this->resource->execute();
            }
        }

        /**
         * Check that the response is actually valid, otherwise give an exception.
         */
        if(empty($response)) {
            throw new Exception("Your resource \"" . get_class($this->resource). "\" must return a Response object to execute()");
        }

        /**
         * Call the before_render function on the response.
         * Typically this is where response headers are set.
         */
        $response->before_render();

        /**
         * Render the response body, then echo it to the browser.
         */
        $responseBody = $response->render_body();
        echo $responseBody;
    }

    /**
     * Using this function you can get the root directory of your project.
     * @return string
     */
    public function get_root_path() {
        return dirname(dirname(__FILE__)) . '/';
    }

    /**
     * Using this function you can get the application directory of your project.
     * @return string
     */
    function get_application_path() {
        return $this->get_root_path() . 'application/';
    }

    /**
     * Using this function you can get the framework directory of your project.
     * @return string
     */
    function get_framework_path() {
        return $this->get_root_path() . 'framework/';
    }

    /**
     * Using this function you can get the configuration directory of your application.
     * @return string
     */
    public function get_config_path() {
        return $this->get_application_path() . 'configs/';
    }

    /**
     * Using this function you can get the resources directory of your application.
     * @return string
     */
    public function get_resources_path() {
        return $this->get_application_path() . 'resources/';
    }

    /**
     * Using this function you can get the models directory of your application.
     * @return string
     */
    public function get_models_path() {
        return $this->get_application_path() . 'models/';
    }

    /**
     * Using this function you can get the views directory of your application.
     * @return string
     */
    public function get_views_path() {
        return $this->get_application_path() . 'views/';
    }

    /**
     * Retrieve the current request object which can be used to probe information about the incoming HTTP request.
     * @return Request
     */
    public function get_request() {
        return $this->request;
    }

    /**
     * Retrieve the autoloader object which can be used to customize the autoloader rules.
     * @return Autoloader
     */
    public function get_autoloader() {
        return $this->autoloader;
    }

    /**
     * Retrieve the route that was selected when routing the request URI.
     * @return Route|null
     */
    public function get_route() {
        return $this->route;
    }
    /**
     * Retrieve the route parameters that were outputted when routing the request URI.
     * @return array
     */
    public function get_route_params() {
        return $this->route_params;
    }
}