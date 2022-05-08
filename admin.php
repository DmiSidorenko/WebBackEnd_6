<?php

$db = new PDO('mysql:host=localhost;dbname=u41123', 'u41123', '1452343', array(PDO::ATTR_PERSISTENT => true));
$stmt = $db->prepare("SELECT * FROM admin WHERE id=?");
$stmt -> execute([1]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != $user['login'] ||
    md5($_SERVER['PHP_AUTH_PW']) != $user['password']) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Требуется авторизация</h1>');
  exit();
}

print('Вы успешно авторизовались и видите защищенные паролем данные.');

// *********
// Здесь нужно прочитать отправленные ранее пользователями данные и вывести в таблицу.
// Реализовать просмотр и удаление всех данных.
// *********
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["1"]);
$countof1 = $stmt->rowCount();
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["2"]);
$countof2 = $stmt->rowCount();
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["3"]);
$countof3 = $stmt->rowCount();
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["4"]);
$countof4 = $stmt->rowCount();
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["5"]);
$countof5 = $stmt->rowCount();
$stmt = $db->prepare("SELECT * FROM abilities WHERE ability = ?");
$stmt -> execute(["6"]);
$countof6 = $stmt->rowCount();

$stmt = $db->query("SELECT max(id) FROM humans");
$row = $stmt->fetch();
$countofusers = (int) $row[0];


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])){

  if($_POST['select_user'] == 0){
      header('Location: admin.php');
  }

  $id_of_user = (int) $_POST['select_user'];
  $stmt = $db->prepare("DELETE FROM abilities WHERE humans_id = ?");
  $stmt -> execute([$id_of_user]);
  $stmt = $db->prepare("DELETE FROM login_password WHERE humans_id = ?");
  $stmt -> execute([$id_of_user]);
  $stmt = $db->prepare("DELETE FROM humans WHERE id = ?");
  $stmt -> execute([$id_of_user]);
  header('Location: admin.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])){

  $user_id = (int) $_COOKIE['user_id'];

  $stmt = $db->prepare("UPDATE humans SET name = ?, email = ?, date = ?, gender = ?, limbs = ?, bio = ? WHERE id = ?");
  $stmt -> execute([$_POST['name'], $_POST['email'], $_POST['date'], $_POST['gender'], $_POST['limbs'], $_POST['bio'], $user_id]);

  $stmt = $db->prepare("DELETE FROM abilities WHERE humans_id = ?");
  $stmt -> execute([$user_id]);

  $ability = $_POST['superpowers'];

  foreach($ability as $item) {
    $stmt = $db->prepare("INSERT INTO abilities SET humans_id = ?, ability = ?");
    $stmt -> execute([$user_id, $item]);
  }
  header('Location: admin.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Режим администратора</title>
</head>
<body>
<div class="container">
  <h3>Статистика суперспособностей:</h3>
  <p>Бессмертие: <?php print $countof1 ?></p> <br>
  <p>Прохождение сквозь стены: <?php print $countof2 ?></p> <br>
  <p>Левитация: <?php print $countof3 ?></p> <br>
  <p>Невидимость: <?php print $countof4 ?></p> <br>
  <p>Другие: <?php print $countof5 ?></p> <br>
  <p>Отсутствуют: <?php print $countof6 ?></p> <br>
  <h3>Выбор пользователя для редактирования:</h3>

  <form action="" method="POST">
    <select name="select_user">
      <option selected disabled value ="0">Выбрать пользователя</option>
      <?php
      for($index =1 ;$index <= $countofusers;$index++){
        $stmt = $db->prepare("SELECT * FROM humans WHERE id = ?");
        $stmt -> execute([$index]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user['id'] == $index){
            print("Пользователь с ID:". $user['id'] . "и именем: " . $user['name']);
        }
      }
      ?>
    </select><br>
    <input name="delete" type="submit" value="Удаление" />
    <input name="editing" type="submit" value="Редактирование" />
  </form>

  <?php
if(isset($_POST['editing']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
    if($_POST['select_user'] == 0){
      header('Location: admin.php');
    }
    $user_id = (int) $_POST['select_user'];
    setcookie('user_id', $user_id);
    $values = array();
    $stmt = $db->prepare("SELECT * FROM human WHERE id = ?");
    $stmt -> execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $values['name'] = strip_tags($row['name']);
    $values['email'] = strip_tags($row['email']);
    $values['date'] = $row['date'];
    $values['gender'] = $row['gender'];
    $values['limbs'] = $row['limbs'];
    $values['bio'] = strip_tags($row['bio']);
    $values['checkbox'] = true;

    $stmt = $db->prepare("SELECT * FROM abilities WHERE humans_id = ?");
    $stmt -> execute([$user_id]);
    $ability = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
      array_push($ability, strip_tags($row['ability']));
    }
    $values['ability'] = $ability;
}
?>

  <br>
  <h3>Режим редактирования:</h3>
  <form action="" method="POST">
    Имя:<br><input type="text" name="name" class="ok" value="<?php print $values['name']; ?>">
    <br>
    E-mail:<br><input type="text" name="email"class="ok" value="<?php print $values['email']; ?>">
    <br>
    Год рождения:<br>
         <input type="date" name="date" min="1900-01-01" max="2004-01-01" value="<?php print $values['date']; ?>">
    <br>
    <div>
      Пол:<br>
      <input class="radio" type="radio" name="sex" value="male" <?php if ($values['gender'] == 'male') {print 'checked';} ?>> Мужской
      <input class="radio" type="radio" name="sex" value="female" <?php if ($values['gender'] == 'female') {print 'checked';} ?>> Женский
    </div>
    <div>
    Количество конечностей (указывайте честно, не стесняйтесь &#128513):<br>
        <input class="radio" type="radio" name="limbs" value="4+" <?php if ($values['limbs'] == '4+') {print 'checked';} ?>> 4+
        <input class="radio" type="radio" name="limbs" value="4" <?php if ($values['limbs'] == '4') {print 'checked';} ?>> 4
        <input class="radio" type="radio" name="limbs" value="3" <?php if ($values['limbs'] == '3') {print 'checked';} ?>> 3
        <input class="radio" type="radio" name="limbs" value="2" <?php if ($values['limbs'] == '2') {print 'checked';} ?>> 2
        <input class="radio" type="radio" name="limbs" value="1" <?php if ($values['limbs'] == '1') {print 'checked';} ?>> 1
    </div>
    Cверхспособности:<br>
      <select class="ok" name="superpowers[]" size="6" multiple>
         <option value="1" <?php if (in_array("1", $values['ability'])) {print 'selected';} ?>>Бессмертие</option>
         <option value="2" <?php if (in_array("2", $values['ability'])) {print 'selected';} ?>>Прохождение сквозь стены</option>
         <option value="3" <?php if (in_array("3", $values['ability'])) {print 'selected';} ?>>Левитация</option>
         <option value="4" <?php if (in_array("4", $values['ability'])) {print 'selected';} ?>>Невидимость</option>
         <option value="5" <?php if (in_array("5", $values['ability'])) {print 'selected';} ?>>Другие</option>
         <option value="6" <?php if (in_array("6", $values['ability'])) {print 'selected';} ?>>Отсутствуют</option>
      </select>
    <br>
    Биография:<br><textarea class="ok" name="bio" rows="3" cols="30"><?php print $values['bio']; ?></textarea>
    <div>
     <input type="checkbox" name="checkbox" required> С контрактом ознакомлен(a)
    </div>
    <input name="edit" type="submit" id="send" value="СОХРАНИТЬ">
  </form>

</div>
</body>
</html>