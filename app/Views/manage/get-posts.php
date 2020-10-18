<?= $this->include('layout/header'); ?>

  <section>
    <?= $this->include('manage/nav'); ?>
    <h1>Complete</h1>

    <?php if (!empty($accounts)) : foreach ($accounts as $index => $account) : ?>
      <?= '@', $account['login'], ' +', $account['new']; ?><br/>
    <?php endforeach; endif; ?>
  </section>

<?= $this->include('layout/footer'); ?>