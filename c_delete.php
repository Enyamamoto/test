<?php
session_start();
require('database.php');

//htmlspecialcharsを何度も使うので、関数にしてコードをすっきりさせる。
function h($f){
	return htmlspecialchars($f,ENT_QUOTES,'utf-8');
}

//postsテーブルから、URLでGET送信されてきたidを指定してデータを取得
if(isset($_REQUEST['id'])){
	$sql = sprintf('SELECT * FROM comments WHERE id=%d',
		mysqli_real_escape_string($db,$_REQUEST['comment_id']));
	$record = mysqli_query($db,$sql) or die(mysqli_error($db));
	$table = mysqli_fetch_array($record);
}

//バリデーション
if(!empty($_POST)){
	if ($_POST['comment_password'] == ''){
		$error['comment_password'] = 'blank';
	}

	if(empty($error)){
	if(sha1($_POST['comment_password']) == $table['comment_password']){
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
		<h1>Nexseed掲示板＜コメント削除ページ＞</h1>
		<div class="form-group">
		<?php if(isset($table)):?>
		<div>
			<h3>★<?php echo h($table['comment_name']);?>さんのコメントを削除しますか？</h3>
			<!-- ?res=でget送信してる -->
			<p>
			メッセージ：<?php echo h($table['comment']) ;?>
			</p>
			<p>
			ニックネーム：<?php echo h($table['comment_name']);?>さん
			</p>
			<p>作成日時：<?php echo h($table['created']);?></p>
		</div>
		<?php else :?>
		<p>その投稿は削除されたか、URLが間違えています</p>
	<?php endif;?>
	</div>
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
				<input type="submit" class="btn btn-primary" value="削除する">
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