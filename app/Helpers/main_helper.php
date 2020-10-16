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

/**
 * Генератор UUID значений
 * universally unique identifier «универсальный уникальный идентификатор»
 * https://ru.wikipedia.org/wiki/UUID
 * @return string
 */
function gen_uuid()
{
  // Формат : xxxxxxxx-xxxx-Mxxx-Nxxx-xxxxxxxxxxxx
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    // 32 bits for "time_low" - целое число, обозначающее младшие 32 бита времени
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),

    // 16 bits for "time_mid" - целое число, обозначающее средние 16 бит времени
    mt_rand(0, 0xffff),

    // 16 bits for "time_hi_and_version",
    // 4 старших бита обозначают версию UUID, младшие биты обозначают старшие 12 бит времени
    mt_rand(0, 0x0fff) | 0x4000,

    // 16 bits, 8 bits for "clk_seq_hi_res",
    // 1-3 старших бита обозначают вариант UUID, остальные 13-15 бит обозначают clock sequence
    mt_rand(0, 0x3fff) | 0x8000,

    // 48-битный идентификатор узла
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
  );
}