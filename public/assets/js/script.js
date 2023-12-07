function adjustMargin() {
  // Vérifie si la navbar existe et est visible
  let navbar = document.querySelector('.navbar');
  let navbarHeight = navbar ? navbar.offsetHeight : 0;

  // Définit la marge supérieure pour les éléments spécifiés
  document.querySelectorAll('.reset, .condi, .legal, .historiquePage, .one, .shop, .divPresta, .detailProd, .reservationPage, .validPage, .panierPage, .formCommandePage, .validCommandePage, .loginPage, .registerPage, .editProfil, .profil')
    .forEach(el => {
      el.style.marginTop = navbarHeight + 'px';
    });
}

// Ajuster au chargement de la page et lors du redimensionnement de la fenêtre
// window.onload = adjustMargin;
window.onresize = adjustMargin;



function onSubmit(token) {
  document.getElementById("register-form").submit();
}

function getOffset (){
  
  if (window.innerWidth > 1200 ){
    return 180 }
  else if (window.innerWidth > 768 ){
    return 130 
  }else{
    return -5
  }
}

window.onload = function () {
  adjustMargin();
  if (window.location.hash === '#six') {

    const section = document.getElementById('six');

    const topPos = section.getBoundingClientRect().top + window.scrollY - getOffset();

    setTimeout(function () {
      window.scrollTo({
        top: topPos,
        behavior: 'smooth'
      });

    }, 10)
  }
}
const section = document.getElementById('six');
if (section) {
  document.querySelector('a[href="/#six"]').addEventListener('click', function (e) {

    e.preventDefault();
    const topPos = section.offsetTop;

    window.scrollTo({

      top: topPos - getOffset(), // ici tu mets + ou - en fonction de si t'es trop haut ou pas

    });

  });
}
//Supprime le label Rdv dans mon reserve preta

document.querySelectorAll('label.required').forEach(function(label) {
  if (label.textContent.trim() === 'Rdv') {
      label.style.display = 'none';
  }
});