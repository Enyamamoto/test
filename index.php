<?php
session_start();
require('database.php');

function h($f){
	return htmlspecialchars($f,ENT_QUOTES,'UTF-8');
}


if (!empty($_POST)){


	if ($_POST['name'] == ''){
		$error['name'] = 'blank';
	}
	if ($_POST['name'] !== '' && strlen($_POST['name']) >30){
		$error['name'] = 'length';
	}

	if ($_POST['password'] == ''){
		$error['password'] = 'blank';
	}
	if ($_POST['password'] !== ''){
		if (strlen($_POST['password']) < 4){
			$error['password'] = 'length';
		}elseif(strlen($_POST['password']) >8){
			$error['password'] = 'length';
		}
	}

	if ($_POST['message'] == ''){
		$error['message'] = 'blank';
	}
	if ($_POST['message'] !== '' && strlen($_POST['message']) >1200){
		$error['message'] = 'length';
	}

	if(empty($error)){

		//重複アカウントのチェック。
		$sql=sprintf('SELECT COUNT(*) as cnt FROM posts where password="%s"',
			mysqli_real_escape_string($db,sha1($_POST['password'])));
		//mysqli_error：直近のエラーの内容を返す
		$record = mysqli_query($db,$sql) or die(mysqli_error($db));
		//mysqli/queryだけだと実行しただけなので、実行した結果をmysqli_fetch_arrayで抽出する。
		$table = mysqli_fetch_array($record);
		if($table['cnt'] > 0){
			//ちなみにduplicateは二重のという意味
			$error['password'] = 'duplicate';
		}
	}


	if(empty($error)){

	$_SESSION['name'] = $_POST['name'];

	$sql = sprintf("INSERT INTO `posts` (`name`,`message`,`password`,`created`,`modified`)VALUES ('%s','%s','%s',NOW(),NOW())",
		mysqli_real_escape_string($db,$_POST['name']),
		mysqli_real_escape_string($db,$_POST['message']),
		mysqli_real_escape_string($db,sha1($_POST['password']))
		);

	mysqli_query($db,$sql) or die(mysqli_error($db));

	header('Location:index.php');
	exit();
	}

}

//論理削除(del_flg = 1を非表示)
$sqls = sprintf('SELECT p.* FROM posts p WHERE del_flg = 0 ORDER BY p.created DESC');
$posts = mysqli_query($db,$sqls) or die(mysqli_error($db));


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
			<h1>ひとこと掲示板</h1>
		<div class="form-group">
			<dl>
			<dt>ニックネーム(全角10文字以内)<span class="red">※必須</span></dt>
				<dd>
					<input id="name" type="text" name="name" class="form-control" value="<?php if(isset($_POST['name'])){echo h($_SESSION['name']);}?>">
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
		<div class="form-group">
			<dt>パスワード(4〜8桁の半角英数・大文小文字可)<span class="red">※必須</span></dt>
				<dd>
					<input id="password" type="password" name="password" class="form-control" value="">
					<label for="password">Your Password</label>
					<?php
					if(isset($error['password'])){
						if($error['password'] == 'blank'){
						echo '<p class="red">'.'※パスワードを入力して下さい'."</p>";
						//空文字を入れることによってlengthじゃなくて何も入ってない時「※パスワードは4〜8桁の半角英数大文字小文字で入力して下さい」がでないようになる
						$error['password'] = '';

						}
					}?>
					<?php if (isset($error['password'])):?>
					<?php if($error['password'] == 'length'):?>
					<p class="red">※パスワードは4〜8桁の半角英数大文字小文字で入力して下さい</p>
					<?php endif;?>
					<?php endif;?>

					<?php if (isset($error['password'])):?>
					<?php if ($error['password'] == 'duplicate'):?>
					<p class="red">※指定されたパスワードはすでに指定されています</p>
					<?php endif ;?>
					<?php endif ;?>

				</dd>
		</div>
		<div class="form-group">
			<dt>メッセージをどうぞ(全角400文字以内)<span class="red">※必須</span></dt>
				<dd>
					<textarea id="message" name="message" class="form-control" cols="100" rows="10"><?php if(isset($_REQUEST['res'])){ echo h($message);}?></textarea>
					<label for="message">Your Message</label>
					<?php
					if(isset($error['message'])){
						if($error['message'] == 'blank'){
						echo '<p class="red">'.'※メッセージを入力して下さい'."</p>";
						//空文字を入れることによってlengthじゃなくして何も入ってない時「※パスワードは４文字以上入力して下さい」がでないようになる
						$error['message'] = '';

						}
					}?>
					<?php if (isset($error['message'])):?>
					<?php if($error['message'] == 'length'):?>
					<p class="red">※メッセージは400文字以内で入力して下さい</p>
					<?php endif;?>
					<?php endif;?>
				</dd>
			</dl>
		</div>
			<div>
				
				<input type="submit" value="投稿する">
				
			</div>
		<!-- </form>
	</div>
	</div> -->
	<h2>記事一覧表示</h2>
	<?php while($post = mysqli_fetch_array($posts)):?>
	<div>
		<p>
		メッセージ：<?php echo h($post['message']) ;?>
		</p>
		<p>
		ニックネーム：<?php echo h($post['name']);?>さん
		</p>
		<p><a href="view2.php?id=<?php echo h($post['id']);?>">[記事詳細]</a><br><a href="update.php?id=<?php echo h($post['id']);?>">[編集]</a><br><a href="delete.php?id=<?php echo h($post['id']);?>">[削除]</a><br>
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