document.addEventListener("DOMContentLoaded", function () {

  const slider = document.querySelector(".cm-slider");

  if (!slider) return; // seguridad

  const slidesContainer = slider.querySelector(".cm-slides");
  const slides = slider.querySelectorAll(".cm-slide");
  const nextBtn = slider.querySelector(".cm-next");
  const prevBtn = slider.querySelector(".cm-prev");

  if (!slidesContainer || !slides.length || !nextBtn || !prevBtn) return;

  let index = 0;
  const total = slides.length;

  nextBtn.addEventListener("click", function () {
    index = (index + 1) % total;
    slidesContainer.style.transform = "translateX(-" + (index * 100) + "%)";
  });

  prevBtn.addEventListener("click", function () {
    index = (index - 1 + total) % total;
    slidesContainer.style.transform = "translateX(-" + (index * 100) + "%)";
  });

});
