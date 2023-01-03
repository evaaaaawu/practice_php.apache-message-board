<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  $username = NULL;
  $user = NULL;
  if(!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user = getUserFromUsername($username);
  }
  
  $sql = "SELECT * FROM comments ORDER BY id DESC";
  $stmt = $conn->prepare($sql);
  $result = $stmt->execute();
  if(!$result) {
    die("Error:" . $conn->error);
  }

  $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>留言板</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="warning">
    <strong>注意！本站為練習用網站，因教學用途刻意忽略資安的實作，註冊時請勿使用任何真實的帳號或密碼。</strong>
  </header>
  <main class="board">
      <div>
        <?php if(!$username) {?>
          <a class="board__btn" href="./register.php">註冊</a>
          <a class="board__btn" href="./login.php">登入</a>
        <?php } else { ?>
          <a class="board__btn" href="./logout.php">登出</a>
          <span class = "board__btn update-nickname">編輯暱稱</span>
          <form class = "hide board__nickname-form board__new-comment-form" method ="POST" action="update_user.php">
            <div class = "board__nickname">
              <span>新的暱稱:</span>
              <input type="text" name="nickname" />
            </div>
            <input class="board__submit-btn" type="submit" />
          </form>
          <h3>你好！<?php echo $user['nickname']; ?></h3>
        <?php } ?>
      </div>
    <h1 class="board__title">Comments</h1>
    <?php
      if (!empty($_GET['errCode'])) {
        $code = $_GET['errCode'];
        $msg = 'Error';
        if($code === '1') {
          $msg ='資料不齊全';
        }
        echo '<h2 class= "error">錯誤:' . $msg . '</h2>';
      }
    ?>
    <form class="board__new-comment-form" method="POST" action="./handle_add_comment.php">
      <textarea name="content" id="" cols="30" rows="5"></textarea>
      <?php if($username) { ?>
        <input class="board__submit-btn" type="submit" />
      <?php } else {?>
        <h3 class="error">請登入發布留言</h3>
      <?php } ?>
    </form>
    <div class="board__hr"></div>
    <section>
      <?php while($row = $result->fetch_assoc()) { ?>
        <div class="card">
          <div class="card__avatar"></div>
          <div class="card__body">
            <div class="card__info">
              <span class="card__author"><?php echo escape($row['nickname']); ?></span>
              <span class="card__time"><?php echo escape($row['created_at']); ?></span>
            </div>
            <p class="card__content">
              <?php echo escape($row['content']); ?>
            </p>
          </div>
        </div>
      <?php } ?>
      
    </section>
  </main>
  <script>
    let btn = document.querySelector('.update-nickname')
    btn.addEventListener('click', function() {
      let form = document.querySelector('.board__nickname-form')
      form.classList.toggle('hide')
    })
  </script>
</body>
</html>