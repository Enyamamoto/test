<?php
session_start();
require('database.php');

function h($f){
	return htmlspecialchars($f,ENT_QUOTES,'UTF-8');
}
//URLパラメーターのidが正しく指定されてるかチェック
// if(empty($_REQUEST['id'])){
// 	header('Location:view2.php');
// 	exit();
// }

$sqls = sprintf('SELECT * FROM posts WHERE id=%d',
	mysqli_real_escape_string($db,$_REQUEST['id']));
$posts = mysqli_query($db,$sqls) or die(mysqli_error($db));

if (!empty($_POST)){

	if ($_POST['comment_name'] == ''){
		$error['comment_name'] = 'blank';
	}
	if ($_POST['comment_name'] !== '' && strlen($_POST['comment_name']) >30){
		$error['comment_name'] = 'length';
	}

	if ($_POST['comment_password'] == ''){
		$error['comment_password'] = 'blank';
	}
	if ($_POST['comment_password'] !== ''){
		if (strlen($_POST['comment_password']) < 4){
			$error['comment_password'] = 'length';
		}elseif(strlen($_POST['comment_password']) >8){
			$error['comment_password'] = 'length';
		}
	}

	if ($_POST['comment'] == ''){
		$error['comment'] = 'blank';
	}
	if ($_POST['comment'] !== '' && strlen($_POST['comment']) >300){
		$error['comment'] = 'length';
	}

	if(empty($error)){

		//重複アカウントのチェック。
		$sql=sprintf('SELECT COUNT(*) as cnt FROM comments where comment_password="%s"',
			mysqli_real_escape_string($db,sha1($_POST['comment_password'])));
		//mysqli_error：直近のエラーの内容を返す
		$record = mysqli_query($db,$sql) or die(mysqli_error($db));
		//mysqli/queryだけだと実行しただけなので、実行した結果をmysqli_fetch_arrayで抽出する。
		$table = mysqli_fetch_array($record);
		if($table['cnt'] > 0){
			//ちなみにduplicateは二重のという意味
			$error['comment_password'] = 'duplicate';
		}
	}

	if(empty($error)){

	$_SESSION['comment_name'] = $_POST['comment_name'];

	$sql = sprintf("INSERT INTO `comments` (`post_id`,`comment_name`,`comment_password`,`comment`,`created`,`modified`)VALUES ('%d','%s','%s','%s',NOW(),NOW())",
		mysqli_real_escape_string($db,$_REQUEST['id']),
		mysqli_real_escape_string($db,$_POST['comment_name']),
		mysqli_real_escape_string($db,sha1($_POST['comment_password'])),
		mysqli_real_escape_string($db,$_POST['comment'])
		);

	mysqli_query($db,$sql) or die(mysqli_error($db));

	$url = "view2.php?id=".$_REQUEST['id'];
	header('Location:'.$url);
	exit();
	}
}

//論理削除(del_flg = 1を非表示)
$sqls = sprintf('SELECT * FROM comments WHERE del_flg = 0 AND post_id = %d ORDER BY created DESC',
	mysqli_real_escape_string($db,$_REQUEST['id'])
	);
$comments = mysqli_query($db,$sqls) or die(mysqli_error($db));
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
		<h1>ひとこと掲示板＜詳細ページ＞</h1>
	<div class="form-group">
		<?php if($post = mysqli_fetch_array($posts)):?>
		<div>
			<p>
				<h3>★<?php echo h($post['name']);?>さんの記事</h3>
				<!-- ?res=でget送信してる -->
				<?php echo h($post['message']);?><span>(<?php echo h($post['name']);?>)</span>
			</p>
			<p><?php echo h($post['created']);?></p>
		</div>
		<?php else :?>
		<p>その投稿は削除されたか、URLが間違えています</p>
	<?php endif;?>
	</div>
		<div class="form-group">
			<dl>
			<dt>コメントニックネーム(全角10文字以内)<span class="red">※必須</span></dt>
				<dd>
					<input id="name" type="text" name="comment_name" class="form-control" value="<?php if(isset($_POST['comment_name'])){echo h($_SESSION['comment_name']);}?>">
					<label for="name">Your Comment Nickname</label>
					<?php if (isset($error['comment_name'])):?>
					<p class="red">※ニックネームを入力して下さい</p>
					<?php endif ;?>

					<?php if (isset($error['comment_name'])):?>
					<?php if($error['comment_name'] == 'length'):?>
					<p class="red">※ニックネームは10文字以内にして下さい</p>
					<?php endif;?>
					<?php endif;?>
				</dd>
			</div>
			<div class="form-group">
				<dt>コメントパスワード(4〜8桁の半角英数・大文小文字可)<span class="red">※必須</span></dt>
				<dd>
					<input id="comment_password" type="password" name="comment_password" class="form-control" value="<?php if(isset($_POST['comment_password'])){echo h($_POST['comment_password']);} ?>">
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
					<?php if($error['comment_password'] == 'length'):?>
					<p class="red">※パスワードは半角英数+大文字小文字の4〜8桁で入力して下さい</p>
					<?php endif;?>
					<?php endif;?>

					<?php if (isset($error['comment_password'])):?>
					<?php if ($error['comment_password'] == 'duplicate'):?>
					<p class="red">※指定されたパスワードはすでに指定されています</p>
					<?php endif ;?>
					<?php endif ;?>

				</dd>
			</div>
			<div class="form-group">
				<dt>コメントをどうぞ(全角100文字以内)<span class="red">※必須</span></dt>
					<dd>
						<textarea id="comment" name="comment" class="form-control"><?php if(isset($_REQUEST['res'])){ echo h($message);}?></textarea>
						<label for="comment">Your Comment</label>
						<?php
						if(isset($error['comment'])){
							if($error['comment'] == 'blank'){
							echo '<p class="red">'.'※コメントを入力して下さい'."</p>";
							//空文字を入れることによってlengthじゃなくして何も入ってない時「※パスワードは４文字以上入力して下さい」がでないようになる
							$error['comment'] = '';

							}
						}?>
						<?php if (isset($error['comment'])):?>
						<?php if($error['comment'] == 'length'):?>
						<p class="red">※コメントは全角100文字以内で入力して下さい</p>
						<?php endif;?>
						<?php endif;?>
					</dd>
				</dl>
			</div>
			<div>
				<input type="submit" value="投稿する">
				<a href="index.php">[トップへ戻る]</a>
			</div>
	<h2>コメント一覧表示</h2>
	<?php while($comment = mysqli_fetch_array($comments)):?>
	<div>
		<p>
		コメント：<?php echo h($comment['comment']) ;?>
		</p>
		<p>
		コメントニックネーム：<?php echo h($comment['comment_name']);?>
		</p>
		<p><a href="c_update.php?id=<?php echo $_REQUEST['id'];?>&comment_id=<?php echo h($comment['id']);?>">[編集]</a><br><a href="c_delete.php?id=<?php echo $_REQUEST['id'];?>&comment_id=<?php echo h($comment['id']);?>">[削除]</a>
		</p>
	</div>
	<?php endwhile ;?>
	</form>
	</div>
	</div>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap.js"></script>
	<script src="bootstrap/js/npm.js"></script>
</body>
</html>