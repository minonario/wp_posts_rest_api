<?php
/**
 * The template for displaying all pages, single posts and attachments.
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package     Sinatra
 * @author      Sinatra Team <hello@sinatrawp.com>
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<?php 
  //var_dump($GLOBALS['bodyd']);
  $data = $GLOBALS['bodyd'];
?>

<div class="headerView">
    <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAf/AABEIAAQACgMBEQACEQEDEQH/xAGiAAABBQEBAQEBAQAAAAAAAAAAAQIDBAUGBwgJCgsQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+gEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoLEQACAQIEBAMEBwUEBAABAncAAQIDEQQFITEGEkFRB2FxEyIygQgUQpGhscEJIzNS8BVictEKFiQ04SXxFxgZGiYnKCkqNTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqCg4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2dri4+Tl5ufo6ery8/T19vf4+fr/2gAMAwEAAhEDEQA/AP6JvhLp3h74h/En9r/wLrnhXw5Z+Cfg78a/Anwt8CeEvD1g/h7QdJ8JWv7KvwV+KEFqlnpdxb+VKvizxdqlwWs5LS3ktI7G1ktnW23yfK4jhPII08TgcPgFgcLmmbRr4unltfEZc3Xhw5VwyrUp4KrQqUqkqOFowquEkq3K5VlUlObl9jR4lzmpLB4ivi1i6+BwEqVCtjaNDGSdKpnlOt7OqsVTqwrQpzqz9hGpGX1ePLGj7OMIpflp4t/ba8U2HivxNYW/wK/ZZ+z2XiHWrSDzvgrplxN5NtqVzDF5txPqkk88mxF3zTSPLK2XkdnYk64XLcnq4bD1JZHkylUoUptLLsPZOdOMmlzRlK13peTfdt6ns4qljKOJxNKOdZ7JUq9ampSzOvzNQqSinLl5Y8zSu+WMVfZJaH//2Q==" alt="imgBlur">
    <img src="<?php echo get_template_directory_uri()?>/_next/static/images/Banner_informes-b6e494c40365c85fc3a870a4187dbba4.jpg.webp" alt="">
</div>


<?php 
 
  $dataa = preg_replace('/(?:\r\n|\r|\n)/i', '<br>', $data['content']['rendered']);
  $pattern = "#<iframe[^>]+>.*?</iframe>#is";
  $dataa = preg_replace($pattern, "", $dataa);
  
  $content = substr($data['content']['rendered'], strrpos($data['content']['rendered'], '<iframe'));
  
  $datec = wp_date('F j, Y', rest_parse_date($data['date']));
 
?>
<section class="page-informe">
  <div data-test="container" class="container">
    <div data-test="row" class="row title">
      <div data-test="col" class="col-sm-12">
        <h1><?php echo $data['title']['rendered'] ?></h1>
      </div>
    </div>
    <div data-test="row" class="row date">
      <div data-test="col" class="col-sm-12">
        <p><?php echo ucfirst($datec); ?></p>
      </div>
    </div>
    <div data-test="row" class="row">
      <div data-test="col" class="col-sm-12">
        <div class="contentHtml">
          <?php echo $dataa; ?>
        </div>
      </div>
    </div>
    <div data-test="row" class="row row-scribd">
        <div data-test="col" class="col-sm-12">
          <div class="contentHtml">
            <?php echo $content ?>
          </div>
        </div>
    </div>
  </div>
  <div data-test="container" class="container">
      <div data-test="row" class="row d-flex justify-content-space-between">
          <div data-test="col" class="col">
              <a data-test="button" role="button" class="btn-default btn Ripple-parent btn-color-primary" href="/prensa">
                  Otras Notas de Prensa
                  <div data-test="waves" class="Ripple" ></div>
              </a>
          </div>
      </div>
  </div>

</div><!-- END .si-container -->
</section>

<?php
get_footer();
