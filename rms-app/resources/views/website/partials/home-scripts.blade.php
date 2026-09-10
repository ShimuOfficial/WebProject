<script>
    const menuSearch = document.getElementById('menuSearch');
    const menuEmpty = document.getElementById('menuEmpty');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const categoryDropdownButton = document.getElementById('categoryDropdownButton');
    const categoryDropdownMenu = document.getElementById('categoryDropdownMenu');
    const catTabs = Array.from(categoryDropdownMenu?.querySelectorAll('.cat-tab') || []);
    let activeCategory = '';

    function setActiveCategory(category, label) {
        activeCategory = category;
        if (categoryDropdownButton) {
            categoryDropdownButton.textContent = `Category: ${label || 'All products'}`;
            categoryDropdownButton.setAttribute('aria-expanded', 'false');
        }
        filterCards();
        closeCategoryDropdown();
    }

    function filterCards() {
        const q = (menuSearch?.value || '').trim().toLowerCase();
        let matched = 0;

        document.querySelectorAll('.menu-filter-card').forEach(card => {
            const cat = card.dataset.category || '';
            const ok = (!activeCategory || cat === activeCategory) && (!q || card.dataset.search.includes(q));
            if (ok) matched++;
            card.style.display = ok ? '' : 'none';
        });

        if (menuEmpty) menuEmpty.style.display = matched === 0 ? 'block' : 'none';
    }

    function closeCategoryDropdown() {
        if (categoryDropdownMenu) {
            categoryDropdownMenu.hidden = true;
        }
    }

    function toggleCategoryDropdown() {
        if (!categoryDropdownMenu) return;
        const isOpen = !categoryDropdownMenu.hidden;
        categoryDropdownMenu.hidden = isOpen;
        if (categoryDropdownButton) {
            categoryDropdownButton.setAttribute('aria-expanded', String(!isOpen));
        }
    }

    menuSearch?.addEventListener('input', () => {
        filterCards();
    });
    closeCategoryDropdown();
    categoryDropdownButton?.addEventListener('click', toggleCategoryDropdown);
    catTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            setActiveCategory(tab.dataset.category, tab.textContent.trim());
        });
    });
    document.addEventListener('click', event => {
        if (!categoryDropdown) return;
        if (!categoryDropdown.contains(event.target)) {
            closeCategoryDropdown();
            if (categoryDropdownButton) {
                categoryDropdownButton.setAttribute('aria-expanded', 'false');
            }
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            closeCategoryDropdown();
            if (categoryDropdownButton) {
                categoryDropdownButton.setAttribute('aria-expanded', 'false');
            }
        }
    });
    filterCards();

    const navToggle = document.getElementById('siteNavToggle');
    const navInner = document.querySelector('.nav-inner');
    navToggle?.addEventListener('click', () => {
        const open = navInner?.classList.toggle('is-open');
        navToggle.setAttribute('aria-expanded', String(!!open));
    });

    const lightbox = document.getElementById('imgLightbox');
    const lightboxImage = document.getElementById('imgLightboxImage');
    const lightboxCaption = document.getElementById('imgLightboxCaption');
    const lightboxClose = document.getElementById('imgLightboxClose');

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.hidden = true;
        lightbox.classList.remove('is-open');
    }

    document.querySelectorAll('.js-dish-preview').forEach(button => {
        button.addEventListener('click', () => {
            if (!lightbox || !lightboxImage) return;
            lightboxImage.src = button.dataset.image || '';
            lightboxImage.alt = button.dataset.title || '';
            if (lightboxCaption) lightboxCaption.textContent = button.dataset.title || '';
            lightbox.hidden = false;
            lightbox.classList.add('is-open');
        });
    });

    lightboxClose?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', event => {
        if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeLightbox();
    });

    // Signature section JS removed per request

    // Navbar scroll effect
    const nav = document.querySelector('.nav');
    window.addEventListener('scroll', () => {
        nav.style.boxShadow = window.scrollY > 20 ? '0 4px 30px rgba(0,0,0,.5)' : 'none';
    });
</script>
