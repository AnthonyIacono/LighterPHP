<?php

/**
 * Class Autoloader
 * This class is used to automatically PHP files using default conventions, custom rules, or a combination of the two.
 */
class Autoloader {
    /**
     * Array of rules controlling the autoloader.
     * @var array
     */
    private $rules = array();

    /**
     * Reset the rules used by the autoloader.
     * This should be used to remove the default conventions.
     */
    public function reset_rules() {
        $this->rules = array();
    }

    /**
     * Adds a file to be included when a specific class is detected by the autoloader.
     * @param $className - Class name to detect.
     * @param $includePath - Path of PHP file to include when class is detected.
     * @param bool $includeOnce - When true, include_once() is used instead of include()
     */
    public function add_simple_rule($className, $includePath, $includeOnce = true) {
        $this->rules[] = array(
            'type' => 'simple',
            'class_name' => $className,
            'include_path' => $includePath,
            'include_once' => $includeOnce
        );
    }

    /**
     * Adds a file to be included when a specific class using regex against the name is detected by the autoloader.
     * @param $classRegex - Class name regex to detect.
     * @param $includePath - Path of PHP file to include when class is detected.
     * @param bool $includeOnce - When true, include_once() is used instead of include()
     */
    public function add_regex_rule($classRegex, $includePath, $includeOnce = true) {
        $this->rules[] = array(
            'type' => 'regex',
            'class_regex' => $classRegex,
            'include_path' => $includePath,
            'include_once' => $includeOnce
        );
    }

    /**
     * Adds a custom rule that may return one or more files to include() or include_once()
     * @param callable $ruleCallback($className) - Called when the rule is evaluated. First parameter is detected class name.
     * You must call include() or include_once() within $ruleCallback if the class name passed in matches your criteria.
     */
    public function add_custom_rule($ruleCallback) {
        $this->rules[] = array(
            'type' => 'custom',
            'rule_callback' => $ruleCallback
        );
    }

    /**
     * Retrieves the full array of rules added to the autoloader.
     * @return array
     */
    public function get_all_rules() {
        return $this->rules;
    }

    /**
     * Register the autoloader with PHP using spl_autoload_register()
     */
    public function register() {
        /**
         * Below we will have to access the autoloader inside our callback for spl_autoload_register()
         */
        $autoloader = $this;

        /**
         * Use spl_autoload_register() to install our autoloader's logic.
         */
        spl_autoload_register(function($className) use($autoloader) {
            /**
             * Grab all the rules defined into the autoloader.
             */
            $autoloaderRules = $autoloader->get_all_rules();

            /**
             * Go through each rule and evaluate it.
             * Valid rule types are 'simple', 'regex', and 'custom'
             */
            foreach($autoloaderRules as $rule) {
                $ruleType = $rule['type'];

                if($ruleType == 'custom') {
                    /**
                     * Since the rule is a custom rule, we just need to call the callback.
                     * @var callable $ruleCallback
                     */
                    $ruleCallback = $rule['rule_callback'];
                    $ruleCallback($className);
                }
                else if($ruleType == 'simple') {
                    /**
                     * Simple rules just check to see if the class name matches, and include() or include_once() the path.
                     */
                    if($rule['class_name'] != $className) {
                        continue;
                    }

                    if($rule['include_once']) {
                        include_once($rule['include_path']);
                    }
                    else {
                        include($rule['include_path']);
                    }
                }
                else if($ruleType == 'regex') {
                    /**
                     * Regex rules check the class name against a regex pattern, and include() or include_once() the path.
                     */

                    if(!preg_match($rule['class_regex'], $className)) {
                        continue;
                    }

                    if($rule['include_once']) {
                        include_once($rule['include_path']);
                    }
                    else {
                        include($rule['include_path']);
                    }
                }

                /**
                 * If we get here, the rule type is invalid. Just skip to the next item in the loop.
                 */
                continue;
            }
        });
    }

    /**
     * Add the default rules to the autoloader, which is done automatically at startup.
     * You may remove these rules using the reset_rules() function of this class.
     */
    public function add_default_rules() {
        /**
         * Below we will need to access the global framework object in our custom callbacks.
         */
        $lighter = lighter();

        /**
         * This rule is added to automatically include resources with matching file names.
         */
        $this->add_custom_rule(function($className) use($lighter) {
            if(!preg_match('/^[a-zA-Z0-9]+Resource$/', $className)) {
                return;
            }

            $includePath = $lighter->get_resources_path() . $className . '.php';

            if(!file_exists($includePath)) {
                return;
            }

            include_once($includePath);
        });

        /**
         * This rule is added to automatically include models with matching file names.
         */
        $this->add_custom_rule(function($className) use($lighter) {
            if(!preg_match('/^[a-zA-Z0-9]+Model$/', $className)) {
                return;
            }

            $includePath = $lighter->get_models_path() . $className . '.php';

            if(!file_exists($includePath)) {
                return;
            }

            include_once($includePath);
        });
    }
}