<?php

class LanguageLocalizer  {
    /**
     * @var LanguageLocalizer
     */
    public static $singleton = null;

    public static function changeSingleton(LanguageLocalizer $s) {
        self::$singleton = $s;
    }

    public $stack = array();
    public $phrases = array();

    public function push_language($lang) {
        $this->stack[] = $lang;
    }

    public function get_current_language() {
        if(empty($this->stack)) {
            return 'english';
        }

        return $this->stack[count($this->stack) - 1];
    }

    public function pop_language() {
        if(empty($this->stack)) {
            return;
        }

        $this->stack = array_slice($this->stack, 0, count($this->stack) - 1);
    }

    public function load_phrases($language) {
        $language = empty($language) ? 'english' : $language;

        require_once(Config::$Configs['application']['paths']['application'] . 'strings/' . "strings_" . $language . ".php");

        $vars = get_defined_vars();

        foreach($vars as $name => $value) {
            $this->phrases[$language][$name] = $value;
        }
    }

    public function get_phrase($phrase, $language = null) {
        $language = $language === null ? $this->get_current_language() : $language;

        if(empty($this->phrases[$language])) {
            $this->load_phrases($language);
        }

        if(empty($this->phrases[$language][$phrase])) {
            return $phrase;
        }

        return $this->phrases[$language][$phrase];
    }
}

if(LanguageLocalizer::$singleton === null) {
    LanguageLocalizer::changeSingleton(new LanguageLocalizer());
}

function lang_get($phrase, $language = null, $languageLocalizer = null) {
    $languageLocalizer = $languageLocalizer === null ?
        LanguageLocalizer::$singleton : $languageLocalizer;

    return $languageLocalizer->get_phrase($phrase, $language);
}