<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

try {
  $pdo = new PDO($dsn, $user, $password);

  // idカラム値をプレースホルダ（：id)に置き換えたSQL文をあらかじめ用意する
  $sql_delete = 'DELETE FROM books WHERE id = :id';
  $stmt_delete = $pdo->prepare($sql_delete);

  // bindメソッドを使って実際の値をプレースホルダにバインドする
  $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

  // SQL実行
  $stmt_delete->execute();

  // 削除した件数を取得
  $count = $stmt_delete->rowCount();
  $message = "書籍を{$count}件削除しました。";

  // 商品一覧ページにリダイレクトさせる（同時にmessageも渡す）
  header("Location: read.php?message={$message}");

}catch (PDOException $e){
  exit ($e->getMessage());
}
