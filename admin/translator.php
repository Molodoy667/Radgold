<?php
session_start();

// Підключення до бази даних
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';

// Перевіряємо аутентифікацію адміністратора
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . SITE_URL . '/admin/login');
    exit();
}

// Обробка AJAX запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_translation':
            $translation_key = trim($_POST['translation_key'] ?? '');
            $uk = trim($_POST['uk'] ?? '');
            $ru = trim($_POST['ru'] ?? '');
            $en = trim($_POST['en'] ?? '');
            $category = trim($_POST['category'] ?? 'general');
            $id = intval($_POST['id'] ?? 0);
            
            if (empty($translation_key)) {
                echo json_encode(['success' => false, 'message' => 'Ключ перекладу не може бути порожнім']);
                exit;
            }
            
            try {
                if ($id > 0) {
                    // Оновлення існуючого перекладу
                    $stmt = $db->prepare("UPDATE translations SET translation_key = ?, uk = ?, ru = ?, en = ?, category = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->bind_param("sssssi", $translation_key, $uk, $ru, $en, $category, $id);
                } else {
                    // Додавання нового перекладу
                    $stmt = $db->prepare("INSERT INTO translations (translation_key, uk, ru, en, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $translation_key, $uk, $ru, $en, $category);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Переклад збережено успішно']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Помилка збереження: ' . $db->error]);
                }
                $stmt->close();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_translation':
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Невірний ID перекладу']);
                exit;
            }
            
            try {
                $stmt = $db->prepare("DELETE FROM translations WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Переклад видалено успішно']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Помилка видалення: ' . $db->error]);
                }
                $stmt->close();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
            
        case 'get_translation':
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Невірний ID перекладу']);
                exit;
            }
            
            try {
                $stmt = $db->prepare("SELECT * FROM translations WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    echo json_encode(['success' => true, 'data' => $row]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Переклад не знайдено']);
                }
                $stmt->close();
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Помилка: ' . $e->getMessage()]);
            }
            exit;
    }
}

// Отримання списку перекладів з пагінацією та фільтрацією
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(translation_key LIKE ? OR uk LIKE ? OR ru LIKE ? OR en LIKE ?)";
    $search_param = '%' . $search . '%';
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}

if (!empty($category_filter)) {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Загальна кількість
$count_sql = "SELECT COUNT(*) as total FROM translations $where_clause";
$stmt = $db->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_count = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Список перекладів
$sql = "SELECT * FROM translations $where_clause ORDER BY category, translation_key LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$translations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Список категорій
$categories_result = $db->query("SELECT DISTINCT category FROM translations ORDER BY category");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);

$total_pages = ceil($total_count / $limit);

include 'header.php';
?>

<style>
.translator-container {
    background: #f8f9fa;
    min-height: calc(100vh - 120px);
}

.translation-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: none;
    transition: all 0.3s ease;
}

.translation-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.translation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
}

.language-tabs {
    border: none;
    background: #f8f9fa;
}

.language-tabs .nav-link {
    border: none;
    background: transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    margin-right: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.language-tabs .nav-link.active {
    background: white;
    color: #495057;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.translation-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    resize: vertical;
    min-height: 80px;
}

.translation-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-action {
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #17a2b8;
    color: white;
    border: none;
}

.btn-edit:hover {
    background: #138496;
    transform: translateY(-1px);
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: none;
}

.btn-delete:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.pagination-custom .page-link {
    border: none;
    color: #667eea;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    margin: 0 0.25rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pagination-custom .page-link:hover {
    background: #667eea;
    color: white;
    transform: translateY(-1px);
}

.pagination-custom .page-item.active .page-link {
    background: #667eea;
    color: white;
}

.search-box {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.search-box:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.category-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 500;
}

.add-translation-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.add-translation-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}
</style>

