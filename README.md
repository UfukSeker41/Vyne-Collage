# Proje Genel Bakış

## Açıklama
Bu proje, kullanıcı kimlik doğrulama, paneller, profiller ve diğer yönetimsel görevler için tasarlanmış PHP tabanlı bir web uygulamasıdır. Özellikle FiveM sunucuları için tasarlanmış bir yönetim paneli olarak işlev görmektedir. Kullanıcı giriş-çıkış işlemleri, şikayet yönetimi, beyaz liste başvuruları ve okul kuralları gibi özellikler sunar.

## Özellikler
- **Kimlik Doğrulama Sistemi**:
  - Kullanıcı giriş, kayıt ve şifre sıfırlama işlemleri.
  - Yönetici giriş paneli.
- **Yönetim Paneli**:
  - Kullanıcı ve içerik yönetimi için admin paneli.
  - Profil yönetimi ve düzenleme özellikleri.
- **Şikayet ve Beyaz Liste Yönetimi**:
  - Kullanıcı şikayetlerini ve beyaz liste başvurularını yönetme.
- **İçerik Yönetimi**:
  - Okul kuralları ve diğer yönergeler için sayfalar.
- **Duyarlı Tasarım**:
  - CSS ve Sass dosyaları ile modern ve duyarlı bir arayüz.

## Proje Yapısı
- **PHP Dosyaları**:
  - `admin_panel.php`: Yönetici paneli ana sayfası.
  - `adminlogin.php`: Yönetici giriş ekranı.
  - `auth-cover-login.php`, `auth-cover-register.php`, `auth-cover-forgot-password.php`: Kullanıcı kimlik doğrulama işlemleri için sayfalar.
  - `be_pages_dashboard.php`: Kullanıcılar için genel bir kontrol paneli.
  - `be_pages_generic_profile.php`, `be_pages_generic_profile_edit.php`: Profil görüntüleme ve düzenleme sayfaları.
  - `be_pages_okulkurallari.php`: Okul kuralları sayfası.
  - `be_pages_rules.php`: Genel kurallar sayfası.
  - `be_pages_whitelist.php`: Beyaz liste başvurularını yönetmek için sayfa.
  - `be_pages_yetkilisikayet.php`: Yetkili şikayetlerini yönetmek için sayfa.
  - `update_complaint_status.php`, `update_whitelist_status.php`: Şikayet ve beyaz liste durumlarını güncellemek için kullanılan dosyalar.
- **Varlıklar (Assets)**:
  - CSS, JavaScript, yazı tipleri ve görseller.
  - `assets/` klasörü, genel kullanıcı arayüzü için gerekli kaynakları içerir.
  - `dashboardassets/` klasörü, yönetim paneline özel kaynakları içerir.
- **Sass**:
  - SCSS dosyaları ile tema özelleştirme ve duyarlı tasarım.
- **Görseller**:
  - Logolar, arka planlar, avatarlar ve diğer medya dosyaları.

## Güçlü Yönler
- FiveM sunucuları için özelleştirilmiş bir yönetim paneli.
- Modüler yapı ve ayrı dosyalarla işlevsellik.
- Sass kullanımı ile ölçeklenebilir ve sürdürülebilir tasarım.
- Duyarlı tasarım ile farklı cihazlarda iyi kullanıcı deneyimi.

## Zayıf Yönler
- Geliştiriciler için eksik dokümantasyon.
- Giriş doğrulama ve veri temizleme eksiklikleri nedeniyle potansiyel güvenlik açıkları.
- Kodun bazı bölümlerinde PHP mantığı ile HTML karışımı.
- Birim testleri veya otomatik test çerçevesi eksikliği.

## Nasıl Çalıştırılır
1. Yerel bir sunucu (ör. XAMPP, WAMP) kurun ve proje dosyalarını sunucunun kök dizinine yerleştirin.
2. `config.php` dosyasını veritabanı kimlik bilgileriyle yapılandırın.
3. Web tarayıcınızda `http://localhost/<proje-dizini>` adresine giderek uygulamayı çalıştırın.

## Gelecekteki İyileştirmeler
- Geliştiriciler için ayrıntılı dokümantasyon eklenmesi.
- Daha iyi kod güvenilirliği için bir test çerçevesi uygulanması.
- Kodun MVC (Model-View-Controller) mimarisine göre yeniden düzenlenmesi.
- Giriş doğrulama ve veritabanı sorguları için hazırlıklı ifadeler gibi güvenlik önlemlerinin artırılması.
