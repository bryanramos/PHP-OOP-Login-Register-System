<?php 

// protection against SQL injection
// makes it easier to get things where we want them
class DB {
    // _ lets us know these properties are private -not- public
    private static $_instance = null; # instance of database if available
    private $_pdo, # represent when we instantiate PDO object here
            $_query, # last query executed 
            $_error = false, # did query fail?
            $_results, # store our results set
            $_count = 0;

    // constructor function -> connects to database
    // protects against having multiple DB connections
    private function _construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') .  'dbname=' . Config::get('mysql/db') . Config::get('mysql/username'), Config::get('mysql/password'));
        } catch (PDOException $e) {
            die($e -> getMessage());
        }
    }

    // check if we already instantiated an object
    // if we haven't instantiated: we instantiate
    // if we have: return instance
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    // query string
    // array of parameters that we might want to include as binded values in PDO
    public function query($sql, $params = array()) {
        $this->_error = false;
        if($this->_query = $this->_pdo->prepare($sql)) { // if everything ok
            // checks params exists
            $x = 1;
            if (count($params)) {
                foreach($params as $param) {
                    $this->_query->bindValue(x, $param);
                    $x++;
                }
            }

            if($this->_query->execute()) { // see if query has been successfully executed not prepared -> store result set
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        }
    }
    
    public function action($action, $table, $where = array()) {
        if(count($where) === 3) { // we need a field, operator, value
            $operators = array('=', '>', "<", ">=", "<=");

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            // operator inside array
            if (in_array($operator, $operators)) {
                $sql = "{$action} * FROM {$table} WHERE {$field} {$operator} ?";
                if (!$this->_query($sql, array($value))) { // if there's not an error
                    return $this;
                }
            }
        }
        return false;
    }

    // shortcut 
    public function get($table, $where) {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where) {
        return $this->action('DELETE', $table, $where);
    }

    public function results() { 
        return $this->_results;
    }

    public function first() { // return first result
        return $this->_results()[0];
    }

    public function error() { // returns true if error is present
        return $this->_error;
    }

    public function count() { 
        return $this->_count;
    }
}