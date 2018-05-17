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
        $sql = "INSERT INTO User(id, username, email, pass, birthDay, birthMonth, birthYear, created_at, updated_at) VALUES(null, :username, :email, :password, :birthDay, :birthMonth, :birthYear, :created_at, :updated_at)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("username", $user->getUsername(), 'string');
        $stmt->bindValue("email", $user->getEmail(), 'string');
        $stmt->bindValue("password", md5($user->getPassword()), 'string');
        $stmt->bindValue("birthDay", $user->getBirthDay(), 'integer');
        $stmt->bindValue("birthMonth", $user->getBirthMonth(), 'string');
        $stmt->bindValue("birthYear", $user->getBirthYear(), 'integer');
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
        //Revisar
        //var_dump($newUser);

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
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createDirectory($nomCarpetaActual, $idCarpetaParent, $idUsuari, $esCarpeta)
    {

        var_dump($nomCarpetaActual);
        var_dump($idUsuari);
        var_dump($idCarpetaParent);
        var_dump($esCarpeta);

        $idHash = 0;

        //Primer accedim a la bbdd per veure quin es l'ultim id de carpeta, perque el hash per fer la url sempre sigui
        //diferent.

        $sql = "SELECT id FROM Directori ORDER BY id desc limit 1";
        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute();
        $idHash = $stmt->fetchColumn (0);
        $idHash++;


        //Despres creem la carpeta a la base de dades.

        $sql = "INSERT INTO Directori (id, nomCarpeta, isRoot, carpetaParent, urlPath, esCarpeta, esShared) VALUES (null, :nomCarpeta, :isRoot, :carpetaParent, :urlPath, :esCarpeta, :esShared)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("nomCarpeta", $nomCarpetaActual, 'string');

        if ($idCarpetaParent == null) {

            $stmt->bindValue("isRoot", 1, 'integer');
            $stmt->bindValue("carpetaParent", null, 'integer');
            $stmt->bindValue("ulrPath", '', 'string');

        } else {

            $stmt->bindValue("isRoot", 0, 'integer');
            $stmt->bindValue("carpetaParent", $idCarpetaParent, 'integer');
            $stmt->bindValue("ulrPath", md5($idHash), 'string');

        }

        $stmt->bindValue("esCarpeta", $esCarpeta, 'boolean');
        $stmt->bindValue("esShared", false, 'boolean');

        //$stmt->bindValue("email", , 'string');
        //$stmt->bindValue("password", md5($user->getPassword()), 'string');
        $result = $stmt->execute();

        //Ara linkem la carpeta a l'usuari que l'ha creat donant permisos.
        $sql = "SELECT id FROM Directori WHERE urlPath = :urlPath";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("ulrPath", md5($idHash), 'string');
        $result = $stmt->execute();
        $idCarpeta = $stmt->fetchColumn (0);


        //Ara que tenim l'id de la carpeta relacionem la carpeta amb el rol de l'usuari.
        //Afegim fila id_carpeta i id_usuari a taula Admin.
        $sql = "INSERT INTO userCarpeta (id_usuari, id_carpeta, admin, reader) VALUES (:id_usuari, :id_carpeta, :admin, :reader)";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("id_usuari", $idUsuari, 'integer');
        $stmt->bindValue("id_carpeta", $idCarpeta, 'integer');
        $stmt->bindValue("admin", true, 'boolean');
        $stmt->bindValue("reader", false, 'boolean');
        $result = $stmt->execute();


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

    public function deleteDirectory($idCarpetaAEsborrar, $idUsuari)
    {

        /*$sql = "SELECT d.nomCarpeta, uc.admin, uc.reader  FROM Directori AS d, UserCarpeta AS uc WHERE d.carpetaParent = :idCarpetaClicada AND d.id = uc.id_carpeta AND (uc.admin = true OR uc.reader = true) AND uc.id_usuari = :idUsuari";
        $stmt = $this->database->prepare($sql);
        $stmt->bindValue("idCarpetaClicada", $idCarpetaClicada, 'integer');
        $stmt->bindValue("idUsuari", $idUsuari, 'integer');
        $result = $stmt->execute();
        $carpetes = $stmt->fetchAll();

        return $carpetes;*/

    }




}