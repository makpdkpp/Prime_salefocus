<?php
// ปิด error display ใน production (แนะนำให้ใช้ ENV ตรวจสอบ dev/prod จริง)
if (empty($_SERVER['DEV_ENV'])) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}

// ตั้งค่า session cookie ให้ปลอดภัย (ควรเรียกก่อน session_start())
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/**
 * Establish a database connection and return the mysqli instance
 *
 * @return mysqli
 */
function connectDb(): mysqli
{
        $host = '127.0.0.1';
        $db   = 'sale1';
        $user = 'root';
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
    $stmt = $db->prepare('SELECT user_id, nname, email, role_id, avatar_path FROM user WHERE email = ? AND password = ?');
    $hashed = md5(trim($password));
    $stmt->bind_param('ss', $email, $hashed);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $nname, $emailDb, $roleId, $avatarPath);
        $stmt->fetch();
        $stmt->close();
        // ตรวจสอบ/validate path ของ avatar (อนุญาตเฉพาะ path ที่ขึ้นต้น uploads/avatars หรือ dist/img)
        $safeAvatar = '';
        if ($avatarPath && (preg_match('#^(uploads/avatars/|dist/img/)#', $avatarPath) && !preg_match('#\.\./#', $avatarPath))) {
            $safeAvatar = htmlspecialchars($avatarPath, ENT_QUOTES, 'UTF-8');
        }
        return ['id' => $id, 'nname' => $nname, 'email' => $emailDb, 'role_id' => $roleId, 'avatar_path' => $safeAvatar];
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
        $_SESSION['avatar'] = $user['avatar_path'] ?? '';
        switch ($user['role_id']) {
            case 1:
                header('Location: home_admin.php');
                break;
            case 2:
                header('Location: home_team.php');
                break;
            case 3:
                header('Location: home_user.php');
                break;
            default:
                header('Location: index.php');
        }
        exit;
    }
    return 'Invalid email or password.';
}
