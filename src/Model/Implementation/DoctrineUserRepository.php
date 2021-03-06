<?php

namespace PwBox\Model\Implementation;

use PwBox\Model\User;
use PwBox\Model\UserRepository;
use Doctrine\DBAL\Connection;

class DoctrineUserRepository implements UserRepository{

    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private $database;

    /**
     * DoctrineUserRepository constructor.
     * @param $database
     */
    public function __construct(Connection $database)
    {
        //No cal connectar-se a la base de dades, perque ho fa sol amb els parametres
        // de app/settings.php
        $this->database = $database;
    }


    /**
     * @param User $user
     * @throws \Doctrine\DBAL\DBALException
     */
    public function save(User $user)
    {
        $sql = "INSERT INTO User(id, username, email, pass, birthDay, birthMonth, birthYear, activateHash, activatedAccount, created_at, updated_at) VALUES(null, :username, :email, :password, :birthDay, :birthMonth, :birthYear, :activateHash, 0,:created_at, :updated_at)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $user->getUsername(), 'string');
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $stmt->bindValue("birthDay", $user->getBirthDay(), 'integer');
        $stmt->bindValue("birthMonth", $user->getBirthMonth(), 'string');
        $stmt->bindValue("birthYear", $user->getBirthYear(), 'integer');
        $stmt->bindValue("activateHash", md5($user->getUsername()), 'string');
        $stmt->bindValue("created_at", $user->getCreatedAt()->format(self::DATE_FORMAT));
        $stmt->bindValue("updated_at", $user->getUpdatedAt()->format(self::DATE_FORMAT));
        $stmt->execute();

        //Ara retornem l'id de l'ususari.
        $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();
        $newUser = $stmt->fetchColumn (0);

        return $newUser;
    }

    /**
     * @param User $user
     * @throws \Doctrine\DBAL\DBALException
     */
    public function lookFor(User $user)
    {
        if ($user->getEmail() != null) {
            $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("email", $user->getEmail(), 'string');
            $stmt->bindValue("password", md5($user->getPassword()), 'string');

        } else {
            $sql = "SELECT * FROM User WHERE username = :username AND pass = :password";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("username", $user->getUsername(), 'string');
            $stmt->bindValue("password", md5($user->getPassword()), 'string');

        }

        $result = $stmt->execute();
        $newUser = $stmt->fetch();

        if ($newUser != null) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserId(User $user)
    {
        if ($user->getEmail() != null) {
            $sql = "SELECT * FROM User WHERE email = :email AND pass = :password";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("email", $user->getEmail(), 'string');
            $stmt->bindValue("password", md5($user->getPassword()), 'string');

        } else {
            $sql = "SELECT * FROM User WHERE username = :username AND pass = :password";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("username", $user->getUsername(), 'string');
            $stmt->bindValue("password", md5($user->getPassword()), 'string');

        }

        $result = $stmt->execute();
        $newUser = $stmt->fetchColumn (0);

        return $newUser;
    }

    public function getEmailById($idUsuari)
    {
        $sql = "SELECT email FROM User WHERE id = :id";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id", $idUsuari, 'integer');

        $result = $stmt->execute();
        $email = $stmt->fetchColumn (0);

        return $email;
    }

    public function getPasswordById($idUsuari)
    {
        $sql = "SELECT pass FROM User WHERE id = :id";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id", $idUsuari, 'integer');

        $result = $stmt->execute();
        $password = $stmt->fetchColumn (0);

        return $password;
    }

    public function getUsernameById($idUsuari)
    {
        $sql = "SELECT username FROM User WHERE id = :id";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id", $idUsuari, 'integer');

        $result = $stmt->execute();
        $username = $stmt->fetchColumn (0);

        return $username;
    }

    public function getIdByUrlPath($urlPath)
    {
        $sql = "SELECT * FROM Directori WHERE urlPath = :urlPath;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("urlPath", $urlPath, 'string');

        $result = $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param $username
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM User WHERE username = :username;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $username, 'string');

        $result = $stmt->execute();

        return $stmt->fetch();
    }

