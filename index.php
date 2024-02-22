<?php

session_start();
class User
{

    public $conn;

    //Az osztály konstruktora, amely inicializálja az adatbáziskapcsolatot. //Működik
    function __construct()
    {
        $this->conn = mysqli_connect('localhost', 'root', '12345678', 'php_a');
    }

    //Új felhasználó létrehozása a megadott adatok alapján. A jelszót titkosítva kell tárolni.
    //function registerUser($username, $password, $fullname, $email)
    function registerUser($username, $password, $fullname, $email)
    {
        $errors = [];



        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'A felhasználónévnek minimum 3, maximum 30 karakterből kell állnia!';
        }

        if (strlen($fullname) < 5 || strlen($fullname) > 40) {
            $errors[] = 'A névnek minimum 5, maximum 40 karakterből kell állnia!';
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Az email cím invalid!';
        }

        if ($password < 3) {
            $errors[] = 'A jelszónak minimum 4 karakterből kell állnia!';
        }


        if (count($errors) === 0) {

            $hashpass = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($this->conn, "INSERT INTO users (username, fullname, password, email) VALUES ('$username', '$fullname', '$hashpass', '$email')");

            $_SESSION["success"] = 'Sikeres regisztráció!';
        } else {
            $_SESSION["errors"] = $errors;
        }
    }

    //Felhasználó bejelentkezése a megadott felhasználónév és jelszó alapján. Sikeres bejelentkezés esetén true, míg hiba esetén false értékkel térjen vissza. //Működik_A
  //  function login($username, $password)
    function login($id, $password)
    {
        $errors = [];

       // $lekerdezes = mysqli_query($this->conn, "SELECT * FROM users WHERE username = '{$username}'");
        $lekerdezes = mysqli_query($this->conn, "SELECT * FROM users WHERE id = '{$id}'");

        if (mysqli_num_rows($lekerdezes) === 0) {
            $errors[] = 'A felhasználó nem található!';
            $_SESSION["errors"] = $errors;
            return false;
        } else {
            $user = mysqli_fetch_assoc($lekerdezes);

            $loginResult = password_verify($password, $user["password"]);
            if (!$loginResult) {
                $errors[] = 'Sikertelen belépés!';
                $_SESSION["errors"] = $errors;
                 return "false";
               // return false;
            } else {
                $_SESSION["user"] = $user;
                 return "true";
                //return true;
            }
        }
    }

    //A felhasználó kijelentkeztetése. //Mükődik_A
    function logout()
    {
        unset($_SESSION["user"]);
        print 'A felhasználó kiléptetve';
    }

    //A felhasználó adatainak frissítése. //Működik_A
   // function updateUser($username, $password, $fullname, $email)
    function updateUser($id, $username, $password, $fullname, $email)
    {
       // mysqli_query($this->conn, "UPDATE users SET ,fullname= '$fullname',password='$password',email='$email' WHERE username='$username'");
        mysqli_query($this->conn, "UPDATE users SET username='$username',fullname= '$fullname',password='$password',email='$email' WHERE id='$id'");
    }

    //Felhasználó törlése. //Mükődik_A
    function deleteUser($id)
    {
      //  mysqli_query($this->conn, "DELETE FROM users WHERE username='$username'");
        mysqli_query($this->conn, "DELETE FROM users WHERE id='$id'");
    }
    //Az összes felhasználó kilistázása. //Mükődik_A
    function listUsers()
    {
        $result = mysqli_query($this->conn, "SELECT * FROM users");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                print_r($row);
            }
        } else {
            echo "Error executing query: " . mysqli_error($this->conn);
        }
    }
}

$user = new User;

//print_r($user->registerUser('Lajos','12345','Horváth Lajos','hlajso@gmail.com'));
//print_r($user->login('3','12345'));
//print_r($user->logout());
//print_r($user->listUsers());
print_r($user->deleteUser('1'));
print_r($user->listUsers());
