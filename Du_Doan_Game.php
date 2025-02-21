<?php
session_start();
//Khởi tạo biến
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
    $_SESSION['rounds'] = 0;
    $_SESSION['history'] = [];
}
//Tạo câu trả lời đúng ngẫu nhiên
function generateRandomOptions() {
    $options = ['A', 'B', 'C', 'D'];
    shuffle($options);
    return array_slice($options, 0, 2);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Khi người chơi nhấn nút xác nhận thì thực hiện
   // Kiểm tra đã chơi đủ 5 lượt chưa, nếu chưa đủ thì tạo đáp án đúng ngẫu nhiên
    if ($_SESSION['rounds'] < 5) {
        $correctAnswer = generateRandomOptions();
        $selectedOptions = $_POST['selectedOptions'] ?? [];
        
        $matched = count(array_intersect($selectedOptions, $correctAnswer));// Kiểm tra đáp án đúng với đáp án người chơi nhập vào
        $points = $matched == 2 ? 10 : ($matched == 1 ? 5 : 0);  // Nếu đúng cả 2 thì 10 điểm, đúng 1 trong 2 thì trả về 5 nếu k đúng thì là 0
        $_SESSION['score'] += $points; // Cập nhật là điểm
        $_SESSION['rounds']++; // Tăng số vòng chơi lên 1
        $_SESSION['history'][] = ['selected' => $selectedOptions, 'correct' => $correctAnswer, 'score' => $points]; // Lưu lại lịch sử
    }
    //Kiểm tra nếu đã chơi đủ 5 lượt thì đặt biến $gameOver = true để kết thúc game.
    if ($_SESSION['rounds'] >= 5) {
        $gameOver = true;
        session_destroy(); // Hủy session
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Game Dự Đoán</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color:rgb(217, 215, 215);
            margin: 0;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }
        .options label {
            display: block;
            margin: 10px 0;
            font-size: 20px;
        }
        button {
            background:rgb(19, 177, 56);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 6px;
        }
        button:hover {
            background:rgb(13, 236, 61);
        }
        .history {
            margin-top: 18px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Game Dự Đoán</h2>
   <p>Điểm số: <?php echo $_SESSION['score'] ?? 0; ?></p>    <!--In điểm hiện tại-->
    <?php if (!isset($gameOver)) : ?>   <!--Kiểm tra đã kết thúc game chưa -->
        <form method="POST">
            <?php foreach (["A", "B", "C", "D"] as $option): ?>   <!-- Hiện thị 4 ký tự để lựa chọn -->
                <label><input type="checkbox" name="selectedOptions[]" value="<?php echo $option; ?>"> <?php echo $option; ?></label>  <!--người chơi được chọn 2 ký tự-->
            <?php endforeach; ?>
            <button type="submit">Xác nhận</button> 
        </form>
    <?php else: ?> <!-- Tồn tại $gameOver thông báo điểm -->
        <p>Trò chơi kết thúc! Tổng điểm của bạn: <?php echo $_SESSION['score']; ?></p>
    <?php endif; ?>
    <h3>Lịch sử chơi:</h3>
    <ul>
    	<!-- Hiển thị thông người chơi bằng cách duyện qua $_SESSION['history'] -->
        <?php foreach ($_SESSION['history'] as $entry): ?> 
            <li>Chọn: <?php echo implode(", ", $entry['selected']); ?> - Đáp án đúng: <?php echo implode(", ", $entry['correct']); ?> - Điểm: <?php echo $entry['score']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
