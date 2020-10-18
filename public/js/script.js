var inProcess = false,
    xhr = new XMLHttpRequest(),
    auto_loading = true;

if (auto_loading) {
  // Событие скролла
  window.addEventListener('scroll', function () {
    var documentHeight = Math.max(
        document.body.scrollHeight,
        document.documentElement.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.offsetHeight,
        document.body.clientHeight,
        document.documentElement.clientHeight
        ),
        browserHeight = document.documentElement.clientHeight,
        scroll = window.pageYOffset,
        balance = 600;

    // При достижении низа страницы - вызывать функцию погрузки контента
    if (scroll > (documentHeight - browserHeight - balance) && !inProcess) {
      loadingItems();
    }
  });
}

// Подгрузка контента

function loadingItems() {
  var transition = document.getElementById('transition'),
      page, url;

  if (!transition) return;
  page = transition.dataset.page;
  url = transition.dataset.url;

  if (!url || !page) return true;

  inProcess = true;
  transition.classList.add("active");

  xhr.open('GET', url + '&ajax=true&t=' + (new Date()).getTime(), true);
  xhr.onreadystatechange = function(e) {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Готово. Информируем пользователя
      if (xhr.response.length > 0)  {
        transition.outerHTML = xhr.response;
        if (!url) window.history.pushState('', '', url);
        inProcess = false;
      } else {
        transition.textContent = '';
      }
    } else if (xhr.readyState == 4 && xhr.status != 200) {
      // Ошибка. Информируем пользователя
      console.log(xhr);
    }
  };
  xhr.send(null);
}