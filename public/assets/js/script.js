function adjustMargin() {
  // Récupère l'élément avec la classe 'navbar' et stocke sa hauteur dans 'navbarHeight'
  let navbarHeight = document.querySelector('.navbar').offsetHeight;
  // console.log(navbarHeight)
  // Définit la marge supérieure de l'élément avec la classe 'one' 
  // pour être égale à la hauteur de la navbar (en pixels)
  document.querySelector('.reset, .historiquePage, .one,.shop,.divPresta,.detailProd,.reservationPage,.validPage,.panierPage,.formCommandePage,.validCommandePage,.loginPage,.registerPage,.editProfil,.profil').style.marginTop = navbarHeight + 'px';
}
// Ajuster au chargement de la page
// window.onload = adjustMargin();

// Ajuster lors du redimensionnement de la fenêtre
window.onresize = adjustMargin();


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