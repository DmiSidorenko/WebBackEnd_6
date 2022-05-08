<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Вход в систему</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// Начинаем сессию.
session_start();

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
if (!empty($_SESSION['login'])) {
  // Если есть логин в сессии, то пользователь уже авторизован.
  // TODO: Сделать выход (окончание сессии вызовом session_destroy()
  //при нажатии на кнопку Выход).
  session_destroy();
  // Делаем перенаправление на форму.
  header('Location: ./index.php');
}

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

  if (!empty($_GET['wronglogin']))
    print("<div>Пользователя с таким логином не существует</div>");
  if (!empty($_GET['wrongpassword']))
    print("<div>Неверный пароль!</div>");

?>

  <form action="" method="post">
    <input name="login" placeholder="Введите логин"/>
    <input name="password" placeholder="Введите пароль"/>
    <input type="submit" id="log_in" value="Войти" />
  </form>

  <?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

  // TODO: Проверть есть ли такой логин и пароль в базе данных.
  // Выдать сообщение об ошибках.
  $db = new PDO('mysql:host=localhost;dbname=u41123', 'u41123', '1452343', array(PDO::ATTR_PERSISTENT => true));
  $stmt = $db->prepare("SELECT humans_id, password FROM login_password WHERE login = ?");
  $stmt -> execute([$_POST['login']]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    header('Location: ?wronglogin=1');
    exit();
  }
  if($row['password'] != md5($_POST['password'])) {
    header('Location: ?wrongpassword=1');
    exit();
  }
  $_SESSION['login'] = $_POST['login'];
  $_SESSION['id'] = $row["humans_id"];

  header('Location: ./index.php');
}

?>
</body>
</html>
