<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/validate.php';

$errors = [];

// Default values
$values = [
    'salutation' => '',
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'email' => '',
    'country' => '',
];

// Success message
$success = isset($_GET['success']) && $_GET['success'] === '1';

// Verwerk POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['salutation']  = sanitize($_POST['salutation'] ?? '');
    $values['first_name']  = sanitize($_POST['first_name'] ?? '');
    $values['middle_name'] = sanitize($_POST['middle_name'] ?? '');
    $values['last_name']   = sanitize($_POST['last_name'] ?? '');
    $values['email']       = sanitize($_POST['email'] ?? '');
    $values['country']     = sanitize($_POST['country'] ?? '');

    // Valideer submit
    if (!validSalutation($values['salutation'])) {
        $errors['salutation'] = 'Kies een aanhef.';
    }

    if (!required($values['first_name'])) {
        $errors['first_name'] = 'Voornaam is verplicht.';
    }

    if (!required($values['last_name'])) {
        $errors['last_name'] = 'Achternaam is verplicht.';
    }

    if (!required($values['email'])) {
        $errors['email'] = 'E-mailadres is verplicht.';
    } elseif (!validEmail($values['email'])) {
        $errors['email'] = 'Vul een geldig e-mailadres in.';
    }

    if (!required($values['country'])) {
        $errors['country'] = 'Land is verplicht.';
    }

    // Sla op in database
    if (empty($errors)) {
        $pdo = db();

        $stmt = $pdo->prepare(
            'INSERT INTO submissions 
            (salutation, first_name, middle_name, last_name, email, country)
            VALUES (:salutation, :first_name, :middle_name, :last_name, :email, :country)'
        );

        $stmt->execute([
            'salutation'  => $values['salutation'],
            'first_name'  => $values['first_name'],
            'middle_name' => $values['middle_name'] !== '' ? $values['middle_name'] : null,
            'last_name'   => $values['last_name'],
            'email'       => $values['email'],
            'country'     => $values['country'],
        ]);

        // Redirect voorkomt dubbel submitten bij refresh
        header('Location: /?success=1');
        exit;
    }
}

function checked(string $current, string $value): string
{
    return $current === $value ? ' checked' : '';
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Testopdracht</title>

    <!-- Country dropdown -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/country-select-js@2.1.0/build/css/countrySelect.min.css">
    <link rel="stylesheet" href="/style.css">
</head>

<body>

    <!-- Maak HTML formulier -->
    <div class="container">
        <h1>Formulier</h1>

        <?php if ($success): ?>
            <div class="success">✅ Inzending succesvol opgeslagen.</div>
        <?php endif; ?>

        <!-- novalidate aangezien het een PHP opdracht is  -->
        <form method="post" action="/" novalidate>

            <p><strong>Aanhef <span class="required">*</span></strong></p>
            <div class="radio-group">
                <label>
                    <input type="radio" name="salutation" value="heer" <?= checked($values['salutation'], 'heer'); ?>>
                    Heer
                </label>
                <label>
                    <input type="radio" name="salutation" value="mevrouw" <?= checked($values['salutation'], 'mevrouw'); ?>>
                    Mevrouw
                </label>
            </div>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($errors['salutation'])): ?>
                <div class="error"><?= h($errors['salutation']); ?></div>
            <?php endif; ?>

            <p>
                <label for="first_name">Voornaam <span class="required">*</span></label><br>
                <input id="first_name" type="text" name="first_name" value="<?= h($values['first_name']); ?>">
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($errors['first_name'])): ?>
            <div class="error"><?= h($errors['first_name']); ?></div>
        <?php endif; ?>
        </p>

        <p>
            <label for="middle_name">Tussenvoegsel</label><br>
            <input id="middle_name" type="text" name="middle_name" value="<?= h($values['middle_name']); ?>">
        </p>

        <p>
            <label for="last_name">Achternaam <span class="required">*</span></label><br>
            <input id="last_name" type="text" name="last_name" value="<?= h($values['last_name']); ?>">
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($errors['last_name'])): ?>
        <div class="error"><?= h($errors['last_name']); ?></div>
    <?php endif; ?>
    </p>

    <p>
        <label for="email">E-mailadres <span class="required">*</span></label><br>
        <input id="email" type="email" name="email" value="<?= h($values['email']); ?>">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($errors['email'])): ?>
    <div class="error"><?= h($errors['email']); ?></div>
<?php endif; ?>
</p>

<p>
    <label for="country">Land <span class="required">*</span></label><br>
    <input type="text" id="country" name="country" value="<?= h($values['country']); ?>" autocomplete="off">
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($errors['country'])): ?>
<div class="error"><?= h($errors['country']); ?></div>
<?php endif; ?>
</p>

<button type="submit">Versturen</button>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/country-select-js@2.1.0/build/js/countrySelect.min.js"></script>
    <script>
        $(function() {
            $('#country').countrySelect({
                defaultCountry: 'nl',
                preferredCountries: ['nl', 'be', 'de']
            });
        });
    </script>

</body>

</html>