<?php
  // ここにDBに登録する処理を記述する
  // ①DBへ接続

$dsn = 'mysql:dbname=oneline_bbs;host=localhost';
$user = 'root';
$password = '';
$dbh = new PDO($dsn, $user, $password);
$dbh->query('SET NAMES utf8');

//-----------------------------
//歯車アイコンクリック時
  $editName='';
  $editComment='';
  $id='';
  if(!empty($_GET['action'])&&$_GET['action']=='edit'){

    $sql='SELECT*FROM `posts`  WHERE `id`=?';
    $data[]=$_GET['id'];
    //SQL実行
    $stmt=$dbh->prepare($sql);
    $stmt->execute($data);
    //データを取得
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    //値を変数に格納
    $editName=$rec['nickname'];
    $editComment=$rec['comment'];
    $id=$rec['id'];
  }

//POST送信された時のみ登録処理を実行
  if(!empty($_POST)){
    if(!empty($_POST['id'])){
      //データを更新する
     $sql='UPDATE `posts` SET `nickname`=?, `comment`=? WHERE `id`=?';
     $data[]=$_POST['nickname'];
     $data[]=$_POST['comment'];
     $data[]=$_POST['id'];

      }else{
 // ②SQL文の作成
    //データを登録する
  $sql = 'INSERT INTO `posts`(`nickname`,`comment`,`created`)VALUES(?,?,now())';
  $data[] = $_POST['nickname'];
  $data[] = $_POST['comment'];
}
  // SQLを実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
  }
//データの一覧表示 
  $sql='SELECT * FROM `posts`ORDER BY `created` DESC';

 // SQLを実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  //データ格納用変数
    $data= array();
    
    while (1) {
    // データを取得する
    $rec = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rec == false) {
      break;
    }
    //格納用変数にレコードのデータを入れる
    $data[]=$rec;
 }


  // ③DB切断
  $dbh = null;
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-linux"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <form action="bbs.php" method="post">
          <!-- nickname -->
          <div class="form-group">
             <div class="input-group">
              <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" required value="<?php echo $editName; ?>">

              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>

          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php echo $editComment; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- つぶやくボタン -->
      <?php if(!empty($_GET['action'])&&$_GET['action']=='edit'):?>
   <button type="submit" class="btn btn-primary col-xs-12" >更新する</button>
   <input type="hidden" name="id" value="<?php echo $id; ?>">
   <?php else:?>
    <button type="submit" class="btn btn-primary col-xs-12" >つぶやく</button>
  <?php endif; ?>
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">
          <?php foreach($data as $d): ?>
          <article class="timeline-entry">
              <div class="timeline-entry-inner">
                   <a href="http://localhost/oneline_bbs/bbs.php?action=edit&id=<?php echo $d['id']; ?>">
                  <div class="timeline-icon bg-success">
                  <i class="entypo-feather"></i>
                      <i class="fa fa-cogs"></i>
                  </div>
                </a>
                  <div class="timeline-label">
            
                    <?php 
                   //1.文字列から日付型へ変換する
                    $created = strtotime($d['created']); 
                    //2.フォーマットを指定
                    $created =date('Y/m/d' , $created);
                    ?>
                      <h2><a href="#"><?php echo $d['nickname']; ?></a> 
                        <span><?php echo $created; ?></span></h2>
                      <p><?php echo $d['comment']; ?></p>
                  </div>
              </div>
          </article>
        <?php endforeach; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>
      </div>

    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>



