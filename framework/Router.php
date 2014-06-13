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

/**
 * Class Router
 * Responsible for routing requests to a specific resource, and outputting parameters.
 */
class Router {
    /**
     * Internal list of routes loaded from the configuration.
     * @var Route[] $routes
     */
    private $routes = array();

    /**
     * Should the router ignore extensions?
     * @var bool
     */
    private $ignore_extensions = false;

    /**
     * Construct a new instance of Router.
     * @param $routes
     * @param bool $ignoreExtensions
     */
    public function __construct($routes, $ignoreExtensions = false) {
        $this->routes = $routes;
        $this->ignore_extensions = $ignoreExtensions;
    }

    /**
     * Routes a request to a specific resource, and outputs parameters.
     * @param Request $request
     * @param &$routeParams
     * @return Route
     */
    public function route(Request $request, &$routeParams) {
        /**
         * First we need to get the entire URI of the request.
         */
        $requestUri = $request->getUri();

        /**
         * Check if we are ignoring extensions, and if we ignoring them use a regex to remove it.
         */
        if($this->ignore_extensions) {
            $requestUri = preg_replace('/\\.[a-z0-9A-Z]+$/', '', $requestUri);
        }

        /**
         * Extract the pieces of the URI, which are separated by slashes.
         */
        $uriPieces = $this->pieces($requestUri);

        /**
         * Loop through the routes and attempt to match to the request URI.
         */
        foreach($this->routes as $route) {
            /**
             * Take the current route's pattern, and extract the pieces. Similar to above.
             */
            $patternPieces = $this->pieces($route->pattern);

            /**
             * The most basic route will have zero pieces, and should only match for the URI '/'
             */
            if(count($patternPieces) == 0 && $requestUri == '/') {
                /**
                 * There are no parameters to output, so we write empty arrays.
                 */
                $routeParams = array('segments' => array(), 'pieces' => array());

                /**
                 * We have found our route.
                 */
                return $route;
            }

            /**
             * Any other route may have named parameters and multiple segments.
             */
            $named = array();
            $segments = array();

            /**
             * Our flag to determine if the route is a match.
             */
            $matches = true;

            /**
             * Loop through all the pieces of the pattern and determine if the route matches.
             */
            foreach($patternPieces as $pieceIndex => $patternPiece) {
                /**
                 * URI piece that lives at the same index as the pattern piece.
                 */
                $uriPiece = isset($uriPieces[$pieceIndex]) ? $uriPieces[$pieceIndex] : '';

                if($patternPiece == '*') {
                    /**
                     * Pattern piece is a wildcard, so as long as there is at least one more URI piece then the route matches.
                     */
                    if(($pieceIndex + 1) > count($uriPieces)) {
                        $matches = false;
                        break;
                    }

                    /**
                     * Extract the rest of the segments from the remaining URI pieces.
                     */
                    $segments = array_merge($segments, array_slice($uriPieces, $pieceIndex));

                    /**
                     * Output the route parameters.
                     */
                    $routeParams = array('segments' => $segments, 'named' => $named);

                    /**
                     * We have found our route.
                     */
                    return $route;
                }
                else if($patternPiece[0] == ':') {
                    /**
                     * Pattern piece begins with ':', indicating a named parameter.
                     */

                    /**
                     * Add the current piece of the request URI to segments.
                     */
                    $segments[] = $uriPiece;

                    /**
                     * Store the URI piece as a named parameter based on the rest of the pattern piece.
                     */
                    $named[substr($patternPiece, 1)] = $uriPiece;

                    /**
                     * Continue our loop to check if this route continues to match.
                     */
                    continue;
                }
                else if($patternPiece == $uriPiece) {
                    /**
                     * Pattern piece matches current piece of the request URI, add it to segments.
                     */
                    $segments[] = $uriPiece;

                    /**
                     * Continue our loop to check if this route continues to match.
                     */
                    continue;
                }

                /**
                 * If none of the above match, then our route must not match.
                 */
                $matches = false;
                break;
            }

            /**
             * Make sure that we have the same number of pattern pieces and URI pieces.
             */
            if(count($patternPieces) != count($uriPieces)) {
                $matches = false;
            }

            /**
             * If the route does not match we should continue our loop to check the next route.
             */
            if(!$matches) {
                continue;
            }

            /**
             * Since the route matches we need to output the route parameters.
             */
            $routeParams = array('segments' => $segments, 'named' => $named);

            /**
             * We have found our route.
             */
            return $route;
        }

        /**
         * If the above loop fails to return a route, then no route matches the request URI.
         */
        return null;
    }

    /**
     * Extracts pieces of the given URI, separated by slashes, ignoring empty pieces.
     * @param $uri
     * @return array
     */
    private function pieces($uri) {
        /**
         * Get rid of the slash at the beginning of the URI.
         */
        $uri = preg_replace('/^\\//', '', $uri);

        /**
         * Explode the URI by slashes.
         */
        $pieces = explode('/', $uri);

        /**
         * Filter out the empty pieces.
         */
        $pieces = array_filter($pieces, function($piece) {
            return trim($piece) != '';
        });

        return $pieces;
    }
}