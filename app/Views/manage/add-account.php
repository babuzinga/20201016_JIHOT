<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>Add</h1>

  <form action="/manage/save-account" method="post">
    <label>
      Social network:
      <select name="nid">
        <?php if (!empty($networks)) : foreach ($networks as $network) : ?>
          <option value="<?= $network['id']; ?>"><?= $network['name']; ?></option>
        <?php endforeach; endif; ?>
      </select>
    </label>
    <label>
      Account:
      <input type="text" name="login" value="" required="required">
    </label>

    <button type="submit">Save</button>
  </form>
</section>

<?= $this->include('layout/footer'); ?>