<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>Посты на модерацию</h1>

  <div id="posts">
  <?php if (!empty($posts)) : foreach ($posts as $index => $post) : ?>
    <?php foreach ($post['medias'] as $media) : ?>
    <div class="item">
      <span><?= $post['uuid']; ?></span>
      <img src="<?= $media['preview']; ?>"/>
    </div>
    <?php endforeach; ?>
  <?php endforeach; else : echo 'Ничего нет'; endif; ?>
  </div>
</section>

<?= $this->include('layout/footer'); ?>