    public function getUserByEmail($email)
    {
        $sql = "SELECT * FROM User WHERE email = :email;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("email", $email, 'string');

        $result = $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createDirectory($nomCarpetaActual, $idCarpetaParent, $idUsuari, $esCarpeta)
    {
        //Primer accedim a la bbdd per veure quin es l'ultim id de carpeta, perque el hash per fer la url sempre sigui
        //diferent.

        $sql = "SELECT id FROM Directori ORDER BY id desc limit 1";
        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute();
        $idHash = $stmt->fetchColumn (0);
        $idHash++;


        //Despres creem la carpeta a la base de dades.

        $sql = "INSERT INTO Directori (id, nomCarpeta, isRoot, carpetaParent, urlPath, esCarpeta, esShared, id_propietari) VALUES (null, :nomCarpeta, :isRoot, :carpetaParent, :urlPath, :esCarpeta, :esShared, :id_propietari); ";
        $stmt = $this->database->prepare($sql);


        $stmt->bindValue("nomCarpeta", $nomCarpetaActual, 'string');
        $stmt->bindValue("isRoot", false, 'boolean');
        $stmt->bindValue("carpetaParent", $idCarpetaParent, 'integer');
        $stmt->bindValue("urlPath", md5($idHash), 'string');

        $stmt->bindValue("esCarpeta", $esCarpeta, 'boolean');
        $stmt->bindValue("esShared", false, 'boolean');
        $stmt->bindValue("id_propietari", $_COOKIE['user_id'], 'integer');


        //$stmt->bindValue("email", , 'string');
        //$stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();

        //Ara linkem la carpeta a l'usuari que l'ha creat donant permisos.
        $sql = "SELECT id FROM Directori WHERE urlPath = :urlPath";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("urlPath", md5($idHash), 'string');
        $result = $stmt->execute();
        $idCarpeta = $stmt->fetchColumn (0);

        //Ara que tenim l'id de la carpeta relacionem la carpeta amb el rol de l'usuari.
        //Afegim fila id_carpeta i id_usuari a taula Admin.
        $sql = "INSERT INTO UserCarpeta (id_usuari, id_carpeta, admin, reader) VALUES (:id_usuari, :id_carpeta, :admin, :reader)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id_usuari", $idUsuari, 'integer');
        $stmt->bindValue("id_carpeta", $idCarpeta, 'integer');
        $stmt->bindValue("admin", true, 'boolean');
        $stmt->bindValue("reader", false, 'boolean');
        $result = $stmt->execute();
    }

    public function createRootDirectory($idUsuari)
    {

        $sql = "INSERT INTO Directori (id, nomCarpeta, isRoot, carpetaParent, urlPath, esCarpeta, esShared, id_propietari) VALUES (null, :idUsuari, true, null, '', true, false, :id_propietari); ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $stmt->bindValue("id_propietari", $idUsuari, 'integer');

        $result = $stmt->execute();

        $sql = "SELECT * FROM Directori ORDER BY id desc limit 1;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $rootFolderId = $stmt->fetchColumn(0);

        $sql = "INSERT INTO UserCarpeta (id_usuari, id_carpeta, admin, reader) VALUES (:idUsuari, :idCarpeta, true, false); ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $stmt->bindValue("idCarpeta", $rootFolderId, 'integer');
        $result = $stmt->execute();

        return $rootFolderId;

    }

    public function isFile($idCarpeta)
    {

        $sql = "SELECT * FROM Directori WHERE id = :idCarpeta; ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpeta", $idCarpeta, 'integer');
        $result = $stmt->execute();
        $carpeta = $stmt->fetch();

