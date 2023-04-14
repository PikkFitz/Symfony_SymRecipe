
// !!!!!  ALERT POUR SUPPRESSION  !!!!!

const supp = document.querySelectorAll('.supp');

supp.forEach(element => {
  element.addEventListener("click", loadAlertDelete)
});

function loadAlertDelete(e) 
{
  e.preventDefault();

  let url = e.currentTarget.getAttribute('href');
  console.log(url);


  Swal.fire({
    title: 'Supprimer la recette ?',
    text: "La recette ne pourra plus être récupérée...",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: 'rgb(219, 44, 44)',
    cancelButtonColor: 'rgb(17, 122, 141)',
    confirmButtonText: 'Supprimer'
  }).then((result) => {
      if (result.isConfirmed) 
      {
        Swal.fire(
          'Suppression',
          'Votre recette a été supprimée !',
          'error'
        )
        .then(() => 
        {
          window.location.href=url;
        })
      }
    })
}

