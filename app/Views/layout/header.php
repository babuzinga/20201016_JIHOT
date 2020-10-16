<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= !empty($page_title) ? $page_title : PROJECT_NAME; ?></title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" type="image/png" href="../../../public/favicon.ico"/>

  <link rel="stylesheet" href="<?= getUrlWithHash('/css/reset.min.css'); ?>">
  <link rel="stylesheet" href="<?= getUrlWithHash('/css/style.min.css'); ?>">
</head>
<body>