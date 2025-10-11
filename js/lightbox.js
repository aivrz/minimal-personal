// 轻量 lightbox + 九宫格交互（覆盖主题内原有 js/lightbox.js）
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var galleries = document.querySelectorAll('.mp-gallery');
    if (!galleries.length) return;

    // 创建 lightbox DOM 并加入 body
    var lb = document.createElement('div');
    lb.className = 'mp-lightbox';
    lb.setAttribute('role', 'dialog');
    lb.setAttribute('aria-modal', 'true');
    lb.innerHTML = '\
      <div class="mp-lightbox__inner">\
        <button class="mp-lightbox__close" aria-label="关闭">×</button>\
        <button class="mp-lightbox__prev" aria-label="上一张">‹</button>\
        <img class="mp-lightbox__img" src="" alt="">\
        <button class="mp-lightbox__next" aria-label="下一张">›</button>\
        <div class="mp-lightbox__caption" aria-hidden="true"></div>\
      </div>';
    document.body.appendChild(lb);

    var imgEl = lb.querySelector('.mp-lightbox__img');
    var captionEl = lb.querySelector('.mp-lightbox__caption');
    var closeBtn = lb.querySelector('.mp-lightbox__close');
    var prevBtn = lb.querySelector('.mp-lightbox__prev');
    var nextBtn = lb.querySelector('.mp-lightbox__next');

    var state = {
      images: [],
      index: 0
    };

    function open(index) {
      if (!state.images.length) return;
      state.index = index || 0;
      update();
      lb.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      // 聚焦关闭按钮（便于无障碍）
      closeBtn.focus();
    }

    function close() {
      lb.classList.remove('is-open');
      document.body.style.overflow = '';
      imgEl.setAttribute('src', '');
    }

    function update() {
      var src = state.images[state.index];
      imgEl.setAttribute('src', src);
      captionEl.textContent = (state.index + 1) + ' / ' + state.images.length;
    }

    function navigate(delta) {
      state.index = (state.index + delta + state.images.length) % state.images.length;
      update();
    }

    // 事件绑定
    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', function () { navigate(-1); });
    nextBtn.addEventListener('click', function () { navigate(1); });

    lb.addEventListener('click', function (e) {
      if (e.target === lb) close();
    });

    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') navigate(-1);
      if (e.key === 'ArrowRight') navigate(1);
    });

    // 绑定每个画廊内的按钮
    galleries.forEach(function (gallery) {
      var json = gallery.getAttribute('data-full-urls');
      var fullUrls = [];
      try {
        fullUrls = JSON.parse(json) || [];
      } catch (err) {
        fullUrls = [];
      }
      // 保存到 state when opening
      gallery.addEventListener('click', function (e) {
        var btn = e.target.closest('.mp-gallery__btn');
        if (!btn) return;
        var idx = parseInt(btn.getAttribute('data-index'), 10) || 0;
        state.images = fullUrls;
        // 打开 lightbox，从被点击的索引开始
        open(idx);
      });
    });
  });
})();