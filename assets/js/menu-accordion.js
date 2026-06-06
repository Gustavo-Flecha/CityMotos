jQuery(document).ready(function ($) {

    $('.accordion-toggle').on('click', function (e) {
        e.preventDefault();

        const parent = $(this).closest('.cat-item');
        const sublist = parent.find('.subcat-list');

        // toggle
        parent.toggleClass('open');
        sublist.slideToggle(180);

        // cerrar otros para tener menú limpio
        $('.cat-item').not(parent).removeClass('open').find('.subcat-list').slideUp(150);
    });

});

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggle-category-menu");
    const menu = document.querySelector(".citymotos-category-menu");
    const overlay = document.getElementById("menu-overlay");

    if (btn && menu && overlay) {
        btn.setAttribute("aria-expanded", "false");

        // Abrir / cerrar desde el botón
        btn.addEventListener("click", (e) => {
            e.stopPropagation(); // evita que el clic del botón cuente como clic "afuera"
            menu.classList.toggle("active");
            overlay.classList.toggle("active");
            btn.setAttribute("aria-expanded", menu.classList.contains("active") ? "true" : "false");
        });

        // Cerrar al hacer clic en el overlay
        overlay.addEventListener("click", () => {
            menu.classList.remove("active");
            overlay.classList.remove("active");
            btn.setAttribute("aria-expanded", "false");
        });

        // Cerrar si el clic es afuera del menú
        document.addEventListener("click", (e) => {
            if (
                menu.classList.contains("active") &&
                !menu.contains(e.target) &&
                e.target !== btn
            ) {
                menu.classList.remove("active");
                overlay.classList.remove("active");
                btn.setAttribute("aria-expanded", "false");
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".citymotos-category-menu");
    const handle = document.querySelector(".menu-handle");
    const overlay = document.getElementById("menu-overlay");

    if (menu && handle && overlay) {
        handle.setAttribute("aria-expanded", "false");

        const toggleMenu = () => {
            menu.classList.toggle("active");
            overlay.classList.toggle("active");
            handle.setAttribute("aria-expanded", menu.classList.contains("active") ? "true" : "false");
        };

        // abrir/cerrar desde la barrita vertical
        handle.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleMenu();
        });

        // clic en overlay = cerrar
        overlay.addEventListener("click", () => {
            menu.classList.remove("active");
            overlay.classList.remove("active");
            handle.setAttribute("aria-expanded", "false");
        });
    }
});

