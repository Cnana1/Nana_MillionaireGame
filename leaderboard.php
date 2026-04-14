<?php
$file = "leaderboard.json";

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}

$data = json_decode(file_get_contents($file), true);

if (!is_array($data)) {
    $data = [];
}

/* =========================
   MERGE DUPLICATES (BEST SCORE ONLY)
========================= */

$cleaned = [];

foreach ($data as $entry) {
    if (!isset($entry['user']) || !isset($entry['score'])) continue;

    $user = $entry['user'];
    $score = $entry['score'];

    // keep ONLY highest score per user
    if (!isset($cleaned[$user]) || $score > $cleaned[$user]['score']) {
        $cleaned[$user] = [
            "user" => $user,
            "score" => $score
        ];
    }
}

/* convert back to indexed array */
$data = array_values($cleaned);

/* =========================
   SORT HIGHEST SCORE FIRST
========================= */
usort($data, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

/* =========================
   WRITE CLEAN DATA BACK TO FILE
========================= */
file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
</head>
<body>

<form method="POST" action="logout.php">
    <button type="submit">Logout</button>
</form>

<h2>Leaderboard</h2>

<?php if (empty($data)): ?>
    <p>No scores yet.</p>
<?php else: ?>
    <ol>
        <?php foreach (array_slice($data, 0, 10) as $entry): ?>
            <li>
                <?php echo htmlspecialchars($entry['user']); ?> -
                <?php echo $entry['score']; ?>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

</body>
</html>