<?php

/**
 * @param $array
 * @param bool $exit
 */
function print_array($array, $exit = false)
{
  echo '<pre>', print_r($array, 1), '</pre>';
  if ($exit) exit;
}

/**
 * @param $link
 * @return string
 */
function getUrlWithHash($link)
{
  $path = FCPATH . $link;
  return (file_exists($path)) ? $link . '?' . md5_file($path) : '';
}