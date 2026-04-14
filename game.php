<?php
session_start();



// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// --- RESET ---
if (isset($_GET['reset'])) {
    session_destroy();
    session_start();
}

// --- USER if (!isset($_SESSION['user'])) $_SESSION['user'] = "Guest"; ---


// --- INIT ---
if (!isset($_SESSION['score'])) $_SESSION['score'] = 0;
if (!isset($_SESSION['level'])) $_SESSION['level'] = 1;
if (!isset($_SESSION['questions_in_level'])) $_SESSION['questions_in_level'] = 0;

if (!isset($_SESSION['seen_ids'])) $_SESSION['seen_ids'] = [];
if (!isset($_SESSION['history'])) $_SESSION['history'] = [];
if (!isset($_SESSION['streak'])) $_SESSION['streak'] = 0;
if (!isset($_SESSION['lives'])) $_SESSION['lives'] = 3;

if (!isset($_SESSION['correct_count'])) $_SESSION['correct_count'] = 0;
if (!isset($_SESSION['total_count'])) $_SESSION['total_count'] = 0;

if (!isset($_SESSION['lifelines'])) {
    $_SESSION['lifelines'] = ["5050"=>true,"phone"=>true];
}

if (!isset($_SESSION['question_locked'])) {
    $_SESSION['question_locked'] = false;
}

if (!isset($_SESSION['filtered_options'])) {
    $_SESSION['filtered_options'] = [];
}

$MAX_LEVEL = 5;

// --- QUESTIONS ---
$questions = include("questions.php");

// --- AI DIFFICULTY ---
function getDifficulty() {
    $correct = $_SESSION['correct_count'];
    $total = max(1, $_SESSION['total_count']);
    $accuracy = $correct / $total;

    if ($accuracy >= 0.80) return "hard";
    if ($accuracy >= 0.50) return "medium";
    return "easy";
}

$currentDifficulty = getDifficulty();

/*
====================================================
QUESTION GENERATION
====================================================
*/
if (!isset($_SESSION['current_question'])) {

    $available = array_filter($questions, function($q) use ($currentDifficulty) {
        return $q['difficulty'] === $currentDifficulty &&
               !in_array($q['id'], $_SESSION['seen_ids']);
    });

    if (empty($available)) {
        $available = array_filter($questions, function($q) {
            return !in_array($q['id'], $_SESSION['seen_ids']);
        });
    }

    $available = array_values($available);

    if (empty($available)) {
        header("Location: leaderboard.php");
        exit();
    }

    $current = $available[array_rand($available)];

    $_SESSION['current_question'] = $current;
    $_SESSION['seen_ids'][] = $current['id'];
    $_SESSION['filtered_options'] = $current['options'];
    
}

$current = $_SESSION['current_question'];


/*
====================================================
FIRST QUESTION CHECK (LIFELINE LOCK)
====================================================
*/
$isFirstQuestion = (count($_SESSION['seen_ids']) === 1);

// toolbox tip message
$toolboxTip = "";
if ($isFirstQuestion) {
    $toolboxTip = "💡 Toolbox Tip: Lifelines unlock after your first question. Get through this one first!";
}

/*
====================================================
LIFELINE HANDLER
====================================================
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lifeline'])) {

    if (!isset($_SESSION['current_question'])) {
        header("Location: game.php");
        exit();
    }

    $current = $_SESSION['current_question'];

    $_SESSION['filtered_options'] = $_SESSION['filtered_options'] ?? $current['options'];

    // BLOCK lifelines on first question safely
    if ($isFirstQuestion) {
        header("Location: game.php");
        exit();
    }

    // 50/50
    if ($_POST['lifeline'] === "5050" && $_SESSION['lifelines']['5050']) {

        $_SESSION['lifelines']['5050'] = false;
        $_SESSION['question_locked'] = true;

        $correct = $current['answer'];

        $wrong = array_keys(array_filter(
            $current['options'],
            fn($k) => $k !== $correct,
            ARRAY_FILTER_USE_KEY
        ));

        shuffle($wrong);

        foreach (array_slice($wrong, 0, 2) as $r) {
            unset($_SESSION['filtered_options'][$r]);
        }
    }

    // phone a friend
    if ($_POST['lifeline'] === "phone" && $_SESSION['lifelines']['phone']) {

        $_SESSION['lifelines']['phone'] = false;
        $_SESSION['question_locked'] = true;

        $_SESSION['phone_hint'] =
            "Your friend thinks it's: " .
            $current['options'][$current['answer']];
    }

    header("Location: game.php");
    exit();
}

// --- OPTIONS ---
$optionsToShow = $_SESSION['filtered_options'] ?? $current['options'];
$hint = $_SESSION['phone_hint'] ?? "";

// --- POINT SYSTEM ---
$streakMultiplier = min($_SESSION['streak'], 3);
$basePoints = 100 * $_SESSION['level'];
$bonus = 50;
$questionValue = ($basePoints + $bonus) * ($streakMultiplier + 1);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Game</title>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['user']; ?></h2>

<p>Score: <?php echo $_SESSION['score']; ?></p>
<p>Level: <?php echo $_SESSION['level']; ?> / 5</p>
<p>Lives: <?php echo $_SESSION['lives']; ?> ❤️</p>
<p>AI Difficulty: <?php echo $currentDifficulty; ?></p>



<?php if ($toolboxTip): ?>
    <div style="padding:10px; background:#f5f5f5; border-left:4px solid #999; margin-bottom:10px;">
        <?php echo $toolboxTip; ?>
    </div>
<?php endif; ?>



<h3>🎯 Base Points: <?php echo $basePoints; ?></h3>
<p>🔥 Multiplier: x<?php echo ($streakMultiplier + 1); ?></p>
<p>💰 Question Value: <?php echo $questionValue; ?></p>

<hr>

<h3><?php echo $current['q']; ?></h3>

<!-- LIFELINES -->
<form method="POST">
    <?php if (!$isFirstQuestion): ?>

        <?php if ($_SESSION['lifelines']['5050']): ?>
            <button name="lifeline" value="5050">50/50</button>
        <?php endif; ?>

        <?php if ($_SESSION['lifelines']['phone']): ?>
            <button name="lifeline" value="phone">Phone a Friend</button>
        <?php endif; ?>

    <?php else: ?>
        <p><i>🚫 Lifelines are locked for the first question</i></p>
    <?php endif; ?>
</form>

<p><?php echo $hint; ?></p>

<!-- ANSWERS -->
<form method="POST" action="result.php">
    <?php foreach ($optionsToShow as $k=>$v): ?>
        <label>
            <input type="radio" name="answer" value="<?php echo $k; ?>" required>
            <?php echo "$k: $v"; ?>
        </label><br>
    <?php endforeach; ?>

    <button type="submit">Submit</button>
</form>

</body>
</html>