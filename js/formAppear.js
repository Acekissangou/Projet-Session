document.addEventListener("DOMContentLoaded", () => {
    const btnReserver = document.getElementById("btnReserver");
    const formReservation = document.getElementById("formReservation");
    const btnClose = document.getElementById("btnCancel");
    const cercle1 = document.getElementById("avatar-icon-sidebar2");


    // Afficher le formulaire au clic
    btnReserver.addEventListener("click", () => {
        formReservation.classList.add("active");
    });

    // Fermer le formulaire
    btnClose.addEventListener("click", () => {
        formReservation.classList.remove("active");
    });
});


const cercleContent = document.querySelector('.avatar-icon');



const generateColor =()=> {

    const lettre = '123456789ABCDEF';

    let couleur = '#'

    do{
        couleur = '#';

        for(let i = 0; i < 6; i++) {
            
            couleur += lettre[Math.floor(Math.random()* 16)];
        }
    }while(couleur.toLowerCase() === '#0b1220' || couleur.toLowerCase() === '#0c1321' || couleur.toLowerCase() === '#0a1020' || couleur.toLowerCase() === '#0d1422' || couleur.toLowerCase() === '#0b1120' || couleur.toLowerCase() === '#0b1221' || couleur.toLowerCase() === '#0c1220' || couleur.toLowerCase() === '#0a1220' || couleur.toLowerCase() === '#0b1320' || couleur.toLowerCase() === '#FFFFFF' || couleur.toLowerCase() === '#FEFEFE' || couleur.toLowerCase() === '#FDFDFD' || couleur.toLowerCase() === '#FCFCFC' || couleur.toLowerCase() === '#FFFEFE' || couleur.toLowerCase() === '#FEFFFF' || couleur.toLowerCase() === '#FFFFFE' || couleur.toLowerCase() === '#000000');  
        
    return couleur;
}



document.addEventListener("DOMContentLoaded", function () {

    cercleContent.style.backgroundColor = `${generateColor()}`;
    cercle1.style.backgroundColor = `${generateColor()}`;

 
});


const menuBtn = document.getElementById("menu-btn");
const menuBtn2 = document.getElementById("menu-btn2");
const sidebar = document.getElementById("sidebar");

menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("closed");
    content.classList.toggle("full");
});

menuBtn2.addEventListener("click", () => {
    sidebar.classList.toggle("closed");
    content.classList.toggle("full");
});


// Modal description
    const modal = document.getElementById("modalDescription");
    const modalText = document.getElementById("modalText");
    const closeBtn = document.querySelector(".close");

    document.querySelectorAll(".btn-view").forEach(button => {
        button.addEventListener("click", () => {
            modalText.textContent = button.dataset.description;
            modal.style.display = "block";
        });
    });

    closeBtn.onclick = () => {
        modal.style.display = "none";
    };

    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    };
