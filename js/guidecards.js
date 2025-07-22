$(document).ready(function () {
  const profiles = [
    { name: "Irman", role: "Team Lead", img: "materijali/irman.webp" },
    { name: "Leon", role: "Guide", img: "materijali/leon.webp" },
    { name: "Endi", role: "Guide", img: "materijali/endi.webp" },
    { name: "Ervin", role: "Guide", img: "materijali/galic.webp" },
    { name: "Haris", role: "Guide", img: "materijali/haris.webp" },
    { name: "Tarik", role: "Guide", img: "materijali/skender.webp" },
  ];

  profiles.forEach((profile) => {
    $("#profile-cards").append(`
        <div class="profile-card card2">
          <div class="profile-card__content content2">
            <img src="${profile.img}" class="profile-card__circle circle2" />
            <h2>${profile.name}</h2>
            <h3>${profile.role}</h3>
          </div>
        </div>
      `);
  });
});
