<?php
namespace Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Model;

class User extends Model{
    const SESSION = "User";
    public static function login($login, $passwd){
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM TB_USERS WHERE DESLOGIN = :LOGIN", array(
            ":LOGIN"=>$login
        ));
        if(count($results) === 0){
            throw new \Exception("Invalid Data");
            
        }
        $data = $results[0];

        if(password_verify($passwd, $data["despassword"]) === true)
        {
            $user = new User();
            $user->setData($data);
            
            $_SESSION[User::SESSION] = $user->getValues();

            //var_dump($user);
            //exit;
            return $user;


        } else {
            throw new \Exception("Invalid Data");
            
        }
    }
    public static function verifyLogin($inadmin = true)
    {
        if(
            !isset($_SESSION[User::SESSION])
            || 
            !$_SESSION[User::SESSION]
            || 
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
            ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
        ){
            header("Location: /admin/login");
            exit;
        }
    }
    public static function logout(){
        $_SESSION[User::SESSION] = null;
    }

}

?>