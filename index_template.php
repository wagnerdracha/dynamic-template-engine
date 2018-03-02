<?php
//Seu login
$login = $_POST['login'];
//Seu grupo
$grupo = $_POST['group'];
//Valores de banco
$arr = null;
//Componentes
$componentes = $_POST['components'];
//Templates
$template    = $_POST['template'];


/*
* 
*/
include_once('templateEngene.php');

$templateUm = new \TemplateEngeneCreador\TemplateEngene();

echo $templateUm->createTemplate($template, $componentes, $login, $grupo, $arr, TRUE);

?>