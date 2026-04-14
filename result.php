<?php
session_start();

$current = $_SESSION['current_question'];
$userAnswer = $_POST['answer'] ?? '';

$correctLetter = $current['answer'];
$correctText = $current['options'][$correctLetter];

$isCorrect = ($userAnswer === $correctLetter);

// --- AI TRACKING ---
$_SESSION['total_count']++;
if ($isCorrect) $_SESSION['correct_count']++;

// --- HISTORY ---
$_SESSION['history'][] = $isCorrect ? 1 : 0;

// --- POINT CALCULATION (SYNCED WITH GAME DISPLAY) ---
$base = 100 * $_SESSION['level'];
$mult = min($_SESSION['streak'] + ($isCorrect ? 1 : 0), 3);
$bonus = 50;
$points = ($base + $bonus) * $mult;

// --- SCORE + STREAK ---
if ($isCorrect) {

    $_SESSION['streak']++;
    $_SESSION['score'] += $points;

} else {

    $_SESSION['streak'] = 0;
    $_SESSION['lives']--;
    $_SESSION['score'] = max(0, $_SESSION['score'] - 100);
}

// --- PROGRESSION ---
$_SESSION['questions_in_level']++;

if ($_SESSION['questions_in_level'] >= 5) {
    $_SESSION['level']++;
    $_SESSION['questions_in_level'] = 0;
}

// --- GAME OVER ---
$gameOver = ($_SESSION['lives'] <= 0);

// --- SAVE SCORE ---
function saveScore($user, $score) {

    $file = "leaderboard.json";

    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }

    $data = json_decode(file_get_contents($file), true);

    $data[] = ["user"=>$user,"score"=>$score];

    usort($data, fn($a,$b)=>$b['score']-$a['score']);

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

if ($gameOver) {
    saveScore($_SESSION['user'], $_SESSION['score']);
}

// --- RESET QUESTION STATE ---
unset($_SESSION['current_question']);
unset($_SESSION['filtered_options']);
?>

<!DOCTYPE html>
<html>
<head><title>Result</title></head>
<body>

<h2><?php echo $isCorrect ? "✅ Correct!" : "❌ Wrong!"; ?></h2>

<p><b>Correct Answer:</b> <?php echo $correctText; ?></p>

<p><b>Points Earned This Question:</b> <?php echo $points; ?></p>

<p><b>Total Score:</b> <?php echo $_SESSION['score']; ?></p>
<p><b>Lives:</b> <?php echo $_SESSION['lives']; ?> ❤️</p>

<?php if ($isCorrect): ?>
<p><b>Streak:</b> <?php echo $_SESSION['streak']; ?>x</p>
<?php endif; ?>

<?php if (!$gameOver): ?>

<form action="game.php">
    <button>➡️ Next Question</button>
</form>

<form action="cashout.php" method="POST">
    <button>💰 Cash Out</button>
</form>

<?php else: ?>

<h3>Game Over</h3>

<form action="leaderboard.php">
    <button>View Leaderboard</button>
</form>

<?php endif; ?>

<?php unset($_SESSION['current_question']);
unset($_SESSION['filtered_options']);
$_SESSION['question_locked'] = false; 
unset($_SESSION['phone_hint']);?>


</body>
</html>