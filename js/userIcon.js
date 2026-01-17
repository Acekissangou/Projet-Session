document.addEventListener('DOMContentLoaded', ()=> {


const menuBtn = document.getElementById("menu-btn");
const menuBtn2 = document.getElementById("menu-btn2");
const sidebar = document.getElementById("sidebar");
const content = document.querySelector(".container");
const message = document.querySelector('.message');
const maDiv = document.querySelector('.upload-profil');

// Toggle sidebar
if (menuBtn && sidebar && content) {
    menuBtn.addEventListener("click", () => {
        sidebar.classList.toggle("closed");
        content.classList.toggle("full");
    });
}

// Toggle sidebar
if (menuBtn2 && sidebar && content) {
    menuBtn2.addEventListener("click", () => {
        sidebar.classList.toggle("closed");
        content.classList.toggle("full");
    });
}

// Message auto-disparition
if (message) {
    setTimeout(() => {
        message.style.opacity = "0";
        setTimeout(() => {
            message.style.display = "none";
        }, 500); // delay correspond à transition
    }, 3500);
}

// Toggle upload profil div
// if (avatar && maDiv) {
//     avatar.addEventListener('click', () => {
//         maDiv.style.visibility = (maDiv.style.visibility === 'hidden') ? 'visible' : 'hidden';
//     });
// }


// QR Code display
const qrBtns = document.querySelectorAll('.qr_code_btn');
const qrModal = document.getElementById('qrModal');
const qrImage = document.getElementById('qrImage');
const qrClose = document.querySelector('.qr-close');

qrBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const token = btn.dataset.token;

        // chemin vers l'image QR (déjà générée)
        qrImage.src = `../qrCodes/${token}.png`;

        qrModal.style.display = 'flex';
    });
});

// Fermer le modal
qrClose.addEventListener('click', () => {
    qrModal.style.display = 'none';
});

// Fermer si clic en dehors du modal
window.addEventListener('click', (e) => {
    if (e.target == qrModal) {
        qrModal.style.display = 'none';
    }
});


})