<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmSubmission() {
            const message = document.getElementById('message').value;
            const name = document.getElementById('name').value;
            if (message.length < 1 || message.length > 30) {
                alert('記事は1字以上30字以内で入力してください。');
                return false;
            }else if (name.length < 1 || name.length > 30) {
                alert('タイトルは1字以上30字以内で入力してください。');
                return false;
            }
            return confirm('この内容で投稿しますか？');
        }
    </script>    
</head>
<body>
    <h1><div class="background"><a href="index.php"><i>Laravel News</i></a></div></h1>
    <h2 style="padding-left: 10px;"><b>さぁ、最新のニュースをシェアしましょう</b></h2>
    <?php
    $filename = 'data/messages.csv'; //保存されるファイルのパス
    $error = '';//エラーメッセージの宣言
    
    // 新しい投稿が送信された場合
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '匿名';
        $message = $_POST['message'] ?? '';//フォームから送信された名前と中身の取得

        //ここにモーダル入れたい（投稿しますかダイアログ）


        if (strlen($message) < 1 || strlen($message) > 30) {
            $error = 'タイトルは1字以上30字以内で入力してください。現在の文字数: ' . strlen($message);
        } else {
            $id = uniqid(); // 一意のIDを生成
            $fp = fopen($filename, 'a');//csvファイルを追記モードで開く
            fputcsv($fp, [$id, $name, $message, date('Y-m-d H:i:s')]);//投稿データ（ID、名前、メッセージ、日時）をCSVファイルに書き込む
            fclose($fp);
        }
    }
    
    // CSVファイルから投稿を読み込む
    $messages = [];//データを保存する配列の初期化
    if (file_exists($filename)) {//csvファイルが存在する場合
        $fp = fopen($filename, 'r');//ファイルを読み取りで開く
        while ($row = fgetcsv($fp)) {
            $messages[] = $row;//読み込んだ行だけ配列に追加
        }
        fclose($fp);
    }
    ?>

    <!-- 投稿フォーム -->
    <form action="" method="post" onsubmit="return confirmSubmission();">
        <?php if ($error): ?>
            <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <p>
            <label for="name">タイトル：</label>
            <input type="text" name="name" id="name">
        </p>
        <p>
            <label for="message">記事：</label>
            <textarea name="message" id="message" cols="30" rows="5"></textarea>
        </p>
        <p>
            <button type="submit">投稿</button>
        </p>
    </form>

    <!-- ニュース一覧 -->
    <h2>ニュース一覧</h2>
    <?php if (empty($messages)): ?>
        <p>まだ投稿はありません。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($messages as $msg): ?>
                <li>
                    <str><?php echo htmlspecialchars($msg[1], ENT_QUOTES, 'UTF-8'); ?></str>:
                    <a href="detail.php?id=<?php echo htmlspecialchars($msg[0], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo nl2br(htmlspecialchars($msg[2], ENT_QUOTES, 'UTF-8')); ?>
                    </a>
                    <det>(<?php echo htmlspecialchars($msg[3], ENT_QUOTES, 'UTF-8'); ?>)</det>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>

