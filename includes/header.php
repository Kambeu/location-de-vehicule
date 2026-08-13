<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/style.css">
    <?php if (!empty($extra_css)): ?>
        <link rel="stylesheet" href="<?= APP_URL ?>/assets/<?= htmlspecialchars($extra_css) ?>">
    <?php endif; ?>
</head>
<body>
