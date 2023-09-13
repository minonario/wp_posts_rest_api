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
          if ( is_wp_error( $response_media ) ) {
            $body_media['guid']['rendered'] = '';
          }else{
            $body_media = json_decode(wp_remote_retrieve_body( $response_media ),true);
          }
          $fecha = rest_parse_date($post['date']);
          $fecha = wp_date('F j, Y', $fecha);
          ?>
          <div class="ds8-post col-sm-6 col-md-4 col-lg-3">
            <a class="link" href="/informe/<?php echo $post['slug']; ?>">
            <div class="card card-noticia" data-idnoticia="<?php echo $post['id']?>">
                <div class="view" data-test="view">
                    <div class="Ripple-parent noaction" style="touch-action: unset;">
                      <img src="<?php echo $body_media['guid']['rendered']; ?>" class="img-fluid" alt="img" />
                      <div data-test="mask" class="mask rgba-white-slight"></div>
                      <div data-test="waves" class="Ripple " style="top: 0px; left: 0px; width: 0px; height: 0px;"></div>  
                    </div>
                </div>
              <div data-test="card-body" class="card-body">
                <p class="date"> <?php echo $fecha; ?> </p>
                <h4 data-test="card-title" class="card-title"> <?php echo $post['title']['rendered']; ?> </h4>
              </div>
            </div>
            </a>
                <?php //echo $post['excerpt']['rendered'] ?>
          </div>
          <?php	
          endforeach;
          ?>
</div>

<?php if ($total_pages > 1) : ?>
<div data-test="row" class="row" style="display: flex; justify-content: space-between;">
  <?php if ($page > 1) : ?>
   <button data-test="button" type="button" class="btn-default btn Ripple-parent borderRadius eventpage" data-page="<?php echo $page-1; ?>">
      Anterior
      <div data-test="waves" class="Ripple "></div>
   </button>
  <?php endif; ?>
  <?php if($total_pages > $page) : ?>
   <button data-test="button" type="button" class="btn-default btn Ripple-parent borderRadius eventpage" data-page="<?php echo $page+1; ?>">
      Siguiente
      <div data-test="waves" class="Ripple is-reppling"></div>
   </button>
  <?php endif; ?>
<?php endif; ?>
</div>