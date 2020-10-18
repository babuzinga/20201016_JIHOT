<?php if (!empty($post) && !empty($post['medias'])) : ?>
<div>
  <?php if ($post['medias'][0]['type'] == 1) : ?>
    <img src="/medias/<?= $post['medias'][0]['uuid']; ?>.jpg"/>
  <?php endif; ?>

  <?php if ($post['medias'][0]['type'] == 2) : ?>
    <video controls="controls" >
      <source src="/medias/<?= $post['medias'][0]['uuid']; ?>.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
    </video>
  <?php endif; ?>

  <span class="f_block">
    <?= date('Y-m-d H:i:s', $post['timestamp']), ' [ count : ', $post['media_count']; ?> ]
    <a href="/posts/account/<?= $post['uuid_account']; ?>/">@<?= $post['login']; ?></a>
  </span>
</div>
<?php endif; ?>