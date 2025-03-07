<?php

class Database{
    public $conn;

    /**
     * Constructor for database class
     *
     * @param array $config
     */
    public function __construct($config) {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ];
        try{
            $this->conn = new PDO(
                $dsn, $config['username'], 
                $config['password'],
                $options
            );
        }catch(PDOException $e){
            throw new Exception("Database Connection Failed: {$e->getMessage()}");
        }
    }

    /**]
     * Query the database
     * 
     * @param string $query
     * 
     * @return PDOStatement
     * @throws PDException
     */
    public function query($query){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
        catch(PDOException $e){
            throw new Exception("Query failed to execute: {$e->getMessage()}");
        }
    }

}