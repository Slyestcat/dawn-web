  <div class="social-bar-top-section">
    <div class="container social-bar-flex">
      <div class="players-online-group">
        <div>Start playing with <?php include dirname( __FILE__ ) . '/players_online.php';?> others. <a href="https://playdawn.net/play" class="link">Play now!</a></div>
      </div>
	  <!--<div class="user-group-block">
	      <a href="#" target="_blank" class="login-link">Login to Forums</a>
	      <a href="#" target="_blank" class="register-link">Create an account</a>
	      </div>-->
	
      <!--<div class="social-links">
        <div>Follow us on:</div><a href="#" class="social-icon twitter w-inline-block"></a><a href="#" class="social-icon youtube w-inline-block"></a><a href="#" class="social-icon facebook w-inline-block"></a><a href="https://discord.gg/primalps" target="_blank" class="social-icon discord w-inline-block"></a></div>-->
    </div>
  </div>

  <?php
  $image = dirname( __FILE__ ) . "/images/Logo.png";
  $imageData = base64_encode(file_get_contents($image));
  // Format the image SRC:  data:{mime};base64,{data};
  $src = 'data: '.mime_content_type($image).';base64,'.$imageData;
  ?>

  <div data-collapse="medium" data-animation="default" data-duration="400" role="banner" class="navigation-section w-nav">
    <div class="container navigation-flex"><a href="https://playdawn.net" class="logo-brand-box w-nav-brand"><img src="<?php echo $src;?>" alt="Dawn Logo" class="logo-image"></a>
      <nav role="navigation" class="navigation-menu w-nav-menu">
          <a href="https://playdawn.net/" class="nav-link w-nav-link">Home</a>
          <a href="https://playdawn.net/play" class="nav-link w-nav-link">Play</a>
          <a href="https://discord.gg/59bDxrPA9a" target="_blank" class="nav-link w-nav-link">Discord</a>
          <a href="#" class="nav-link w-nav-link">vote</a>
          <a href="#" class="nav-link w-nav-link">store</a>
          <a href="https://playdawn.net/hiscores" class="nav-link w-nav-link">hiscores</a>
          </nav>
      <div class="menu-button w-nav-button">
        <div class="w-icon-nav-menu"></div>
      </div>
    </div>
  </div>