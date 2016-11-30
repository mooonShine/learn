<?php

/* !
 * Medoo database framework
 * http://medoo.in
 * Version 0.8.2
 * 
 * Copyright 2013, Angel Lai
 * Released under the MIT license
 * 
 * 
 * @edit:Sgenmi  修改说明：以抽像类做model底层，读写分离设置，默认配置文件
 * @date:2014-02-18 16:20
 */

abstract class Medoo
{

//    protected $database_type = 'mysql';
//    // For MySQL, MSSQL, Sybase
//    protected $server = 'localhost';
//    protected $port = '3306';
//    protected $username = 'username';
//    protected $password = 'password';
//    // For SQLite
//    protected $database_file = '';
//    // Optional
//    protected $charset = 'utf8';
//    protected $database_name = '';
    static $option = array(
        PDO::ATTR_PERSISTENT => true
    );
    protected $table;
    protected $pdo = null;
    static $_prefix = NULL;
    protected $prefix = '';
    static $connect = array();
    private $queryString = null;

    public function __construct($options = null)
    {
        $this->pdo = self::set_connect($options);
        $this->prefix = self::$_prefix;
    }

    static function set_connect($config = NULL, $m = 0)
    {
        $pdo = $_config = array();
        $_k = "s";
        $_count = 0;

        if ($m == 1) {
            $_k = 'm';
        }

        if (!empty(self::$connect[$_k])) {
//            print_r( '1111' . $_k );
            $pdo = self::$connect[$_k];
        } else {
            if (!$config || !is_array($config)) {
                $_c = Yaf_Application::app()->getConfig();
                $config = $_c->database->params->toArray();
            }

            if ($m == 1) {
                $_config = $config['master'];
            } else {
                $config['slave'][] = $config['master'];
                $_count = $config['slave'];

                $s_i = rand(0, count($config['slave']) - 1);
                $_config = $config['slave'][$s_i];
            }
            $_config['option'] = self::$option;
            self::$_prefix = $_config['prefix'];
            try {

                $type = strtolower($_config['database_type']);
                switch ($type) {
                    case 'mysql':
                    case 'pgsql':
                        $pdo = new PDO(
                                $type . ':host=' . $_config['server'] . ';port=' . $_config['port'] . ';dbname=' . $_config['database_name'], $_config['username'], $_config['password'], $_config['option']
                        );
                        break;
                }
                $pdo->exec('SET NAMES \'' . $_config['charset'] . '\'');

                //如果从库个数是一个，那么没有从库，只有主库
                if ($_count == 1 || (isset($_config['master']) && $_config['master'] == 1)) {
                    self::$connect['m'] = $pdo;
                    self::$connect['s'] = $pdo;
                } else {
                    self::$connect[$_k] = $pdo;
                }

//                print_r( '2222' . $_k );
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
//        print_r( self::$connect );

        return $pdo;
    }

    private function check_r_w()
    {
        if (stripos(trim($this->queryString), "select") !== 0) {
            $this->pdo = self::set_connect(NULL, 1);
        }
    }

    public function query($query)
    {
        $this->queryString = $query;
        $this->check_r_w();
        return $this->pdo->query($query);
    }

    /**
     * query扩展方法
     * @param type $sql
     * @param type $data
     */
    public function queryext($sql, $data)
    {
        $str = '$temp = sprintf($sql ';
        foreach ($data as $value) {
            $value = $this->quote($value);
            $str .= ',' . $value;
        }

        $str .= ");";

        eval($str);
        $this->queryString = $temp;
        $this->check_r_w();
        return $this->pdo->query($temp);
    }

    public function exec($query)
    {
        $this->queryString = $query;
        $this->check_r_w();
        return $this->pdo->exec($query);
    }

    public function quote($string)
    {
        return $this->pdo->quote($string);
    }

    protected function array_quote($array)
    {
        $temp = array();
        foreach ($array as $value) {
            $temp[] = is_int($value) ? $value : $this->pdo->quote($value);
        }

        return implode($temp, ',');
    }

    protected function inner_conjunct($data, $conjunctor, $outer_conjunctor)
    {
        $haystack = array();
        foreach ($data as $value) {
            $haystack[] = '(' . $this->data_implode($value, $conjunctor) . ')';
        }

        return implode($outer_conjunctor . ' ', $haystack);
    }

    protected function data_implode($data, $conjunctor, $outer_conjunctor = null)
    {
        $wheres = array();
        foreach ($data as $key => $value) {
            if (($key == 'AND' || $key == 'OR') && is_array($value)) {
                $wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ?
                        '(' . $this->data_implode($value, ' ' . $key) . ')' :
                        '(' . $this->inner_conjunct($value, ' ' . $key, $conjunctor) . ')';
            } else {
                preg_match('/([\w\.]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>)\])?/i', $key, $match);
                if (isset($match[3])) {
                    if ($match[3] == '' || $match[3] == '!') {
                        $wheres[] = is_array($value) ? $match[1] . ' not IN (' . $this->array_quote($value) . ')' :
                                $match[1] . ' ' . $match[3] . '= ' . $this->quote($value);
                    } else {
                        if ($match[3] == '<>') {
                            if (is_array($value) && is_numeric($value[0]) && is_numeric($value[1])) {
                                $wheres[] = $match[1] . ' BETWEEN ' . $value[0] . ' AND ' . $value[1];
                            }
                        } else {
                            if (is_numeric($value)) {
                                $wheres[] = $match[1] . ' ' . $match[3] . ' ' . $value;
                            }
                        }
                    }
                } else {
                    if (is_int($key)) {
                        $wheres[] = $this->quote($value);
                    } else {
                        $wheres[] = is_array($value) ? $match[1] . ' IN (' . $this->array_quote($value) . ')' :
                                $match[1] . ' = ' . $this->quote($value);
                    }
                }
            }
        }

        return implode($conjunctor . ' ', $wheres);
    }

    public function where_clause($where)
    {
        $where_clause = '';
        if (is_array($where)) {
            $single_condition = array_diff_key($where, array_flip(
                            array('AND', 'OR', 'GROUP', 'ORDER', 'HAVING', 'LIMIT', 'LIKE', 'MATCH')
                    ));
            if ($single_condition != array()) {
                $where_clause = ' WHERE ' . $this->data_implode($single_condition, '');
            }
            if (isset($where['AND'])) {
                $where_clause = ' WHERE ' . $this->data_implode($where['AND'], ' AND ');
            }
            if (isset($where['OR'])) {
                $where_clause = ' WHERE ' . $this->data_implode($where['OR'], ' OR ');
            }
            if (isset($where['LIKE'])) {
                $like_query = $where['LIKE'];
                if (is_array($like_query)) {
                    if (isset($like_query['OR']) || isset($like_query['AND'])) {
                        $connector = isset($like_query['OR']) ? 'OR' : 'AND';
                        $like_query = isset($like_query['OR']) ? $like_query['OR'] : $like_query['AND'];
                    } else {
                        $connector = 'AND';
                    }

                    $clause_wrap = array();
                    foreach ($like_query as $column => $keyword) {
                        if (is_array($keyword)) {
                            foreach ($keyword as $key) {
                                $clause_wrap[] = $column . ' LIKE ' . $this->quote('%' . $key . '%');
                            }
                        } else {
                            $clause_wrap[] = $column . ' LIKE ' . $this->quote('%' . $keyword . '%');
                        }
                    }
                    $where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . '(' . implode($clause_wrap, ' ' . $connector . ' ') . ')';
                }
            }
            if (isset($where['MATCH'])) {
                $match_query = $where['MATCH'];
                if (is_array($match_query) && isset($match_query['columns']) && isset($match_query['keyword'])) {
                    $where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . ' MATCH (' . implode($match_query['columns'], ', ') . ') AGAINST (' . $this->quote($match_query['keyword']) . ')';
                }
            }
            if (isset($where['GROUP'])) {
                $where_clause .= ' GROUP BY ' . $where['GROUP'];
            }

            if (isset($where['HAVING'])) {
                $where_clause .= ' HAVING ' . $this->data_implode($where['HAVING'], '');
            }

            if (isset($where['ORDER'])) {
                $where_clause .= ' ORDER BY ' . $where['ORDER'];
            }

            if (isset($where['LIMIT'])) {
                if (is_numeric($where['LIMIT'])) {
                    $where_clause .= ' LIMIT ' . $where['LIMIT'];
                }
                if (is_array($where['LIMIT']) && is_numeric($where['LIMIT'][0]) && is_numeric($where['LIMIT'][1])) {
                    $where_clause .= ' LIMIT ' . $where['LIMIT'][0] . ',' . $where['LIMIT'][1];
                }
            }
        } else {
            if ($where != null) {
                $where_clause .= ' where ' . $where;
            }
        }

        return $where_clause;
    }

    public function select($columns, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        $query = $this->query('SELECT ' . (
                is_array($columns) ? implode(', ', $columns) : $columns
                ) . ' FROM ' . $table . $this->where_clause($where));

        return $query ? $query->fetchAll(
                        (is_string($columns) && $columns != '*') ? PDO::FETCH_COLUMN : PDO::FETCH_ASSOC
                ) : false;
    }

    public function insert($data, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        $keys = implode(',', array_keys($data));
        $values = array();
        foreach ($data as $key => $value) {
            $values[] = is_array($value) ? serialize($value) : $value;
        }

        $this->query('INSERT INTO ' . $table . ' (' . $keys . ') VALUES (' . $this->data_implode(array_values($values), ',') . ')');

        return $this->pdo->lastInsertId();
    }

    public function update($data, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        $fields = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $fields[] = $key . '=' . $this->quote(serialize($value));
            } else if (is_null($value)) {
                $fields[] = $key . '=NULL';
            } else {
                preg_match('/([\w]+)(\[(\+|\-)\])?/i', $key, $match);
                if (isset($match[3])) {
                    if (is_numeric($value)) {
                        $fields[] = $match[1] . ' = ' . $match[1] . ' ' . $match[3] . ' ' . $value;
                    }
                } else {
                    $fields[] = $key . ' = ' . $this->quote($value);
                }
            }
        }
        return $this->exec('UPDATE ' . $table . ' SET ' . implode(',', $fields) . $this->where_clause($where));
    }

