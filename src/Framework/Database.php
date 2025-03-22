<?php

declare(strict_types=1);

namespace Framework;

use PDO,PDOException,PDOStatement;

class Database
{   
    private PDO $connection;
    private PDOStatement $stmt;

    public function __construct(string $driver, array $config, string $username, string $password)
    {   
        ## binning the confige params with ; separeator
        $config = http_build_query(data: $config, arg_separator:';');

        $dsn = "{$driver}:{$config}";

        try{
            $this->connection =  new PDO($dsn, $username, $password,[
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }catch(PDOException $e){
            die("Unable to connect to database");
        }
    }

    ## make the query function capebale of handeling prepared statement
    public function query(string $query,array $params = []): Database
    {
        $this->stmt = $this->connection->prepare($query);

        $this->stmt->execute($params);

        ## make the count function able to grep the result 
        return $this;
    }

    public function count(){

        ## fetching a single result from the array of database
        return $this->stmt->fetchColumn();
    }

    public function find(){
        return $this->stmt->fetch();
    }  

    ## return the id of the last inserted value on the database
    public function id()
    {
        return $this->connection->lastInsertId();
    }

    ## return all the transaction of the user from database7
    public function findAll()
    {
        return $this->stmt->fetchAll();
    }
}
