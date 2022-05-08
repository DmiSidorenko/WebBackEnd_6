<?php

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 100000);
        setcookie('login', '', time() - 100000);
        setcookie('password', '', time() - 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
        if (!empty($_COOKIE['password'])) {
          $messages[] = sprintf('Вы можете <a href="authorization.php">войти</a> с логином <strong>%s</strong>
            и паролем <strong>%s</strong> для изменения данных.',
            strip_tags($_COOKIE['login']),
            strip_tags($_COOKIE['password']));
        }
  }

       $errors = array();
       $errors['fio'] = !empty($_COOKIE['fio_error']);
       $errors['email'] = !empty($_COOKIE['email_error']);
       $errors['date'] = !empty($_COOKIE['date_error']);
       $errors['sex'] = !empty($_COOKIE['sex_error']);
       $errors['superpowers'] = !empty($_COOKIE['superpowers_error']);
       $errors['limbs'] = !empty($_COOKIE['limbs_error']);

       if ($errors['fio']) {
           setcookie('fio_error', '', 100000);
           $messages[] = '<div class="error">Заполните имя.</div>';
       }
       if ($errors['email']) {
           setcookie('email_error', '', 100000);
           $messages[] = '<div class="error">Заполните email.</div>';
       }
       if ($errors['date']) {
           setcookie('date_error', '', 100000);
           $messages[] = '<div class="error">Заполните дату рождения.</div>';
       }
       if ($errors['sex']) {
           setcookie('sex_error', '', 100000);
           $messages[] = '<div class="error">Укажите пол.</div>';
       }
       if ($errors['superpowers']) {
           setcookie('superpowers_error', '', 100000);
           $messages[] = '<div class="error">Укажите суперспособности.</div>';
       }
       if ($errors['limbs']) {
           setcookie('limbs_error', '', 100000);
           $messages[] = '<div class="error">Укажите количество конечностей.</div>';
       }


   $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['date'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
    $values['sex'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
    $values['superpowers'] = empty($_COOKIE['superpowers_value']) ? '' : $_COOKIE['superpowers_value'];
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : $_COOKIE['limbs_value'];
    $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
    if(empty($_COOKIE['superpowers_value']))
     $values['superpowers'] = array();
   else
    $values['superpowers'] = json_decode($_COOKIE['superpowers_value'], true);

    session_start();
    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']) && empty($errors) && session_start()) {
        $db = new PDO('mysql:host=localhost;dbname=u41123', 'u41123', '1452343', array(PDO::ATTR_PERSISTENT => true));
        $stmt = $db->prepare("SELECT * FROM humans WHERE id = ?");
        $stmt -> execute([$_SESSION['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $values['fio'] = strip_tags($row['fio']);
        $values['email'] = strip_tags($row['email']);
        $values['date'] = $row['date'];
        $values['sex'] = $row['sex'];
        $values['limbs'] = $row['limbs'];
        $values['bio'] = strip_tags($row['bio']);

        $stmt = $db->prepare("SELECT * FROM abilities WHERE humans_id = ?");
        $stmt -> execute([$_SESSION['id']]);
        $ability = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
          array_push($ability, strip_tags($row['ability']));
        }
        $values['superpowers'] = $ability;

        printf('Вход с логином %s, id %d', $_SESSION['login'], $_SESSION['id']);
      }


   include('form.php');
}
else {
    $errors = FALSE;
    if (empty($_POST['fio'])) {
      setcookie('fio_error', '1');
      $errors = TRUE;
    }
    else {
      setcookie('fio_value', $_POST['fio']);
    }

    if (empty($_POST['email'])) {
      setcookie('email_error', '1');
      $errors = TRUE;
    }
    else {
      setcookie('email_value', $_POST['email']);
    }

    if (empty($_POST['date'])) {
      setcookie('date_error', '1');
      $errors = TRUE;
    }
    else {
      setcookie('date_value', $_POST['date']);
    }

    if (empty($_POST['sex'])) {
      setcookie('sex_error', '1');
      $errors = TRUE;
    }
    else {
      setcookie('sex_value', $_POST['sex']);
    }

    if(!empty($_POST['superpowers'])){
      $json = json_encode($_POST['superpowers']);
      setcookie('superpowers_value', $json);
    }

    if (empty($_POST['limbs'])) {
      setcookie('limbs_error', '1');
      $errors = TRUE;
    }
    else {
      setcookie('limbs_value', $_POST['limbs']);
    }

   if(!empty($_POST['bio'])) {
      setcookie ('bio_value', $_POST['bio']);
   }

    if ($errors) {
    header('Location: index.php');
    exit();
  }
   else {
          setcookie('fio_error', '', 100000);
          setcookie('email_error', '', 100000);
          setcookie('date_error', '', 100000);
          setcookie('limbs_error', '', 100000);
          setcookie('superpowers_error', '', 100000);
          setcookie('sex_error', '', 100000);
    }

    if (!empty($_COOKIE[session_name()]) &&
          session_start() && !empty($_SESSION['login']))
      {
        $db = new PDO('mysql:host=localhost;dbname=u41123', 'u41123', '1452343', array(PDO::ATTR_PERSISTENT => true));
        $stmt = $db->prepare("UPDATE humans SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ? WHERE id=?");
        $stmt -> execute([$_POST['fio'], $_POST['email'], $_POST['date'], $_POST['sex'], $_POST['limbs'], $_POST['bio'], $_SESSION['id']]);
        $stmt = $db->prepare("DELETE FROM abilities WHERE humans_id = ?");
        $stmt -> execute([$_SESSION['id']]);
        $ability = $_POST['superpowers'];
        foreach($ability as $item) {
            $stmt = $db->prepare("INSERT INTO abilities SET humans_id = ?, ability = ?");
            $stmt -> execute([$_SESSION['id'], $item]);
        }
     }
     else {
         $chars="qwertyuiopasdfghjklzxcvbnm1234567890QWERTUIOPASDFGHJKLZXCVBNM";
         $max=rand(8,16);
         $size=StrLen($chars)-1;
         $pass=null;
         while($max--)
           $pass.=$chars[rand(0,$size)];
         $login = $chars[rand(0,25)] . strval(time());
         setcookie('login', $login);
         setcookie('password', $pass);

         $db = new PDO('mysql:host=localhost;dbname=u41123', 'u41123', '1452343', array(PDO::ATTR_PERSISTENT => true));

         $stmt = $db->prepare("INSERT INTO humans SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ?");
         $stmt -> execute([$_POST['fio'], $_POST['email'], $_POST['date'], $_POST['sex'], $_POST['limbs'], $_POST['bio']]);

         $res = $db->query("SELECT max(id) FROM humans");
         $row = $res->fetch();
         $count = (int) $row[0];
         $ability = $_POST['superpowers'];

         foreach($ability as $item) {
            $stmt = $db->prepare("INSERT INTO abilities SET humans_id = ?, ability = ?");
            $stmt -> execute([$count, $item]);
         }

        $stmt = $db->prepare("INSERT INTO login_password SET humans_id = ?, login = ?, password = ?");
        $stmt -> execute([$count, $login, md5($pass)]);
    }
    setcookie('save', '1');

    header('Location: ./index.php');
}
?>