<?php
  // ここにDBに登録する処理を記述する
  // ①DBへ接続
  $dsn = 'mysql:dbname=oneline_bbs;host=localhost';
  $user = 'root';
  $password = '';
  $dbh = new PDO($dsn, $user, $password);
  $dbh->query('SET NAMES utf8');

//POST送信された時のみ登録処理を実行
  if(!empty($_POST)){
 // ②SQL文の作成
  $sql = 'INSERT INTO `posts`(`nickname`,`comment`,`created`)VALUES(?,?,now())';
  $data[] = $_POST['nickname'];
  $data[] = $_POST['comment'];
  // SQLを実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  }
//データの一覧表示 
$sql='SELECT * FROM `posts`ORDER BY `created` DESC';

 // SQLを実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  
    while (1) {
    // データを取得する
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rec == false) {
      break;
    }
    echo $rec['nickname'];
    echo $rec['comment'];
    echo $rec['created'];
    echo '<br>';
  }

  // ③DB切断
  $dbh = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
</head>
<body>
    <form method="post" action="">
      <p><input type="text" name="nickname" placeholder="nickname"></p>
      <p><textarea type="text" name="comment" placeholder="comment"></textarea></p>
      <p><button type="submit" >つぶやく</button></p>
    </form>
    <!-- ここにニックネーム、つぶやいた内容、日付を表示する -->

</body>
</html>