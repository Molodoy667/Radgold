<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Отримання ID оголошення
$adId = (int)($_GET['id'] ?? 0);

if (!$adId) {
    header('Location: ads.php');
    exit();
}

// Отримання даних оголошення
$ad = getAdDetails($adId);

if (!$ad) {
    header('Location: ads.php');
    exit();
}

// Збільшення лічильника переглядів
incrementAdViews($adId);

// Отримання додаткових даних
$images = getAdImages($adId);
$attributes = getAdAttributes($adId);
$similarAds = getSimilarAds($ad['category_id'], $adId, 6);
$seller = getUserInfo($ad['user_id']);
$isFavorite = isLoggedIn() ? isAdInFavorites($adId, $_SESSION['user_id']) : false;

// Мета дані для SEO
$pageTitle = htmlspecialchars($ad['title']) . ' - ' . getSiteName();
$pageDescription = truncateText(strip_tags($ad['description']), 160);
$mainImage = !empty($images) ? getSiteUrl('images/uploads/' . $images[0]['filename']) : getSiteUrl('images/no-image.svg');

include '../themes/header.php';
?>

<div class="ad-detail-page">
    <!-- Breadcrumb -->
    <section class="breadcrumb-section py-3 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo getSiteUrl(); ?>">Головна</a></li>
                    <li class="breadcrumb-item"><a href="ads.php">Оголошення</a></li>
                    <li class="breadcrumb-item"><a href="ads.php?category=<?php echo $ad['category_id']; ?>"><?php echo htmlspecialchars($ad['category_name']); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($ad['title']); ?></li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <section class="ad-content py-5">
        <div class="container">
            <div class="row">
                <!-- Left Column - Images and Info -->
                <div class="col-lg-8">
                    <!-- Ad Header -->
                    <div class="ad-header mb-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="ad-title"><?php echo htmlspecialchars($ad['title']); ?></h1>
                                <div class="ad-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                        <?php echo htmlspecialchars($ad['location_name']); ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-clock text-muted me-1"></i>
                                        <?php echo timeAgo($ad['created_at']); ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-eye text-muted me-1"></i>
                                        <?php echo number_format($ad['views_count']); ?> переглядів
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-heart text-danger me-1"></i>
                                        <?php echo $ad['favorites_count']; ?> в улюблених
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ad-actions">
                                <?php if ($ad['price']): ?>
                                    <div class="ad-price mb-2">
                                        <?php echo number_format($ad['price']); ?> <?php echo $ad['currency']; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="action-buttons">
                                    <button class="btn btn-outline-danger" onclick="toggleFavorite(<?php echo $adId; ?>)" 
                                            title="<?php echo $isFavorite ? 'Видалити з улюблених' : 'Додати в улюблені'; ?>">
                                        <i class="fas fa-heart <?php echo $isFavorite ? 'text-danger' : ''; ?>"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="shareAd()">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="reportAd(<?php echo $adId; ?>)">
                                        <i class="fas fa-flag"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($ad['is_featured'] || $ad['is_urgent']): ?>
                            <div class="ad-badges mt-2">
                                <?php if ($ad['is_featured']): ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star me-1"></i>Рекомендоване
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($ad['is_urgent']): ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation me-1"></i>Термінове
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Image Gallery -->
                    <div class="image-gallery mb-4">
                        <?php if (!empty($images)): ?>
                            <div class="main-image mb-3">
                                <img src="<?php echo getSiteUrl('images/uploads/' . $images[0]['filename']); ?>" 
                                     alt="<?php echo htmlspecialchars($ad['title']); ?>" 
                                     class="img-fluid rounded main-ad-image" 
                                     id="mainImage">
                            </div>
                            
                            <?php if (count($images) > 1): ?>
                                <div class="image-thumbnails">
                                    <div class="row g-2">
                                        <?php foreach ($images as $index => $image): ?>
                                            <div class="col-3 col-md-2">
                                                <img src="<?php echo getSiteUrl('images/uploads/' . $image['filename']); ?>" 
                                                     alt="Зображення <?php echo $index + 1; ?>" 
                                                     class="img-fluid rounded thumbnail-image <?php echo $index === 0 ? 'active' : ''; ?>"
                                                     onclick="changeMainImage('<?php echo getSiteUrl('images/uploads/' . $image['filename']); ?>', this)">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="no-image">
                                <img src="<?php echo getSiteUrl('images/no-image.svg'); ?>" 
                                     alt="Зображення відсутнє" 
                                     class="img-fluid rounded">
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <div class="ad-description mb-4">
                        <h3>Опис</h3>
                        <div class="description-content">
                            <?php echo nl2br(htmlspecialchars($ad['description'])); ?>
                        </div>
                    </div>

                    <!-- Attributes -->
                    <?php if (!empty($attributes)): ?>
                        <div class="ad-attributes mb-4">
                            <h3>Характеристики</h3>
                            <div class="row">
                                <?php foreach ($attributes as $attr): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="attribute-item">
                                            <strong><?php echo htmlspecialchars($attr['name']); ?>:</strong>
                                            <span><?php echo htmlspecialchars($attr['value']); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Map -->
                    <?php if (!empty($ad['address'])): ?>
                        <div class="ad-location mb-4">
                            <h3>Місцезнаходження</h3>
                            <div class="location-info mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?php echo htmlspecialchars($ad['address']); ?>
                            </div>
                            <div id="adMap" class="ad-map rounded"></div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Column - Contact and Similar -->
                <div class="col-lg-4">
                    <!-- Contact Card -->
                    <div class="contact-card card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Контактна інформація
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="seller-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?php echo $seller['avatar'] ?: getSiteUrl('images/default-avatar.svg'); ?>" 
                                         alt="<?php echo htmlspecialchars($seller['name']); ?>" 
                                         class="seller-avatar me-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($seller['name']); ?></h6>
                                        <small class="text-muted">На сайті з <?php echo date('M Y', strtotime($seller['created_at'])); ?></small>
                                    </div>
                                </div>
                                
                                <div class="seller-rating mb-2">
                                    <?php $rating = $seller['rating'] ?? 0; ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $rating ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    <span class="ms-2"><?php echo $seller['reviews_count'] ?? 0; ?> відгуків</span>
                                </div>
                            </div>

                            <div class="contact-info">
                                <?php if ($ad['contact_phone']): ?>
                                    <div class="contact-item mb-2">
                                        <button class="btn btn-primary w-100" onclick="showPhone()">
                                            <i class="fas fa-phone me-2"></i>
                                            <span id="phoneBtn">Показати телефон</span>
                                        </button>
                                        <div id="phoneNumber" class="phone-number mt-2" style="display: none;">
                                            <a href="tel:<?php echo $ad['contact_phone']; ?>" class="btn btn-success w-100">
                                                <i class="fas fa-phone me-2"></i><?php echo $ad['contact_phone']; ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isLoggedIn()): ?>
                                    <button class="btn btn-outline-primary w-100 mb-2" onclick="startChat(<?php echo $ad['user_id']; ?>)">
                                        <i class="fas fa-comments me-2"></i>Написати повідомлення
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-primary w-100 mb-2" onclick="showLoginModal()">
                                        <i class="fas fa-comments me-2"></i>Написати повідомлення
                                    </button>
                                <?php endif; ?>

                                <?php if ($ad['contact_email']): ?>
                                    <a href="mailto:<?php echo $ad['contact_email']; ?>" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-envelope me-2"></i>Email
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Tips -->
                    <div class="safety-tips card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i>Поради безпеки
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="safety-list">
                                <li>Зустрічайтесь в людних місцях</li>
                                <li>Перевіряйте товар перед оплатою</li>
                                <li>Не передавайте гроші наперед</li>
                                <li>Довіряйте своїй інтуїції</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Similar Ads -->
                    <?php if (!empty($similarAds)): ?>
                        <div class="similar-ads">
                            <h5 class="mb-3">Схожі оголошення</h5>
                            <?php foreach ($similarAds as $similarAd): ?>
                                <div class="similar-ad-item mb-3">
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <?php $similarImage = getAdMainImage($similarAd['id']); ?>
                                            <img src="<?php echo $similarImage ?: getSiteUrl('images/no-image.svg'); ?>" 
                                                 alt="<?php echo htmlspecialchars($similarAd['title']); ?>"
                                                 class="img-fluid rounded">
                                        </div>
                                        <div class="col-8">
                                            <h6 class="similar-title">
                                                <a href="ad-detail.php?id=<?php echo $similarAd['id']; ?>">
                                                    <?php echo truncateText($similarAd['title'], 50); ?>
                                                </a>
                                            </h6>
                                            <?php if ($similarAd['price']): ?>
                                                <div class="similar-price text-primary fw-bold">
                                                    <?php echo number_format($similarAd['price']); ?> <?php echo $similarAd['currency']; ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($similarAd['location_name']); ?> • 
                                                <?php echo timeAgo($similarAd['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Скарга на оголошення</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <div class="mb-3">
                        <label class="form-label">Причина скарги</label>
                        <select class="form-select" name="reason" required>
                            <option value="">Оберіть причину</option>
                            <option value="spam">Спам</option>
                            <option value="fraud">Шахрайство</option>
                            <option value="inappropriate">Неприйнятний контент</option>
                            <option value="duplicate">Дублікат</option>
                            <option value="wrong_category">Невірна категорія</option>
                            <option value="other">Інше</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Додаткова інформація</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Опишіть проблему детальніше..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                <button type="button" class="btn btn-danger" onclick="submitReport()">Надіслати скаргу</button>
            </div>
        </div>
    </div>
</div>

<style>
.ad-detail-page {
    background: var(--theme-bg);
}

.ad-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.ad-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.meta-item {
    font-size: 0.9rem;
    color: #666;
}

.ad-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.main-ad-image {
    max-height: 500px;
    width: 100%;
    object-fit: cover;
    cursor: zoom-in;
}

.thumbnail-image {
    height: 80px;
    object-fit: cover;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.thumbnail-image:hover,
.thumbnail-image.active {
    opacity: 1;
}

.description-content {
    line-height: 1.6;
    font-size: 1.1rem;
}

.attribute-item {
    padding: 0.5rem;
    background: var(--theme-bg-secondary);
    border-radius: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ad-map {
    height: 300px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
}

.contact-card {
    position: sticky;
    top: 100px;
}

.seller-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.safety-list {
    list-style: none;
    padding: 0;
}

.safety-list li {
    padding: 0.25rem 0;
    border-bottom: 1px solid var(--theme-border);
}

.safety-list li:before {
    content: '✓';
    color: #28a745;
    font-weight: bold;
    margin-right: 0.5rem;
}

.similar-ad-item {
    border: 1px solid var(--theme-border);
    border-radius: 8px;
    padding: 1rem;
    transition: all 0.3s ease;
}

.similar-ad-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.similar-title a {
    text-decoration: none;
    color: inherit;
}

.similar-title a:hover {
    color: var(--primary-color);
}

.phone-number a {
    letter-spacing: 1px;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .ad-title {
        font-size: 1.5rem;
    }
    
    .ad-price {
        font-size: 1.5rem;
    }
    
    .ad-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .contact-card {
        position: static;
        margin-top: 2rem;
    }
}
</style>

<script>
// Image gallery functionality
function changeMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.classList.remove('active');
    });
    thumbnail.classList.add('active');
}

// Show phone number
let phoneShown = false;
function showPhone() {
    if (!phoneShown) {
        document.getElementById('phoneBtn').textContent = 'Приховати телефон';
        document.getElementById('phoneNumber').style.display = 'block';
        phoneShown = true;
        
        // Track phone view
        fetch('/ajax/track_phone_view.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ad_id: <?php echo $adId; ?> })
        });
    } else {
        document.getElementById('phoneBtn').textContent = 'Показати телефон';
        document.getElementById('phoneNumber').style.display = 'none';
        phoneShown = false;
    }
}

