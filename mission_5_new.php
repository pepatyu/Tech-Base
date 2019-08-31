<?php
//データベースへの接続
$dsn = 'データベース名';
$user ='ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//

//テーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS mis5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "YMD char(32),"
	. "pass char(10)"
	.");";
	$stmt = $pdo->query($sql);
//

if(@$_POST['edi_nm']){
	$sql = 'SELECT * FROM mis5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();

	$edi = htmlspecialchars($_POST['edi_nm']);

	foreach($results as $lines){
		if($lines[0] == $edi){
			$name = $lines[1];
			$come = $lines[2];
			$pass = $lines[4];
		}
	}
	if(htmlspecialchars($_POST['edi_pass']) != $pass){
		echo "パスワードが違います<br>";
		$name = "";
		$come = "";
	}
}else{
	$name = "";
	$come = "";
}//編集番号指定

?>
<html>
<head>
<meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
<meta charset="utf-8">
<title>mission_5</title>
</head>
<body>
<form method="POST" action="mission_5.php">
<div>
		【　投稿フォーム　】<br>
    		<label for="name">名前:</label>
    		<input type="text" name="user_name" value = <?php if($name != ""){echo $name;}?>>
		<input type="hidden" name="edi_name" value=<?php echo $name?>>
		
		<br>
		<label for="come">コメント:</label>
		<input type="text" name="user_come"  value = <?php if($come != ""){echo $come;}?>>
		
		<br>
		<label for="pass">パスワード:</label>
		<input type="password" name="user_pass">
		<br>
		<input type="submit" value="送信">
	</div>

	<div>
		【　削除フォーム　】<br>
		<label for="del_NM">投稿番号:</label>
		<input type="text" name="del_nm"><br>
		<label for="del_pass">パスワード:</label>
		<input type="password" name="del_pass"><br>
		<input type="submit" value="削除">
	</div>
	<div>
		【　編集フォーム　】<br>
		<label for="edi_NM">投稿番号:</label>
		<input type="text" name="edi_nm"><br>
		<label for="edi_pass">パスワード:</label>
		<input type="password" name="edi_pass"><br>
		<input type="submit" value="編集">
	</div>
</form>

<?php
$NM=1;
$ymd=date("Y/m/d g:i");

if(@$_POST['user_name']){
	$nm = htmlspecialchars($_POST['user_name']);
}else{
	$nm = "";
}

if(@$_POST['user_come']){
	$cm = htmlspecialchars($_POST['user_come']);
}else{
	$cm = "";
}

if(@$_POST['user_come']){
	$ps = htmlspecialchars($_POST['user_pass']);
}else{
	$ps = "";
}//代入

if($nm != "" or $cm != ""){
	if($nm != htmlspecialchars($_POST['edi_name'])){
		$sql = $pdo -> prepare("INSERT INTO mis5 (name, comment, YMD, pass) VALUES (:name, :comment, :YMD, :pass)");
		$sql -> bindParam(':name', $name, PDO::PARAM_STR);
		$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
		$sql -> bindParam(':YMD', $YMD, PDO::PARAM_STR);
		$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
		$name = $nm;
		$comment = $cm; 
		$YMD = $ymd;
		$pass = $ps;
		$sql -> execute();
	}//新規投稿
	else{
		$name = htmlspecialchars($_POST['edi_name']);
		$comment = $cm;
		$YMD = $ymd;
		$pass = $ps;
		$sql = 'update mis5 set comment=:comment,YMD=:YMD,pass=:pass where name=:name';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':YMD', $YMD, PDO::PARAM_STR);
		$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
		$stmt->execute();
	}//編集
}

if(@$_POST['del_nm']){
	$sql = 'SELECT * FROM mis5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();

	$del = htmlspecialchars($_POST['del_nm']);

	foreach($results as $lines){
		if($lines[0] == $del){
			$pass = $lines[4];
		}
	}
	if(htmlspecialchars($_POST['del_pass']) != $pass){
	echo "パスワードが違います<br>";
	}else{
	$id = htmlspecialchars($_POST['del_nm']);
	$sql = 'delete from mis5 where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	}
}//削除

echo "-----------------------<br>";
echo "【　投稿一覧　】<br>";
	$sql = 'SELECT * FROM mis5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['YMD'].'<br>';
	echo "<hr>";
	}//表示
?>
</body>
</html>
