<?php
    // SQL接続
    $username = 'laravel_user';
    $pass = 'C78A]cuWUh_]65k';
    $dsn = 'mysql:host=localhost;dbname=laravel_news;';
    try{
        $dbh = new PDO($dsn,$username,$pass,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }catch(Exception $e){
        echo '接続失敗'.$e->getMessage();
    }
    //newsSQLの準備
    $news_sql = 'SELECT * FROM news ORDER BY id DESC';
    //newsSQLの実行
    $news_stmt = $dbh->query($news_sql);
    //newsSQLの結果を受け取る
    $news_result = $news_stmt->fetchall(PDO::FETCH_ASSOC);

    $title = "";
    $body = "";
    $error_message = array();
    $clean = array();
    $titleLimit =30;
    // 投稿form実行
    if( !empty($_POST["send"])){
        // titleの確認
        if(empty($_POST["title"])){
            $error_message[] = "タイトルは必須です。";
        }
        // titleの文字数制限
        elseif(strlen($_POST["title"]) > $titleLimit){
            $error_message[] = "タイトルは３０文字以内です。";
        }
        // titleの整形
        else{
            $clean["title"] = htmlspecialchars( $_POST["title"], ENT_QUOTES);
            $clean["title"] = preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['title']);
        }
        // bodyの確認
        if(empty($_POST["body"])){
            $error_message[] = "記事は必須です。";
        // bodyの整形
        }else{
            $clean["body"] = htmlspecialchars( $_POST["body"], ENT_QUOTES);
            $clean["body"] = preg_replace( '/\\r\\n|\\n|\\r/', '<br>', $clean['body']);
        }
        // newsテーブルに挿入
        if(empty($error_message)){
            $sql = 'INSERT INTO 
                        news (title,body) 
                    VALUES 
                        (:title,:body)';
            $news_stmt = $dbh->prepare($sql);
            $params = array(':title'=> $clean["title"],':body' => $clean["body"]);
            $news_stmt->execute($params);
            header('Location: ' . $_SERVER['REQUEST_URI']);
        //プログラム終了
        exit;
        }
    }
    
?>
<script>
function submitChk() {
    var flag = confirm("投稿してよろしいですか？")
    return flag;
}
</script>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Laravel News</title>
</head>
<body>
    <nav class="main-header">
        <div class="nav-bar">
            <a href="home.php" class="nav-link">Laravel News</a>
        </div>
    </nav>
    <section class="form-post">
        <h2 class="comment-header">さぁ、最新のニュースをシェアしましょう！</h2>
        <?php if(!empty($error_message)): ?>
            <ul class="error_message">
                <?php foreach($error_message as $value): ?>
                    <li><?php echo $value; ?></li>
                    <?php endforeach;?>
            </ul>
        <?php endif;?>
        <form id="formPost" method="POST" action="home.php" onsubmit="return submitChk()">
            <div class="input-title">
                <label for="title">タイトル：</label>
                <input name="title" type="text" value="">
            </div>
            <div class="input-body">
                <label for="body">記事：</label>
                <textarea name="body" cols="50" rows="10" value=""></textarea>
            </div>
            <div class="input-submit">
                <input type="hidden" name="id" value="$lastnum">
                <input class="btn-submit" type="submit" name="send" value="投稿">
            </div>
        </form>
    </section>
    <section class="posts">
        <?php
        if(!empty($news_result)): ?>
        <?php foreach($news_result as $column): ?>
        <div class="post">
            <h3 class="post-title"><?php echo $column["title"]; ?></h3>
            <p class="post-body"><?php echo $column["body"]; ?></p>
            <a href="comment.php?id=<?php echo  $column["id"]; ?>">記事全文・コメントを見る</a>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </section>
</body>
</html>