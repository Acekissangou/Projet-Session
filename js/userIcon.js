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
// const qrBtns = document.querySelectorAll('.qr_code_btn');
// const qrModal = document.getElementById('qrModal');
// const qrImage = document.getElementById('qrImage');
// const qrClose = document.querySelector('.qr-close');

// qrBtns.forEach(btn => {
//     btn.addEventListener('click', () => {
//         const token = btn.dataset.token;

//         // chemin vers l'image QR (déjà générée)
//         qrImage.src = `../qrCodes/${token}.png`;

//         qrModal.style.display = 'flex';
//     });
// });

// // Fermer le modal
// qrClose.addEventListener('click', () => {
//     qrModal.style.display = 'none';
// });

// // Fermer si clic en dehors du modal
// window.addEventListener('click', (e) => {
//     if (e.target == qrModal) {
//         qrModal.style.display = 'none';
//     }
// });


// QR Code display
    const qrBtns = document.querySelectorAll('.qr_code_btn');
    const qrModal = document.getElementById('qrModal');
    const qrImage = document.getElementById('qrImage');
    const qrClose = document.querySelector('.qr-close');
    
    // Vérifier si les éléments QR existent
    if (qrBtns.length > 0 && qrModal && qrImage && qrClose) {
        // Créer le bouton de téléchargement
        const downloadBtn = document.createElement('button');
        downloadBtn.id = 'downloadQrBtn';
        downloadBtn.className = 'download-btn';
        downloadBtn.innerHTML = '<i class="fas fa-download"></i> Télécharger QR Code';
        
        // Ajouter le bouton après l'image QR
        qrImage.parentNode.insertBefore(downloadBtn, qrImage.nextSibling);
        
        // Variable pour stocker le token actuel
        let currentToken = '';
        
        qrBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const token = btn.dataset.token;
                currentToken = token; // Stocker le token pour le téléchargement
                
                // chemin vers l'image QR
                qrImage.src = `../qrCodes/${token}.png`;
                
                // Mettre à jour l'URL du bouton de téléchargement
                downloadBtn.onclick = () => {
                    downloadQRCode(token);
                };
                
                qrModal.style.display = 'flex';
            });
        });
        
        // Fonction pour télécharger le QR Code
        function downloadQRCode(token) {
            // Créer un lien temporaire
            const downloadLink = document.createElement('a');
            downloadLink.href = `../qrCodes/${token}.png`;
            downloadLink.download = `qr-code-${token}.png`; // Nom du fichier
            downloadLink.target = '_blank';
            
            // Simuler un clic pour déclencher le téléchargement
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        
        qrClose.addEventListener('click', () => {
            qrModal.style.display = 'none';
        });
        
        window.addEventListener('click', (e) => {
            if (e.target == qrModal) {
                qrModal.style.display = 'none';
            }
        });
    }
    
});