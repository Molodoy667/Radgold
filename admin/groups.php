<?php
session_start();

// Підключення до бази даних
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';

// Перевіряємо аутентифікацію адміністратора
if (!isLoggedIn() || !hasPermission('admin.groups')) {
    header('Location: ' . SITE_URL . '/admin/login');
    exit();
}

// Обробка AJAX запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_group':
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $color = trim($_POST['color'] ?? '#007bff');
            $permissions = $_POST['permissions'] ?? '{}';
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            $sort_order = intval($_POST['sort_order'] ?? 0);
            $id = intval($_POST['id'] ?? 0);
            
            if (empty($name) || empty($slug)) {
                echo json_encode(['success' => false, 'message' => 'Назва та slug обов\'язкові']);
                exit;
            }
            
            // Валідація JSON
            if (!json_decode($permissions)) {
                echo json_encode(['success' => false, 'message' => 'Невірний формат дозволів']);
                exit;
            }
            
            try {
                if ($id > 0) {
                    // Перевіряємо чи не системна група
                    $existingGroup = getUserGroupById($id);
                    if ($existingGroup && $existingGroup['is_system'] && !isSuperAdmin()) {
                        echo json_encode(['success' => false, 'message' => 'Неможливо редагувати системну групу']);
                        exit;
                    }
                    
                    // Оновлення існуючої групи
                    $stmt = $db->prepare("UPDATE user_groups SET name = ?, slug = ?, description = ?, permissions = ?, is_default = ?, color = ?, sort_order = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("ssssisii", $name, $slug, $description, $permissions, $is_default, $color, $sort_order, $id);
                } else {
                    // Додавання нової групи
                    $stmt = $db->prepare("INSERT INTO user_groups (name, slug, description, permissions, is_default, color, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssisr", $name, $slug, $description, $permissions, $is_default, $color, $sort_order);
                }
                
                // Якщо це дефолтна група, скидаємо is_default у всіх інших
                if ($is_default) {
                    $db->query("UPDATE user_groups SET is_default = 0 WHERE id != " . ($id ?: 0));
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Групу збережено успішно']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Помилка збереження: ' . $db->error]);
                }
                $stmt->close();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_group':
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Невірний ID групи']);
                exit;
            }
            
            try {
                // Перевіряємо чи не системна група
                $group = getUserGroupById($id);
                if ($group && $group['is_system'] && !isSuperAdmin()) {
                    echo json_encode(['success' => false, 'message' => 'Неможливо видалити системну групу']);
                    exit;
                }
                
                // Перевіряємо чи є користувачі в цій групі
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE group_id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                if ($row['count'] > 0) {
                    echo json_encode(['success' => false, 'message' => 'Неможливо видалити групу яка містить користувачів']);
                    exit;
                }
                
                $stmt = $db->prepare("DELETE FROM user_groups WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    // Також видаляємо дозволи групи
                    $db->prepare("DELETE FROM group_permissions WHERE group_id = ?")->execute([$id]);
                    echo json_encode(['success' => true, 'message' => 'Групу видалено успішно']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Помилка видалення: ' . $db->error]);
                }
                $stmt->close();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
            
        case 'get_group':
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Невірний ID групи']);
                exit;
            }
            
            try {
                $group = getUserGroupById($id);
                if ($group) {
                    echo json_encode(['success' => true, 'data' => $group]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Групу не знайдено']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
    }
}

// Отримання списку груп
$groups = getAllUserGroups();
$permissions = getAllPermissions();

// Групуємо дозволи за категоріями
$permissionsByCategory = [];
foreach ($permissions as $permission) {
    $permissionsByCategory[$permission['category']][] = $permission;
}

include 'header.php';
?>

<style>
.groups-container {
    background: #f8f9fa;
    min-height: calc(100vh - 120px);
}

.group-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: none;
    transition: all 0.3s ease;
    border-left: 4px solid;
}

.group-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.groups-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
}

