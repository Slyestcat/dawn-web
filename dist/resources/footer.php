<?php
$image = dirname( __FILE__ ) . "/images/Logo.png";
$imageData = base64_encode(file_get_contents($image));
// Format the image SRC:  data:{mime};base64,{data};
$src = 'data: '.mime_content_type($image).';base64,'.$imageData;
?>
<div class="footer-section">
    <div class="container">
      <div class="footer-flex-box">
        <div class="logo-footer-box"><a href="#" class="logo-brand-footer w-nav-brand"><img src="<?php echo $src;?>" alt="" class="logo-image"></a>
          <div>Copyright © 2023 Dawn All rights reserved.</div><a href="https://www.gfxdistrict.com/" target="_blank">Design by GFXDistrict.com</a></div>
        <div>

          <h2 class="footer-heading">Quick Links</h2>
          <ul role="list" class="footer-list w-list-unstyled">
            <li><a href="#" class="footer-link">Register</a></li>
            <li><a href="https://playdawn.net/play" class="footer-link">Download</a></li>
            <li><a href="#" class="footer-link">Vote</a></li>
            <li><a href="https://discord.gg/59bDxrPA9a" target="_blank" class="footer-link">Discord</a></li>
          </ul>
        </div>
        <div>
          <h2 class="footer-heading">Game</h2>
          <ul role="list" class="footer-list w-list-unstyled">
            <li><a href="https://playdawn.net/play" class="footer-link">Download</a></li>
            <li><a href="#" class="footer-link">Guides</a></li>
            <li><a href="#" class="footer-link">Rules</a></li>
            <li><a href="#" class="footer-link">Store</a></li>
          </ul>
        </div>
        <div>
          <h2 class="footer-heading">Community</h2>
          <ul role="list" class="footer-list w-list-unstyled">
            <li><a href="#" class="footer-link">Forums</a></li>
            <li><a href="https://discord.gg/59bDxrPA9a" target="_blank" class="footer-link">Discord</a></li>
            <li><a href="#" class="footer-link">Introductions</a></li>
            <li><a href="https://playdawn.net/hiscores" class="footer-link">Hiscores</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>