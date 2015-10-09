<?php
session_start();
require('database.php');

function h($f){
	return htmlspecialchars($f,ENT_QUOTES,'utf-8');
}

if(isset($_REQUEST['id'])){
	$sql = sprintf('SELECT * FROM comments WHERE id=%d',
		mysqli_real_escape_string($db,$_REQUEST['comment_id']));
	$record = mysqli_query($db,$sql) or die(mysqli_error($db));
	$table = mysqli_fetch_array($record);
}

//更新している
if(!empty($_POST)){
	if ($_POST['comment_password'] == ''){
		$error['comment_password'] = 'blank';
	}

	if(empty($error)){
	if(sha1($_POST['comment_password']) == $table['comment_password']){
		// $sql = sprintf('DELETE FROM posts WHERE id=%d',
		// mysqli_real_escape_string($db,$_GET['id']));
		// mysqli_query($db,$sql) or die(mysqli_error($db));

		//論理削除
		$sql = sprintf('UPDATE comments SET del_flg = 1 WHERE id=%d',
		mysqli_real_escape_string($db,$_REQUEST['comment_id']));
		mysqli_query($db,$sql) or die(mysqli_error($db));

			//これすることによって再読込ボタンを押したことによる、二重投稿を防止している。
			$url = "view2.php?id=".$_REQUEST['id'];
			header('Location:'.$url);
			exit();
	}else{
		$error['comment_password'] = 'diference';
	}
	}
}

?>

<!DOCTYPE>
<html>
<head>
	<meta charset="UTF-8">
	<title>ひとこと掲示板</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>
	<div class="container">
    <div class="row">
	<form action="" method="post" role="form" class="col-md-9 go-right">
		<h1>ひとこと掲示板＜コメント削除ページ＞</h1>
		<div class="form-group">
			<h3>★本人確認</h3>
			<!-- dlタグは定義・説明を表す際に使用。dlで全体を囲み、dtは説明される言葉・ddは説明や定義 -->
			<dl>
				<dt>コメントパスワードを入力してください<span class="red">※必須</span></dt>
				<dd>
					<input id="comment_password" type="password" name="comment_password" class="form-control" value="">
					<label for="comment_password">Your Comment Password</label>
					<?php
					if(isset($error['comment_password'])){
						if($error['comment_password'] == 'blank'){
						echo '<p class="red">'.'※パスワードを入力して下さい'."</p>";
						//空文字を入れることによってlengthじゃなくして何も入ってない時「※パスワードは４文字以上入力して下さい」がでないようになる
						$error['comment_password'] = '';

						}
					}?>

					<?php if (isset($error['comment_password'])):?>
					<?php if($error['comment_password'] == 'diference'):?>
					<p class="red">※パスワードが違います</p>
					<?php endif;?>
					<?php endif;?>
				</dd>
			</dl>
		</div>
			<div>
				<input type="submit" value="削除する">
				<a href="view2.php?id=<?php echo $_REQUEST['id'];?>">[コメント一覧へ戻る]</a>
			</div>
		</form>
	</div>
	</div>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	<script src="bootstrap/js/npm.js"></script>
</body>
</html>