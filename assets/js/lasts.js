/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */


jQuery( function ( $ ) {
  
  $(document).on('click','.card-noticia .Ripple-parent:not(.noaction),.card-noticia .card-body,.fd-name', function(event){
    event.stopPropagation();
    event.preventDefault();
    //console.log($(event.target).closest('.card').attr('class'));
    var $this = $(event.target).closest('.card');
    //console.log('CC:'+$this.data("idnoticia"));
    console.log('action=='+$this.hasClass('noaction'));
    if (!$this.hasClass('noaction')) {
      $('#myModal').modal('toggle',{ idnoticia: $this.data("idnoticia")});
    }
  });
  
  $(document).on('hidden.bs.modal','#myModal', function (e) {
    var modal = $(this)
    modal.find('.contentHtml').html("")
    modal.find('h3.modalNoticia').html("")
    modal.find('.date.modalNoticia span').html("")
    modal.find('.btn.guid').attr('href',"")
  })
  
  $(document).on('shown.bs.modal','#myModal', function (event) {
    var button = $(event.relatedTarget);
    var idnoticia = '0';
    var modal = $(this)
    modal.find('.contentHtml').html('<div class="container-load-posts"><div class="spinner-border" role="status"><span class="sr-only">Cargando...</span></div></div>');
    
    $.ajax({
        url: lasts.ajaxurl,
        type: "POST",
        data: {action: 'lasts_action',
          idnoticia: button[0].idnoticia,
          security: lasts.security
        },  
        success: function (response) {
            if (response) {
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