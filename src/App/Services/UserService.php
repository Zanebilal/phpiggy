<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{

    ## getting an instance of databases and passing it to the authcontroller
    public function __construct(private Database $db)
    {
        
    }

    ## validating emails from database
    public function isEmailTaken(string $email){

        ## check if an email exist in user's table ussing p;aceholder (prepared statements)
        $emailCount = $this->db->query(
            "SELECT COUNT(*) FROM users WHERE email = :email",
            [
                'email' => $email
            ]
            )->count(); ## grepping the resulte from the query method by chainnig another method

            if($emailCount > 0) {
                throw new ValidationException(['email' => 'Email taken']);
            }
    }

    public function create(array $formData){

        ## hashing the password
        $password = password_hash($formData['password'], PASSWORD_BCRYPT,['cost' => 12]);

        $this->db->query(
            "INSERT INTO users (email, password, age, country) VALUES (:email, :password, :age, :country)",
            [
                'email' => $formData['email'],
                'password' => $password,
                'age' => $formData['age'],
                'country' => $formData['country'],
                
            ]
            );

            ## authenticating registred users
            session_regenerate_id();

            # storing the id of the user
            $_SESSION['user'] = $this->db->id();
    }

    public function login(array $formData){
        
        ## grep user from the database with thier email
        $user = $this->db->query(
            "SELECT * FROM users WHERE email = :email",
            [
                'email' => $formData['email']
            ]
            )->find(); ##  return the result from the query

            ## check if the password in database (if exist) matches the loging password
            $passwordsMatch = password_verify($formData['password'], $user['password'] ?? '');
            
            ## check if the user or password exist in database
            if(!$user || !$passwordsMatch){
                throw new ValidationException([
                    'password' => ['Invalid credentials']
                ]);
            }

            ## regenerating a session id (get a new session id when ever user looged in or out to the app)
            session_regenerate_id();

            ## to verify the users logged in to thier account
            $_SESSION['user'] = $user['id'];

    }

    ## delete the session id thats make the middleware assume that the user does not login to the site
    public function logout()
    {
        unset($_SESSION['user']);

        session_regenerate_id();
    }

}