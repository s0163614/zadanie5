<?php

/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */
session_start();
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
  // Выдаем сообщение об успешном сохранении.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass_in', '', 100000);
    // Выводим сообщение пользователю.
    $messages[] = 'Спасибо, результаты сохранены.';
    // Если в куках есть пароль, то выводим сообщение.
    if (!empty($_COOKIE['pass_in'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
        strip_tags($_COOKIE['login']),
        strip_tags($_COOKIE['pass_in']));
    }
    setcookie('fio_value', '', 100000);
    setcookie('mail_value', '', 100000);
    setcookie('year_value', '', 100000);
    setcookie('sex_value', '', 100000);
    setcookie('limb_value', '', 100000);
    setcookie('bio_value', '', 100000);
    setcookie('immortal_value', '', 100000);
    setcookie('ghost_value', '', 100000);
    setcookie('levitation_value', '', 100000);
    setcookie('privacy_value', '', 100000);
  }

  // Складываем признак ошибок в массив.
  $errors_ar = array();
  $error=FALSE;
  $errors_ar['fio'] = !empty($_COOKIE['fio_error']);
  $errors_ar['mail'] = !empty($_COOKIE['mail_error']);
  $errors_ar['year'] = !empty($_COOKIE['year_error']);
  $errors_ar['sex'] = !empty($_COOKIE['sex_error']);
  $errors_ar['limb'] = !empty($_COOKIE['limb_error']);
  $errors_ar['powers'] = !empty($_COOKIE['powers_error']);
  $errors_ar['privacy'] = !empty($_COOKIE['privacy_error']);
  if (!empty($errors_ar['fio'])) {
    setcookie('fio_error', '', 100000);
    $messages[] = '<div class="error">Заполните имя.</div>';
    $error=TRUE;
  }
  if ($errors_ar['mail']) {
    setcookie('mail_error', '', 100000);
    $messages[] = '<div class="error">Заполните или исправьте почту.</div>';
    $error=TRUE;
  }
  if ($errors_ar['year']) {
    setcookie('year_error', '', 100000);
    $messages[] = '<div class="error">Выберите год рождения.</div>';
    $error=TRUE;
  }
  if ($errors_ar['sex']) {
    setcookie('sex_error', '', 100000);
    $messages[] = '<div class="error">Выберите пол.</div>';
    $error=TRUE;
  }
  if ($errors_ar['limb']) {
    setcookie('limb_error', '', 100000);
    $messages[] = '<div class="error">Выберите сколько у вас конечностей.</div>';
    $error=TRUE;
  }
  if ($errors_ar['powers']) {
    setcookie('powers_error', '', 100000);
    $messages[] = '<div class="error">Выберите хотя бы одну суперспособность.</div>';
    $error=TRUE;
  }
  if ($errors_ar['privacy']) {
    setcookie('privacy_error', '', 100000);
    $messages[] = '<div class="error">Необходимо согласиться с политикой конфиденциальности.</div>';
    $error=TRUE;
  }
  // Складываем предыдущие значения полей в массив, если есть.
  // При этом санитизуем все данные для безопасного отображения в браузере.
  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
  $values['mail'] = empty($_COOKIE['mail_value']) ? '' : strip_tags($_COOKIE['mail_value']);
  $values['year'] = empty($_COOKIE['year_value']) ? 0 : $_COOKIE['year_value'];
  $values['sex'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
  $values['limb'] = empty($_COOKIE['limb_value']) ? '' : $_COOKIE['limb_value'];
  $values['immortal'] = empty($_COOKIE['immortal_value']) ? 0 : $_COOKIE['immortal_value'];
  $values['ghost'] = empty($_COOKIE['ghost_value']) ? 0 : $_COOKIE['ghost_value'];
  $values['levitation'] = empty($_COOKIE['levitation_value']) ? 0 : $_COOKIE['levitation_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : strip_tags($_COOKIE['bio_value']);
  $values['privacy'] = empty($_COOKIE['privacy_value']) ? FALSE : $_COOKIE['privacy_value'];
  // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
  // ранее в сессию записан факт успешного логина.
  //print_r(empty($_SESSION['login']).' '.$_COOKIE[session_name()].' '.empty($_SESSION['uid']));
  if (!$error and !empty($_COOKIE[session_name()]) and !empty($_SESSION['login'])) {
    $user = 'u41026';
    $pass = '4433573';
    $db2 = new PDO('mysql:host=localhost;dbname=u41026', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    try{
      $get=$db2->prepare("select * from application where id=?");
      $get->bindParam(1,$_SESSION['uid']);
      $get->execute();
      $inf=$get->fetchALL();
      $values['fio']=$inf[0]['name'];
      $values['mail']=$inf[0]['mail'];
      $values['year']=$inf[0]['date'];
      $values['sex']=$inf[0]['sex'];
      $values['limb']=$inf[0]['limb'];
      $values['bio']=$inf[0]['bio'];

      $get2=$db2->prepare("select power from powers where id=?");
      $get2->bindParam(1,$_SESSION['uid']);
      $get2->execute();
      $inf2=$get2->fetchALL();
      for($i=0;$i<count($inf2);$i++){
        if($inf2[$i]['power']=='бессмертие'){
          $values['immortal']=1;
        }
        if($inf2[$i]['power']=='прохождение сквозь стены'){
          $values['ghost']=1;
        }
        if($inf2[$i]['power']=='левитация'){
          $values['levitation']=1;
        }
      }
    }
    catch(PDOException $e){
      print('Error: '.$e->getMessage());
      exit();
    }
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
  }
  // Включаем содержимое файла form.php.
  // В нем будут доступны переменные $messages, $errors и $values для вывода 
  // сообщений, полей с ранее заполненными данными и признаками ошибок.
  include('form.php');
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
  // Проверяем ошибки.
  $fio=$_POST['fio'];
  $mail=$_POST['mail'];
  $year=$_POST['year'];
  $sex=$_POST['sex'];
  $limb=$_POST['limb'];
  $pwrs=$_POST['power'];
  $bio=$_POST['bio'];
  if(empty($_SESSION['login'])){
    $priv=$_POST['priv'];
  }
  $errors = FALSE;
  if (empty($fio)) {
    setcookie('fio_error', '1', time() + 24*60 * 60);
    setcookie('fio_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('fio_value', $fio, time() + 60 * 60);
    setcookie('fio_error','',100000);
  }
  //проверка почты
  if (empty($mail) or !filter_var($mail,FILTER_VALIDATE_EMAIL)) {
    setcookie('mail_error', '1', time() + 24*60 * 60);
    setcookie('mail_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('mail_value', $mail, time() + 60 * 60);
    setcookie('mail_error','',100000);
  }
  //проверка года
  if ($year=='Выбрать') {
    setcookie('year_error', '1', time() + 24 * 60 * 60);
    setcookie('year_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('year_value', intval($year), time() + 60 * 60);
    setcookie('year_error','',100000);
  }
  //проверка пола
  if (!isset($sex)) {
    setcookie('sex_error', '1', time() + 24 * 60 * 60);
    setcookie('sex_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('sex_value', $sex, time() + 60 * 60);
    setcookie('sex_error','',100000);
  }
  //проверка конечностей
  if (!isset($limb)) {
    setcookie('limb_error', '1', time() + 24 * 60 * 60);
    setcookie('limb_value', '', 100000);
    $errors = TRUE;
  }
  else {
    setcookie('limb_value', $limb, time() + 60 * 60);
    setcookie('limb_error','',100000);
  }
  //проверка суперспособностей
  if (!isset($pwrs)) {
    setcookie('powers_error', '1', time() + 24 * 60 * 60);
    setcookie('immortal_value', '', 100000);
    setcookie('ghost_value', '', 100000);
    setcookie('levitation_value', '', 100000);
    $errors = TRUE;
  }
  else {
    $a=array(
      "immortal_value"=>0,
      "ghost_value"=>0,
      "levitation_value"=>0
    );
    foreach($pwrs as $pwr){
      if($pwr=='бессмертие'){setcookie('immortal_value', 1, time() + 60 * 60); $a['immortal_value']=1;} 
      if($pwr=='прохождение сквозь стены'){setcookie('ghost_value', 1, time() + 60 * 60);$a['ghost_value']=1;} 
      if($pwr=='левитация'){setcookie('levitation_value', 1, time() + 60 * 60);$a['levitation_value']=1;} 
    }
    foreach($a as $c=>$val){
      if($val==0){
        setcookie($c,'',100000);
      }
    }
  }
  //запись куки для биографии
  setcookie('bio_value',$bio,time()+ 60*60);
  //проверка согласия с политикой конфиденциальности
  if(empty($_SESSION['login'])){
    if(!isset($priv)){
      setcookie('privacy_error','1',time()+ 24*60*60);
      setcookie('privacy_value', '', 100000);
      $errors=TRUE;
    }
    else{
      setcookie('privacy_value',TRUE,time()+ 60*60);
      setcookie('privacy_error','',100000);
    }
  }
  if ($errors) {
    setcookie('save','',100000);
    header('Location: login.php');
  }
  else {
    setcookie('fio_error', '', 100000);
    setcookie('mail_error', '', 100000);
    setcookie('year_error', '', 100000);
    setcookie('sex_error', '', 100000);
    setcookie('limb_error', '', 100000);
    setcookie('powers_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('privacy_error', '', 100000);
  }
  
  $user = 'u47502';
  $pass = '8701243';
  $db = new PDO('mysql:host=localhost;dbname=u47502', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
  // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
  if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']) and !$errors) {
    $id=$_SESSION['uid'];
    
    $upd=$db->prepare("update application set name=:name,mail=:mail,date=:date,sex=:sex,limb=:limb,bio=:bio where id=:id");
    $cols=array(
      ':name'=>$fio,
      ':mail'=>$mail,
      ':date'=>$year,
      ':sex'=>$sex,
      ':limb'=>$limb,
      ':bio'=>$bio
    );
    foreach($cols as $k=>&$v){
      $upd->bindParam($k,$v);
    }
    $upd->bindParam(':id',$id);
    $upd->execute();
    $del=$db->prepare("delete from powers where id=?");
    $del->execute(array($id));
    $upd1=$db->prepare("insert into powers set power=:power,id=:id");
    $upd1->bindParam(':id',$id);
    foreach($pwrs as $pwr){
      $upd1->bindParam(':power',$pwr);
      $upd1->execute();
    }
  }
  else {
    if(!$errors){
      $login = 'u'.substr(uniqid(),-5);
      $pass_in = substr(md5(uniqid()),0,10);
      $pass_hash=password_hash($pass_in,PASSWORD_DEFAULT);
      setcookie('login', $login);
      setcookie('pass_in', $pass_in);

      try {
        $stmt = $db->prepare("INSERT INTO application SET name=:name,mail=:mail,date=:date,sex=:sex,limb=:limb,bio=:bio");
        $stmt->bindParam(':name',$_POST['fio']);
        $stmt->bindParam(':mail',$_POST['mail']);
        $stmt->bindParam(':date',$_POST['year']);
        $stmt->bindParam(':sex',$_POST['sex']);
        $stmt->bindParam(':limb',$_POST['limb']);
        $stmt->bindParam(':bio',$_POST['bio']);
        $stmt -> execute();

        $id=$db->lastInsertId();

        $usr=$db->prepare("insert into username set id=?,login=?,pass=?");
        $usr->bindParam(1,$id);
        $usr->bindParam(2,$login);
        $usr->bindParam(3,$pass_hash);
        $usr->execute();

        $pwr=$db->prepare("INSERT INTO powers SET power=:power,id=:id");
        $pwr->bindParam(':id',$id);
        foreach($_POST['power'] as $power){
          $pwr->bindParam(':power',$power); 
          $pwr->execute();  
        }
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }
  }

  if(!$errors){
    setcookie('save', '1');
  }

  // Делаем перенаправление.
  header('Location: ./');
}