.badge-group {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.permission-category {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #dee2e6;
}

.permission-category h6 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.permission-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.permission-item input[type="checkbox"] {
    margin-right: 0.5rem;
}

.add-group-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.add-group-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.system-badge {
    background: #dc3545;
    color: white;
}

.default-badge {
    background: #28a745;
    color: white;
}

.users-count {
    font-size: 0.9rem;
    color: #6c757d;
}

.json-editor {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    min-height: 200px;
}
</style>

<div class="groups-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="groups-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2"><i class="fas fa-users-cog me-3"></i>Управління групами користувачів</h2>
                            <p class="mb-0 opacity-75">Створюйте та налаштовуйте групи з різними рівнями доступу</p>
                        </div>
                        <button class="btn add-group-btn" data-bs-toggle="modal" data-bs-target="#groupModal">
                            <i class="fas fa-plus me-2"></i>Додати групу
                        </button>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-primary text-white">
                            <div class="card-body">
                                <h4><?php echo count($groups); ?></h4>
                                <p class="mb-0">Всього груп</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-success text-white">
                            <div class="card-body">
                                <h4><?php echo count(array_filter($groups, fn($g) => $g['is_system'])); ?></h4>
                                <p class="mb-0">Системних груп</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-info text-white">
                            <div class="card-body">
                                <h4><?php echo count($permissions); ?></h4>
                                <p class="mb-0">Дозволів</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-warning text-white">
                            <div class="card-body">
                                <h4><?php echo count($permissionsByCategory); ?></h4>
                                <p class="mb-0">Категорій дозволів</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список груп -->
                <div class="row">
                    <?php foreach ($groups as $group): ?>
                        <?php
                        // Підраховуємо кількість користувачів в групі
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE group_id = ?");
                        $stmt->bind_param("i", $group['id']);
                        $stmt->execute();
                        $userCount = $stmt->get_result()->fetch_assoc()['count'];
                        ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="group-card h-100" style="border-left-color: <?php echo $group['color']; ?>;">
                                <div class="card-header d-flex justify-content-between align-items-start bg-light">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2 fw-bold d-flex align-items-center">
                                            <i class="fas fa-users me-2" style="color: <?php echo $group['color']; ?>;"></i>
                                            <?php echo htmlspecialchars($group['name']); ?>
                                        </h5>
                                        <div class="mb-2">
                                            <?php if ($group['is_system']): ?>
                                                <span class="badge system-badge me-1">Системна</span>
                                            <?php endif; ?>
                                            <?php if ($group['is_default']): ?>
                                                <span class="badge default-badge">За замовчуванням</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($group['description']); ?></p>
                                        <div class="users-count">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo $userCount; ?> користувач<?php echo $userCount == 1 ? '' : ($userCount < 5 ? 'і' : 'ів'); ?>
                                        </div>
                                    </div>
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="editGroup(<?php echo $group['id']; ?>)">
                                                    <i class="fas fa-edit me-2"></i>Редагувати
                                                </a>
                                            </li>
                                            <?php if (!$group['is_system'] || isSuperAdmin()): ?>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteGroup(<?php echo $group['id']; ?>)">
                                                        <i class="fas fa-trash me-2"></i>Видалити
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-muted">Дозволи:</h6>
                                    <?php 
                                    $groupPermissions = $group['permissions'] ?? [];
                                    $permissionCount = 0;
                                    
                                    function countPermissions($perms) {
                                        $count = 0;
                                        if (is_array($perms)) {
                                            foreach ($perms as $value) {
                                                if ($value === true) {
                                                    $count++;
                                                } elseif (is_array($value)) {
                                                    $count += countPermissions($value);
                                                }
                                            }
                                        }
                                        return $count;
                                    }
                                    
                                    $permissionCount = countPermissions($groupPermissions);
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge" style="background-color: <?php echo $group['color']; ?>;">
                                            <?php echo $permissionCount; ?> дозвол<?php echo $permissionCount == 1 ? '' : ($permissionCount < 5 ? 'и' : 'ів'); ?>
                                        </span>
                                        <small class="text-muted">Порядок: <?php echo $group['sort_order']; ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно для редагування групи -->
<div class="modal fade" id="groupModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users-cog me-2"></i>
                    <span id="modalTitle">Додати групу</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="groupForm">
                <div class="modal-body">
                    <input type="hidden" id="groupId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="groupName" class="form-label">Назва групи <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="groupName" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="groupSlug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="groupSlug" name="slug" required>
                        </div>
                        <div class="col-md-3">
                            <label for="groupColor" class="form-label">Колір</label>
                            <input type="color" class="form-control form-control-color" id="groupColor" name="color" value="#007bff">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="groupDescription" class="form-label">Опис</label>
                            <textarea class="form-control" id="groupDescription" name="description" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="sortOrder" class="form-label">Порядок сортування</label>
                            <input type="number" class="form-control" id="sortOrder" name="sort_order" value="0">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="isDefault" name="is_default">
                                <label class="form-check-label" for="isDefault">
                                    Група за замовчуванням
                                </label>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-3">Дозволи групи:</h6>
                    <div class="permissions-grid">
                        <?php foreach ($permissionsByCategory as $category => $categoryPermissions): ?>
                            <div class="permission-category">
                                <h6><?php echo ucfirst($category); ?></h6>
                                <?php foreach ($categoryPermissions as $permission): ?>
                                    <div class="permission-item">
                                        <input type="checkbox" 
                                               id="perm_<?php echo $permission['id']; ?>" 
                                               value="<?php echo $permission['slug']; ?>"
                                               data-category="<?php echo $permission['category']; ?>">
                                        <label for="perm_<?php echo $permission['id']; ?>" class="form-check-label">
                                            <?php echo htmlspecialchars($permission['name']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="mt-4 mb-3">JSON дозволів (розширені налаштування):</h6>
                    <textarea class="form-control json-editor" id="permissionsJson" name="permissions" rows="8">{}</textarea>
                    <small class="form-text text-muted">
                        Редагуйте JSON безпосередньо для тонкого налаштування дозволів
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Зберегти
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Автогенерація slug з назви
document.getElementById('groupName').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('groupSlug').value = slug;
});

