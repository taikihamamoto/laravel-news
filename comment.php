<?php
    define("commentBox","comment.txt");
    $id = $_GET["id"];
    $commentText = "";
    $message =array();
    $message_array =array();
    $error_message = array();
    $clean = array();
    $fp = fopen(commentBox, "a+");
    $titleLimit =50;
    /*登録番号の定義*/
    if ( file_exists(commentBox) ) { /*ファイルの存在確認。*/
    //最後の行にプラス1
        $lines=file(commentBox);
        $lastline= $lines[count($lines) - 1];
        $num=explode(",",$lastline);
        $lastnum=$num[0]+1;
    } else { /*ファイルが無かった場合変数の定義を１とする*/
    $num = 1;
    }
    if( !empty($_POST["commentSend"])){
        if(empty($_POST["comment"])){
            $error_message[] = "コメントは必須です。";
        }
        elseif(strlen($_POST["comment"]) > $titleLimit){
            $error_message[] = "コメントは5０文字以内です。";
        }
        else{
            $clean["comment"] = htmlspecialchars( $_POST["comment"], ENT_QUOTES);
            $clean["comment"] = preg_replace( '/\\r\\n|\\n|\\r/', '', $clean['comment']);
        }
        if(empty($error_message)){
            if($fp = fopen(commentBox, "a")){
                $data = "".$lastnum.",".$id.",".$clean['comment']."\n";
                fwrite($fp,$data);
                fclose($fp);
            }
        }
    }
    if($fp = fopen(commentBox, "r")){
        while ($data = fgets($fp)){
            $splitData = explode(",",$data);
            $message = array(
                "commentnum" => $splitData[0],
                "id" => $splitData[1],
                "comment" => $splitData[2]
            );
            array_unshift($message_array, $message);
        }
        fclose($fp);
    }
    if (isset($_POST['deleteSend'])) {

        $delete = $_POST['delete'];
        $delCon = file(commentBox);
        for ($j = 0; $j < count($delCon) ; $j++){ 
        $delData = explode(",", $delCon[$j]);
        
        if ($delData[0] == $delete) { 
        array_splice($delCon, $j, 1);
        file_put_contents(commentBox, implode("", $delCon));
        }
        }
    }
?>  
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="home.css">
    <title>Laravel News</title>
</head>
<body>
    <nav>
        <a href="home.php">Laravel News</a>
    </nav>
    <?php if(!empty($error_message)): ?>
        <ul class="error_message">
            <?php foreach($error_message as $value): ?>
                <li><?php echo $value; ?></li>
                <?php endforeach;?>
        </ul>
    <?php endif;?>
    <section>
        <h3><?php echo $_GET["title"];?></h3>
        <p><?php echo $_GET["body"];?></p>
    </section>
    <hr>
    <section>
    <form method="POST" action="">
        <textarea name="comment"></textarea>
        <input type="submit" name="commentSend" value="コメントを書く"></form>
    </form>
    </section>
    <section>
    <?php foreach($message_array as $value ): ?>
        <form method="POST" action="">
            <?php
            if(!empty($message_array)): ?>
            <?php if($id == $value["id"]): ?>
            <div>
            <div><?php echo $value["comment"]; ?></div>
            <input type="hidden" name="delete" value="<?php echo $value["commentnum"];?>">
            <input type="submit" name="deleteSend" value="コメントを消す">
        </div>
        <?php endif; ?>
        <?php endif; ?>
        </form>
        <?php endforeach; ?>
    </section>
</body>
</html>