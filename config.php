<?php
$host="localhost";
$port=3306;
$dbname="reservation";
$user="root";
$password="";

try{
    $pdo_init= new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$password);
    $pdo_init->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $error){
    die("Une erreur est survenue $error");
}