// Share ad
function shareAd() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($ad['title']); ?>',
            text: '<?php echo addslashes(truncateText($ad['description'], 100)); ?>',
            url: window.location.href
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Посилання скопійовано в буфер обміну!', 'success');
        });
    }
}

// Report ad
function reportAd(adId) {
    if (!isLoggedIn()) {
        showLoginModal();
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();
}

function submitReport() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    
    fetch('/ajax/report_ad.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            ad_id: <?php echo $adId; ?>,
            reason: formData.get('reason'),
            description: formData.get('description')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Скаргу надіслано. Дякуємо!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
        } else {
            showNotification(data.message, 'error');
        }
    });
}

// Start chat
function startChat(sellerId) {
    fetch('/ajax/start_chat.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            ad_id: <?php echo $adId; ?>,
            seller_id: sellerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/pages/user/messages.php?chat=' + data.chat_id;
        } else {
            showNotification(data.message, 'error');
        }
    });
}

// Image zoom on click
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.addEventListener('click', function() {
            // Simple zoom implementation
            if (this.style.transform === 'scale(2)') {
                this.style.transform = 'scale(1)';
                this.style.cursor = 'zoom-in';
            } else {
                this.style.transform = 'scale(2)';
                this.style.cursor = 'zoom-out';
            }
        });
    }
});

