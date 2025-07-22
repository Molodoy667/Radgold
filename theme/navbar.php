<nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="/"><?= SITE_NAME ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/">Головна</a></li>
        <li class="nav-item"><a class="nav-link" href="/admin/">Адмінка</a></li>
      </ul>
      <button class="btn btn-outline-secondary me-2 theme-toggle" title="Перемкнути тему"><i class="bi bi-moon"></i></button>
      <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#designModal">Оформлення</button>
    </div>
  </div>
</nav>
<!-- Модальне вікно для вибору оформлення -->
<div class="modal fade" id="designModal" tabindex="-1" aria-labelledby="designModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="designModalLabel">Налаштування оформлення</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Виберіть градієнт фону:</strong>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <?php for($i=1;$i<=30;$i++): ?>
              <button class="btn gradient-select gradient-<?= $i ?>" data-gradient="gradient-<?= $i ?>" style="width:40px;height:40px;border-radius:50%;border:2px solid #fff;"></button>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>