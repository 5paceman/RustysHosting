<?php
class Validate {
    private $_passed = false,
            $_errors = array(),
            $_db = null;

    public function __construct() {
        $this->_db = DB::getInstance();
    }

    public function check($source, $items = array()) {
        foreach($items as $item => $rules) {
            foreach($rules as $rule => $rule_value) {
                $value = $source[$item];
                $item = escape($item);
                if($rule == 'required' && $rule_value && empty ($value)) {
                    $this->addError("{$item} is required");
                } else if(!empty($value)) {
                    switch($rule) {
                        case 'min':
                            if(strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                        break;
                        case 'minNum':
                            if($value < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value}.");
                            }
                        break;
                        case 'maxNum':
                            if($value > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value}.");
                            }
                        break;
                        case 'max':
                            if(strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters.");
                            }
                        break;
                        case 'email':
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError("You must enter a valid email address.");
                            }
                        break;
                        case 'matches':
                            if($value != $source[$rule_value]){
                                $this->addError("{$rule_value} must match {$item}");
                            }
                        break;
                        case 'numeric':
                            if($rule_value && !is_numeric($value)) {
                                $this->addError("{$item} must be numeric.");
                            } else if(!$rule_value && is_numeric($value)) {
                                $this->addError("{$item} must not be numeric.");
                            }
                        break;
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                        break;
                        case 'url':
                            if(!filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                                $this->addError("You must enter a valid URL.");
                            }
                        break;
                    }
                }
            }
        }

        if(empty($this->_errors)){
            $this->_passed = true;
        }

        return $this;
    }

    private function addError($error) {
        $this->_errors[] = $error;
    }

    public function passed() {
        return $this->_passed;
    }

    public function errors() {
        return $this->_errors;
    }
}