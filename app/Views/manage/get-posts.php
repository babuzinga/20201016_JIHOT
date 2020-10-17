<?= $this->include('layout/header'); ?>

  <section>
    <?= $this->include('manage/nav'); ?>
    <h1>Завершение</h1>

    <?php if (!empty($accounts)) : foreach ($accounts as $index => $account) : ?>
      <?= $account['login'], ' Новых: ', $account['new']; ?><br/>
    <?php endforeach; endif; ?>
  </section>

<?= $this->include('layout/footer'); ?>