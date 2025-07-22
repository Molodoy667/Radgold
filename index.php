<?php
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';
include __DIR__ . '/theme/header.php';

// Тестові категорії та оголошення (замість БД)
$categories = [
  ['id'=>1,'name'=>'Авто','icon'=>'bi-car-front-fill','gradient'=>'gradient-1'],
  ['id'=>2,'name'=>'Нерухомість','icon'=>'bi-house-door-fill','gradient'=>'gradient-2'],
  ['id'=>3,'name'=>'Робота','icon'=>'bi-briefcase-fill','gradient'=>'gradient-3'],
  ['id'=>4,'name'=>'Електроніка','icon'=>'bi-phone-fill','gradient'=>'gradient-4'],
  ['id'=>5,'name'=>'Тварини','icon'=>'bi-paw','gradient'=>'gradient-5'],
  ['id'=>6,'name'=>'Мода','icon'=>'bi-bag-fill','gradient'=>'gradient-6'],
];
$ads = [
  ['id'=>1,'title'=>'Продам авто','cat'=>1,'desc'=>'Стан ідеальний, нова гума.','img'=>'https://picsum.photos/seed/auto/300/200'],
  ['id'=>2,'title'=>'Здам квартиру','cat'=>2,'desc'=>'Центр міста, всі зручності.','img'=>'https://picsum.photos/seed/flat/300/200'],
  ['id'=>3,'title'=>'Потрібен дизайнер','cat'=>3,'desc'=>'Віддалено, гнучкий графік.','img'=>'https://picsum.photos/seed/job/300/200'],
  ['id'=>4,'title'=>'iPhone 13 Pro','cat'=>4,'desc'=>'Новий, гарантія.','img'=>'https://picsum.photos/seed/iphone/300/200'],
  ['id'=>5,'title'=>'Щеня лабрадора','cat'=>5,'desc'=>'Вік 2 місяці, документи.','img'=>'https://picsum.photos/seed/dog/300/200'],
  ['id'=>6,'title'=>'Сумка Gucci','cat'=>6,'desc'=>'Оригінал, нова.','img'=>'https://picsum.photos/seed/bag/300/200'],
];
?>
<div class="container py-4">
  <h2 class="mb-4 text-center animate__animated animate__fadeInDown">Категорії</h2>
  <div class="row g-4 mb-5 justify-content-center">
    <?php foreach($categories as $cat): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="card category-card text-white text-center <?= $cat['gradient'] ?> animate__animated animate__zoomIn">
          <div class="card-body d-flex flex-column align-items-center justify-content-center">
            <i class="bi <?= $cat['icon'] ?>"></i>
            <h5 class="card-title mt-2 mb-0"><?= htmlspecialchars($cat['name']) ?></h5>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <h2 class="mb-4 text-center animate__animated animate__fadeInDown">Оголошення</h2>
  <div class="row g-4">
    <?php foreach($ads as $ad): $cat = $categories[$ad['cat']-1]; ?>
      <div class="col-md-4">
        <div class="card ad-card <?= $cat['gradient'] ?> animate__animated animate__fadeInUp h-100">
          <img src="<?= $ad['img'] ?>" class="card-img-top" alt="<?= htmlspecialchars($ad['title']) ?>">
          <div class="card-body">
            <h5 class="card-title"><i class="bi <?= $cat['icon'] ?>"></i> <?= htmlspecialchars($ad['title']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($ad['desc']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<?php include __DIR__ . '/theme/footer.php'; ?>