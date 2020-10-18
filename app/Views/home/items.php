<?php
if (!empty($posts)) :
  foreach ($posts as $index => $post) :
    echo view('home/item', ['post' => $post]);
  endforeach;
?>

<div id="transition" data-page="<?= !empty($page) ? $page + 1 : 2; ?>" data-url="<?= !empty($url) ? $url : '/'; ?>">
  <div class="preloader_css"></div>
  <button type="button" id="more" onclick="loadingItems()">Показать еще</button>
</div>

<?php
else :
  echo (!empty($ajax)) ? 'Всё :(' : 'Пока ничего нет, но скора появится';
endif;