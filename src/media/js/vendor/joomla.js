// File#: _1_joomla

(function () {
  var item = document.querySelector(".com-content-article__body");
  if (item) {
    if (item.children.length == 0) {
      item.parentNode.parentNode.remove();
    }
  }
})();
