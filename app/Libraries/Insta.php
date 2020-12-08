<?php namespace App\Libraries;

require 'Unirest.php';
use Unirest\Request;

class Insta
{
  const INI_SET_FILE      = FCPATH . '../app/Libraries/setting.ini';
  const BASE_URL          = 'https://www.instagram.com';
  const LOGIN_URL         = 'https://www.instagram.com/accounts/login/ajax/';
  const HTTP_NOT_FOUND    = 404;
  const HTTP_OK           = 200;
  const HTTP_FORBIDDEN    = 403;
  const HTTP_BAD_REQUEST  = 400;

  private $setting;
  private $userSession;
  private $sleep_time = 3;
  private $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';

  public function __construct()
  {
    $this->getSetting();
  }

  private function getSetting()
  {
    $this->setting = parse_ini_file(static::INI_SET_FILE, true);
  }

  private function setSetting()
  {
    $inisave = $this->arr2ini($this->setting);
    $file_handle = fopen(static::INI_SET_FILE, "w");
    fwrite($file_handle, $inisave);
    fclose($file_handle);
  }

  public function arr2ini(array $a, array $parent = array())
  {
    $out = '';
    foreach ($a as $k => $v) {
      if (is_array($v)) {
        $sec = array_merge((array)$parent, (array)$k);
        $out .= '[' . join('.', $sec) . ']' . PHP_EOL;
        $out .= $this->arr2ini($v, $sec);
      } else {
        // plain key->value case
        $out .= "$k=$v" . PHP_EOL;
      }
    }
    return $out;
  }

  public function login()
  {
    $account = $this->setting['account'];

    $session = !empty($account['session']) ? unserialize($account['session']) : null;
    if (!$this->isLoggedIn($session)) {
      echo 'test request<br/>';

      // Запрос к основной странице
      $response = Request::get(static::BASE_URL);

      echo 'end test request<br/>';

      if (empty($response) || $response->code !== static::HTTP_OK) {
        $error = 'error response to login scraper';
      }

      preg_match('/"csrf_token":"(.*?)"/', $response->body, $match);
      $csrfToken = isset($match[1]) ? $match[1] : '';
      $cookies = $this->parseCookies($response->headers);

      $mid = array_key_exists('mid', $cookies) ? $cookies['mid'] : '';

      $cookieString = 'ig_cb=1';
      if ($csrfToken !== '') {
        $cookieString .= '; csrftoken=' . $csrfToken;
      }
      if ($mid !== '') {
        $cookieString .= '; mid=' . $mid;
      }

      $headers = [
        'cookie' => $cookieString,
        'referer' => static::BASE_URL . '/',
        'x-csrftoken' => $csrfToken,
        'X-CSRFToken' => $csrfToken,
        'user-agent' => $this->userAgent,
      ];

      $response = Request::post(static::LOGIN_URL, $headers, ['username' => $account['username'], 'password' => $account['password']]);
      echo 'logon request : ' . $account['username'] . '<br/>';

      if ($response->code !== static::HTTP_OK) {
        if (
          $response->code === static::HTTP_BAD_REQUEST
          && isset($response->body->message)
          && $response->body->message == 'checkpoint_required'
        ) {
          if (!empty($debug)) { echo '<h2>Status : start two step verificator</h2>'; }
          // Запрос двухтактной аутентификации
          $response = $this->verifyTwoStep($response, $cookies);
        } elseif ((is_string($response->code) || is_numeric($response->code)) && is_string($response->body)) {
          echo ('Response code is ' . $response->code . '. Body: ' . $response->body . ' Something went wrong. Please report issue. Response_code ' . $response->code);
        } else {
          echo ('Something went wrong. Please report issue. Response_code ' . $response->code);
        }
      }

      $cookies = $this->parseCookies($response->headers);
      $cookies['mid'] = $mid;

      $this->setting['account']['session'] = serialize($cookies);
      $this->setting['account']['update_session'] = date("Y-m-d H:i:s");
      $this->setSetting();

      echo '<h2>Status : login success</h2>';
      $this->userSession = $cookies;
    } else {
      echo '<h2>Status : logged in</h2>';
      $this->userSession = $session;
    }

    return $this->generateHeaders($this->userSession);
  }

