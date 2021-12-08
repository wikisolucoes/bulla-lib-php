<?php

namespace Bulla\lib;

/**
 * Classe de validação de dados
 *
 * @author Felipe Alves <felipe.ci@hotmail.com>
 */
class Validation
{

    protected $exceptionHandling = true;
    protected $stopFirstError = false;
    protected $_data = array();
    protected $_errors = array();
    protected $_pattern = array();
    protected $_messages = array();

    /**
     * Construct method (Set the error messages default)
     * @access public
     * @param boolean $exceptionHandling Use Exception Handling?
     * @return void
     */
    public function __construct($exceptionHandling = true, $stopFirstError = false)
    {
        $this->setMessagesDefault();
        $this->definePattern();
        $this->exceptionHandling = $exceptionHandling;
        $this->stopFirstError = $stopFirstError;
    }

    /**
     * Set a data for validate
     * @access public
     * @param $name String The name of data
     * @param $value Mixed The value of data
     * @return Validation The self instance
     */
    public function set($name, $value)
    {
        $this->_data['name'] = $name;
        $this->_data['value'] = $value;
        return $this;
    }

    /**
     * Set error messages default born in the class
     * @access protected
     * @return void
     */
    protected function setMessagesDefault()
    {
        $this->_messages = array(
            'isRequired' => 'O campo %s é obrigatório',
            'minLength' => 'O campo %s deve conter ao mínimo %s caracter(es)',
            'maxLength' => 'O campo %s deve conter ao máximo %s caracter(es)',
            'betweenLength' => 'O campo %s deve conter entre %s e %s caracter(es)',
            'minValue' => 'O valor do campo %s deve ser maior que %s ',
            'maxValue' => 'O valor do campo %s deve ser menor que %s ',
            'betweenValues' => 'O valor do campo %s deve estar entre %s e %s',
            'isEmail' => 'O email %s não é válido ',
            'isUrl' => 'A URL %s não é válida ',
            'isSlug' => '%s não é um slug ',
            'isNum' => 'O valor %s do campo %s não é numérico ',
            'isInteger' => 'O valor %s do campo %s não é inteiro ',
            'isFloat' => 'O valor %s do campo %s não é float ',
            'isString' => 'O valor %s do campo %s não é String ',
            'isBoolean' => 'O valor %s do campo %s não é booleano ',
            'isObj' => 'A variável %s não é um objeto ',
            'isInstanceOf' => '%s não é uma instância de %s ',
            'isArray' => 'A variável %s não é um array ',
            'isDirectory' => '%s não é um diretório válido ',
            'isEquals' => 'O valor do campo %s deve ser igual à %s ',
            'isNotEquals' => 'O valor do campo %s não deve ser igual à %s ',
            'isCpf' => 'O valor %s não é um CPF válido ',
            'isCnpj' => 'O valor %s não é um CNPJ válido ',
            'contains' => 'O campo %s só aceita um do(s) seguinte(s) valore(s): [%s] ',
            'notContains' => 'O campo %s não aceita o(s) seguinte(s) valore(s): [%s] ',
            'isLowercase' => 'O campo %s só aceita caracteres minúsculos ',
            'isUppercase' => 'O campo %s só aceita caracteres maiúsculos ',
            'isMultiple' => 'O valor %s não é múltiplo de %s',
            'isPositive' => 'O campo %s só aceita valores positivos',
            'isNegative' => 'O campo %s só aceita valores negativos',
            'isDate' => 'A data %s não é válida',
            'isAlpha' => 'O campo %s só aceita caracteres alfabéticos',
            'isAlphaNum' => 'O campo %s só aceita caracteres alfanuméricos',
            'noWhiteSpaces' => 'O campo %s não aceita espaços em branco',
        );
    }

    /**
     * The number of validators methods available in DataValidator
     * @access public
     * @return int Number of validators methods
     */
    public function getNumberValidatorsMethods()
    {
        return count($this->_messages);
    }

