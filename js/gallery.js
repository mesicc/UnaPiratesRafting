// --- Main entry ---
$(document).ready(function () {
  createLightboxStructure(); // Ensure lightbox exists immediately
  setupGallery();
  setupCarousel();
  initLightbox();
});

let galleryLoaded = false;

function loadImages() {
  if (galleryLoaded) return; // Prevent loading again if already loaded
  galleryLoaded = true;

  const galleryItems = [
    { src: "materijali/newphoto1.webp", title: "Family enjoying rafting", caption: "Kostela Rafting" },
    { src: "materijali/newphoto2.webp", title: "Guide standing near waterfall", caption: "Kostela Rafting" },
    { src: "materijali/newphoto3.webp", title: "Group rafting team", caption: "Kostela Rafting" },
    { src: "materijali/newphoto4.webp", title: "Girl on a red kayak", caption: "Kostela Rafting" },
    { src: "materijali/newphoto5.webp", title: "Group Lohovo Pirates sign", caption: "Strbacki Rafting" },
    { src: "materijali/photo3.webp", title: "Rafting in a cave", caption: "Kostela Rafting" },
    { src: "materijali/rent6.webp", title: "Woman diving into the river", caption: "Strbacki Rafting" },
    { src: "materijali/newbgnd.webp", title: "Group rafting team", caption: "Kostela Rafting" },
    { src: "materijali/newphoto6.webp", title: "Two women posing by the river", caption: "Strbacki Rafting" },
    { src: "materijali/newphoto7.webp", title: "Leon on top of the world", caption: "Strbacki Rafting" },
  ];

  // Load into Desktop Gallery
  galleryItems.forEach(item => {
    $("#gallery").append(`
      <div class="gallery-item">
        <img
          class="thumb placeholder"
          src="${item.src}"
          data-image="${item.src}"
          data-title="${item.title}"
          alt="${item.title}"
        />
        <div class="caption"><span>${item.caption}</span></div>
      </div>
    `);
  });

  // Load into Mobile Carousel
  galleryItems.forEach((item, index) => {
    $("#carousel-viewport").append(`
      <li id="carousel__slide${index+1}" tabindex="0" class="carousel__slide" style="background-image: url('${item.src}')">
        <div class="carousel__snapper"></div>
      </li>
    `);

    $("#carousel-navigation").append(`
      <li class="carousel__navigation-item">
        <a href="#carousel__slide${index+1}" class="carousel__navigation-button">Go to slide ${index+1}</a>
      </li>
    `);
  });

  bindGalleryClicks(); // Bind lightbox clicks again

  // ✅ Change button instead of hiding it
  const button = document.querySelector('.gallery_seeMoreButton button');
  button.innerText = "Sve slike učitane!";
  button.style.backgroundColor = "#999"; // Light grey
  button.style.cursor = "default"; // No hover effect anymore
  button.disabled = true; // Disable the button
}

// --- Load gallery items into #gallery when in view ---
function setupGallery() {
  const galleryItems = [
    { src: "materijali/photo1.webp", title: "Family enjoying rafting", caption: "Lohovo Rafting" },
    { src: "materijali/photo2.webp", title: "Guide standing near waterfall", caption: "Waterfall Adventure" },
    { src: "materijali/ekipa2.webp", title: "Group rafting team", caption: "Team Visionect" },
    { src: "materijali/photo4.webp", title: "Girl on a red kayak", caption: "Kayaking Fun" },
    { src: "materijali/raftingsl.webp", title: "Group Lohovo Pirates sign", caption: "Lohovo Pirates" },
    { src: "materijali/raftingsl2.webp", title: "Rafting in a cave", caption: "Cave Rafting" },
    { src: "materijali/cura2.webp", title: "Woman diving into the river", caption: "River Dive" },
    { src: "materijali/ekipa3.webp", title: "Group rafting team", caption: "Team Adventure" },
    { src: "materijali/cure.webp", title: "Two women posing by the river", caption: "Nature Enjoyment" },
    { src: "materijali/leonnavodi.webp", title: "Leon on top of the world", caption: "Nature Enjoyment" },
  ];

  const loadGalleryItems = () => {
    galleryItems.forEach(item => {
      $("#gallery").append(`
        <div class="gallery-item">
          <img
            class="thumb placeholder"
            src="${item.src}"
            data-image="${item.src}"
            data-title="${item.title}"
            alt="${item.title}"
          />
          <div class="caption"><span>${item.caption}</span></div>
        </div>
      `);
    });
  };

  const observer = new IntersectionObserver(entries => {
    if (entries[0].isIntersecting) {
      loadGalleryItems();
      observer.disconnect();
    }
  }, { threshold: 0.1 });

  observer.observe(document.querySelector("#galerija"));
}

