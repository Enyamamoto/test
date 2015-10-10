<?php
session_start();
require('database.php');

//htmlspecialcharsを何度も使うので、関数にしてコードをすっきりさせる。
function h($f){
	return htmlspecialchars($f,ENT_QUOTES,'utf-8');
}

//postsテーブルから、URLでGET送信されてきたidを指定してデータを取得
if(isset($_REQUEST['id'])){
	$sql = sprintf('SELECT * FROM posts WHERE id=%d',
		mysqli_real_escape_string($db,$_REQUEST['id']));
	$record = mysqli_query($db,$sql) or die(mysqli_error($db));
	$table = mysqli_fetch_array($record);
}

//バリデーション
if(!empty($_POST)){
	if ($_POST['name'] == ''){
		$error['name'] = 'blank';
	}
	if ($_POST['name'] !== '' && strlen($_POST['name']) >30){
		$error['name'] = 'length';
	}

	if ($_POST['password'] == ''){
		$error['password'] = 'blank';
	}

	if ($_POST['message'] == ''){
		$error['message'] = 'blank';
	}
	if ($_POST['message'] !== '' && strlen($_POST['message']) >300){
		$error['message'] = 'length';
	}

	//メッセージを編集（アップデート）
	if(empty($error)){
	if(sha1($_POST['password']) == $table['password']){
		$sql = sprintf('UPDATE posts SET message="%s",modified=NOW() WHERE id=%d',
			mysqli_real_escape_string($db,$_POST['message']),
			mysqli_real_escape_string($db,$_GET['id']));

		mysqli_query($db,$sql) or die(mysqli_error($db));

		
		header('Location:index.php');
		exit();
	}else{
		$error['password'] = 'diference';
	}
	}
}

?>

<!DOCTYPE>
<html>
<head>
	<meta charset="UTF-8">
	<title>Nexseed掲示板</title>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-theme.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
</head>
<body>
	<div class="container">
    <div class="row">
    <form action="" method="post" role="form" class="col-md-9 go-right">
		<h1>Nexseed掲示板＜編集ページ＞</h1>
		<div class="form-group">
			<h3>★本人確認</h3>
			<!-- dlタグは定義・説明を表す際に使用。dlで全体を囲み、dtは説明される言葉・ddは説明や定義 -->
			<dl>
			<div class="form-group">
				<dt>ニックネームを入力してください<span class="red">※必須</span></dt>
				<dd>
					<input id="name" type="text" name="name" class="form-control" value="<?php if(isset($_REQUEST['id'])){echo h($table['name']);}?>">
					<label for="name">Your Nickname</label>
					<?php if (isset($error['name'])):?>
					<p class="red">※ニックネームを入力して下さい</p>
					<?php endif ;?>

					<?php if (isset($error['name'])):?>
					<?php if($error['name'] == 'length'):?>
					<p class="red">※ニックネームは全角10文字以内にして下さい</p>
					<?php endif;?>
					<?php endif;?>
				</dd>
			</div>
				<dt>パスワードを入力してください<span class="red">※必須</span></dt>
				<dd>
					<input id="password" type="password" name="password" class="form-control" value="">
					<label for="pasword">Your Password</label>
					<?php
					if(isset($error['password'])){
						if($error['password'] == 'blank'){
						echo '<p class="red">'.'※パスワードを入力して下さい'."</p>";
						//空文字を入れることによってlengthじゃなくして何も入ってない時「※パスワードは４文字以上入力して下さい」がでないようになる
						$error['password'] = '';

						}
					}?>
					<?php if (isset($error['password'])):?>
					<?php if($error['password'] == 'diference'):?>
					<p class="red">※パスワードが違います</p>
					<?php endif;?>
					<?php endif;?>
				</dd>
			</div>
			<div class="form-group">
				<dt>メッセージを編集してください</dt>
				<dd>
					<!-- <TEXTAREA>は複数行の入力フィールドを作成するタグです。<TEXTAREA>～</TEXTAREA>内に記述されたテキストは、入力フィールドの初期値として表示されます。 -->
					<!-- colsとrowsは必須。colsは横の長さ、rowsは行数を指定 -->
					<textarea id="message" name="message" class="form-control" cols="100" rows="10"><?php if(isset($_REQUEST['id'])){echo h($table['message']);}?></textarea>
					<label for="message">Your Message</label>
					<?php
					if(isset($error['message'])){
						if($error['message'] == 'blank'){
						echo '<p class="red">'.'※メッセージを入力して下さい'."</p>";
						$error['message'] = '';

						}
					}?>
					<?php if (isset($error['message'])):?>
					<?php if($error['message'] == 'length'):?>
					<p class="red">※本文は400文字以内で入力して下さい</p>
					<?php endif;?>
					<?php endif;?>				
				</dd>
			</dl>
			<div>
			<div>
				<input type="submit" class="btn btn-primary" value="保存する">
				<a href="index.php">[トップへ戻る]</a>
			</div>
		</form>
	</div>
	</div>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	<script src="bootstrap/js/npm.js"></script>
</body>
</html>