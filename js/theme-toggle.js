// theme-toggle.js - 更鲁棒的实现（同时设置 html 和 body 的 data-theme，增加调试/容错，支持跨 tab 同步）
(function() {
  'use strict';

  var STORAGE_KEY = 'minimal_personal_theme'; // 'light'|'dark'|'auto'（auto = 无存储）
  var ATTR = 'data-theme';
  var AUTO = 'auto';
  var LIGHT = 'light';
  var DARK = 'dark';
  var CHECK_INTERVAL = 5 * 60 * 1000; // 自动检查间隔（5 分钟）

  function log() {
    if (window && window.console && typeof window.console.log === 'function') {
      // 在生产可注释掉这一行
      console.log.apply(console, arguments);
    }
  }

  function getAutoThemeByHour() {
    var h = new Date().getHours();
    if (h >= 19 || h < 7) {
      return DARK;
    }
    return LIGHT;
  }

  function setAttrOnRoots(val) {
    try {
      document.documentElement.setAttribute(ATTR, val);
    } catch (e) { /* ignore */ }
    try {
      document.body && document.body.setAttribute(ATTR, val);
    } catch (e) { /* ignore */ }
  }

  function applyTheme(theme) {
    if (theme !== DARK && theme !== LIGHT) theme = LIGHT;
    setAttrOnRoots(theme);
    updateToggleUI(theme);
    log('[theme-toggle] applied theme:', theme);
  }

  function updateToggleUI(currentTheme) {
    var btn = document.getElementById('themeToggle');
    if (!btn) return;
    var icon = btn.querySelector('.theme-toggle-icon');
    if (!icon) {
      // 若没有内嵌图标，则直接更改按钮文本
      btn.textContent = currentTheme === DARK ? '🌙' : '☀️';
      btn.setAttribute('aria-pressed', currentTheme === DARK ? 'true' : 'false');
      btn.title = currentTheme === DARK ? '切换到白天模式' : '切换到夜间模式';
      return;
    }
    if (currentTheme === DARK) {
      icon.textContent = '🌙';
      btn.setAttribute('aria-pressed', 'true');
      btn.title = '切换到白天模式';
    } else {
      icon.textContent = '☀️';
      btn.setAttribute('aria-pressed', 'false');
      btn.title = '切换到夜间模式';
    }
  }

  function getStoredPreference() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch (e) {
      return null;
    }
  }

  function setStoredPreference(val) {
    try {
      if (val === AUTO || val === null) {
        localStorage.removeItem(STORAGE_KEY);
      } else {
        localStorage.setItem(STORAGE_KEY, val);
      }
    } catch (e) {
      // ignore
    }
  }

  function toggleAndStore() {
    var current = (document.documentElement.getAttribute(ATTR) === DARK || document.body.getAttribute(ATTR) === DARK) ? DARK : LIGHT;
    var next = current === DARK ? LIGHT : DARK;
    applyTheme(next);
    setStoredPreference(next);
    // 通知其他 tab（storage 事件已自动发出）
  }

  function init() {
    try {
      var stored = getStoredPreference();
      var effective;
      if (stored === LIGHT || stored === DARK) {
        effective = stored;
      } else {
        effective = getAutoThemeByHour();
        stored = AUTO;
      }

      applyTheme(effective);

      // 如果是自动模式，定时检查（跨天或跨小时切换）
      if (stored === AUTO) {
        setInterval(function() {
          var currAuto = getAutoThemeByHour();
          var currentSet = document.documentElement.getAttribute(ATTR) || document.body.getAttribute(ATTR) || null;
          if (currentSet !== currAuto) {
            applyTheme(currAuto);
            log('[theme-toggle] auto-updated theme to', currAuto);
          }
        }, CHECK_INTERVAL);
      }

      // 安装按钮事件
      var btn = document.getElementById('themeToggle');
      if (!btn) {
        log('[theme-toggle] themeToggle button not found');
        return;
      }

      btn.addEventListener('click', function(e) {
        e.preventDefault();
        toggleAndStore();
      });

      // 键盘支持
      btn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          btn.click();
        }
      });

      // 跨标签同步：当 localStorage 更改时更新主题
      window.addEventListener('storage', function(e) {
        if (!e.key || e.key !== STORAGE_KEY) return;
        var newVal = e.newValue;
        if (!newVal) {
          // 被清除 -> 回到自动
          applyTheme(getAutoThemeByHour());
        } else if (newVal === LIGHT || newVal === DARK) {
          applyTheme(newVal);
        }
      });
    } catch (err) {
      log('[theme-toggle] init error', err);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();