// Оновлення JSON при зміні чекбоксів
function updatePermissionsJson() {
    const permissions = {};
    const checkboxes = document.querySelectorAll('input[type="checkbox"][data-category]');
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const parts = checkbox.value.split('.');
            let current = permissions;
            
            for (let i = 0; i < parts.length; i++) {
                if (i === parts.length - 1) {
                    current[parts[i]] = true;
                } else {
                    if (!current[parts[i]]) {
                        current[parts[i]] = {};
                    }
                    current = current[parts[i]];
                }
            }
        }
    });
    
    document.getElementById('permissionsJson').value = JSON.stringify(permissions, null, 2);
}

// Оновлення чекбоксів при зміні JSON
function updateCheckboxesFromJson() {
    try {
        const permissions = JSON.parse(document.getElementById('permissionsJson').value);
        const checkboxes = document.querySelectorAll('input[type="checkbox"][data-category]');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            const parts = checkbox.value.split('.');
            let current = permissions;
            let hasPermission = true;
            
            for (let part of parts) {
                if (current && typeof current === 'object' && current[part] !== undefined) {
                    current = current[part];
                } else {
                    hasPermission = false;
                    break;
                }
            }
            
            if (hasPermission && current === true) {
                checkbox.checked = true;
            }
        });
    } catch (e) {
        console.error('Invalid JSON:', e);
    }
}

// Слухачі подій
document.querySelectorAll('input[type="checkbox"][data-category]').forEach(checkbox => {
    checkbox.addEventListener('change', updatePermissionsJson);
});

document.getElementById('permissionsJson').addEventListener('input', updateCheckboxesFromJson);

function editGroup(id) {
    fetch('groups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=get_group&id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const g = data.data;
            document.getElementById('modalTitle').textContent = 'Редагувати групу';
            document.getElementById('groupId').value = g.id;
            document.getElementById('groupName').value = g.name;
            document.getElementById('groupSlug').value = g.slug;
            document.getElementById('groupDescription').value = g.description || '';
            document.getElementById('groupColor').value = g.color || '#007bff';
            document.getElementById('sortOrder').value = g.sort_order || 0;
            document.getElementById('isDefault').checked = g.is_default == 1;
            
            const permissions = g.permissions || {};
            document.getElementById('permissionsJson').value = JSON.stringify(permissions, null, 2);
            updateCheckboxesFromJson();
            
            new bootstrap.Modal(document.getElementById('groupModal')).show();
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка завантаження даних');
    });
}

function deleteGroup(id) {
    if (!confirm('Ви впевнені, що хочете видалити цю групу?')) {
        return;
    }
    
    fetch('groups', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=delete_group&id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка видалення');
    });
}

// Очищення форми при закритті модального вікна
document.getElementById('groupModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('groupForm').reset();
    document.getElementById('modalTitle').textContent = 'Додати групу';
    document.getElementById('groupId').value = '';
    document.getElementById('permissionsJson').value = '{}';
    document.querySelectorAll('input[type="checkbox"][data-category]').forEach(cb => cb.checked = false);
});

// Обробка форми
document.getElementById('groupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_group');
    
    fetch('groups', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('groupModal')).hide();
            location.reload();
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка збереження');
    });
});

// Ініціалізація
updatePermissionsJson();
</script>

<?php include 'footer.php'; ?>