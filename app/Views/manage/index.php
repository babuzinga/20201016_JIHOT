<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>manage</h1>

  <a href="/manage/add-account">add account</a>
  <br/>
  <br/>

  <table class="accounts">
    <thead>
      <th></th>
      <th>login</th>
      <th>tags</th>
      <th></th>
      <th>active</th>
      <th>delete</th>
      <th>mod</th>
      <th>upload</th>
    </thead>
    <?php if (!empty($accounts)) : $i = 1; foreach ($accounts as $index => $account) : ?>
    <tr>
      <td><?= $i++; ?>.</td>
      <td><a href="<?= $account['link']; ?>" target="_blank">@<?= $account['login']; ?></a></td>
      <td class="tags-select" data-uuid="<?= $account['uuid']; ?>">
        <?php if(!empty($tags_account)) : foreach ($tags_account as $tag) : ?>
        <span onclick="save_tags(this, <?= $tag['id']; ?>)" class="ee <?= in_array($tag['id'], $account['tag_ids']) ? 'active' : ''; ?>"><?= $tag['title']; ?></span>
        <?php endforeach; endif; ?>
      </td>
      <td><a href="/manage/get-posts-account/<?= $account['uuid']; ?>">parse</a></td>
      <td><a href="/posts/account/<?= $account['uuid']; ?>" target="_blank"><?= !empty($account['posts']) ? $account['posts'] : 0; ?></a></td>
      <td><?= !empty($account['posts_in_del']) ? $account['posts_in_del'] : 0; ?></td>
      <td><?= !empty($account['posts_in_mod']) ? $account['posts_in_mod'] : 0; ?></td>
      <td><?= !empty($account['posts_ok']) ? $account['posts_ok'] : 0; ?>%</td>
    </tr>
    <?php endforeach; endif; ?>
  </table>
</section>

<script>
  function save_tags(e, tag_id) {
    ajax_send('/manage/set-tags-account/' + e.parentNode.dataset.uuid + '/' + tag_id);
    e.classList.toggle("active");
  }
</script>

<?= $this->include('layout/footer'); ?>