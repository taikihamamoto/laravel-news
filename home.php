<?php 
    $user = 'root';
    $password = 'root';
    $db = 'laravel_news';
    $host = 'localhost';
    $port = 3306;
    $link = mysqli_init();
    $success = mysqli_real_connect(
        $link,
        $host,
        $user,
        $password,
        $db,
        $port
    );
    define("TOUKOU","toukou.txt");
    $title = "";
    $body = "";
    $message =array();
    $message_array =array();
    $error_message = array();
    $clean = array();
    $fp = fopen(TOUKOU, "a+");
    $titleLimit =30;
    /*投稿番号の定義*/
    if ( file_exists( TOUKOU ) ) { /*ファイルの存在確認。*/
    //最後の行にプラス1
        $lines=file(TOUKOU);
        $lastline= $lines[count($lines) - 1];
        $num=explode(",",$lastline);
        $lastnum=$num[0]+1;
    } else { /*ファイルが無かった場合変数の定義を１とする*/
    $num = 1;
    }
    if( !empty($_POST["send"])){
        if(empty($_POST["title"])){
            $error_message[] = "タイトルは必須です。";
        }
        elseif(strlen($_POST["title"]) > $titleLimit){
            $error_message[] = "タイトルは３０文字以内です。";
        }
        else{
            $clean["title"] = htmlspecialchars( $_POST["title"], ENT_QUOTES);
            $clean["title"] = preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['title']);
        }
        if(empty($_POST["body"])){
            $error_message[] = "記事は必須です。";
        }else{
            $clean["body"] = htmlspecialchars( $_POST["body"], ENT_QUOTES);
            $clean["body"] = preg_replace( '/\\r\\n|\\n|\\r/', '<br>', $clean['body']);
        }
        if(empty($error_message)){
            if($fp = fopen(TOUKOU, "a")){
                $title = $_POST["title"];
                $body = $_POST["body"];
                $data = "".$lastnum.",".$clean['title'].",".$clean['body']."\n";
                fwrite($fp,$data);
                fclose($fp);
                
            }
        }
    }
    if($fp = fopen(TOUKOU, "r")){
        while ($data = fgets($fp)){
            $splitData = explode(",",$data);
            $message = array(
                "num" => $splitData[0],
                "title" => $splitData[1],
                "body" => $splitData[2]
            );
            array_unshift($message_array, $message);
        }
        fclose($fp);
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
    <link rel="stylesheet" type="text/css" href="home.css">
    <title>Laravel News</title>
</head>
<body>
    <nav>
        <a href="home.php">Laravel News</a>
    </nav>
    <section>
        <h2>さぁ、最新のニュースをシェアしましょう！</h2>
        <?php if(!empty($error_message)): ?>
            <ul class="error_message">
                <?php foreach($error_message as $value): ?>
                    <li><?php echo $value; ?></li>
                    <?php endforeach;?>
            </ul>
        <?php endif;?>
        <form method="POST" action="home.php" onsubmit="return submitChk()">
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
    <section>
        <?php
        if(!empty($message_array)): ?>
        <?php foreach($message_array as $value): ?>
        <div>
            <h3><?php echo $value["title"]; ?></h3>
            <p><?php echo $value["body"]; ?></p>
            <a href="comment.php?id=<?php echo  $value["num"]; ?>&title=<?php echo $value["title"]; ?>&body=<?php echo $value["body"]; ?>">記事全文・コメントを見る</a>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </section>
</body>
</html>