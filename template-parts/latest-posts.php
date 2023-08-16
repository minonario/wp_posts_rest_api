<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
<div class="ds8-posts-container row">
          <?php
          foreach ($body as $post ) :
          $image = $post['_links']['wp:featuredmedia'];
          $response_media = wp_remote_get( $image[0]['href'] );
          $fecha = rest_parse_date($post['date']);
          $fecha = wp_date('F j, Y', $fecha);
          $body_media     = json_decode(wp_remote_retrieve_body( $response_media ),true);
          ?>
          <div class="ds8-post col-sm-6 col-md-4 col-lg-3">
            <div class="card card-noticia" data-toggle="modal" target="#myModal" data-idnoticia="<?php echo $post['id']?>">
                <div class="view" data-test="view">
                    <div class="Ripple-parent" style="touch-action: unset;">
                      <img src="<?php echo $body_media['guid']['rendered']; ?>" class="img-fluid" alt="img" />
                      <div data-test="mask" class="mask rgba-white-slight"></div>
                      <div data-test="waves" class="Ripple " style="top: 0px; left: 0px; width: 0px; height: 0px;"></div>  
                    </div>
                </div>
              <div data-test="card-body" class="card-body">
                <p class="date"> <?php echo $fecha; ?> </p>
                <a href="<?php echo $post['link']; ?>" class="fd-name">
                <h4 data-test="card-title" class="card-title"> <?php echo $post['title']['rendered']; ?> </h4>
                </a>
              </div>
            </div>
                <?php //echo $post['excerpt']['rendered'] ?>
          </div>	
          <?php	
          endforeach;
          ?>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-lg modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      </div>
      <div class="modal-body">
        <div class="text-left"><h3 class="modalNoticia"></h3></div>
        <div class="scrollbar" id="style-2">
          <div class="date modalNoticia"><span></span></div>
          <div class="content">
            <div class="contentHtml modalNoticia">
            
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-default btn Ripple-parent btn-modal" data-dismiss="modal">Cerrar</button>
        <a data-test="button" target="_blank" role="button" class="btn-default btn Ripple-parent btn-modal guid" href="#">Leer más en Finanzas Digital
          <div data-test="waves" class="Ripple " style="top: 0px; left: 0px; width: 0px; height: 0px;"></div>
        </a>
      </div>
    </div>
  </div>
</div>