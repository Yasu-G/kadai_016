<?php
$dsn = 'mysql:dbname=php_book_app;host=localhost;charset=utf8mb4';
$user = 'root';
$password = 'root';

// submitパラメータの値が存在するとき（「更新」ボタンを押したとき）の処理
if(isset($_POST['submit'])){
  try {
    $pdo = new PDO($dsn, $user, $password);

    // 動的に変わる値をプレースホルダに置き換えたINSERT文をあらかじめ用意する
    $sql_update = '
      UPDATE books SET 
      book_code = :book_code,
      book_name = :book_name,
      price = :price,
      stock_quantity = :stock_quantity,
      genre_code = :genre_code
      WHERE id = :id
    ';
      $stmt_update = $pdo->prepare($sql_update);

      // bindValueメソッドを使って実際の値をブレースホルダにバインドする
      $stmt_update-> bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
      $stmt_update-> bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
      $stmt_update-> bindValue(':price', $_POST['price'], PDO::PARAM_INT);
      $stmt_update-> bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
      $stmt_update-> bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_INT);
      $stmt_update-> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
      
      // SQL文実行
      $stmt_update->execute();

      // 更新した件数を取得する
      $count = $stmt_update->rowCount();
      $message = "書籍を{$count}件編集しました。";

      // 書籍一覧ページにリダイレクト（同時にmessageパラメータも渡す）
      header("Location: read.php?message={$message}");

    }catch (PDOException $e){
      exit($e->getMessage());
    }
  }

// idパラメータの値が存在する（登録のボタンを押した時）時の処理
if(isset($_GET['id'])){
  try {
    $pdo = new PDO($dsn, $user, $password);

    // idカラムの値をプレース非オルだ（:id）に書き換えたSQLを用意する
    $sql_select_book = 'SELECT * FROM books WHERE id = :id'; 
    $stmt_select_book = $pdo->prepare($sql_select_book);

      // bindValueメソッドを使って実際の値をブレースホルダにバインドする
      $stmt_select_book-> bindValue(':id', $_GET['id'], PDO::PARAM_INT);
      // SQL文実行
      $stmt_select_book->execute();

      // SQL実行結果を配列で取得
      $book = $stmt_select_book->fetch(PDO::FETCH_ASSOC);

      // idパラメータと同じidが存在しない場合はエラーメッセージを表示して終了
      if ($book === FALSE){
        exit('idパラメータの値が不正です。');
      }

      // genreテーブルからgenre＿codeカラムのデータを取得
      $sql_select_genre_codes = 'SELECT genre_code FROM genres';
      // SQL文実行
      $stmt_select_genre_codes = $pdo->query($sql_select_genre_codes);
      // SQLの実行結果を配列で取得
      $genre_codes = $stmt_select_genre_codes->fetchAll(PDO::FETCH_COLUMN);

    }catch (PDOException $e){
      exit($e->getMessage());
    }

  }else{
    exit('idパラメータの値が存在しません。');
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>書籍編集</title>

  <link rel="stylesheet" href="css/style.css">

  <!-- Google Fontsの読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap">
</head>

<body>
  <header>
    <nav>
      <a href="index.php">書籍管理アプリ</a>
    </nav>
  </header>

  <main>
    <article class="registration">
      <h1>書籍編集</h1>
      <div class="back">
        <a href="read.php" class="btn">&lt; 戻る</a>
      </div>

      <form action="update.php?id=<?=$_GET['id']?>" method="post" class="registration-form">
        <div>
          <label for="book_code">書籍コード</label>
          <input type="number" name="book_code" value="<?= $book['book_code'] ?>" min="0" max="100000000" required>

          <label for="book_name">書籍名</label>
          <input type="text" name="book_name" value="<?= $book['book_name'] ?>" maxlength="50" required>

          <label for="price">単価</label>
          <input type="number" name="price" value="<?= $book['price'] ?>" min="0" max="100000000" required>

          <label for="stock_quantity">在庫数</label>
          <input type="number" name="stock_quantity" value="<?= $book['stock_quantity'] ?>" min="0" max="100000000" required>

          <label for="genre_code">ジャンルコード</label>
          <select name="genre_code"  required>
            <option disabled selected value>選択してください</option>
            <?php
              // 配列の中身を順番に取り出し、セレクトボックスの選択してとして出力する
              foreach($genre_codes as $genre_code) {
                // もし変数＄genre＿codeが書籍の仕入先コードの値と一致していれば、select属性をつけて初期値にする
                if($genre_code === $book['genre_code']){
                  echo "<option value='{$genre_code}' selected> {$genre_code} </option>";
                }else{
                  echo "<option value='{$genre_code}'> {$genre_code} </option>";
                }
              }
              ?>
          </select>
        </div>
        <button type="submit" class="submit-btn" name="submit" value="update">更新</button>
      </form>
    </article>
  </main>

  <footer>
    <p class="copyright">&copy; 書籍管理アプリ All rights reserved.</p>
  </footer>
</body>
</html>