        if ($carpeta['esCarpeta'] == true) {
            return false;
        }
        return true;

    }

    public function getFileById($idCarpeta)
    {

        $sql = "SELECT * FROM Directori WHERE id = :idCarpeta; ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpeta", $idCarpeta, 'integer');
        $result = $stmt->execute();
        $fitxer = $stmt->fetch();

        return $fitxer;

    }

    public function getRootFolderId($idUsuari)
    {

        $sql = "SELECT d.id, d.nomCarpeta, d.urlPath, uc.admin, uc.reader FROM Directori AS d, UserCarpeta AS uc WHERE d.id = uc.id_carpeta AND uc.id_usuari = :idUsuari AND d.isRoot = 1; ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $rootFolderId = $stmt->fetchColumn(0);

        return $rootFolderId;

    }

    public function lookIfDirectoryExists($URLPath)
    {

        $sql = "SELECT * FROM Directori WHERE urlPath = :urlPath";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("urlPath", $URLPath, 'string');
        $result = $stmt->execute();
        $carpeta = $stmt->fetch();

        return $carpeta;

    }

    /**
     * @param $idCarpeta
     * @param $idUsuari
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function userPrivileges($idCarpetaActual, $idUsuari)
    {

        $sql = "SELECT uc.admin, uc.reader FROM UserCarpeta AS uc WHERE uc.id_carpeta = :idCarpetaActual AND (uc.admin = true OR uc.reader = true) AND uc.id_usuari = :idUsuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaActual", $idCarpetaActual, 'integer');
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $permisos = $stmt->fetch();

        return $permisos;

    }

    public function getCarpetesChildId($idCarpetaActual)
    {

        $sql = "SELECT id FROM Directori WHERE carpetaParent = :idCarpetaActual";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaActual", $idCarpetaActual, 'integer');
        $result = $stmt->execute();
        $carpetes = $stmt->fetchAll();

        return $carpetes;

    }

    public function getParentFolderId($urlPath)
    {

        $sql = "SELECT carpetaParent FROM Directori WHERE urlPath = :urlPath;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("urlPath", $urlPath, 'string');
        $result = $stmt->execute();
        $parentId = $stmt->fetch();

        return $parentId;

    }

    public function getFolderPath($idCarpeta)
    {

        $sql = "SELECT urlPath FROM Directori WHERE id = :idCarpeta;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpeta", $idCarpeta, 'integer');
        $result = $stmt->execute();
        $urlPath = $stmt->fetch();

        return $urlPath;

    }

    public function renameFolder($idCarpetaActual, $newName)
    {

        $sql = "UPDATE Directori SET nomCarpeta = :newNameCarpeta WHERE id = :idCarpeta;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpeta", $idCarpetaActual, 'integer');
        $stmt->bindValue("newNameCarpeta", $newName, 'string');
        $result = $stmt->execute();

    }

    /**
     * @param $urlPath
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function esCarpeta($urlPath)
    {

        $sql = "SELECT esCarpeta FROM Directori WHERE urlPath = :urlPath;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("urlPath", $urlPath, 'string');
        $result = $stmt->execute();
        $esCarpeta = $stmt->fetch();

        return $esCarpeta;

    }
    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFitxerPerId($id)
    {

        $sql = "SELECT esCarpeta, nomCarpeta FROM Directori WHERE id = :id;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id", $id, 'integer');
        $result = $stmt->execute();
        $esCarpeta = $stmt->fetch();

        return $esCarpeta['esCarpeta'] ? false : $esCarpeta['nomCarpeta'];

    }


    /**
     * @param $idCarpetaClicada
     * @param $idUsuari
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function showDirectory($idCarpetaClicada, $idUsuari)
    {
        $idHash = 0;

        //Primer accedim a la bbdd per veure quin es l'ultim id de carpeta, perque el hash per fer la url sempre sigui
        //diferent.

        $sql = "SELECT d.id, d.nomCarpeta, d.urlPath, d.esCarpeta, uc.admin, uc.reader, d.esShared  FROM Directori AS d, UserCarpeta AS uc WHERE d.carpetaParent = :idCarpetaClicada AND d.id = uc.id_carpeta AND (uc.admin = true OR uc.reader = true) AND uc.id_usuari = :idUsuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaClicada", $idCarpetaClicada, 'integer');
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $carpetes = $stmt->fetchAll();

        /*foreach ($carpetes as $carpeta) {
            
        }*/

        return $carpetes;

    }

    public function showFilesOfSharedDirectory($idCarpetaClicada)
    {

        $sql = " SELECT id, nomCarpeta, urlPath, esCarpeta FROM Directori WHERE carpetaParent = :idCarpetaClicada AND esCarpeta = false; ";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaClicada", $idCarpetaClicada, 'integer');
        $result = $stmt->execute();
        $fitxers = $stmt->fetchAll();

       return $fitxers;

    }

    public function showSharedDirectory($idUsuari)
    {

        $sql = "SELECT d.id, d.nomCarpeta, d.urlPath, d.esCarpeta, uc.admin, uc.reader FROM Directori AS d, SharedUserCarpeta AS uc WHERE d.id = uc.id_carpeta AND uc.id_usuari = :idUsuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $carpetes = $stmt->fetchAll();

        /*foreach ($carpetes as $carpeta) {

        }*/

        return $carpetes;

    }

    public function removeFolder($idCarpetaAEsborrar)
    {

        $sql = "SELECT * FROM SharedUserCarpeta WHERE id_carpeta = :idCarpetaAEsborrar";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaAEsborrar", $idCarpetaAEsborrar, 'integer');
        $result1 = $stmt->execute();

        if ($result1 != null) {
            $sql = "DELETE FROM SharedUserCarpeta WHERE id_carpeta = :idCarpetaAEsborrar;";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("idCarpetaAEsborrar", $idCarpetaAEsborrar, 'integer');
            $result = $stmt->execute();
        }

        $sql = "SELECT * FROM UserCarpeta WHERE id_carpeta = :idCarpetaAEsborrar";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaAEsborrar", $idCarpetaAEsborrar, 'integer');
        $result2 = $stmt->execute();

        if ($result2 != null) {
            $sql = "DELETE FROM UserCarpeta WHERE id_carpeta = :idCarpetaAEsborrar;";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("idCarpetaAEsborrar", $idCarpetaAEsborrar, 'integer');
            $result = $stmt->execute();
        }

        $sql = "DELETE FROM Directori WHERE id = :idCarpetaAEsborrar;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaAEsborrar", $idCarpetaAEsborrar, 'integer');
        $result = $stmt->execute();

    }

    public function insertNotification($idUsuari, $notificationTitle ,$notificationMessage)
    {

        $sql = "INSERT INTO UserNotification(id_notificacio, id_usuari, title, message, time_sent) VALUES (null, :idUsuari, :notificationTitle, :notificationMessage, now());";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $stmt->bindValue("notificationTitle", $notificationTitle, 'string');
        $stmt->bindValue("notificationMessage", $notificationMessage, 'string');
        $result = $stmt->execute();

    }

    public function showNotifications($idUsuari)
    {

        $sql = "SELECT title, message, time_sent FROM UserNotification WHERE id_usuari = :idUsuari;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $notificacions = $stmt->fetchAll();

        return $notificacions;

    }

    public function getActivationById($idUsuari)
    {

        $sql = "SELECT activatedAccount FROM User WHERE id = :idUsuari;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $activated = $stmt->fetchAll();

        if($activated[0]['activatedAccount'] == 1){
            return true;
        }else{
            return false;
        }


    }

    public function getIdByHash($hash)
    {
        $sql = "SELECT id FROM User WHERE activateHash = :hash;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("hash", $hash, 'string');
        $result = $stmt->execute();
        return ($stmt->fetchAll())[0]['id'];

    }

    public function activateUserById($idUser)
    {
        $sql = "UPDATE User SET activatedAccount = 1 WHERE id = :idUser;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idUser", $idUser, 'integer');
        $result = $stmt->execute();

    }

    public function updateEmailById($newEmail, $idUser){
        $sql = "UPDATE User SET email = :newEmail WHERE id = :idUser;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("newEmail", $newEmail, 'string');
        $stmt->bindValue("idUser", $idUser, 'integer');
        $result = $stmt->execute();
    }

    public function updatePasswordById($newPassword, $idUser){
        $sql = "UPDATE User SET pass = :newPassword WHERE id = :idUser;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("newPassword", md5($newPassword), 'string');
        $stmt->bindValue("idUser", $idUser, 'integer');
        $result = $stmt->execute();
    }



    public function getIdByUsername($username)
    {
        $sql = "SELECT id FROM User WHERE username = :username;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $username, 'string');

        $result = $stmt->execute();

        return $stmt->fetchAll();
    }

    public function share($id_user, $id_folder, $admin){

        $sql = "SELECT * FROM SharedUserCarpeta WHERE id_usuari = :id_user AND id_carpeta = :id_carpeta";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id_user", $id_user, 'integer');
        $stmt->bindValue("id_carpeta", $id_folder, 'integer');
        $result = $stmt->execute();

        if ($result != null) {
            $sql = "DELETE FROM SharedUserCarpeta WHERE id_usuari = :id_user AND id_carpeta = :id_carpeta";
            $stmt = $this->database->prepare($sql);
            $stmt->bindValue("id_user", $id_user, 'integer');
            $stmt->bindValue("id_carpeta", $id_folder, 'integer');
            $stmt->execute();
        }

        $sql = "INSERT INTO SharedUserCarpeta(id_usuari, id_carpeta, admin, reader) VALUES (:id_user, :id_carpeta, :admin, :reader);";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id_user", $id_user, 'integer');
        $stmt->bindValue("id_carpeta", $id_folder, 'integer');
        $stmt->bindValue("admin", $admin, 'boolean');
        $stmt->bindValue("reader", true, 'boolean');
        $result = $stmt->execute();
    }

    public function userPrivilegesShared($idCarpetaActual, $idUsuari)
    {

        $sql = "SELECT uc.admin, uc.reader FROM SharedUserCarpeta AS uc WHERE uc.id_carpeta = :idCarpetaActual AND (uc.admin = true OR uc.reader = true) AND uc.id_usuari = :idUsuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaActual", $idCarpetaActual, 'integer');
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $permisos = $stmt->fetch();


        return $permisos;
    }

    public function isAdmin($idCarpetaParent, $idUsuari) {

        $sql = "SELECT admin FROM SharedUserCarpeta WHERE id_carpeta = :id_carpeta AND id_usuari = :id_usuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id_carpeta", $idCarpetaParent, 'integer');
        $stmt->bindValue("id_usuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $admin = $stmt->fetch();

        return $admin;
    }

    public function getUserIdByDirectoryId($directoryId)
    {
        $sql = "SELECT id_usuari FROM UserCarpeta WHERE id_carpeta = :idDirectori;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idDirectori", $directoryId, 'integer');
        $result = $stmt->execute();

        $userId = $stmt->fetch();
        return $userId;
    }

    public function deleteAllUserInformation($userid){
        $sql = "DELETE FROM UserNotification WHERE id_usuari = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();

        //$sql = "DELETE FROM SharedUserCarpeta WHERE id_usuari = :userid;";
        /*$sql = "DELETE suc, d FROM SharedUserCarpeta as suc, Directori AS d WHERE suc.id_carpeta = d.id AND d.id_propietari = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();*/

        $sql = "DELETE suc FROM SharedUserCarpeta as suc, Directori AS d WHERE suc.id_carpeta = d.id AND d.id_propietari = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();

        $sql = "DELETE FROM UserCarpeta WHERE id_usuari = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();

        $sql = "DELETE FROM Directori WHERE id_propietari = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();


        $sql = "DELETE FROM User WHERE id = :userid;";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("userid", $userid, 'integer');
        $result = $stmt->execute();

    }


}

