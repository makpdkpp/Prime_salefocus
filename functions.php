<?php

/**
 * Establish a database connection and return the mysqli instance
 *
 * @return mysqli
 */
function connectDb(): mysqli
{
    $host = '';
    $db   = '';
    $user = '';
    $pass = '';

    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        die('Connection failed: (' . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }
    return $mysqli;
}

/**
 * Attempt to log in with email & MD5-hashed password and retrieve user role.
 *
 * @param mysqli $db
 * @param string $email
 * @param string $password  Plain-text password
 * @return array|false       Returns associative array of user data on success, false on failure
 */
function authenticate(mysqli $db, string $email, string $password)
{
    $stmt = $db->prepare('SELECT user_id, nname, email, role_id FROM user WHERE email = ? AND password = ?');
    $hashed = md5(trim($password));
    $stmt->bind_param('ss', $email, $hashed);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nname, $emailDb, $roleId);
        $stmt->fetch();
        $stmt->close();
        return ['id' => $id, 'nname' => $nname, 'email' => $emailDb, 'role_id' => $roleId];
    }
    $stmt->close();
    return false;
}

function handleLogin(mysqli $db): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return null;
    }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        return 'Please provide both email and password.';
    }
    $user = authenticate($db, $email, $password);
    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nname'] = $user['nname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role_id'] = $user['role_id'];
        switch ($user['role_id']) {
            case 1:
                header('Location: home_admin.php');
                break;
            case 2:
                header('Location: home_user.php');
                break;
            default:
                header('Location: index.php');
        }
        exit;
    }
    return 'Invalid email or password.';
}
