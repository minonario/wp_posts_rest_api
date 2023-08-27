<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
?>
<div data-test="card-group" class="card-group">
      <div data-test="row" class="row">
      
      <?php
      foreach ($body as $post ) :
      $image = $post['_links']['wp:featuredmedia'];
      $response_media = wp_remote_get( $image[0]['href'] );
      if ( is_wp_error( $response_media ) ) {
        $body_media['guid']['rendered'] = '';
      }else{
        $body_media = json_decode(wp_remote_retrieve_body( $response_media ),true);
      }
      ?>

         <div data-test="col" class="col-sm-6 col-md-6 col-lg-4">
            <a class="link" href="/dictamen/<?php echo $post['slug']; ?>">
               <div data-test="card" class="card card-dictamen2">
                  <div class="view" data-test="view">
                     <div class="Ripple-parent" style="touch-action: unset;">
                        <img data-test="card-image" src="<?php echo $body_media['guid']['rendered']; ?>" alt="" class="img-fluid">
                        <div data-test="mask" class="mask rgba-white-slight"></div>
                        <div data-test="waves" class="Ripple " style="top: 0px; left: 0px; width: 0px; height: 0px;"></div>
                     </div>
                  </div>
                  <div data-test="card-body" class="card-body">
                     <h4 data-test="card-title" class="card-title"> <?php echo $post['title']['rendered']; ?> </h4>
                  </div>
               </div>
            </a>
         </div>
      <?php	
      endforeach;
      ?>
      </div>
</div>