<?php

// *
// *      Created by Tazio
// * Copyright to Tazio de Bruin
// *            2021
// *

class PHPSecure {
    public function __construct($defaultWhere = "OR") {
        $this->regex = (object)array(
            "scriptTag" => "/<(.*)>/i", // https://regex101.com/r/ZQxmkk/1
            "valueVerif" => "/(DELETE|SELECT|UPDATE|CREATE|DROP).*(;)/i", // https://regexr.com/5qafn
            "lastWord" => "/\W\w+\s*(\W*)$/" // https://stackoverflow.com/a/29428662
        );

        $this->default = (object)array(
            "where" => $defaultWhere
        );
    }


    // !                   BUILDERS                   ! //

    /**
     * * SQLSelect *
     * 
     * Create a safe SQL Selection
     * 
     * @param {string} $table
     * @param {object} $values
     * @param {object} $where
     */
    public function SQLSelect(String $table, $values, $where) {
        $string = "SELECT ";

        if(gettype($values) == "array") {
            $string .= "(";
            foreach ($values as $key => $value) {
                $value = $this->SQLValue($value);
                $string .= "$value, ";
            }
            $string = substr_replace($string ,"", -2);
            $string .= ")";
        } elseif(gettype($value) == "string") {
            $string .= $this->SQLValue($value);
        }

        $string .= " FROM $table";

        if(gettype($where) == "object") {
            $string .= " WHERE ";
            foreach ($where as $key => $value) {
                $type = gettype($value);

                if($type == "string") {
                    $string .= "$key = '{$this->SQLValue($value)}' {$this->default->where} ";
                } elseif($type == "integer") {
                    $string .= "$key = {$this->SQLValue($value)} {$this->default->where} ";
                } elseif($type == "object") {
                    if(isset($value->_json) && $value->_json) {
                        unset($value->_json);
                        $json = json_encode($value);
                        $string .= "$key = '{$this->SQLValue($json)}' {$this->default->where}";
                    } elseif($value->value != null) {
                        $or = ($value->or || (isset($value->and) && !$value->and)) ? true : false;
                        $type = (isset($value->type)) ? $value->type : gettype($value->value);
                        $value = $value->value;

                        if($type == "string") {
                            $string .= "$key = '{$this->SQLValue($value)}' ";
                        } elseif($type == "integer") {
                            $string .= "$key = {$this->SQLValue($value)} ";
                        }

                        if($or) {
                            $string .= "OR ";
                        } else {
                            $string .= "AND ";
                        }
                    }
                }
            }

            $string = preg_replace($this->regex->lastWord, '$1', $string);
        }


        return $string;
    }

    /**
     * * SQLInsert *
     * 
     * Create a safe SQL Insertion
     * 
     * @param {string} $table
     * @param {array} $keys
     * @param {array} $values
     */
    public function SQLInsert(String $table, $keys, $values) {
        $string = "INSERT INTO ";

        $string .= $table . " ";

        if(gettype($keys) == "array") {
            $string .= "(";
            foreach ($keys as $key => $value) {
                $value = $this->SQLValue($value);
                $string .= "`$value`, ";
            }
            $string = substr_replace($string ,"", -2);
            $string .= ")";
        } else {
            return null;
        }

        $string .= " VALUES ";

        if(gettype($values) == "array") {
            $string .= "(";
            foreach ($values as $key => $value) {
                $value = $this->SQLValue($value);
                $type = gettype($value);

                if($type == "string") {
                    $string .= "'$value', ";
                }elseif($type == "integer") {
                    $string .= "$value, ";
                }
            }
            $string = substr_replace($string ,"", -2);
            $string .= ")";
        } else {
            return null;
        }


        return $string;
    }


    // !                   VERIFICATION                   ! //

    /**
     * * SQL Value Verification *
     * 
     * Verifies if the string that's given is not a injection
     * 
     * @param {string} $str
     * @param {string} $replace
     */
    public function SQLValue($str = null, String $replace = "") {
        if($str == null || gettype($str) != "string") return $str;

        $str = preg_replace($this->regex->valueVerif, $replace, $str);
        return $str;
    }

    /**
     * * String Tag *
     * 
     * Verifies if the string that's given doesn't include a tag (for script injection)
     * 
     * @param {string} $str
     * @param {string} $replace
     */
    public function TextScript(String $str = null, String $replace = "") {
        if($str == null || gettype($str) != "string") return null;

        $str = preg_replace($this->regex->scriptTag, $replace, $str);
        return $str;
    }
}