<?php

namespace Framework;
use PDO;
use PDOException;
use Exception;
class Database
{
    public $conn;
    /**
     * Comstructor  for database class
     * 
     * @param array $config
     * 
     */
    public function __construct($config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->conn = new PDO($dsn, $config['username'], $config['password']);
        } catch (PDOException $e) {
            throw new Exception("Database Connection Failed:{$e->getMessage()}");
        }
    }
    /**
     * Query the database
     * 
     * @param string $query
     * 
     * @return PDOStatement
     * @throws PDOExeception
     * 
     */
    public function query($query, $params = [])
    {
        try {
            $sth = $this->conn->prepare($query);
            //Bind named  params
            foreach ($params as $param => $value) {
                $sth->bindValue(':' . $param, $value);
            }

            $sth->execute();
            return $sth;
        } catch (PDOException $e) {
            throw new Exception("Query failed to execute:{$e->getMessage()}");
        }
    }
}
