<h4>Крок 1: Згода з ліцензією</h4>
<form method="post" class="animate__animated animate__fadeIn">
  <div class="mb-3" style="max-height:200px;overflow:auto;border:1px solid #eee;padding:10px;background:#fafafa;border-radius:1rem;">
    <?php echo nl2br(htmlspecialchars(file_get_contents(__DIR__.'/license.txt'))); ?>
  </div>
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="agree" id="agree" required>
    <label class="form-check-label" for="agree">Я погоджуюсь з умовами ліцензії</label>
  </div>
  <div class="d-flex justify-content-end">
    <button class="btn btn-installer btn-primary" type="submit"><i class="bi bi-arrow-right"></i>Далі</button>
  </div>
</form>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['agree'])) {
  goToStep(2);
}
?>