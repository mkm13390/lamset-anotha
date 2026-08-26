(() => {
    const root = document.documentElement;
    const body = document.body;
    const qs = (s, scope = document) => scope.querySelector(s);
    const qsa = (s, scope = document) => [...scope.querySelectorAll(s)];

    const defaults = window.StorefrontConfig || {};
    let language = localStorage.getItem('siteLanguage') || defaults.language || 'ar';
    let theme = localStorage.getItem('siteTheme') || defaults.theme || 'light';

    function applyTheme() {
        root.dataset.theme = theme;
        qsa('[data-theme-icon]').forEach(el => {
            el.textContent = theme === 'dark' ? '☀' : '☾';
        });
    }

    function applyLanguage() {
        root.lang = language;
        root.dir = language === 'en' ? 'ltr' : 'rtl';

        qsa('[data-i18n-ar][data-i18n-en]').forEach(el => {
            el.textContent = language === 'en' ? el.dataset.i18nEn : el.dataset.i18nAr;
        });

        qsa('[data-placeholder-ar][data-placeholder-en]').forEach(el => {
            el.placeholder = language === 'en' ? el.dataset.placeholderEn : el.dataset.placeholderAr;
        });

        qsa('[data-language-toggle]').forEach(el => {
            el.textContent = language === 'en' ? 'AR' : 'EN';
        });
    }

    function openDrawer(drawer, overlay) {
        if (!drawer || !overlay) return;
        drawer.hidden = false;
        overlay.hidden = false;
        body.classList.add('sf-lock');
    }

    function closeDrawer(drawer, overlay) {
        if (!drawer || !overlay) return;
        drawer.hidden = true;
        overlay.hidden = true;
        body.classList.remove('sf-lock');
    }

    const menu = qs('[data-mobile-menu]');
    const menuOverlay = qs('[data-mobile-menu-overlay]');
    const cart = qs('[data-mini-cart]');
    const cartOverlay = qs('[data-mini-cart-overlay]');

    qsa('[data-theme-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            theme = theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('siteTheme', theme);
            applyTheme();
        });
    });

    qsa('[data-language-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            language = language === 'en' ? 'ar' : 'en';
            localStorage.setItem('siteLanguage', language);
            applyLanguage();
        });
    });

    qsa('[data-mobile-menu-open]').forEach(b => b.addEventListener('click', () => openDrawer(menu, menuOverlay)));
    qsa('[data-mobile-menu-close]').forEach(b => b.addEventListener('click', () => closeDrawer(menu, menuOverlay)));
    menuOverlay?.addEventListener('click', () => closeDrawer(menu, menuOverlay));

    qsa('[data-mini-cart-open]').forEach(b => b.addEventListener('click', () => openDrawer(cart, cartOverlay)));
    qsa('[data-mini-cart-close]').forEach(b => b.addEventListener('click', () => closeDrawer(cart, cartOverlay)));
    cartOverlay?.addEventListener('click', () => closeDrawer(cart, cartOverlay));

    qs('[data-close-announcement]')?.addEventListener('click', e => e.currentTarget.closest('[data-announcement]')?.remove());

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeDrawer(menu, menuOverlay);
            closeDrawer(cart, cartOverlay);
        }
    });

    window.Storefront = {
        get language() { return language; },
        get theme() { return theme; },
        openMiniCart() { openDrawer(cart, cartOverlay); },
        closeMiniCart() { closeDrawer(cart, cartOverlay); },
        setCartCount(count) {
            qsa('[data-cart-count]').forEach(el => {
                el.textContent = String(Math.max(0, Number(count) || 0));
            });
        }
    };

    applyTheme();
    applyLanguage();
})();