// --- Setup carousel for mobile screens ---
function setupCarousel() {
  const slides = [
    { id: "carousel__slide1", label: "Go to slide 1" },
    { id: "carousel__slide2", label: "Go to slide 2" },
    { id: "carousel__slide3", label: "Go to slide 3" },
    { id: "carousel__slide4", label: "Go to slide 4" },
  ];

  const loadCarousel = () => {
    if (window.innerWidth <= 768 && $("#carousel-viewport").children().length === 0) {
      slides.forEach(slide => {
        $("#carousel-viewport").append(`
          <li id="${slide.id}" tabindex="0" class="carousel__slide">
            <div class="carousel__snapper"></div>
          </li>
        `);
        $("#carousel-navigation").append(`
          <li class="carousel__navigation-item">
            <a href="#${slide.id}" class="carousel__navigation-button">${slide.label}</a>
          </li>
        `);
      });
    }
  };

  loadCarousel();
  $(window).resize(loadCarousel);
}

// --- Lightbox functionality ---
let $lightbox;
let images = [];
let currentIndex = 0;

function initLightbox() {
  bindGalleryClicks();
}

// Create the lightbox HTML structure
function createLightboxStructure() {
  const $wrapper = $('<div class="lightbox-wrapper">').hide();
  $lightbox = $('<div class="lightbox">');

  $lightbox.append(`
    <div class="lightbox-header">
      <div class="lightbox-numbers"></div>
      <div class="lightbox-title"></div>
      <button type="button" class="lightbox-close" aria-label="Close"></button>
    </div>
    <div class="lightbox-slides-wrapper">
      <div class="lightbox-slide" data-state="prev"><img class="lightbox-image" draggable="false" /><div class="spinner"></div></div>
      <div class="lightbox-slide" data-state="current"><img class="lightbox-image" draggable="false" /><div class="spinner"></div></div>
      <div class="lightbox-slide" data-state="next"><img class="lightbox-image" draggable="false" /><div class="spinner"></div></div>
    </div>
    <div class="lightbox-arrow arrow-left"></div>
    <div class="lightbox-arrow arrow-right"></div>
  `);

  $wrapper.append($lightbox);
  $("body").append($wrapper);
}

// Attach click event to dynamically created gallery items
function bindGalleryClicks() {
  $(document).on("click", ".gallery-item", function () {
    const $gallery = $(this).closest("#gallery");
    const index = $(this).index();

    images = $gallery.find(".gallery-item img").map((_, img) => ({
      src: $(img).data("image"),
      title: $(img).data("title"),
    })).get();

    openLightbox(index);
  });
}

// Open lightbox at given index
function openLightbox(index) {
  currentIndex = index;
  updateLightboxContent();

  $(".lightbox-wrapper").fadeIn("fast");
  $lightbox.addClass("open");

  $lightbox.find(".lightbox-close").on("click", closeLightbox);
}

// Close the lightbox
function closeLightbox() {
  $(".lightbox-wrapper").fadeOut("fast", function () {
    $lightbox.removeClass("open");
  });
}

// Update content inside lightbox based on currentIndex
function updateLightboxContent() {
  const image = images[currentIndex];

  $lightbox.find(".lightbox-title").text(image.title);
  $lightbox.find(".lightbox-numbers").text(`${currentIndex + 1}/${images.length}`);

  const $currentSlide = $lightbox.find('.lightbox-slide[data-state="current"]');
  const $currentImage = $currentSlide.find(".lightbox-image");
  const $spinner = $currentSlide.find(".spinner");

  $spinner.show();
  $currentImage.hide();

  $currentImage
    .off("load") // clear previous handlers just in case
    .on("load", function () {
      $spinner.hide();
      $currentImage.show();
    })
    .attr("src", image.src);
}


// Move to next or previous image
function navigateLightbox(direction) {
  if (direction === "next") currentIndex = (currentIndex + 1) % images.length;
  else if (direction === "prev") currentIndex = (currentIndex - 1 + images.length) % images.length;

  updateLightboxContent();
}

// Arrows Navigation
$(document).on("click", ".arrow-left", () => navigateLightbox("prev"));
$(document).on("click", ".arrow-right", () => navigateLightbox("next"));

// Close on ESC key
$(document).on("keydown", (e) => {
  if ($lightbox.hasClass("open")) {
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowRight") navigateLightbox("next");
    if (e.key === "ArrowLeft") navigateLightbox("prev");
  }
});
