<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>Модерация</h1>

  <div id="posts">
  <?php if (!empty($posts)) : foreach ($posts as $index => $post) : ?>
    <div id="p<?= $post['uuid']; ?>">
      <span class="header">
        <?= $post['social'], ' : ', $post['login']; ?>
        -
        <span class="link" onclick="up('<?= $post['uuid']; ?>')">[ Сохранить пост ]</span>
        -
        <span class="link" onclick="rm('<?= $post['uuid']; ?>', 'p')">[ Удалить пост ]</span>
      </span>

      <div>
        <?php if (!empty($post['medias_temp'])) : foreach ($post['medias_temp'] as $media) : ?>
        <div class="media" id="m<?= $media['uuid']; ?>">
          <span>[ <?= $media['type'] == 1 ? 'Image' : 'Video'; ?> ]</span>
          <br/>
          <a href="<?= $media['url']; ?>" target="_blank">
            <img src="<?= $media['preview']; ?>"/>
          </a>
          <br/>
          <span class="link" onclick="rm('<?= $media['uuid']; ?>', 'm')">Удалить</span>
        </div>
        <?php endforeach; else : echo 'Медиа нет'; endif; ?>
      </div>
    </div>
  <?php endforeach; else : echo 'Очередь пустая'; endif; ?>
  </div>
</section>

<script>
  function rm(uuid, t) {
    if (confirm('Удалить ' + (t == 'm' ? 'медиа' : 'пост') + '?')) {
      let elem = document.getElementById(t + uuid);
      if (elem) s(elem, (t == 'm' ? '/manage/remove-temp-media/' : '/manage/remove-post/') + uuid, true);
    }
  }

  function up(uuid) {
    if (confirm('Сохранить пост?')) {
      let elem = document.getElementById('p' + uuid);
      if (elem) s(elem, '/manage/upload-post/' + uuid, false);
    }
  }

  let xhr_manage = new XMLHttpRequest();
  function s(elem, link, wait) {
    if (!wait) elem.parentNode.removeChild(elem);

    xhr_manage.open('GET', link, true);
    xhr_manage.onreadystatechange = function(e) {
      if (xhr_manage.readyState == 4 && xhr_manage.status == 200) {
        if (xhr_manage.response == 1 && wait) elem.parentNode.removeChild(elem);
      } else if (xhr_manage.readyState == 4 && xhr_manage.status != 200) {
        console.log(xhr_manage.status + ': ' + xhr_manage.statusText);
      }
    };
    xhr_manage.send(null);
  }
</script>

<?= $this->include('layout/footer'); ?>