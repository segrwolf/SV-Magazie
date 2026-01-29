/* ===== SV Magazine — Main JavaScript ===== */

document.addEventListener('DOMContentLoaded', function () {

  // ===== Mobile Menu =====
  const burger = document.querySelector('.burger');
  const nav = document.querySelector('.header__nav');

  if (burger && nav) {
    burger.addEventListener('click', function () {
      burger.classList.toggle('active');
      nav.classList.toggle('open');
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        burger.classList.remove('active');
        nav.classList.remove('open');
      });
    });
  }

  // ===== Active Nav Link =====
  var currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.header__nav a').forEach(function (link) {
    var href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('active');
    }
  });

  // ===== Contact Manager Modal =====
  var modalOverlay = document.getElementById('contactModal');
  var openModalBtns = document.querySelectorAll('[data-open-modal]');
  var closeModalBtn = document.querySelector('.modal__close');

  function openModal() {
    if (modalOverlay) {
      modalOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal() {
    if (modalOverlay) {
      modalOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  openModalBtns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      openModal();
    });
  });

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeModal);
  }

  if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
      if (e.target === modalOverlay) {
        closeModal();
      }
    });
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeModal();
    }
  });

  // ===== Form Submit Helper =====
  function submitForm(form, onSuccess) {
    var recaptchaWidget = form.querySelector('.g-recaptcha');
    var recaptchaResponse = '';
    if (recaptchaWidget) {
      var widgetId = recaptchaWidget.getAttribute('data-widget-id');
      if (widgetId !== null) {
        recaptchaResponse = grecaptcha.getResponse(parseInt(widgetId));
      } else {
        recaptchaResponse = grecaptcha.getResponse();
      }
    }
    if (recaptchaWidget && !recaptchaResponse) {
      var msgEl = form.querySelector('.form-message');
      if (msgEl) {
        msgEl.textContent = 'Please confirm you are not a robot.';
        msgEl.className = 'form-message form-message--error';
      }
      return;
    }

    var formData = new FormData(form);
    if (recaptchaResponse) {
      formData.append('g-recaptcha-response', recaptchaResponse);
    }
    var msgEl = form.querySelector('.form-message');
    var submitBtn = form.querySelector('button[type="submit"]');

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';
    }

    fetch('api/contact-form.php', {
      method: 'POST',
      body: formData
    })
      .then(function (response) {
        return response.text();
      })
      .then(function (text) {
        var data;
        try {
          data = JSON.parse(text);
        } catch (e) {
          data = { success: true, message: 'Thank you! We will contact you soon.' };
        }
        if (msgEl) {
          msgEl.textContent = data.message || 'Thank you! We will contact you soon.';
          msgEl.className = 'form-message form-message--success';
        }
        form.reset();
        if (typeof grecaptcha !== 'undefined' && recaptchaWidget) {
          var wid = recaptchaWidget.getAttribute('data-widget-id');
          grecaptcha.reset(wid !== null ? parseInt(wid) : undefined);
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
        if (onSuccess) onSuccess();
      })
      .catch(function () {
        if (msgEl) {
          msgEl.textContent = 'Something went wrong. Please try again or contact us directly.';
          msgEl.className = 'form-message form-message--error';
        }
        if (typeof grecaptcha !== 'undefined' && recaptchaWidget) {
          var wid = recaptchaWidget.getAttribute('data-widget-id');
          grecaptcha.reset(wid !== null ? parseInt(wid) : undefined);
        }
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send Message';
        }
      });
  }

  // ===== Contact Form Submission =====
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      submitForm(contactForm);
    });
  }

  // ===== Modal Form Submission =====
  var modalForm = document.getElementById('modalContactForm');
  if (modalForm) {
    modalForm.addEventListener('submit', function (e) {
      e.preventDefault();
      submitForm(modalForm, function () {
        setTimeout(closeModal, 2000);
      });
    });
  }

  // ===== Load Magazines Dynamically =====
  var magazinesContainer = document.getElementById('magazinesGrid');
  if (magazinesContainer) {
    fetch('data/magazines.json')
      .then(function (response) { return response.json(); })
      .then(function (magazines) {
        magazinesContainer.innerHTML = '';
        magazines.forEach(function (mag) {
          var card = document.createElement('div');
          card.className = 'magazine-card fade-in';
          card.innerHTML =
            '<img src="' + mag.cover + '" alt="' + mag.title + '" class="magazine-card__cover" loading="lazy">' +
            '<div class="magazine-card__info">' +
            '<div class="magazine-card__title">' + mag.title + '</div>' +
            '<div class="magazine-card__year">' + mag.year + '</div>' +
            '<a href="' + mag.pdf + '" target="_blank" rel="noopener" class="magazine-card__link">' +
            'View PDF &rarr;</a>' +
            '</div>';
          magazinesContainer.appendChild(card);
        });
        observeFadeIns();
      })
      .catch(function () {
        magazinesContainer.innerHTML = '<p>Unable to load magazines. Please try again later.</p>';
      });
  }

  // ===== Load Contacts Dynamically =====
  var contactsContainer = document.getElementById('contactsInfo');
  if (contactsContainer) {
    fetch('data/contacts.json')
      .then(function (response) { return response.json(); })
      .then(function (info) {
        var phoneEl = document.getElementById('contactPhone');
        var emailEl = document.getElementById('contactEmail');
        var instaEl = document.getElementById('contactInstagram');

        if (phoneEl && info.phone) {
          phoneEl.innerHTML = '<a href="tel:' + info.phone.replace(/\s/g, '') + '">' + info.phone + '</a>';
        }
        if (emailEl && info.email) {
          emailEl.innerHTML = '<a href="mailto:' + info.email + '">' + info.email + '</a>';
        }
        if (instaEl && info.instagram) {
          instaEl.innerHTML = '<a href="' + info.instagram + '" target="_blank" rel="noopener">@_sv_magazine_</a>';
        }
      });
  }

  // ===== Fade-in on Scroll =====
  function observeFadeIns() {
    var elements = document.querySelectorAll('.fade-in:not(.visible)');
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });

      elements.forEach(function (el) {
        observer.observe(el);
      });
    } else {
      elements.forEach(function (el) {
        el.classList.add('visible');
      });
    }
  }

  observeFadeIns();

  // ===== reCAPTCHA Explicit Render (for pages with multiple widgets) =====
  window.onRecaptchaLoad = function () {
    var widgets = document.querySelectorAll('.g-recaptcha');
    widgets.forEach(function (el) {
      if (!el.getAttribute('data-widget-id')) {
        var widgetId = grecaptcha.render(el, {
          sitekey: el.getAttribute('data-sitekey')
        });
        el.setAttribute('data-widget-id', widgetId);
      }
    });
  };
});
