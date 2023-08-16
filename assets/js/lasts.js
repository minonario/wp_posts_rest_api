/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


jQuery( function ( $ ) {
  
  $(document).on('click','.card-noticia .Ripple-parent,.card-noticia .card-body,.fd-name', function(event){
    event.stopPropagation();
    event.preventDefault();
    console.log($(event.target).closest('.card').attr('class'));
    var $this = $(event.target).closest('.card');
    console.log('CC:'+$this.data("idnoticia"));
    $('#myModal').modal('toggle',{ idnoticia: $this.data("idnoticia")});
  });
  
  $('#myModal').on('hidden.bs.modal', function (e) {
    var modal = $(this)
    modal.find('.contentHtml').html("")
    modal.find('h3.modalNoticia').html("")
    modal.find('.date.modalNoticia span').html("")
  })
  
  $('#myModal').on('shown.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var idnoticia = '0'; 
    
    var modal = $(this)
    //modal.find('.modal-title').text('New message to ');
    $.ajax({
        url: lasts.ajaxurl,
        type: "POST",
        data: {action: 'lasts_action',
          idnoticia: button[0].idnoticia,
          security: lasts.security
        },  
        success: function (response) {
            console.log(response);
            if (response) {
                console.log(response['data']);
                modal.find('h3.modalNoticia').html(response['data'].title);
                modal.find('.contentHtml').html(response['data'].content);
                modal.find('.contentHtml .ds8-rp-container').remove();
                modal.find('.date.modalNoticia span').html(response['data'].date);
                modal.find('.btn.guid').attr('href',response['data'].guid);
            }
        }
    });
    
    
  });
  
});