// Initialize map if address is available
<?php if (!empty($ad['address'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Leaflet map or Google Maps here
    const mapElement = document.getElementById('adMap');
    if (mapElement) {
        mapElement.innerHTML = `
            <div class="text-center">
                <i class="fas fa-map-marked-alt fa-3x text-muted mb-2"></i>
                <p class="text-muted">Карта буде додана в наступних оновленнях</p>
                <small><?php echo htmlspecialchars($ad['address']); ?></small>
            </div>
        `;
    }
});
<?php endif; ?>
</script>

<?php 
include '../themes/footer.php';

// Helper functions for ad details
function getAdDetails($adId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT a.*, c.name as category_name, l.name as location_name,
                   u.username as seller_name
            FROM ads a
            JOIN categories c ON a.category_id = c.id
            JOIN locations l ON a.location_id = l.id
            JOIN users u ON a.user_id = u.id
            WHERE a.id = ? AND a.status = 'active'
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return null;
    }
}

function getAdImages($adId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT * FROM ad_images 
            WHERE ad_id = ? 
            ORDER BY is_main DESC, sort_order ASC
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getAdAttributes($adId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT ca.name, aa.value 
            FROM ad_attributes aa
            JOIN category_attributes ca ON aa.attribute_id = ca.id
            WHERE aa.ad_id = ?
            ORDER BY ca.sort_order ASC
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSimilarAds($categoryId, $excludeId, $limit = 6) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT a.*, l.name as location_name
            FROM ads a
            JOIN locations l ON a.location_id = l.id
            WHERE a.category_id = ? AND a.id != ? AND a.status = 'active'
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->bind_param("iii", $categoryId, $excludeId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getUserInfo($userId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            SELECT username as name, email, created_at, avatar,
                   (SELECT AVG(rating) FROM user_ratings WHERE user_id = ?) as rating,
                   (SELECT COUNT(*) FROM user_ratings WHERE user_id = ?) as reviews_count
            FROM users 
            WHERE id = ?
        ");
        $stmt->bind_param("iii", $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return ['name' => 'Користувач', 'created_at' => date('Y-m-d')];
    }
}

function incrementAdViews($adId) {
    try {
        $db = new Database();
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $db->prepare("
            INSERT INTO ad_views (ad_id, user_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiss", $adId, $userId, $ip, $userAgent);
        $stmt->execute();
    } catch (Exception $e) {
        // Ignore view tracking errors
    }
}

function isAdInFavorites($adId, $userId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT id FROM favorites WHERE ad_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    } catch (Exception $e) {
        return false;
    }
}
?>