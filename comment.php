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
    $news_sql = 'SELECT * FROM news';
    //nesSQLの実行
    $news_stmt = $dbh->query($news_sql);
    //newsSQLの結果を受け取る
    $news_result = $news_stmt->fetchall(PDO::FETCH_ASSOC);

    //commentSQLの準備
    $comments_sql = 'SELECT * FROM comments ORDER BY id DESC';
    //commentSQLの実行
    $comments_stmt = $dbh->query($comments_sql);
    //commentSQLの結果を受け取る
    $comments_result = $comments_stmt->fetchall(PDO::FETCH_ASSOC);

    $id = $_GET["id"];
    $commentText = "";
    $error_message = array();
    $clean = array();
    $titleLimit =50;
    // idの確認
    if(empty($id)){
        exit("idがありません");
    }
    // コメントform実行
    if( !empty($_POST["commentSend"])){
        // commentの確認
        if(empty($_POST["comment"])){
            $error_message[] = "コメントは必須です。";
        }
        // commentの文字数制限
        elseif(strlen($_POST["comment"]) > $titleLimit){
            $error_message[] = "コメントは5０文字以内です。";
        }
        // commentの整形
        else{
            $clean["comment"] = htmlspecialchars( $_POST["comment"], ENT_QUOTES);
            $clean["comment"] = preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['comment']);
        }
        // commentsテーブルに挿入
        if(empty($error_message)){
            $sql = 'INSERT INTO 
                        comments (news_id,comment) 
                    VALUES 
                        (:news_id,:comment)';
            $comments_stmt = $dbh->prepare($sql);
            $params = array(':news_id'=> $id,':comment' => $clean["comment"]);
            $comments_stmt->execute($params);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            //プログラム終了
            exit;
        }
    }
    //削除form実行
    if (isset($_POST['deleteSend'])) {
        // commentsテーブルから削除
        $delete = $_POST['delete'];
                $sql = 'DELETE FROM 
                            comments 
                        WHERE
                            id=:id';
            $comments_stmt = $dbh->prepare($sql);
            $params = array(':id' => $delete);
            $comments_stmt->execute($params);
            
        header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了
    exit;
    }
?>  
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    <?php if(!empty($error_message)): ?>
        <ul class="error_message">
            <?php foreach($error_message as $value): ?>
                <li><?php echo $value; ?></li>
                <?php endforeach;?>
        </ul>
    <?php endif;?>
    <section class="post-detail">
        <?php foreach($news_result as $column): ?>
        <?php if($id == $column["id"]): ?>
        <h3 class="post-title"><?php echo $column["title"];?></h3>
        <p class="post-body"><?php echo $column["body"];?></p>
        <?php endif; ?>
        <?php endforeach; ?>
    </section>
    <hr>
    <section class="comments">
    <div class="form-comment">
    <form method="POST" action="">
        <div class="input-body">
            <textarea name="comment" class="post-it post-it-red"></textarea>
            <input class="btn-submit" type="submit" name="commentSend" value="コメントを書く">
        </div>
    </form>
    </div>
    <?php foreach($comments_result as $column): ?>
            <?php
            if(!empty($comments_result)): ?>
            <?php if($id == $column["news_id"]): ?>
                <div class="commentContent">
                <?php echo $column["comment"]; ?>
                <form method="POST" action="">
                <input type="hidden" name="delete" value="<?php echo $column["id"];?>">
                <input class="deleteComment" type="submit" name="deleteSend" value="コメントを消す">
        <?php endif; ?>
        <?php endif; ?>
        </form>
        </div>
        <?php endforeach; ?>
    </section>
</body>
</html>