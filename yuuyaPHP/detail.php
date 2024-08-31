<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿の詳細</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmCommentSubmission() {
            const comment = document.getElementById('comment').value;
            const name = document.getElementById('name').value;
            if (comment.length < 1 || comment.length > 30) {
                alert('コメントは1字以上30字以内で入力してください。');
                return false;
            }elseif (name.length < 1 || name.length > 30);{
                alert('名前は1字以上30字以内で入力してください。');
                return false;
            }
            return confirm('この内容でコメントしますか？??');
        }
    </script>
</head>
<body>
<h1><div class="background"><a href="index.php"><i>Laravel News</i></a></div></h1>
<h2 style="padding-left: 10px;"><b>さぁ、最新のニュースをシェアしましょう</b></h2>

    <?php
    $filename = 'data/messages.csv';
    $commentsFilename = 'data/comments.csv';
    $error = '';

    // GETパラメータから投稿IDを取得
    if (!isset($_GET['id'])) {
        echo '<p>投稿が見つかりません。</p>';
        exit;
    }

    $postId = $_GET['id'];
    $post = null;

    // CSVファイルから投稿を読み込む
    if (file_exists($filename)) {
        $fp = fopen($filename, 'r');
        while ($row = fgetcsv($fp)) {
            if ($row[0] === $postId) {
                $post = $row;
                break;
            }
        }
        fclose($fp);
    }

    if (!$post) {
        echo '<p>投稿が見つかりません。</p>';
        exit;
    }

    // 新しいコメントが送信された場合
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $name = $_POST['comment_name'] ?? '匿名';
        $comment = $_POST['comment'] ?? '';
        
        if (strlen($comment) < 1 || strlen($comment) > 30) {
            $error = 'コメントは1字以上30字以内で入力してください。現在の文字数: ' . strlen($comment);
        }elseif(strlen($name) < 1 || strlen($name) > 30){
            $error = '名前は1字以上30字以内で入力してください。現在の文字数: ' . strlen($name);
        }else {
            $fp = fopen($commentsFilename, 'a');
            fputcsv($fp, [$postId, $name, $comment, date('Y-m-d H:i:s')]);
            fclose($fp);
        }
    }

    // CSVファイルからコメントを読み込む
    $comments = [];
    if (file_exists($commentsFilename)) {
        $fp = fopen($commentsFilename, 'r');
        while ($row = fgetcsv($fp)) {
            if ($row[0] === $postId) {
                $comments[] = $row;
            }
        }
        fclose($fp);
    }
    ?>

    <!-- 投稿の詳細表示 -->
    <h2><?php echo htmlspecialchars($post[1], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($post[2], ENT_QUOTES, 'UTF-8')); ?></p>
    <p><em><?php echo htmlspecialchars($post[3], ENT_QUOTES, 'UTF-8'); ?></em></p>

    <!-- コメント表示 -->
    <h3><div class="background">コメント一覧</div></h3>
    <?php if (empty($comments)): ?>
        <p>まだコメントはありません。</p>
    <?php else: ?>
        <ul>
            <?php foreach ($comments as $comment): ?>
                <li>
                    <strong><?php echo htmlspecialchars($comment[1], ENT_QUOTES, 'UTF-8'); ?></strong>:
                    <span><?php echo nl2br(htmlspecialchars($comment[2], ENT_QUOTES, 'UTF-8')); ?></span>
                    <em>(<?php echo htmlspecialchars($comment[3], ENT_QUOTES, 'UTF-8'); ?>)</em>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- コメント投稿フォーム -->
    <h3><div class="background">コメントを投稿する</div></h3>
    <form action="" method="post" onsubmit="return confirmCommentSubmission();">
        <?php if ($error): ?>
            <p><?php echo htmlspecialchars($error, ENT_QUOTES,); ?></p>
        <?php endif; ?>
        <p>
            <label for="comment_name">名前：</label>
            <input type="text" name="comment_name" id="comment_name">
        </p>
        <p>
            <label for="comment">コメント：</label>
            <textarea name="comment" id="comment" cols="30" rows="3"></textarea>
        </p>
        <p>
            <button type="submit">コメント投稿</button>
        </p>
    </form>

    <p><a href="index.php">掲示板に戻る</a></p>
</body>
</html>
