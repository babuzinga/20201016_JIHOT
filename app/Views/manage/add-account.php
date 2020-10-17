<?= $this->include('layout/header'); ?>

<section>
  <?= $this->include('manage/nav'); ?>
  <h1>Добавление аккаунта</h1>

  <form action="/manage/save-account" method="post">
    <label>
      Социальная сеть:
      <select name="nid">
        <?php if (!empty($networks)) : foreach ($networks as $network) : ?>
          <option value="<?= $network['id']; ?>"><?= $network['name']; ?></option>
        <?php endforeach; endif; ?>
      </select>
    </label>
    <label>
      Имя аккаунта:
      <input type="text" name="login" value="" required="required">
    </label>

    <button type="submit">Добавить</button>
  </form>
</section>

<?= $this->include('layout/footer'); ?>