    public function delete($where, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return $this->exec('DELETE FROM ' . $table . $this->where_clause($where));
    }

    public function replace($columns, $search = null, $replace = null, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        if (is_array($columns)) {
            $replace_query = array();
            foreach ($columns as $column => $replacements) {
                foreach ($replacements as $replace_search => $replace_replacement) {
                    $replace_query[] = $column . ' = REPLACE(' . $column . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
                }
            }
            $replace_query = implode(', ', $replace_query);
            $where = $search;
        } else {
            if (is_array($search)) {
                $replace_query = array();
                foreach ($search as $replace_search => $replace_replacement) {
                    $replace_query[] = $columns . ' = REPLACE(' . $columns . ', ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
                }
                $replace_query = implode(', ', $replace_query);
                $where = $replace;
            } else {
                $replace_query = $columns . ' = REPLACE(' . $columns . ', ' . $this->quote($search) . ', ' . $this->quote($replace) . ')';
            }
        }

        return $this->exec('UPDATE ' . $table . ' SET ' . $replace_query . $this->where_clause($where));
    }

    public function get($columns, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        if (is_array($where)) {
            $where['LIMIT'] = 1;
        }
        $data = $this->select($columns, $where, $table);

        return isset($data[0]) ? $data[0] : false;
    }

    public function has($where, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return $this->query('SELECT EXISTS(SELECT 1 FROM ' . $table . $this->where_clause($where) . ')')->fetchColumn() === '1';
    }

    public function count($where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT COUNT(*) FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    //add by zb
    public function count_ex($column, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT COUNT(' . $column . ') FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    public function max($table, $column, $where = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT MAX(' . $column . ') FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    public function min($column, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT MIN(' . $column . ') FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    public function avg($column, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT AVG(' . $column . ') FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    public function sum($column, $where = null, $table = null)
    {
        if (!$table) {
            $table = $this->prefix . $this->table;
        }
        return 0 + ($this->query('SELECT SUM(' . $column . ') FROM ' . $table . $this->where_clause($where))->fetchColumn());
    }

    public function error()
    {
        return $this->pdo->errorInfo();
    }

    public function last_query()
    {
        return $this->queryString;
    }

    private function info()
    {
        return array(
            'server' => $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO),
            'client' => $this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
            'driver' => $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
            'version' => $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
            'connection' => $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
        );
    }

}
