<?php

## this server is responsi=sible to insert user to database

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class transactionService
{
    public function __construct(private Database $db)
    {   
    }

    public function create(array $formData)
    {   
        $formattedDate = "{$formData['date']} 00:00:00";
        
        $this->db->query(
            "INSERT INTO transactions (user_id, description, amount, date) VALUES (:user_id, :description, :amount, :date)",
            [
                'user_id' => $_SESSION['user'],
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate
            ]
        );
    }

    ## retrieve all the transaction of the user from database7
    public function getUserTransactions(int $length , int $offset)
    {
        ## retrieving query parameters (the search term) from the url if exist and
        # escaping  custom caracters ( % and _ ) from the search term variable 
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_') ;

        $params = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%"
        ];

        $transactions = $this->db->query(
            "SELECT * , DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM transactions 
             WHERE user_id = :user_id AND description LIKE :description LIMIT {$length} OFFSET {$offset}",
            $params
            )->findAll();

        ## get the receipts with the respective transaction
        $transactions = array_map(function(array $transaction){
            $transaction['receipts'] = $this->db->query(
                "SELECT * FROM receipts WHERE transaction_id = :transaction_id",
                [
                    'transaction_id' => $transaction['id']
                ])->findAll();            

            return $transaction;
        },$transactions);    

        $transactionsCount = $this->db->query(
            "SELECT COUNT(*) FROM transactions 
             WHERE user_id = :user_id AND description LIKE :description",
            $params
            )->count(); 

            return [$transactions, $transactionsCount];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "SELECT * , DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->find();
    }

    public function update(array $formData , int $id)
    {   
        $formattedDate = "{$formData['date']} 00:00:00";

        $this->db->query(
            "UPDATE transactions SET 
                description = :description,
                amount = :amount,
                date = :date
            WHERE user_id = :user_id AND id = :id",
            [
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate,
                'user_id' => $_SESSION['user'],
                'id' => $id
            ]
        );
    }

    public function delete(int $id)
    {
        $this->db->query(
            "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
            );
    }
}