  /**
   * Проверка, что логирование уже произведено
   * @param $session
   * @return bool
   */
  public function isLoggedIn($session) {
    if (empty($session) || !isset($session['sessionid'])) {
      return false;
    }

    $sessionId = $session['sessionid'];
    $csrfToken = $session['csrftoken'];
    $headers = [
      'cookie' => "ig_cb=1; csrftoken=$csrfToken; sessionid=$sessionId;",
      'referer' => static::BASE_URL . '/',
      'x-csrftoken' => $csrfToken,
      'X-CSRFToken' => $csrfToken,
      'user-agent' => $this->userAgent,
    ];

    $response = Request::get(static::BASE_URL, $headers);
    if ($response->code !== static::HTTP_OK) {
      return false;
    }
    $cookies = $this->parseCookies($response->headers);
    if (!isset($cookies['ds_user_id'])) {
      return false;
    }
    return true;
  }

  /**
   * Парсер кук из полученого заголовка ответа
   * @param array $headers
   * @return array
   */
  private function parseCookies($headers) {
    $rawCookies = isset($headers['Set-Cookie']) ? $headers['Set-Cookie'] : (isset($headers['set-cookie']) ? $headers['set-cookie'] : []);

    if (!is_array($rawCookies)) {
      $rawCookies = [$rawCookies];
    }

    $not_secure_cookies = [];
    $secure_cookies = [];

    foreach ($rawCookies as $cookie) {
      $cookie_array = 'not_secure_cookies';
      $cookie_parts = explode(';', $cookie);
      foreach ($cookie_parts as $cookie_part) {
        if (trim($cookie_part) == 'Secure') {
          $cookie_array = 'secure_cookies';
          break;
        }
      }
      $value = array_shift($cookie_parts);
      $parts = explode('=', $value);
      if (sizeof($parts) >= 2 && !is_null($parts[1])) {
        ${$cookie_array}[$parts[0]] = $parts[1];
      }
    }

    $cookies = $secure_cookies + $not_secure_cookies;

    if (isset($cookies['csrftoken'])) {
      $this->userSession['csrftoken'] = $cookies['csrftoken'];
    }

    return $cookies;
  }