    /**
     * Define a custom error message for some method
     * @access public
     * @param String $name The name of the method
     * @param String $value The custom message
     * @return void
     */
    public function setMessage($name, $value)
    {
        if (array_key_exists($name, $this->_messages)) {
            $this->_messages[$name] = $value;
        }
    }

    /**
     * Get the error messages
     * @access public
     * @param String $param [optional] A specific method
     * @return Mixed One array with all error messages or a message of specific method
     */
    public function getMessages($param = false)
    {
        if ($param) {
            return $this->_messages[$param];
        }
        return $this->_messages;
    }

    /**
     * Define the pattern of name of error messages
     * @access public
     * @param String $prefix [optional] The prefix of name
     * @param String $sufix [optional] The sufix of name
     * @return void
     */
    public function definePattern($prefix = '', $sufix = '')
    {
        $this->_pattern['prefix'] = $prefix;
        $this->_pattern['sufix'] = $sufix;
    }

    /**
     * Set a error of the invalid data
     * @access protected
     * @param String $error The error message
     * @return void
     */
    protected function setError($error)
    {
        if ($this->exceptionHandling && $this->stopFirstError) {
            throw new \Exception($error);
        } else {
            $this->_errors[$this->_pattern['prefix'] . $this->_data['name'] . $this->_pattern['sufix']][] = $error;
        }
    }

    /**
     * Verify if the current data is not null
     * @access public
     * @return Validation The self instance
     */
    public function isRequired()
    {
        if ((is_array($this->_data['value']) && !count($this->_data['value']))) {
            $this->setError(sprintf($this->_messages['isRequired'], $this->_data['name']));
        } else if (!is_array($this->_data['value']) && !strlen($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isRequired'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the length of current value is less than the parameter
     * @access public
     * @param Int $length The value for compare
     * @param Boolean $inclusive [optional] Include the lenght in the comparison
     * @return Validation The self instance
     */
    public function minLength($length, $inclusive = true)
    {
        $verify = ($inclusive === true ? strlen($this->_data['value']) >= $length : strlen($this->_data['value']) > $length);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['minLength'], $this->_data['name'], $length));
        }
        return $this;
    }

