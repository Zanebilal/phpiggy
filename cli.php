<?php

    include __DIR__ . "/src/Framework/Database.php";

    use Framework\Database;

    $db = new Database('mysql',[
        'host' =>'localhost',
        'port' => 3306 ,
        'dbname' => 'phpiggy'
    ],'root','');

    $sqlFile = file_get_contents("./database.sql");

    $db->query($sqlFile);

    // try{
        
    //     ## Creating a transaction
    //     $db->connection->beginTransaction();

    //     $db->connection->query("INSERT INTO products VALUES(99,'Gloves')");

    //     $search = "Hats";

    //     $query = "SELECT * FROM products WHERE name =:name";

    //     ## using prepared statement to validate the vlaues so we avoid an SQL Injection attack
    //     $stmt = $db->connection->prepare($query);

    //     ## bined the search variable to the query without excuting it (excute it in later time)
    //     $stmt->bindValue('name','Gloves',PDO::PARAM_STR);

    //     $stmt->execute();

    //     ## end and saving the changes in database if the query is secccesful
    //     $db->connection->commit();

    // }catch(Exception $error){

    //     ## check if the transaction is active
    //     if($db->connection->inTransaction()){

    //     ## revert the changes prformed by the query when the transaction failed 
    //         $db->connection->rollBack();
    //     }

    //     echo "Transaction Failed!";
    // }




