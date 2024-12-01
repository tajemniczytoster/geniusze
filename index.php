<?php
session_start(); // Rozpoczęcie sesji

$errorMessage = ""; // Zmienna na komunikat błędu
$errorMessage_reg = "";

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "login") {
    $email = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $db = new mysqli("localhost", "root", "", "auth");

    $q = $db->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
    $q->bind_param("s", $email);
    $q->execute();
    $result = $q->get_result();

    $userRow = $result->fetch_assoc();
    if ($userRow == null) {
        $errorMessage = "Błędny login lub hasło.";
    } else {
        if (password_verify($password, $userRow['passwordHash'])) {
            // Ustawienie sesji użytkownika
            $_SESSION['user_id'] = $userRow['id'];
            $_SESSION['email'] = $userRow['email'];

            // Rejestracja logowania
            $loginTime = date("Y-m-d H:i:s");
            $logQuery = $db->prepare("INSERT INTO user_logins (user_id, login_time) VALUES (?, ?)");
            $logQuery->bind_param("is", $userRow['id'], $loginTime);
            $logQuery->execute();

            // Przekierowanie po zalogowaniu
            header("Location: index.php");
            exit();
        } else {
            $errorMessage = "Błędny login lub hasło.";
        }
    }
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == "register") {
    $db = new mysqli("localhost", "root", "", "auth");
    $email = $_REQUEST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $password = $_REQUEST['password'];
    $passwordRepeat = $_REQUEST['passwordRepeat'];

    if ($password == $passwordRepeat) {
        $q = $db->prepare("INSERT INTO user (email, passwordHash) VALUES (?, ?)");
        $passwordHash = password_hash($password, PASSWORD_ARGON2I);
        $q->bind_param("ss", $email, $passwordHash);
        $result = $q->execute();
        if ($result) {
            $errorMessage_reg = "Konto utworzono poprawnie";
        } else {
            $errorMessage_reg = "Coś poszło nie tak!";
        }
    } else {
        $errorMessage_reg = "Hasła nie są zgodne - spróbuj ponownie!";
    }
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "logout") {
    // Wylogowanie użytkownika
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<?php if (isset($_SESSION['user_id'])): ?>

    <link rel="stylesheet" href="styl.css">

    <header><h1>Moje Konto</h1></header> 

    <div id="image-container">
        <img class="karteczka" src="https://thispersondoesnotexist.com/" alt="">
    </div>
    <main>
        <p>Login: <?php echo htmlspecialchars($_SESSION['email']); ?> </p><br>
    </main>

<form action="index.php" method="post">
        <input type="hidden" name="action" value="logout">
        <input type="submit" value="Wyloguj">
    </form>
<?php else: ?>

    <head>
        <link rel="stylesheet" href="styl.css">
    </head>
    <header><h1>System logowań i rejestracji</h1></header>
    <main>
        <section id="logowanie">
            <h1>Zaloguj się</h1>
            <form action="index.php" method="post">
                <label for="emailInput">Email:</label>
                <input type="email" name="email" id="emailInput" required><br>
                <label for="passwordInput">Hasło:</label>
                <input type="password" name="password" id="passwordInput" required><br>
                <input type="hidden" name="action" value="login">
                <input type="submit" value="Zaloguj">
            </form>
            <?php if (!empty($errorMessage)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </section>
        <section id="rejestracja">
            <h1>Zarejestruj się</h1>
            <form action="index.php" method="post">
                <label for="emailInput">Email:</label>
                <input type="email" name="email" id="emailInput" required><br>
                <label for="passwordInput">Hasło:</label>
                <input type="password" name="password" id="passwordInput" required><br>
                <label for="passwordRepeatInput">Hasło ponownie:</label>
                <input type="password" name="passwordRepeat" id="passwordRepeatInput" required><br>
                <input type="hidden" name="action" value="register">
                <input type="submit" value="Zarejestruj">
            </form>
            <?php if (!empty($errorMessage_reg)): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage_reg); ?></p>
            <?php endif; ?>
        </section>
    </main>
    <footer>

    <div id="image-container">
        <p>Autorzy:</p>
        <img class="karteczka" id="kacper" alt="Kacper" title="Kacper">
        <img class="karteczka" id="gracjan" alt="Gracjan" title="Gracjan">
        <img class="karteczka" id="anton" alt="Anton" title="Anton">
        <img class="karteczka" id="szymon" alt="Szymon" title="Szymon">
    </div>

    <script>
        const images = [
            { id: "kacper", url: "https://thispersondoesnotexist.com/" },
            { id: "gracjan", url: "https://thispersondoesnotexist.com/" },
            { id: "anton", url: "https://thispersondoesnotexist.com/" },
            { id: "szymon", url: "https://thispersondoesnotexist.com/" }
        ];

        function loadImage(image) {
            return new Promise(resolve => {
                const imgElement = document.getElementById(image.id);
                imgElement.src = image.url;
                imgElement.onload = () => resolve();
            });
        }

        async function loadImagesSequentially(images) {
            for (const image of images) {
                await loadImage(image);
                console.log(`Loaded ${image.id}`);
                await new Promise(resolve => setTimeout(resolve, 150)); // 1 second delay
            }
        }

        loadImagesSequentially(images);
    </script>
    
    </footer>
<?php endif; ?>
