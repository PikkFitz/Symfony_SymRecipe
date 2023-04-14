// !!!!!  ALERT POUR MODIFICATION  !!!!!

const modif = document.querySelector('#swa-confirm-edit');

const form = document.querySelector("form");

modif.addEventListener("click", loadAlertEdit);
console.log(modif);


function loadAlertEdit(e) 
{
  e.preventDefault();

  Swal.fire({
    title: 'Modifier l\'ingrédient ?',
    text: "L'ingrédient va être modifié",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: 'rgb(17, 122, 141)',
    cancelButtonColor: 'rgb(219, 44, 44)',
    confirmButtonText: 'Modifier',
  }).then((result) => {
    if (result.isConfirmed) 
    {
      Swal.fire(
        'Modification',
        'Votre ingrédient a été modifié !',
        'info'
      ).then(() => 
      {
        form.submit();
      })
    }
  })
}