<div class="translator-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="translation-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2"><i class="fas fa-language me-3"></i>Управління перекладами</h2>
                            <p class="mb-0 opacity-75">Редагуйте переклади сайту на трьох мовах</p>
                        </div>
                        <button class="btn add-translation-btn" data-bs-toggle="modal" data-bs-target="#translationModal">
                            <i class="fas fa-plus me-2"></i>Додати переклад
                        </button>
                    </div>
                </div>

                <!-- Пошук та фільтри -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control search-box" id="searchInput" 
                                   placeholder="Пошук по ключу або тексту..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-primary" type="button" onclick="performSearch()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="categoryFilter" onchange="performSearch()">
                            <option value="">Всі категорії</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                        <?php echo $category_filter === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($cat['category']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="translator" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Очистити
                        </a>
                    </div>
                </div>

                <!-- Статистика -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-primary text-white">
                            <div class="card-body">
                                <h4><?php echo $total_count; ?></h4>
                                <p class="mb-0">Всього перекладів</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-0 bg-success text-white">
                            <div class="card-body">
                                <h4><?php echo count($categories); ?></h4>
                                <p class="mb-0">Категорій</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Список перекладів -->
                <div class="row">
                    <?php if (empty($translations)): ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-language fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Переклади не знайдені</h4>
                                <p class="text-muted">Спробуйте змінити параметри пошуку або додайте новий переклад</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($translations as $translation): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="translation-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($translation['translation_key']); ?></h6>
                                            <span class="category-badge"><?php echo ucfirst($translation['category']); ?></span>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="editTranslation(<?php echo $translation['id']; ?>)">
                                                        <i class="fas fa-edit me-2"></i>Редагувати
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteTranslation(<?php echo $translation['id']; ?>)">
                                                        <i class="fas fa-trash me-2"></i>Видалити
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-tabs language-tabs mb-3" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#uk-<?php echo $translation['id']; ?>">🇺🇦 UA</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#ru-<?php echo $translation['id']; ?>">🇷🇺 RU</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#en-<?php echo $translation['id']; ?>">🇺🇸 EN</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="uk-<?php echo $translation['id']; ?>">
                                                <div class="translation-preview">
                                                    <?php echo !empty($translation['uk']) ? nl2br(htmlspecialchars($translation['uk'])) : '<em class="text-muted">Переклад відсутній</em>'; ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="ru-<?php echo $translation['id']; ?>">
                                                <div class="translation-preview">
                                                    <?php echo !empty($translation['ru']) ? nl2br(htmlspecialchars($translation['ru'])) : '<em class="text-muted">Переклад відсутній</em>'; ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="en-<?php echo $translation['id']; ?>">
                                                <div class="translation-preview">
                                                    <?php echo !empty($translation['en']) ? nl2br(htmlspecialchars($translation['en'])) : '<em class="text-muted">Переклад відсутній</em>'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Пагінація -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Пагінація перекладів">
                        <ul class="pagination pagination-custom justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category_filter); ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно для редагування перекладу -->
<div class="modal fade" id="translationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-language me-2"></i>
                    <span id="modalTitle">Додати переклад</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="translationForm">
                <div class="modal-body">
                    <input type="hidden" id="translationId" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="translationKey" class="form-label">Ключ перекладу <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="translationKey" name="translation_key" required>
                            <small class="form-text text-muted">Унікальний ідентифікатор для перекладу (наприклад: button.save)</small>
                        </div>
                        <div class="col-md-4">
                            <label for="translationCategory" class="form-label">Категорія</label>
                            <input type="text" class="form-control" id="translationCategory" name="category" value="general">
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#edit-uk">🇺🇦 Українська</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#edit-ru">🇷🇺 Російська</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#edit-en">🇺🇸 Англійська</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="edit-uk">
                            <label for="translationUk" class="form-label">Переклад українською</label>
                            <textarea class="form-control translation-input" id="translationUk" name="uk" rows="4"></textarea>
                        </div>
                        <div class="tab-pane" id="edit-ru">
                            <label for="translationRu" class="form-label">Переклад російською</label>
                            <textarea class="form-control translation-input" id="translationRu" name="ru" rows="4"></textarea>
                        </div>
                        <div class="tab-pane" id="edit-en">
                            <label for="translationEn" class="form-label">Переклад англійською</label>
                            <textarea class="form-control translation-input" id="translationEn" name="en" rows="4"></textarea>
                        </div>
                    </div>
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
function performSearch() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const url = new URL(window.location);
    
    if (search) {
        url.searchParams.set('search', search);
    } else {
        url.searchParams.delete('search');
    }
    
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    
    url.searchParams.delete('page'); // Скидаємо пагінацію при пошуку
    window.location.href = url.toString();
}

// Пошук по Enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

function editTranslation(id) {
    fetch('translator', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=get_translation&id=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const t = data.data;
            document.getElementById('modalTitle').textContent = 'Редагувати переклад';
            document.getElementById('translationId').value = t.id;
            document.getElementById('translationKey').value = t.translation_key;
            document.getElementById('translationCategory').value = t.category;
            document.getElementById('translationUk').value = t.uk || '';
            document.getElementById('translationRu').value = t.ru || '';
            document.getElementById('translationEn').value = t.en || '';
            
            new bootstrap.Modal(document.getElementById('translationModal')).show();
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Помилка завантаження даних');
    });
}

function deleteTranslation(id) {
    if (!confirm('Ви впевнені, що хочете видалити цей переклад?')) {
        return;
    }
    
    fetch('translator', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=delete_translation&id=' + id
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
document.getElementById('translationModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('translationForm').reset();
    document.getElementById('modalTitle').textContent = 'Додати переклад';
    document.getElementById('translationId').value = '';
});

// Обробка форми
document.getElementById('translationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'save_translation');
    
    fetch('translator', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('translationModal')).hide();
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
</script>

<?php include 'footer.php'; ?>