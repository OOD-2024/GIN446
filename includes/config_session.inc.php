<?php
if (session_status() == PHP_SESSION_NONE) {
    // Disable output before session start
    ob_start();
    
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'secure' => true,
        'httponly' => true
    ]);
    
    session_start();
if (!isset($_SESSION["last_regeneration"])) {
    regenerateSessionId();

} else {
    $interval = 60 * 30;
    if (time() - $_SESSION["last_regeneration"] >= $interval) {

        regenerateSessionId();
    }
}
    
    // Flush output buffer
    ob_end_clean();
}




function regenerateSessionId()
{
    session_regenerate_id(true);
    $_SESSION["last_regeneration"] = time();
}

function regenerate_session_id_loggedin(object $pdo, $email)
{
    session_regenerate_id(true);
    $query = 'Select * from patient where Email = :email';
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $sessionId = $result["ID"];
    $_SESSION["login_user_id"] = $sessionId;
}
