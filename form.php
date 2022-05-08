<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контактная форма</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <?php
  if (!empty($messages)) {
    print('<div class="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
      print($message);
    }
    print('</div>');
  }
  // Далее выводим форму отмечая элементы с ошибками классом error
  // и задавая начальные значения элементов ранее сохраненными.
  ?>

<div class="container">
    <h2>
        Контактная форма
    </h2>
    <form action="index.php" method="POST">
     Имя: <br><input type="text" name="fio" <?php if ($errors['fio']) {print 'class="error"';} else print 'class="ok"'; ?> value="<?php print $values['fio']; ?>">
      <br>
     E-mail: <br><input type="email" name="email" <?php if ($errors['email']) {print 'class="error';} else print 'class="ok"'; ?> value="<?php print $values['email']; ?>">
      <br>
      Год рождения: <br> <input type="date" name="date" min="1900-01-01" max="2004-01-01" <?php if ($errors['date']) {print 'class="error"';} else print 'class="ok"'; ?> value="<?php print $values['date']; ?>">
      <br>
      <div <?php if ($errors['sex']) {print 'class="error"';} ?>>
        Пол:<br>
        <input class="radio" type="radio" name="sex" value="male" <?php if ($values['sex'] == 'male') {print 'checked';} ?>> Мужской
        <input class="radio" type="radio" name="sex" value="female" <?php if ($values['sex'] == 'female') {print 'checked';} ?>> Женский
      </div>
      <div <?php if ($errors['limbs']) {print 'class="error"';} ?>>
        Количество конечностей (указывайте честно, не стесняйтесь &#128513):<br>
        <input class="radio" type="radio" name="limbs" value="4+" <?php if ($values['limbs'] == '4+') {print 'checked';} ?>> 4+
        <input class="radio" type="radio" name="limbs" value="4" <?php if ($values['limbs'] == '4') {print 'checked';} ?>> 4
        <input class="radio" type="radio" name="limbs" value="3" <?php if ($values['limbs'] == '3') {print 'checked';} ?>> 3
        <input class="radio" type="radio" name="limbs" value="2" <?php if ($values['limbs'] == '2') {print 'checked';} ?>> 2
        <input class="radio" type="radio" name="limbs" value="1" <?php if ($values['limbs'] == '1') {print 'checked';} ?>> 1
      </div>
      Cверхспособности:<br>
      <select class="ok" name="superpowers[]" size="6" multiple>
          <option value="1" <?php if (in_array("1", $values['superpowers'])) {print 'selected';} ?>>Бессмертие</option>
          <option value="2" <?php if (in_array("2", $values['superpowers'])) {print 'selected';} ?>>Прохождение сквозь стены</option>
          <option value="3" <?php if (in_array("3", $values['superpowers'])) {print 'selected';} ?>>Левитация</option>
          <option value="4" <?php if (in_array("4", $values['superpowers'])) {print 'selected';} ?>>Невидимость</option>
          <option value="5" <?php if (in_array("5", $values['superpowers'])) {print 'selected';} ?>>Другие</option>
          <option value="6" <?php if (in_array("6", $values['superpowers'])) {print 'selected';} ?>>Отсутствуют</option>
      </select>
      <br>
      Биография: <br><textarea class="group" name="bio" rows="3" cols="30"><?php print $values['bio']; ?></textarea>
           <div>
             <input type="checkbox" name="checkbox" required> С контрактом ознакомлен(a)
           </div>
           <input type="submit" id="send" value="ОТПРАВИТЬ">
         </form>
</div>
<div class="container">
    <?php
        print('<a href="./authorization.php" class = "enter-exit"  title = "Log out">Выйти (может понадобиться 2 нажатия)</a>');
    ?>
</div>
<div class="container">
    <?php
        print('<a href="./admin.php" class = "enter-exit">Режим Администратора</a>');
    ?>
  </div>
</body>
</html>