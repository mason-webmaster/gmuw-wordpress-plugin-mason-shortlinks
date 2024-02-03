<h3>Shortlink</h3>
<p>ID: <?php echo $post->ID; ?></p>
<p>Shortlink text: <?php echo $post->shortlink_text; ?></p>
<p>Target URL: <?php echo $post->shortlink_url; ?></p>
<?php wp_redirect( get_post()->shortlink_url, 301 ); exit; ?>
