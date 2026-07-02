// active nav link (Fungsi Asli Bawaan Kamu)
var ul = document.querySelector('#ul');
var li = document.querySelectorAll('#li');

if(ul && li.length > 0) {
    li.forEach(el => {
        el.addEventListener('click', function() {
            ul.querySelector('.active').classList.remove('active');
            el.classList.add('active');
        });
    });
}

// image slider (Fungsi Asli Swiper Kamu)
if (document.querySelector('.swiper')) {
    const swiper = new Swiper('.swiper', {
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
}

// =========================================================
// INTERAKSI DROPDOWN MENU PROFILE (KODE GABUNGAN BARU)
// =========================================================
document.addEventListener("DOMContentLoaded", function () {
    const profileTrigger = document.getElementById("profileTrigger");
    const myDropdown = document.getElementById("myDropdown");

    // Jika user dalam keadaan login (Trigger Dropdown terdeteksi di layar)
    if (profileTrigger && myDropdown) {
        // Aksi ketika area profil diklik
        profileTrigger.addEventListener("click", function (event) {
            event.stopPropagation(); // Mencegah bentrok dengan klik global window
            myDropdown.classList.toggle("show-menu");
        });

        // Aksi menutup dropdown secara otomatis ketika pengguna klik di luar menu
        window.addEventListener("click", function (event) {
            if (!event.target.closest(".profile-dropdown-wrapper")) {
                if (myDropdown.classList.contains("show-menu")) {
                    myDropdown.classList.remove("show-menu");
                }
            }
        });
    }
});