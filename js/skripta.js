var muteButton = document.getElementById("mute-button");
if (muteButton) {
  muteButton.addEventListener("click", function () {
    var video = document.getElementById("bg-video");
    video.muted = !video.muted;
  });
}
//<---> za video

document.addEventListener("DOMContentLoaded", function () {
  const loadButton = document.getElementById("button-loadvideo");
  const spinner = document.getElementById("spinner");
  const parentDiv = document.getElementsByClassName(
    "video-presentation-section"
  )[0];
  const videoContainer = document.getElementById("video-container");

  loadButton.addEventListener("click", function () {
    // Show the spinner and hide the button
    spinner.style.display = "block";
    loadButton.style.display = "none";
    parentDiv.classList.add("height");

    // Create the video element dynamically
    const video = document.createElement("video");
    video.setAttribute("id", "bg-video2");
    video.setAttribute("autoplay", "true");
    video.setAttribute("loop", "true");
    video.setAttribute("muted", "true");
    video.setAttribute("playsinline", "true");
    video.style.display = "none";

    const source = document.createElement("source");
    source.setAttribute("src", "/materijali/videopres.mp4");
    source.setAttribute("type", "video/mp4");

    video.appendChild(source);
    videoContainer.appendChild(video);

    // Event listener for when the video is ready to play
    video.addEventListener("canplaythrough", function () {
      spinner.style.display = "none";
      video.style.display = "block";
      video.play();
    });

    // Load the video
    video.load();
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const video = document.getElementById("bg-video");

  const observerOptions = {
    root: null,
    rootMargin: "0px",
    threshold: 0.1,
  };

  const videoObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        video.play();
      } else {
        video.pause();
      }
    });
  }, observerOptions);

  videoObserver.observe(video);
});

//<--->

$(document).ready(function () {
  $(".wrapper ul li a").click(function () {
    $("#active").prop("checked", false);
    // Enable scrolling when menu is closed
    $("body").css("overflow", "auto");
  });

  $("#active").change(function () {
    if ($(this).is(":checked")) {
      // Disable scrolling when menu is opened
      $("body").css("overflow", "hidden");
    } else {
      // Enable scrolling when menu is closed
      $("body").css("overflow", "auto");
    }
  });
});

var previousScroll = 0;

$(window).scroll(function () {
  if ($(window).width() <= 768) {
    // Change this value to the maximum width for mobile devices
    var currentScroll = $(this).scrollTop();
    if (currentScroll > previousScroll && currentScroll > 100) {
      $(".menu-btn").fadeOut();
    } else {
      $(".menu-btn").fadeIn();
    }
    previousScroll = currentScroll;
  }
});

function openpage() {
  var filename = "proizvodi";
  window.open(filename + ".html", "_blank");
}

function openGoogleMaps() {
  window.open(
    "https://www.google.com/maps/place/Una+Pirates+Rafting+Bihac/@44.8108414,15.868915,15z/data=!4m8!3m7!1s0x476141bf285bfc63:0xf2ebb5bb72af4b04!8m2!3d44.8108414!4d15.868915!9m1!1b1!16s%2Fg%2F11rcs79r7s?entry=ttu"
  );
}
function openAktivnosti() {
  window.location.href = "aktivnosti.html";
}
function openKajak() {
  window.location.href = "kajak.html";
}
function openSmjestaj() {
  window.location.href = "smjestaj.html";
}
function openRent() {
  window.location.href = "rent.html";
}
function openExtra() {
  window.location.href = "extra.html";
}
//function openForm() {
  //window.open(
    //"https://docs.google.com/forms/d/e/1FAIpQLSeOp6Sr6GmS1bKIEuD9FFxkPxMT-XczuHi2WRreTEFHTUuCAA/viewform?fbclid=IwAR0m4iu2QkcKjmywdE9rwg2oXq1Smz64DsNbsnT3hiK5pKzt8C0ysK2Rw84"
  //);
//}

function googleTranslateElementInit() {
  new google.translate.TranslateElement(
    {
      pageLanguage: "bs",
      layout: google.translate.TranslateElement.InlineLayout.HIDDEN,
      includedLanguages: "ar,en,bs",
    },
    "google_translate_element"
  );
}

function scrollToSection(home) {
  var section = document.getElementById(home);
  if (section) {
    section.scrollIntoView({ behavior: "smooth" });
  }
}



