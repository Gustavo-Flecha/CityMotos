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

        // Abrir / cerrar desde el botón
        btn.addEventListener("click", (e) => {
            e.stopPropagation(); // evita que el clic del botón cuente como clic "afuera"
            menu.classList.toggle("active");
            overlay.classList.toggle("active");
        });

        // Cerrar al hacer clic en el overlay
        overlay.addEventListener("click", () => {
            menu.classList.remove("active");
            overlay.classList.remove("active");
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
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".citymotos-category-menu");
    const handle = document.querySelector(".menu-handle");
    const overlay = document.getElementById("menu-overlay");

    if (menu && handle && overlay) {

        const toggleMenu = () => {
            const isActive = menu.classList.contains("active");
            menu.classList.toggle("active");
            overlay.classList.toggle("active");
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
        });
    }
});

