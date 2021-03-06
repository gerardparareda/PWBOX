<?php

namespace PwBox\Model;

interface UserRepository{
    public function save(User $user);
    public function lookFor(User $user);
    public function getUserId(User $user);
    public function getEmailById(int $idUsuari);
    public function getUsernameById(int $idUsuari);
    public function createDirectory($nomCarpetaActual, $idCarpetaParent, $idUsuari, $esCarpeta);
    public function getRootFolderId($idUsuari);
    public function lookIfDirectoryExists($URLPath);
    public function userPrivileges($idCarpetaActual, $idUsuari);
    public function showDirectory($idCarpetaClicada, $idUsuari);
    public function getIdByUrlPath($urlPath);
    public function getParentFolderId($urlPath);
    public function esCarpeta($urlPath);
    public function renameFolder($idCarpetaActual, $newName);
    public function getFolderPath($idCarpeta);
}