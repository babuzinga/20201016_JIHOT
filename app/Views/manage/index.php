<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>Управление</h1>

  <?php if (!empty($accounts)) : foreach ($accounts as $index => $account) : ?>
  <a href="<?= $account['link']; ?>" target="_blank"><?= $account['login']; ?></a>
  <?= !empty($account['in_mod']) ? ' (На модерации: ' . $account['in_mod'] . ')' : ''; ?>
  <br/>
  <?php endforeach; endif; ?>
</section>

<?= $this->include('layout/footer'); ?>