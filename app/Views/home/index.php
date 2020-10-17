<?= $this->include('layout/header'); ?>

<section>
  <h1><span>Hot</span>Fit</h1>

  <div id="hot">
    <?php
      if (!empty($posts)) :
        foreach ($posts as $index => $post) :
          echo view('home/show', ['post' => $post]);
        endforeach;
        else :
          echo 'Пока ничего нет, но скора появится';
        endif;
    ?>
  </div>
</section>

<?= $this->include('layout/footer'); ?>