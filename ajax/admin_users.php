<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації адміністратора
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit();
}

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_users':
            echo json_encode(getUsers());
            break;
            
        case 'update_user':
            echo json_encode(updateUser());
            break;
            
        case 'delete_user':
            echo json_encode(deleteUser());
            break;
            
        case 'ban_user':
            echo json_encode(banUser());
            break;
            
        case 'get_user_details':
            echo json_encode(getUserDetails());
            break;
            
        case 'bulk_action':
            echo json_encode(bulkAction());
            break;
            
        case 'send_message':
            echo json_encode(sendMessageToUser());
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getUsers() {
    try {
        $db = new Database();
        
        // Параметри фільтрації та пагінації
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        
        $offset = ($page - 1) * $limit;
        
        // Будуємо WHERE умови
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($role)) {
            $whereConditions[] = "role = ?";
            $params[] = $role;
            $types .= 's';
        }
        
        if (!empty($status)) {
            if ($status === 'active') {
                $whereConditions[] = "status = 'active'";
            } elseif ($status === 'banned') {
                $whereConditions[] = "status = 'banned'";
            } elseif ($status === 'inactive') {
                $whereConditions[] = "status != 'active' AND status != 'banned'";
            }
        }
        
        if (!empty($search)) {
            $whereConditions[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ssss';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Дозволені поля для сортування
        $allowedSorts = ['id', 'username', 'email', 'created_at', 'last_login', 'status', 'role'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        // Отримуємо загальну кількість
        $countSql = "SELECT COUNT(*) as total FROM users $whereClause";
        
        if (!empty($params)) {
            $stmt = $db->prepare($countSql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $totalCount = $stmt->get_result()->fetch_assoc()['total'];
        } else {
            $totalCount = $db->query($countSql)->fetch_assoc()['total'];
        }
        
        // Отримуємо дані користувачів
        $sql = "
            SELECT 
                u.id, u.username, u.email, u.first_name, u.last_name, u.role, u.status,
                u.created_at, u.last_login, u.avatar, u.phone, u.user_type,
                (SELECT COUNT(*) FROM ads WHERE user_id = u.id) as ads_count,
                (SELECT COUNT(*) FROM ads WHERE user_id = u.id AND status = 'active') as active_ads_count
            FROM users u
            $whereClause
            ORDER BY $sort $order
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            // Форматуємо дати
            $row['created_at_formatted'] = date('d.m.Y H:i', strtotime($row['created_at']));
            $row['last_login_formatted'] = $row['last_login'] ? date('d.m.Y H:i', strtotime($row['last_login'])) : 'Ніколи';
            
            // Повне ім'я
            $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']) ?: $row['username'];
            
            // Avatar URL
            if ($row['avatar']) {
                $row['avatar_url'] = '/images/avatars/' . $row['avatar'];
            } else {
                $row['avatar_url'] = '/images/default-avatar.svg';
            }
            
            // Статус бейдж
            $statusMap = [
                'active' => ['class' => 'success', 'text' => 'Активний'],
                'banned' => ['class' => 'danger', 'text' => 'Заблокований'],
                'pending' => ['class' => 'warning', 'text' => 'Очікує'],
                'inactive' => ['class' => 'secondary', 'text' => 'Неактивний']
            ];
            $row['status_badge'] = $statusMap[$row['status']] ?? $statusMap['inactive'];
            
            // Роль бейдж
            $roleMap = [
                'admin' => ['class' => 'danger', 'text' => 'Адміністратор'],
                'moderator' => ['class' => 'warning', 'text' => 'Модератор'],
                'user' => ['class' => 'primary', 'text' => 'Користувач'],
                'partner' => ['class' => 'info', 'text' => 'Партнер']
            ];
            $row['role_badge'] = $roleMap[$row['role']] ?? $roleMap['user'];
            
            $users[] = $row;
        }
        
        return [
            'success' => true,
            'data' => [
                'users' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalCount / $limit),
                    'total_items' => (int)$totalCount,
                    'per_page' => $limit
                ]
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function updateUser() {
    try {
        $userId = (int)($_POST['user_id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';
        $phone = trim($_POST['phone'] ?? '');
        
        if (!$userId || empty($username) || empty($email)) {
            throw new Exception('Заповніть обов\'язкові поля');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Невірний формат email');
        }
        
        if (!in_array($role, ['admin', 'moderator', 'user', 'partner'])) {
            throw new Exception('Невірна роль користувача');
        }
        
        if (!in_array($status, ['active', 'banned', 'pending', 'inactive'])) {
            throw new Exception('Невірний статус користувача');
        }
        
        $db = new Database();
        
        // Перевіряємо унікальність username та email
        $stmt = $db->prepare("
            SELECT id FROM users 
            WHERE (username = ? OR email = ?) AND id != ?
        ");
        $stmt->bind_param("ssi", $username, $email, $userId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            throw new Exception('Користувач з таким логіном або email вже існує');
        }
        
        // Оновлюємо користувача
        $stmt = $db->prepare("
            UPDATE users 
            SET username = ?, email = ?, first_name = ?, last_name = ?, 
                role = ?, status = ?, phone = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("sssssssi", $username, $email, $firstName, $lastName, $role, $status, $phone, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення користувача');
        }
        
        logActivity('user_update', "Користувача ID:$userId оновлено", [
            'user_id' => $userId,
            'changes' => [
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'status' => $status
            ]
        ]);
        
        return [
            'success' => true,
            'message' => 'Користувача успішно оновлено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function deleteUser() {
    try {
        $userId = (int)($_POST['user_id'] ?? 0);
        
        if (!$userId) {
            throw new Exception('Невірний ID користувача');
        }
        
        // Перевіряємо, що не видаляємо себе
        if ($userId == $_SESSION['user_id']) {
            throw new Exception('Не можна видалити самого себе');
        }
        
        $db = new Database();
        
        // Отримуємо інформацію про користувача
        $stmt = $db->prepare("SELECT username, role FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            throw new Exception('Користувача не знайдено');
        }
        
        // Перевіряємо, що не видаляємо останнього адміністратора
        if ($user['role'] === 'admin') {
            $stmt = $db->prepare("SELECT COUNT(*) as admin_count FROM users WHERE role = 'admin'");
            $stmt->execute();
            $adminCount = $stmt->get_result()->fetch_assoc()['admin_count'];
            
            if ($adminCount <= 1) {
                throw new Exception('Не можна видалити останнього адміністратора');
            }
        }
        
        // Видаляємо аватар користувача
        $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $avatar = $stmt->get_result()->fetch_assoc()['avatar'];
        
        if ($avatar && file_exists("../images/avatars/$avatar")) {
            unlink("../images/avatars/$avatar");
        }
        
        // Видаляємо користувача (CASCADE видалить пов'язані записи)
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення користувача');
        }
        
        logActivity('user_delete', "Користувача '{$user['username']}' (ID:$userId) видалено", [
            'deleted_user_id' => $userId,
            'deleted_username' => $user['username'],
            'deleted_role' => $user['role']
        ]);
        
        return [
            'success' => true,
            'message' => 'Користувача успішно видалено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function banUser() {
    try {
        $userId = (int)($_POST['user_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $duration = $_POST['duration'] ?? 'permanent'; // 'day', 'week', 'month', 'permanent'
        
        if (!$userId) {
            throw new Exception('Невірний ID користувача');
        }
        
        if ($userId == $_SESSION['user_id']) {
            throw new Exception('Не можна заблокувати самого себе');
        }
        
        $db = new Database();
        
        // Обчислюємо дату розблокування
        $unbanDate = null;
        if ($duration !== 'permanent') {
            $intervals = [
                'day' => '+1 day',
                'week' => '+1 week',
                'month' => '+1 month'
            ];
            
            if (isset($intervals[$duration])) {
                $unbanDate = date('Y-m-d H:i:s', strtotime($intervals[$duration]));
            }
        }
        
        // Оновлюємо статус користувача
        $stmt = $db->prepare("
            UPDATE users 
            SET status = 'banned', ban_reason = ?, ban_until = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $reason, $unbanDate, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка блокування користувача');
        }
        
        logActivity('user_ban', "Користувача ID:$userId заблоковано. Причина: $reason", [
            'user_id' => $userId,
            'reason' => $reason,
            'duration' => $duration,
            'unban_date' => $unbanDate
        ]);
        
        return [
            'success' => true,
            'message' => 'Користувача успішно заблоковано'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getUserDetails() {
    try {
        $userId = (int)($_GET['user_id'] ?? 0);
        
        if (!$userId) {
            throw new Exception('Невірний ID користувача');
        }
        
        $db = new Database();
        
        // Отримуємо детальну інформацію
        $stmt = $db->prepare("
            SELECT u.*, 
                   (SELECT COUNT(*) FROM ads WHERE user_id = u.id) as total_ads,
                   (SELECT COUNT(*) FROM ads WHERE user_id = u.id AND status = 'active') as active_ads,
                   (SELECT COUNT(*) FROM ads WHERE user_id = u.id AND status = 'pending') as pending_ads,
                   (SELECT COUNT(*) FROM ad_views WHERE user_id = u.id) as total_views,
                   (SELECT COUNT(*) FROM favorites WHERE user_id = u.id) as favorites_count
            FROM users u
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            throw new Exception('Користувача не знайдено');
        }
        
        // Останні оголошення
        $stmt = $db->prepare("
            SELECT id, title, status, created_at, views_count, favorites_count
            FROM ads 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user['recent_ads'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Остання активність
        $stmt = $db->prepare("
            SELECT action, description, created_at
            FROM activity_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user['recent_activity'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Статистика по місяцях (останні 6 місяців)
        $stmt = $db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as ads_count
            FROM ads 
            WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user['monthly_stats'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $user
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function bulkAction() {
    try {
        $action = $_POST['bulk_action'] ?? '';
        $userIds = $_POST['user_ids'] ?? [];
        
        if (empty($action) || empty($userIds) || !is_array($userIds)) {
            throw new Exception('Невірні параметри масової операції');
        }
        
        $userIds = array_map('intval', $userIds);
        $userIds = array_filter($userIds, function($id) { return $id > 0; });
        
        // Видаляємо поточного користувача зі списку
        $userIds = array_filter($userIds, function($id) {
            return $id != $_SESSION['user_id'];
        });
        
        if (empty($userIds)) {
            throw new Exception('Не вибрано жодного користувача для операції');
        }
        
        $db = new Database();
        $affected = 0;
        
        switch ($action) {
            case 'activate':
                $stmt = $db->prepare("
                    UPDATE users 
                    SET status = 'active', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'ban':
                $stmt = $db->prepare("
                    UPDATE users 
                    SET status = 'banned', ban_reason = 'Масове блокування', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'set_role_user':
                $stmt = $db->prepare("
                    UPDATE users 
                    SET role = 'user', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'set_role_moderator':
                $stmt = $db->prepare("
                    UPDATE users 
                    SET role = 'moderator', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'delete':
                // Перевіряємо, що не видаляємо останнього адміністратора
                $stmt = $db->prepare("
                    SELECT COUNT(*) as admin_count 
                    FROM users 
                    WHERE role = 'admin' AND id NOT IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $remainingAdmins = $stmt->get_result()->fetch_assoc()['admin_count'];
                
                if ($remainingAdmins < 1) {
                    throw new Exception('Не можна видалити всіх адміністраторів');
                }
                
                // Видаляємо аватари
                $stmt = $db->prepare("
                    SELECT avatar FROM users 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?) AND avatar IS NOT NULL
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $avatars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                foreach ($avatars as $avatar) {
                    if (file_exists("../images/avatars/{$avatar['avatar']}")) {
                        unlink("../images/avatars/{$avatar['avatar']}");
                    }
                }
                
                // Видаляємо користувачів
                $stmt = $db->prepare("
                    DELETE FROM users 
                    WHERE id IN (" . str_repeat('?,', count($userIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($userIds)), ...$userIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            default:
                throw new Exception('Невідома масова операція');
        }
        
        logActivity('users_bulk_action', "Масова операція '$action' виконана для $affected користувачів", [
            'action' => $action,
            'user_ids' => $userIds,
            'affected' => $affected
        ]);
        
        return [
            'success' => true,
            'message' => "Операція виконана для $affected користувачів"
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function sendMessageToUser() {
    try {
        $userId = (int)($_POST['user_id'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (!$userId || empty($subject) || empty($message)) {
            throw new Exception('Заповніть всі поля');
        }
        
        $db = new Database();
        
        // Отримуємо email користувача
        $stmt = $db->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            throw new Exception('Користувача не знайдено');
        }
        
        // Зберігаємо повідомлення в базу
        $stmt = $db->prepare("
            INSERT INTO admin_messages (user_id, admin_id, subject, message, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $adminId = $_SESSION['user_id'];
        $stmt->bind_param("iiss", $userId, $adminId, $subject, $message);
        $stmt->execute();
        
        // TODO: Відправити email (якщо налаштовано)
        // sendEmailToUser($user['email'], $subject, $message);
        
        logActivity('admin_message', "Повідомлення надіслано користувачу {$user['username']}: $subject", [
            'recipient_id' => $userId,
            'subject' => $subject
        ]);
        
        return [
            'success' => true,
            'message' => 'Повідомлення успішно надіслано'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function logActivity($action, $description, $data = []) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, data, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $userId = $_SESSION['user_id'];
        $dataJson = json_encode($data);
        $stmt->bind_param("isss", $userId, $action, $description, $dataJson);
        $stmt->execute();
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}
?>