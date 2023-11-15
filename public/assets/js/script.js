function adjustMargin() {
    // Récupère l'élément avec la classe 'navbar' et stocke sa hauteur dans 'navbarHeight'
    var navbarHeight = document.querySelector('.navbar').offsetHeight;
      // Définit la marge supérieure de l'élément avec la classe 'one' 
    // pour être égale à la hauteur de la navbar (en pixels)
    document.querySelector('.one,.shop,.divPresta,.detailProd,.reservationPage,.validPage,.panierPage,.formCommandePage,.validCommandePage,.loginPage,.registerPage,.editProfil,.profil').style.marginTop = navbarHeight + 'px';
  }
  // Ajuster au chargement de la page
  window.onload = adjustMargin;
  
  // Ajuster lors du redimensionnement de la fenêtre
  window.onresize = adjustMargin;
  

  function onSubmit(token) {
    document.getElementById("demo-form").submit();
  }
