<?php
// index.php
include 'header.php';
?>
<main class="main-wrapper" data-bs-spy="scroll" data-bs-target="#Parent_Scroll_Div" data-bs-smooth-scroll="false" tabindex="0">
  <div class="main-content">
    <!-- start banner -->
    <section class="py-5" id="home">
      <div class="container py-4 px-4 px-lg-0">
        <div class="row align-items-center justify-content-center g-4">
          <!-- Sol taraftaki yazılı kısım -->
          <div class="col-12 col-xl-6 order-xl-first order-last">
            <h1 class="fw-bold mb-3 banner-heading">Gerçekçi College Deneyimine Hoş Geldiniz!</h1>
            <h5 class="mb-0 banner-paragraph">
              Sunucumuzda kendinize ait karkateriniz ile dilediğiniz bölümde öğrencilik yapıp, gerçek hayata yakın bir FiveM deneyimi yaşayabilirsiniz. Ekibimiz, size en iyi RP ortamını sağlamak için sürekli çalışıyor.
            </h5>
            <!-- Butonlar (isteğe bağlı) -->
          </div>
          <!-- Sağ taraftaki görsel -->
          <div class="col-12 col-xl-6 text-center">
            <img src="assets/images/banners/01.png" class="img-fluid" width="560" alt="College Banner">
          </div>
        </div><!-- end row -->

        <!-- 3'lü kartlar bölümü -->
        <div class="row g-4 mt-4">
          <!-- Kart 1 -->
          <div class="col-12 col-lg-6 col-xl-4 d-flex">
            <div class="card rounded-4 mb-0 w-100">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="d-flex align-items-center justify-content-center rounded-circle wh-64 btn-grd-info text-white flex-shrink-0">
                    <i class="material-icons-outlined fs-2">emoji_events</i>
                  </div>
                  <div>
                    <h5>Aktif Etkinlikler</h5>
                    <p class="mb-0">Düzenli olarak gerçekleştirdiğimiz dersler, parti organizasyonları ve etkinliklerle sunucumuzda rol yaparken asla sıkılmayacaksınız.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Kart 2 -->
          <div class="col-12 col-lg-6 col-xl-4 d-flex">
            <div class="card rounded-4 mb-0 w-100">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="d-flex align-items-center justify-content-center rounded-circle wh-64 btn-grd-danger text-white flex-shrink-0">
                    <i class="material-icons-outlined fs-2">privacy_tip</i>
                  </div>
                  <div>
                    <h5>Sıkı Güvenlik ve Kurallar</h5>
                    <p class="mb-0">College kurallarına uymanız için sıkı ama adil bir denetim ekibimiz var. Böylece RP kalitesi hep yüksek kalır.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Kart 3 -->
          <div class="col-12 col-lg-12 col-xl-4 d-flex">
            <div class="card rounded-4 mb-0 w-100">
              <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                  <div class="d-flex align-items-center justify-content-center rounded-circle wh-64 btn-grd-success text-white flex-shrink-0">
                    <i class="material-icons-outlined fs-2">try</i>
                  </div>
                  <div>
                    <h5>Özel Scriptler</h5>
                    <p class="mb-0">Kendi ekibimiz tarafından geliştirilen özel scriptler sayesinde daha önce deneyimlemediğiniz eşsiz bir RP ortamı sunuyoruz.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- end row -->
      </div>
    </section>
    <!-- end banner -->

    <!-- About Us Bölümü -->
    <section class="py-5 bg-section" id="About">
      <div class="container py-4 px-4 px-lg-0">
        <div class="section-title text-center mb-5">
          <h1 class="mb-0 section-title-name">Hakkımızda</h1>
        </div>
        <div class="row g-4">
          <!-- Metin Kısmı -->
          <div class="col-12 col-xl-6">
            <h6 class="text-uppercase mb-3">VYNE Hakkında</h6>
            <h2 class="mb-3">Gerçekçi College Deneyimi İçin Çalışıyoruz</h2>
            <p class="mb-3">
              VYNE bir Oyuncu Topluluğudur. Vyne'ın kuruluş mottosu oyuncuları ve yönetim ekibi ile beraber samimi bir oyun ortamı yaratmaktır. 2019 tarihinden beri FiveM'de bir arada olan yönetim ekibimiz senelerin bilgi birikimi ve elde edilmiş samimiyet ile siz oyunculara sunduğumuz VYNE projesine sonuna kadar güvenmekteyiz. Şimdiden bize güvenip VYNE topluluğuna dahil olan her oyuncuya teşekkür ediyoruz!
            </p>
            <div class="d-flex flex-column gap-2">
              <p class="d-flex align-items-start gap-3 mb-0"><i class="material-icons-outlined fs-5">check_circle</i>FiveM Türkiye'de daha önce görülmemiş mapler ve scriptler</p>
              <p class="d-flex align-items-start gap-3 mb-0"><i class="material-icons-outlined fs-5">check_circle</i>Oyuncu-yönetim iletişiminin korunması, oyuncu fikirlerinin dikkate alınması</p>
              <p class="d-flex align-items-start gap-3 mb-0"><i class="material-icons-outlined fs-5">check_circle</i>Gerçekliğe yakın, düzenli bir rol akışına sahip bir server</p>
              <p class="d-flex align-items-start gap-3 mb-0">Vaad ettiklerimizden sadece bir kaçı, daha fazlası ve tamamı VYNE Oyuncu Topluluğunda.</p>
            </div>
          </div>
          <!-- Görsel Kısmı -->
          <div class="col-12 col-xl-6">
            <img src="assets/images/banners/03.png" class="img-fluid" alt="Hakkımızda Görseli">
          </div>
        </div>
      </div>
    </section>
    <!-- End About Us -->

    <!-- Portfolio Bölümü -->
    <section class="py-5" id="Portfolio">
      <div class="container py-4 px-4 px-lg-0">
        <div class="section-title text-center mb-5">
          <h1 class="mb-0 section-title-name">Oyundan Kareler</h1>
        </div>
        <div class="row row-cols-1 row-cols-lg-3 g-4">
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu1.webp" class="glightbox">
                <img src="assets/images/sunucu1.webp" class="img-fluid rounded-4 p-1 bg-grd-branding" alt="image">
              </a>
            </div>
          </div>
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu2.webp" class="glightbox">
                <img src="assets/images/sunucu2.webp" class="img-fluid rounded-4 p-1 bg-grd-danger" alt="image">
              </a>
            </div>
          </div>
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu3.webp" class="glightbox">
                <img src="assets/images/sunucu3.webp" class="img-fluid rounded-4 p-1 bg-grd-info" alt="image">
              </a>
            </div>
          </div>
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu4.webp" class="glightbox">
                <img src="assets/images/sunucu4.webp" class="img-fluid rounded-4 p-1 bg-grd-warning" alt="image">
              </a>
            </div>
          </div>
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu5.png" class="glightbox">
                <img src="assets/images/sunucu5.png" class="img-fluid rounded-4 p-1 bg-grd-success" alt="image">
              </a>
            </div>
          </div>
          <div class="col">
            <div class="inner">
              <a href="assets/images/sunucu6.webp" class="glightbox">
                <img src="assets/images/sunucu6.webp" class="img-fluid rounded-4 p-1 bg-grd-voilet" alt="image">
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Portfolio -->

    <!-- Team Bölümü -->
    <section class="py-5 bg-section" id="Team">
      <div class="container py-4 px-4 px-lg-0">
        <div class="section-title text-center mb-5">
          <h1 class="mb-0 section-title-name">Geliştirme Ekibi</h1>
        </div>
        <div class="row row-cols-1 row-cols-xl-2 g-4">
          <div class="col">
            <div class="card mb-0 rounded-4">
              <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-center gap-4">
                  <div class="">
                    <img src="assets/images/avatars/fdm.webp" width="120" height="120" class="rounded-circle p-1 bg-white bg-grd-warning" alt="">
                  </div>
                  <div class="profile-info">
                    <div class="my-4">
                      <h3 class="mb-1">fd1em</h3>
                      <p class="mb-3 fs-6">Project Leader</p>
                      <p class="mb-0">V</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-0 rounded-4">
              <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-center gap-4">
                  <div class="">
                    <img src="assets/images/avatars/Isaac.webp" width="120" height="120" class="rounded-circle p-1 bg-info" alt="">
                  </div>
                  <div class="profile-info">
                    <div class="my-4">
                      <h3 class="mb-1">Isaac</h3>
                      <p class="mb-3 fs-6">Story Head</p>
                      <p class="mb-0">Y</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-0 rounded-4">
              <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-center gap-4">
                  <div class="">
                    <img src="assets/images/avatars/priasmr.webp" width="120" height="120" class="rounded-circle p-1 bg-black" alt="">
                  </div>
                  <div class="profile-info">
                    <div class="my-4">
                      <h4 class="mb-1">Priasmr</h4>
                      <p class="mb-3">Web site and Fivem Developer</p>
                      <p class="mb-0">N</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card mb-0 rounded-4">
              <div class="card-body p-4">
                <div class="d-flex flex-column flex-lg-row align-items-center gap-4">
                  <div class="">
                    <img src="assets/images/avatars/Xieahs.webp" width="120" height="120" class="rounded-circle p-1 bg-white bg-grd-success" alt="">
                  </div>
                  <div class="profile-info">
                    <div class="my-4">
                      <h4 class="mb-1">Xieahs</h4>
                      <p class="mb-3">Management</p>
                      <p class="mb-0">E</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!-- end row -->
      </div>
    </section>
    <!-- End Team -->

  </div>
</main>
<?php
include 'footer.php';
?>
