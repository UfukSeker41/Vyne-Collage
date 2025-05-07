<?php
// footer.php
?>
 <!--start footer -->
 <section class="page-footer py-5">
    <div class="container py-4 px-4 px-lg-0">
      <div class="row g-4">
        <div class="col-12 col-xl-4">
          <div class="footer-widget-1">
            <div class="footer-logo mb-4">
              <img src="assets/images/logo1.png" width="160" alt="Sunucu Logo">
            </div>
            <p class="text-white-50">En iyi FiveM deneyimi için doğru adrestesiniz. Kaliteli oyun deneyimi ve profesyonel yönetim kadromuzla hizmetinizdeyiz.</p>
          </div>
        </div>
        <div class="col-12 col-xl-2">
          <div class="footer-widget-2">
            <div class="footer-links">
              <h5 class="mb-4">Hızlı Bağlantılar</h5>
              <div class="d-flex flex-column gap-2">
                <a href="index.php">Anasayfa</a>
                <a href="be_pages_rules.php">Sunucu Kuralları</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="footer-widget-4">
            <h5 class="mb-4">Topluluğumuza Katılın</h5>
            <div class="d-flex flex-column gap-2">
              <p>Discord sunucumuza katılarak diğer oyuncularla tanışın ve en güncel duyuruları takip edin!</p>
              <a href="https://discord.gg/your-invite" class="btn btn-grd btn-grd-primary px-4">
                <i class="bi bi-discord me-2"></i>Discord'a Katıl
              </a>
            </div>
            <h6 class="mb-3 mt-4">Sosyal Medya</h6>
            <div class="d-flex align-items-center justify-content-start gap-3">
              <a href="https://discord.gg/your-invite"
                 class="wh-42 bg-grd-deep-blue text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-discord fs-5"></i>
              </a>
              <a href="https://youtube.com/your-channel"
                 class="wh-42 bg-grd-danger text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-youtube fs-5"></i>
              </a>
              <a href="https://instagram.com/your-profile"
                 class="wh-42 bg-grd-voilet text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-instagram fs-5"></i>
              </a>
            </div>
          </div>
        </div>
      </div><!--end row-->
      <div class="row mt-4">
        <div class="col-12">
          <div class="footer-bottom text-center">
            <p class="mb-0">&copy; 2025 Tüm hakları saklıdır. VYNE College</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  

  <!--Start Back To Top Button-->
  <a href="javascript:;" class="back-to-top"><i class="material-icons-outlined">arrow_upward</i></a>
  <!--End Back To Top Button-->

  <!--start switcher-->
  <button class="btn btn-grd btn-grd-danger btn-switcher position-fixed top-50 d-flex align-items-center gap-2"
    type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Özelleştir
  </button>

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Tema Özelleştirici</h5>
        <p class="mb-0">Temanızı özelleştirin</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Tema varyasyonu</p>
        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Mavi</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Açık</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Koyu</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Yarı Koyu</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Kenarlıklı</span>
            </label>
          </div>
        </div><!--end row-->
      </div>
    </div>
  </div>
  <!--end switcher-->

  <!--bootstrap js-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/plugins/OwlCarousel/js/owl.carousel.min.js"></script>
  <script src="assets/plugins/OwlCarousel/js/owl.carousel2.thumbs.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script src="assets/plugins/lightbox/dist/js/glightbox.min.js"></script>
  <script>
    var lightbox = GLightbox();
    lightbox.on('open', (target) => {
      console.log('lightbox opened');
    });
    var lightboxDescription = GLightbox({
      selector: '.glightbox2'
    });
    var lightboxVideo = GLightbox({
      selector: '.glightbox3'
    });
    lightboxVideo.on('slide_changed', ({ prev, current }) => {
      console.log('Prev slide', prev);
      console.log('Current slide', current);
      const { slideIndex, slideNode, slideConfig, player } = current;
      if (player) {
        if (!player.ready) {
          player.on('ready', (event) => {
            // Video hazır olduğunda yapılacaklar
          });
        }
        player.on('play', (event) => {
          console.log('Started play');
        });
        player.on('volumechange', (event) => {
          console.log('Volume change');
        });
        player.on('ended', (event) => {
          console.log('Video ended');
        });
      }
    });
    var lightboxInlineIframe = GLightbox({
      selector: '.glightbox4'
    });
  </script>
</body>
</html>