  /**
   * Дфухэтапная верификация аккаунта
   * @param $response
   * @param $cookies
   * @return \Unirest\Response
   */
  private function verifyTwoStep($response, $cookies) {
    $new_cookies = $this->parseCookies($response->headers);
    $cookies = array_merge($cookies, $new_cookies);
    $cookie_string = '';
    foreach ($cookies as $name => $value) {
      $cookie_string .= $name . '=' . $value . '; ';
    }
    $headers = [
      'cookie' => $cookie_string,
      'referer' => static::LOGIN_URL,
      'x-csrftoken' => $cookies['csrftoken'],
      'user-agent' => $this->userAgent,
    ];

    $url = static::BASE_URL . $response->body->checkpoint_url;
    $response = Request::get($url, $headers);
    if (preg_match('/window._sharedData\s\=\s(.*?)\;<\/script>/', $response->raw_body, $matches)) {
      $data = json_decode($matches[1], true, 512, JSON_BIGINT_AS_STRING);
      if (!empty($data['entry_data']['Challenge'][0]['extraData']['content'][3]['fields'][0]['values'])) {
        $choices = $data['entry_data']['Challenge'][0]['extraData']['content'][3]['fields'][0]['values'];
      } elseif (!empty($data['entry_data']['Challenge'][0]['fields'])) {
        $fields = $data['entry_data']['Challenge'][0]['fields'];
        if (!empty($fields['email'])) {
          $choices[0] = ['label' => 'Email: ' . $fields['email'], 'value' => 1];
        }
        if (!empty($fields['phone_number'])) {
          $choices[1] = ['label' => 'Phone: ' . $fields['phone_number'], 'value' => 0];
        }
      }
      if (empty($choices)) {
        echo ('<span style="color:red;">No verification methods available (You may need to go through CAPTCHA on the site)</span>');
        exit;
      }
      // Если есть набор варинтов для получения кода ($choices) но его значени еще не получено ($_GET['choices'])
      // выводим их в форму для выбора
      if (!empty($choices) && !isset($_GET['choices'])) {
        // 1 - выбор способа получения кода
        print_array($choices);
        echo '<h3>Choose a way to get the code :</h3>';
        foreach ($choices as $key=>$v)
          echo '<a href="/cron/scraper-inst.php?debug_mode=1&account_check='.$_GET['account_check'].'&choices='.$key.'">'.$v['label'].'</a><br/>';
        exit;

      }
    }
    // Если не передан способ полуени якода - дальнейшие действия не имеют смысла
    if (!isset($_GET['choices'])) {
      exit;
    }

    // 2 - Отсылка кода
    $selected_choice = $_GET['choices'];
    echo 'Message with security code sent to: ' . $choices[$selected_choice]['label'] . '(' . $choices[$selected_choice]['value'] . ')<br/>';
    $response = Request::post($url, $headers, array('choice' => $choices[$selected_choice]['value']));

    //
    if (!preg_match('/name="security_code"/', $response->raw_body, $matches)) {
      print_array($response);
      echo ('<span style="color:red;">Something went wrong when try two step verification. Please report issue.' . $response->code . '</span>');
      exit;
    }
    // Если отсутсвует код-безопасности, выводим форму для его ввода и останавливаем процесс логирования
    if (empty($_GET['security_code'])) {
      echo '<br/>
        <form method="get">
          <input type="hidden" name="choices" value="'.$_GET['choices'].'">
          <input type="hidden" name="debug_mode" value="1">
          <input type="hidden" name="account_check" value="'.$_GET['account_check'].'">
          <input type="text" name="security_code" value="" placeholder="security code">
          <input type="submit" value="send">
        </form>
      ';
      exit;
    }
    // Задержка
    sleep($this->sleep_time);

    $security_code = $_GET['security_code'];

    $post_data = [
      'csrfmiddlewaretoken' => $cookies['csrftoken'],
      'verify' => 'Verify Account',
      'security_code' => $security_code,
    ];

    echo "send security code - $security_code<br/>";
    $response = Request::post($url, $headers, $post_data);
    if ($response->code !== static::HTTP_OK) {
      echo ('<span style="color:red;">Something went wrong when try two step verification and enter security code. Please report issue.' . $response->code . '</span>');
    }

    return $response;
  }

  /**
   * Форимрование Заголовка запроса
   * @param $session
   * @param $gisToken
   *
   * @return array
   */
  private function generateHeaders($session, $gisToken = null) {
    $headers = [];
    if ($session) {
      $cookies = '';
      foreach ($session as $key => $value) {
        $cookies .= "$key=$value; ";
      }

      $csrf = !empty($session['csrftoken']) ? $session['x-csrftoken'] : $session['csrftoken'];

      $headers = [
        'cookie' => $cookies,
        'referer' => static::BASE_URL . '/',
        'x-csrftoken' => $csrf,
      ];
    }

    if ($this->userAgent) {
      $headers['user-agent'] = $this->userAgent;

      if (!is_null($gisToken)) {
        $headers['x-instagram-gis'] = $gisToken;
      }
    }

    return $headers;
  }

  public function sendRequest($url)
  {
    $response = Request::get($url, $this->generateHeaders($this->userSession, null));
    return $response;
  }
}

