<?php if (!empty($post) && !empty($post['medias'])) : ?>
<div>
  <?php $type = $post['medias'][0]['type']; ?>
  <ul class="tags">
    <li>
      <?php if ($type == 1) : ?>
        <a href="/image/">image</a>
      <?php elseif ($type == 2) : ?>
        <a href="/video/">video</a>
      <?php endif; ?>
    </li>
    <?php if (!empty($post['tag_ids'])) : foreach ($post['tag_ids'] as $tag) : ?>
    <li><a href="/tag/<?= $tag['id']; ?>"><?= $tag['title']; ?></a></li>
    <?php endforeach; endif; ?>
  </ul>

  <?php if ($type == 1) : ?>
    <img src="/medias/<?= $post['medias'][0]['uuid']; ?>.jpg"/>
  <?php elseif ($type == 2) : ?>
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