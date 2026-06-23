(function () {
    'use strict';

    const body = document.body;

    /* =====================================
       Grid Toggle
    ===================================== */

    function initGrid() {
        const results = document.querySelector('.kg-listings-results');

        if (!results) return;

        const saved = localStorage.getItem('kg_grid') || '4';

        results.setAttribute('data-grid', saved);

        document.querySelectorAll('[data-grid]').forEach(function (btn) {
            if (btn.dataset.grid === saved) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', function () {
                document.querySelectorAll('[data-grid]').forEach(function (x) {
                    x.classList.remove('active');
                });

                this.classList.add('active');

                results.setAttribute('data-grid', this.dataset.grid);

                localStorage.setItem('kg_grid', this.dataset.grid);
            });
        });
    }

    initGrid();

    /* =====================================
       Sorting
    ===================================== */

    function initSort() {
        document.querySelectorAll('.kg-sort-select').forEach(function (select) {
            select.addEventListener('change', function () {
                body.classList.add('kg-loading');

                const url = new URL(window.location.href);

                if (this.value) {
                    url.searchParams.set('sort', this.value);
                } else {
                    url.searchParams.delete('sort');
                }

                window.location.href = url.toString();
            });
        });
    }

    initSort();

    /* =====================================
       Product Gallery
    ===================================== */

    function initGallery() {
        const main = document.querySelector('[data-main-image]');

        if (!main) return;

        document.querySelectorAll('[data-thumb]').forEach(function (img) {
            img.addEventListener('click', function () {
                main.src = this.src;

                document.querySelectorAll('[data-thumb]').forEach(function (x) {
                    x.classList.remove('active');
                });

                this.classList.add('active');
            });
        });
    }

    initGallery();

    /* =====================================
       Share
    ===================================== */

    function initShare() {
        document.querySelectorAll('[data-share]').forEach(function (btn) {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();

                try {
                    if (navigator.share) {
                        await navigator.share({
                            title: document.title,
                            url: window.location.href
                        });
                    } else if (navigator.clipboard) {
                        await navigator.clipboard.writeText(window.location.href);
                        alert('Link copied');
                    }
                } catch (error) {
                    console.log(error);
                }
            });
        });
    }

    initShare();

    /* =====================================
       Mobile Menu
    ===================================== */

    function initMenu() {
        const menu = document.querySelector('[data-km-links]');
        const button = document.querySelector('[data-km-menu]');

        if (!menu || !button) return;

        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            menu.classList.toggle('open');
            button.classList.toggle('active');
            body.classList.toggle('kg-menu-open');
        });

        document.addEventListener('click', function (e) {
            if (
                menu.classList.contains('open') &&
                !menu.contains(e.target) &&
                !button.contains(e.target)
            ) {
                menu.classList.remove('open');
                button.classList.remove('active');
                body.classList.remove('kg-menu-open');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                menu.classList.remove('open');
                button.classList.remove('active');
                body.classList.remove('kg-menu-open');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024) {
                menu.classList.remove('open');
                button.classList.remove('active');
                body.classList.remove('kg-menu-open');
            }
        });
    }

    initMenu();

    /* =====================================
       Lazy Images Loaded State
    ===================================== */

    function initImages() {
        document.querySelectorAll('img').forEach(function (img) {
            if (img.complete) {
                img.classList.add('loaded');
            }

            img.addEventListener('load', function () {
                this.classList.add('loaded');
            });
        });
    }

    initImages();

    /* =====================================
       Smooth Scroll
    ===================================== */

    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                const href = this.getAttribute('href');

                if (!href || href === '#') return;

                const target = document.querySelector(href);

                if (target) {
                    e.preventDefault();

                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    initSmoothScroll();

})();