    /**
     * Verify if the length of current value is more than the parameter
     * @access public
     * @param Int $length The value for compare
     * @param Boolean $inclusive [optional] Include the lenght in the comparison
     * @return Validation The self instance
     */
    public function maxLength($length, $inclusive = true)
    {
        $verify = ($inclusive === true ? strlen($this->_data['value']) <= $length : strlen($this->_data['value']) < $length);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['maxLength'], $this->_data['name'], $length));
        }
        return $this;
    }

    /**
     * Verify if the length current value is between than the parameters
     * @access public
     * @param Int $min The minimum value for compare
     * @param Int $max The maximum value for compare
     * @return Validation The self instance
     */
    public function betweenLength($min, $max)
    {
        if (strlen($this->_data['value']) < $min || strlen($this->_data['value']) > $max) {
            $this->setError(sprintf($this->_messages['betweenLength'], $this->_data['name'], $min, $max));
        }
        return $this;
    }

    /**
     * Verify if the current value is less than the parameter
     * @access public
     * @param Int $value The value for compare
     * @param Boolean $inclusive [optional] Include the value in the comparison
     * @return Validation The self instance
     */
    public function minValue($value, $inclusive = false)
    {
        $verify = ($inclusive === true ? !is_numeric($this->_data['value']) || $this->_data['value'] >= $value : !is_numeric($this->_data['value']) || $this->_data['value'] > $value);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['minValue'], $this->_data['name'], $value));
        }
        return $this;
    }

    /**
     * Verify if the current value is more than the parameter
     * @access public
     * @param Int $value The value for compare
     * @param Boolean $inclusive [optional] Include the value in the comparison
     * @return Validation The self instance
     */
    public function maxValue($value, $inclusive = false)
    {
        $verify = ($inclusive === true ? !is_numeric($this->_data['value']) || $this->_data['value'] <= $value : !is_numeric($this->_data['value']) || $this->_data['value'] < $value);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['maxValue'], $this->_data['name'], $value));
        }
        return $this;
    }

    /**
     * Verify if the length of current value is more than the parameter
     * @access public
     * @param Int $minValue The minimum value for compare
     * @param Int $maxValue The maximum value for compare
     * @return Validation The self instance
     */
    public function betweenValues($minValue, $maxValue)
    {
        if (!is_numeric($this->_data['value']) || (($this->_data['value'] < $minValue || $this->_data['value'] > $maxValue))) {
            $this->setError(sprintf($this->_messages['betweenValues'], $this->_data['name'], $minValue, $maxValue));
        }
        return $this;
    }

    /**
     * Verify if the current data is a valid email
     * @access public
     * @return Validation The self instance
     */
    public function isEmail()
    {
        if (strlen($this->_data['value']) && filter_var($this->_data['value'], FILTER_VALIDATE_EMAIL) === false) {
            $this->setError(sprintf($this->_messages['isEmail'], $this->_data['value']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a valid URL
     * @access public
     * @return Validation The self instance
     */
    public function isUrl()
    {
        if (strlen($this->_data['value']) && filter_var($this->_data['value'], FILTER_VALIDATE_URL) === false) {
            $this->setError(sprintf($this->_messages['isUrl'], $this->_data['value']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a slug
     * @access public
     * @return Validation The self instance
     */
    public function isSlug()
    {
        $verify = true;

        if (strstr($input, '--')) {
            $verify = false;
        }
        if (!preg_match('@^[0-9a-z\-]+$@', $input)) {
            $verify = false;
        }
        if (preg_match('@^-|-$@', $input)) {
            $verify = false;
        }
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isSlug'], $this->_data['value']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a numeric value
     * @access public
     * @return Validation The self instance
     */
    public function isNum()
    {
        if (strlen($this->_data['value']) && !is_numeric($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isNum'], $this->_data['value'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a integer value
     * @access public
     * @return Validation The self instance
     */
    public function isInteger()
    {
        if (strlen($this->_data['value']) && !is_numeric($this->_data['value']) && (int) $this->_data['value'] != $this->_data['value']) {
            $this->setError(sprintf($this->_messages['isInteger'], $this->_data['value'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a float value
     * @access public
     * @return Validation The self instance
     */
    public function isFloat()
    {
        if (strlen($this->_data['value']) && !is_float(filter_var($this->_data['value'], FILTER_VALIDATE_FLOAT))) {
            $this->setError(sprintf($this->_messages['isFloat'], $this->_data['value'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a string value
     * @access public
     * @return Validation The self instance
     */
    public function isString()
    {
        if (strlen($this->_data['value']) && !is_string($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isString'], $this->_data['value'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a boolean value
     * @access public
     * @return Validation The self instance
     */
    public function isBoolean()
    {
        if (strlen($this->_data['value']) && !is_bool($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isBoolean'], $this->_data['value'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a object
     * @access public
     * @return Validation The self instance
     */
    public function isObj()
    {
        if (!is_object($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isObj'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a instance of the determinate class
     * @access public
     * @param String $class The class for compare
     * @return Validation The self instance
     */
    public function isInstanceOf($class)
    {
        if (!($this->_data['value'] instanceof $class)) {
            $this->setError(sprintf($this->_messages['isInstanceOf'], $this->_data['name'], $class));
        }
        return $this;
    }

    /**
     * Verify if the current data is a array
     * @access public
     * @return Validation The self instance
     */
    public function isArray()
    {
        if (!is_array($this->_data['value'])) {
            $this->setError(sprintf($this->_messages['isArray'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current parameter it is a directory
     * @access public
     * @return Validation The self instance
     */
    public function isDirectory()
    {
        $verify = is_string($this->_data['value']) && is_dir($this->_data['value']);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isDirectory'], $this->_data['value']));
        }
        return $this;
    }

    /**
     * Verify if the current data is equals than the parameter
     * @access public
     * @param String $value The value for compare
     * @param Boolean $identical [optional] Identical?
     * @return Validation The self instance
     */
    public function isEquals($value, $identical = false)
    {
        $verify = ($identical === true ? $this->_data['value'] === $value : strtolower($this->_data['value']) == strtolower($value));
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isEquals'], $this->_data['name'], $value));
        }
        return $this;
    }

    /**
     * Verify if the current data is not equals than the parameter
     * @access public
     * @param String $value The value for compare
     * @param Boolean $identical [optional] Identical?
     * @return Validation The self instance
     */
    public function isNotEquals($value, $identical = false)
    {
        $verify = ($identical === true ? $this->_data['value'] !== $value : strtolower($this->_data['value']) != strtolower($value));
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isNotEquals'], $this->_data['name'], $value));
        }
        return $this;
    }

    /**
     * Verify if the current data is a valid CPF
     * @access public
     * @return Validation The self instance
     */
    public function isCpf()
    {
        $verify = true;

        $c = preg_replace('/\D/', '', $this->_data['value']);

        if (strlen($c) != 11) {
            $verify = false;
        }

        if (preg_match("/^{$c[0]}{11}$/", $c)) {
            $verify = false;
        }

        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--)
        ;

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $verify = false;
        }

        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--)
        ;

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $verify = false;
        }

        if (!$verify) {
            $this->setError(sprintf($this->_messages['isCpf'], $this->_data['value']));
        }

        return $this;
    }

    /**
     * Verify if the current data is a valid CNPJ
     * @access public
     * @return Validation The self instance
     */
    public function isCnpj()
    {
        $verify = true;

        $c = preg_replace('/\D/', '', $this->_data['value']);
        $b = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);

        if (strlen($c) != 14) {
            $verify = false;
        }

        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i])
        ;

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $verify = false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++])
        ;

        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            $verify = false;
        }

        if (!$verify) {
            $this->setError(sprintf($this->_messages['isCnpj'], $this->_data['value']));
        }

        return $this;
    }

    /**
     * Verify if the current data contains in the parameter
     * @access public
     * @param Mixed $values One array or String with valids values
     * @param Mixed $separator [optional] If $values as a String, pass the separator of values (ex: , - | )
     * @return Validation The self instance
     */
    public function contains($values, $separator = false)
    {
        if (!is_array($values)) {
            if (!$separator || is_null($values)) {
                $values = array();
            } else {
                $values = explode($separator, $values);
            }
        }

        if (!in_array($this->_data['value'], $values)) {
            $this->setError(sprintf($this->_messages['contains'], $this->_data['name'], implode(', ', $values)));
        }
        return $this;
    }

    /**
     * Verify if the current data not contains in the parameter
     * @access public
     * @param Mixed $values One array or String with valids values
     * @param Mixed $separator [optional] If $values as a String, pass the separator of values (ex: , - | )
     * @return Validation The self instance
     */
    public function notContains($values, $separator = false)
    {
        if (!is_array($values)) {
            if (!$separator || is_null($values)) {
                $values = array();
            } else {
                $values = explode($separator, $values);
            }
        }

        if (in_array($this->_data['value'], $values)) {
            $this->setError(sprintf($this->_messages['notContains'], $this->_data['name'], implode(', ', $values)));
        }
        return $this;
    }

    /**
     * Verify if the current data is loweracase
     * @access public
     * @return Validation The self instance
     */
    public function isLowercase()
    {
        if ($this->_data['value'] !== mb_strtolower($this->_data['value'], mb_detect_encoding($this->_data['value']))) {
            $this->setError(sprintf($this->_messages['isLowercase'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is uppercase
     * @access public
     * @return Validation The self instance
     */
    public function isUppercase()
    {
        if ($this->_data['value'] !== mb_strtoupper($this->_data['value'], mb_detect_encoding($this->_data['value']))) {
            $this->setError(sprintf($this->_messages['isUppercase'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is multiple of the parameter
     * @access public
     * @param Int $value The value for comparison
     * @return Validation The self instance
     */
    public function isMultiple($value)
    {
        if ($value == 0) {
            $verify = ($this->_data['value'] == 0);
        } else {
            $verify = ($this->_data['value'] % $value == 0);
        }
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isMultiple'], $this->_data['value'], $value));
        }
        return $this;
    }

    /**
     * Verify if the current data is a positive number
     * @access public
     * @param Boolean $inclusive [optional] Include 0 in comparison?
     * @return Validation The self instance
     */
    public function isPositive($inclusive = false)
    {
        $verify = ($inclusive === true ? ($this->_data['value'] >= 0) : ($this->_data['value'] > 0));
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isPositive'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a negative number
     * @access public
     * @param Boolean $inclusive [optional] Include 0 in comparison?
     * @return Validation The self instance
     */
    public function isNegative($inclusive = false)
    {
        $verify = ($inclusive === true ? ($this->_data['value'] <= 0) : ($this->_data['value'] < 0));
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isNegative'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data is a valid Date
     * @access public
     * @param String $format [optional] The Date format
     * @return Validation The self instance
     */
    public function isDate($format = null)
    {
        $verify = true;
        if ($this->_data['value'] instanceof DateTime) {
            return $this;
        } elseif (!is_string($this->_data['value'])) {
            $verify = false;
        } elseif (is_null($format)) {
            $verify = (strtotime($this->_data['value']) !== false);
            if ($verify) {
                return $this;
            }
        }
        if ($verify) {
            $date_from_format = DateTime::createFromFormat($format, $this->_data['value']);
            $verify = $date_from_format && $this->_data['value'] === date($format, $date_from_format->getTimestamp());
        }
        if (!$verify) {
            $this->setError(sprintf($this->_messages['isDate'], $this->_data['value']));
        }
        return $this;
    }

    /**
     * Verify if the current data contains just alpha caracters
     * @access protected
     * @param String $string_format The regex
     * @param String $additional [optional] The extra caracters
     * @return Boolean True if data is valid or false otherwise
     */
    protected function generic_alpha_num($string_format, $additional = '')
    {
        $this->_data['value'] = (string) $this->_data['value'];
        $clean_input = str_replace(str_split($additional), '', $this->_data['value']);
        return ($clean_input !== $this->_data['value'] && $clean_input === '') || preg_match($string_format, $clean_input);
    }

    /**
     * Verify if the current data contains just alpha caracters
     * @access public
     * @param String $additional [optional] The extra caracters
     * @return Validation The self instance
     */
    public function isAlpha($additional = '')
    {
        $string_format = '/^(\s|[a-zA-Z])*$/';
        if (!$this->generic_alpha_num($string_format, $additional)) {
            $this->setError(sprintf($this->_messages['isAlpha'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data contains just alpha-numerics caracters
     * @access public
     * @param String $additional [optional] The extra caracters
     * @return Validation The self instance
     */
    public function isAlphaNum($additional = '')
    {
        $string_format = '/^(\s|[a-zA-Z0-9])*$/';
        if (!$this->generic_alpha_num($string_format, $additional)) {
            $this->setError(sprintf($this->_messages['isAlphaNum'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Verify if the current data no contains white spaces
     * @access public
     * @return Validation The self instance
     */
    public function noWhiteSpaces()
    {
        $verify = is_null($this->_data['value']) || preg_match('#^\S+$#', $this->_data['value']);
        if (!$verify) {
            $this->setError(sprintf($this->_messages['noWhiteSpaces'], $this->_data['name']));
        }
        return $this;
    }

    /**
     * Validate the data
     * @access public
     * @return bool The validation of data
     */
    public function validate()
    {
        if (count($this->_errors) > 0) {
            if ($this->exceptionHandling && !$this->stopFirstError) {
                $msgError = '';
                foreach ($this->_errors as $error) {
                    $msgError .= implode("\n", $error) . "\n";
                }
                throw new \Exception($msgError);
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * The messages of invalid data
     * @param String $param [optional] A specific error
     * @return Mixed One array with messages or a message of specific error
     */
    public function getErrors($param = false)
    {
        if ($param) {
            if (isset($this->_errors[$this->_pattern['prefix'] . $param . $this->_pattern['sufix']])) {
                return $this->_errors[$this->_pattern['prefix'] . $param . $this->_pattern['sufix']];
            } else {
                return false;
            }
        }
        return $this->_errors;
    }

}