// !!!!!  ALERT POUR MODIFICATION  !!!!!

const add = document.querySelector('#swa-confirm-add');

const form = document.querySelector("form");

add.addEventListener("click", loadAlertAdd);
console.log(add);


function loadAlertAdd(e) 
{
  e.preventDefault();

  Swal.fire({
    title: 'Ajouter la recette ?',
    text: "La recette va être Ajoutée à la liste",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: 'rgb(19, 133, 19)',
    cancelButtonColor: 'rgb(219, 44, 44)',
    confirmButtonText: 'Ajouter',
  }).then((result) => {
    if (result.isConfirmed) 
    {
      Swal.fire(
        'Ajout',
        'Votre recette a été ajoutée !',
        'success'
      ).then(() => 
      {
        form.submit();
      })
    }
  })
}


