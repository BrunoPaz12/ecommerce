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
    public static function listAll(){
        $sql = new Sql();
        return $sql->select("SELECT * FROM TB_USERS A INNER JOIN TB_PERSONS B USING(IDPERSON) ORDER BY B.DESPERSON");
    }
    public function save(){
        $sql = new Sql();
        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));
        $this->setData($results[0]);
    }
    public function get($iduser){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM TB_USERS A INNER JOIN TB_PERSONS B USING(IDPERSON) WHERE A.IDUSER = :iduser", array(
            ":iduser"=>$iduser
        ));
        $this->setData($results[0]);
    }
    public function update(){
        $sql = new Sql();
        $results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));
        $this->setData($results[0]);
    }
    public function delete(){
        $sql = new Sql();
        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }

}

?>