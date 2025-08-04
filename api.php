<?php
// Basit log ekle
error_log("=== API ÇAĞRILDI ===");
error_log("REQUEST_METHOD: " . $_SERVER["REQUEST_METHOD"]);
error_log("GET parametreleri: " . json_encode($_GET));
error_log("POST parametreleri: " . json_encode($_POST));

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

$baglanti = new mysqli("localhost", "root", "", "basit_sistem");

if ($baglanti->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Veritabanına bağlanılamadı."]);
    exit;
}

// Film API endpoint'leri
if (isset($_GET["films"])) {
    if (isset($_GET["kategori"])) {
        // Belirli kategorideki filmleri getir
        $kategori = $baglanti->real_escape_string($_GET["kategori"]);
        $sonuc = $baglanti->query("SELECT * FROM filmler WHERE kategori = '$kategori' ORDER BY imdb_puani DESC");
        $filmler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $filmler[] = $satir;
        }
        echo json_encode($filmler);
        exit;
    } elseif (isset($_GET["id"])) {
        // Belirli filmi getir
        $id = (int)$_GET["id"];
        $sonuc = $baglanti->query("SELECT * FROM filmler WHERE id = $id");
        if ($sonuc && $sonuc->num_rows > 0) {
            $film = $sonuc->fetch_assoc();
            echo json_encode($film);
        } else {
            echo json_encode(["error" => "Film bulunamadı"]);
        }
        exit;
    } else {
        // Tüm filmleri getir
        $sonuc = $baglanti->query("SELECT * FROM filmler ORDER BY imdb_puani DESC");
        $filmler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $filmler[] = $satir;
        }
        echo json_encode($filmler);
        exit;
    }
}

// Tiyatro API endpoint'leri
if (isset($_GET["tiyatro"])) {
    if (isset($_GET["kategoriler"])) {
        // Tiyatro kategorilerini getir
        $sonuc = $baglanti->query("SELECT DISTINCT tur FROM tiyatro_eserleri ORDER BY tur");
        $kategoriler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $kategoriler[] = $satir["tur"];
        }
        echo json_encode($kategoriler);
        exit;
    } elseif (isset($_GET["tur"])) {
        // Belirli türdeki tiyatro eserlerini getir
        $tur = $baglanti->real_escape_string($_GET["tur"]);
        $sonuc = $baglanti->query("SELECT * FROM tiyatro_eserleri WHERE tur = '$tur' ORDER BY puan DESC");
        $tiyatro_eserleri = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $tiyatro_eserleri[] = $satir;
        }
        echo json_encode($tiyatro_eserleri);
        exit;
    } elseif (isset($_GET["id"])) {
        // Belirli tiyatro eserini getir
        $id = (int)$_GET["id"];
        $sonuc = $baglanti->query("SELECT * FROM tiyatro_eserleri WHERE id = $id");
        if ($sonuc && $sonuc->num_rows > 0) {
            $tiyatro_eseri = $sonuc->fetch_assoc();
            echo json_encode($tiyatro_eseri);
        } else {
            echo json_encode(["error" => "Tiyatro eseri bulunamadı"]);
        }
        exit;
    } else {
        // Tüm tiyatro eserlerini getir
        $sonuc = $baglanti->query("SELECT * FROM tiyatro_eserleri ORDER BY puan DESC");
        $tiyatro_eserleri = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $tiyatro_eserleri[] = $satir;
        }
        echo json_encode($tiyatro_eserleri);
        exit;
    }
}

// GET: Yönetici Kontrolü
if (isset($_GET["islem"]) && $_GET["islem"] === "yonetici_kontrol") {
    $sorgu = $baglanti->query("SELECT COUNT(*) AS sayi FROM kisiler WHERE rol = 'admin'");
    $satir = $sorgu->fetch_assoc();
    echo json_encode(["yoneticiVar" => $satir["sayi"] > 0]);
    exit;
}

// GET: Belgesel endpoint'leri (önce kontrol et)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["belgesel"])) {
    header("Content-Type: application/json");
    
    if (isset($_GET["kategoriler"])) {
        // Belgesel kategorilerini getir
        $sonuc = $baglanti->query("SELECT DISTINCT tur FROM belgeseller ORDER BY tur");
        $kategoriler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $kategoriler[] = $satir["tur"];
        }
        echo json_encode($kategoriler);
        exit;
    } elseif (isset($_GET["tur"])) {
        // Belirli türdeki belgeselleri getir
        $tur = $baglanti->real_escape_string($_GET["tur"]);
        $sonuc = $baglanti->query("SELECT * FROM belgeseller WHERE tur = '$tur' ORDER BY puan DESC");
        $belgeseller = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $belgeseller[] = $satir;
        }
        echo json_encode($belgeseller);
        exit;
    } elseif (isset($_GET["id"])) {
        // Belirli belgeseli getir
        $id = (int)$_GET["id"];
        $sonuc = $baglanti->query("SELECT * FROM belgeseller WHERE id = $id");
        if ($sonuc && $sonuc->num_rows > 0) {
            $belgesel = $sonuc->fetch_assoc();
            echo json_encode($belgesel);
        } else {
            echo json_encode(["error" => "Belgesel bulunamadı"]);
        }
        exit;
    } else {
        // Tüm belgeselleri getir
        $sonuc = $baglanti->query("SELECT * FROM belgeseller ORDER BY puan DESC");
        $belgeseller = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $belgeseller[] = $satir;
        }
        echo json_encode($belgeseller);
        exit;
    }
}

// ESKİ YORUM ENDPOINT'LERİ SİLİNDİ - ÇAKIŞMA ÖNLENDİ

// GET: Anime endpoint'leri (önce kontrol et)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["anime"])) {
    header("Content-Type: application/json");
    
    if (isset($_GET["kategoriler"])) {
        // Anime kategorilerini getir
        $sonuc = $baglanti->query("SELECT DISTINCT tur FROM animeler ORDER BY tur");
        $kategoriler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $kategoriler[] = $satir["tur"];
        }
        echo json_encode($kategoriler);
        exit;
    } elseif (isset($_GET["tur"])) {
        // Belirli türdeki animeleri getir
        $tur = $baglanti->real_escape_string($_GET["tur"]);
        $sonuc = $baglanti->query("SELECT * FROM animeler WHERE tur LIKE '%$tur%' ORDER BY puan DESC");
        $animeler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $animeler[] = $satir;
        }
        echo json_encode($animeler);
        exit;
    } elseif (isset($_GET["id"])) {
        // Belirli animeyi getir
        $id = (int)$_GET["id"];
        $sonuc = $baglanti->query("SELECT * FROM animeler WHERE id = $id");
        if ($sonuc && $sonuc->num_rows > 0) {
            $anime = $sonuc->fetch_assoc();
            echo json_encode($anime);
        } else {
            echo json_encode(["error" => "Anime bulunamadı"]);
        }
        exit;
    } else {
        // Tüm animeleri getir
        $sonuc = $baglanti->query("SELECT * FROM animeler ORDER BY puan DESC");
        $animeler = [];
        while ($satir = $sonuc->fetch_assoc()) {
            $animeler[] = $satir;
        }
        echo json_encode($animeler);
        exit;
    }
}

// GET: Listeleme (varsayılan) - Sadece belirli parametreler yoksa
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET["yorum"]) && !isset($_GET["films"]) && !isset($_GET["tiyatro"]) && !isset($_GET["belgesel"]) && !isset($_GET["anime"]) && !isset($_GET["son_yorumlar"]) && !isset($_GET["tum_yorumlar"])) {
    $sonuc = $baglanti->query("SELECT * FROM kisiler ORDER BY id ASC");
    $kisiler = [];
    while ($satir = $sonuc->fetch_assoc()) {
        unset($satir["sifre"]); // Güvenlik: şifreyi göstermiyoruz
        $kisiler[] = $satir;
    }
    echo json_encode($kisiler);
    exit;
}

// POST: Yorum ekle
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["yorum"])) {
    header("Content-Type: application/json");
    error_log("Yorum ekleme endpoint'i çağrıldı");
    
    // Yorum tablosunun varlığını kontrol et
    $tablo_kontrol = $baglanti->query("SHOW TABLES LIKE 'yorumlar'");
    if ($tablo_kontrol->num_rows == 0) {
        error_log("yorumlar tablosu bulunamadı!");
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Yorum tablosu bulunamadı"]);
        exit;
    }
    
    $girdi = json_decode(file_get_contents("php://input"), true);
    error_log("Gelen veri: " . json_encode($girdi));
    
    if (!isset($girdi["kullanici_id"]) || !isset($girdi["kullanici_adi"]) || 
        !isset($girdi["tur"]) || !isset($girdi["icerik_id"]) || 
        !isset($girdi["icerik_adi"]) || !isset($girdi["yorum"]) || 
        !isset($girdi["puan"])) {
        error_log("Eksik parametreler tespit edildi");
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Eksik parametreler"]);
        exit;
    }
    
    $kullanici_id = (int)$girdi["kullanici_id"];
    $kullanici_adi = $baglanti->real_escape_string($girdi["kullanici_adi"]);
    $tur = $baglanti->real_escape_string($girdi["tur"]);
    $icerik_id = (int)$girdi["icerik_id"];
    $icerik_adi = $baglanti->real_escape_string($girdi["icerik_adi"]);
    $yorum = $baglanti->real_escape_string($girdi["yorum"]);
    $puan = (int)$girdi["puan"];
    
    // Validasyon
    if ($puan < 1 || $puan > 10) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Puan 1-10 arasında olmalıdır"]);
        exit;
    }
    
    if (strlen($yorum) < 10) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Yorum en az 10 karakter olmalıdır"]);
        exit;
    }
    
    $sql = "INSERT INTO yorumlar (kullanici_id, kullanici_adi, tur, icerik_id, icerik_adi, yorum, puan, created_at) 
            VALUES ($kullanici_id, '$kullanici_adi', '$tur', $icerik_id, '$icerik_adi', '$yorum', $puan, NOW())";
    
    error_log("SQL sorgusu: " . $sql);
    
    if ($baglanti->query($sql)) {
        error_log("Yorum başarıyla eklendi");
        echo json_encode(["success" => true, "message" => "Yorum başarıyla eklendi"]);
    } else {
        error_log("Yorum eklenirken hata: " . $baglanti->error);
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Yorum eklenirken hata: " . $baglanti->error]);
    }
    exit;
}

// POST: Yeni kayıt
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["kayit"])) {
    $girdi = json_decode(file_get_contents("php://input"), true);

    // Parametre kontrolü
    if (!isset($girdi["username"]) || empty($girdi["username"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Kullanıcı adı alanı boş bırakılamaz."]);
        exit;
    }
    
    if (!isset($girdi["adsoyad"]) || empty($girdi["adsoyad"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Ad soyad alanı boş bırakılamaz."]);
        exit;
    }

    $username = $baglanti->real_escape_string($girdi["username"]);
    $adsoyad = $baglanti->real_escape_string($girdi["adsoyad"]);
    if (!isset($girdi["sifre"]) || empty($girdi["sifre"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Şifre alanı boş bırakılamaz."]);
        exit;
    }
    $sifre = password_hash($girdi["sifre"], PASSWORD_DEFAULT);
    $rol = $baglanti->real_escape_string($girdi["rol"]);

    // Yönetici kontrolü
    if ($rol === "admin") {
        $kontrol = $baglanti->query("SELECT COUNT(*) as sayi FROM kisiler WHERE rol = 'admin'");
        $veri = $kontrol->fetch_assoc();
        if ($veri["sayi"] >= 1) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Zaten bir yönetici mevcut. Yeni yönetici atanamaz."]);
            exit;
        }
    }

    $sql = "INSERT INTO kisiler (username, adsoyad, sifre, rol) 
            VALUES ('$username', '$adsoyad', '$sifre', '$rol')";

    if ($baglanti->query($sql)) {
        echo json_encode(["success" => true, "message" => "Kayıt başarıyla eklendi."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Kayıt başarısız: " . $baglanti->error]);
    }
    exit;
}

// POST: Yeni film ekleme
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["films"])) {
    $girdi = json_decode(file_get_contents("php://input"), true);

    $film_adi = $baglanti->real_escape_string($girdi["film_adi"]);
    $yil = (int)$girdi["yil"];
    $sure = $baglanti->real_escape_string($girdi["sure"]);
    $imdb_puani = (float)$girdi["imdb_puani"];
    $poster_url = $baglanti->real_escape_string($girdi["poster_url"]);
    $ozet = $baglanti->real_escape_string($girdi["ozet"]);
    $yonetmen = $baglanti->real_escape_string($girdi["yonetmen"]);
    $oyuncular = $baglanti->real_escape_string($girdi["oyuncular"]);
    $tur = $baglanti->real_escape_string($girdi["tur"]);
    $ulke = $baglanti->real_escape_string($girdi["ulke"]);
    $fragman_url = $baglanti->real_escape_string($girdi["fragman_url"]);
    $kategori = $baglanti->real_escape_string($girdi["kategori"]);

    $sql = "INSERT INTO filmler (film_adi, yil, sure, imdb_puani, poster_url, ozet, yonetmen, oyuncular, tur, ulke, fragman_url, kategori) 
            VALUES ('$film_adi', $yil, '$sure', $imdb_puani, '$poster_url', '$ozet', '$yonetmen', '$oyuncular', '$tur', '$ulke', '$fragman_url', '$kategori')";

    if ($baglanti->query($sql)) {
        echo json_encode(["success" => true, "message" => "Film başarıyla eklendi."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Film ekleme başarısız: " . $baglanti->error]);
    }
    exit;
}

// POST: Giriş işlemi
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["login"])) {
    $girdi = json_decode(file_get_contents("php://input"), true);
    
    // Parametre kontrolü
    if (!isset($girdi["username"]) || empty($girdi["username"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Kullanıcı adı alanı boş bırakılamaz."]);
        exit;
    }
    
    if (!isset($girdi["sifre"]) || empty($girdi["sifre"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Şifre alanı boş bırakılamaz."]);
        exit;
    }
    
    $username = $baglanti->real_escape_string($girdi["username"]);
    $sifre = $girdi["sifre"];

    $sonuc = $baglanti->query("SELECT * FROM kisiler WHERE username='$username'");
    if ($sonuc && $sonuc->num_rows > 0) {
        $kisi = $sonuc->fetch_assoc();
        if (password_verify($sifre, $kisi["sifre"])) {
            unset($kisi["sifre"]);
            echo json_encode(["success" => true, "kullanici" => $kisi]);
            exit;
        }
    }
    echo json_encode(["success" => false, "message" => "Geçersiz kullanıcı adı veya şifre."]);
    exit;
}

// DELETE: Kişi sil
if ($_SERVER["REQUEST_METHOD"] === "DELETE" && !isset($_GET["films"]) && !isset($_GET["yorum"])) {
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
    if ($id > 0) {
        $baglanti->query("DELETE FROM kisiler WHERE id = $id");
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Geçersiz ID."]);
    }
    exit;
}

// DELETE: Film sil
if ($_SERVER["REQUEST_METHOD"] === "DELETE" && isset($_GET["films"])) {
    $id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
    if ($id > 0) {
        $baglanti->query("DELETE FROM filmler WHERE id = $id");
        echo json_encode(["success" => true, "message" => "Film başarıyla silindi."]);
    } else {
        echo json_encode(["success" => false, "message" => "Geçersiz ID."]);
    }
    exit;
}

// PUT: Güncelleme
if ($_SERVER["REQUEST_METHOD"] === "PUT" && !isset($_GET["films"])) {
    $girdi = json_decode(file_get_contents("php://input"), true);

    if (!isset($girdi["id"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID eksik."]);
        exit;
    }

    $id = (int)$girdi["id"];
    $username = $baglanti->real_escape_string($girdi["username"]);
    $adsoyad = $baglanti->real_escape_string($girdi["adsoyad"]);
    $rol = isset($girdi["rol"]) ? $baglanti->real_escape_string($girdi["rol"]) : "kullanici";

    if ($rol === "admin") {
        $kontrol = $baglanti->query("SELECT COUNT(*) as sayi FROM kisiler WHERE rol = 'admin' AND id != $id");
        $veri = $kontrol->fetch_assoc();
        if ($veri["sayi"] >= 1) {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Zaten başka bir yönetici mevcut. Bu kullanıcı yönetici olarak güncellenemez."]);
            exit;
        }
    }

    if (isset($girdi["sifre"]) && !empty($girdi["sifre"])) {
        $sifre = password_hash($girdi["sifre"], PASSWORD_DEFAULT);
        $sql = "UPDATE kisiler 
                SET username='$username', adsoyad='$adsoyad', sifre='$sifre', rol='$rol' 
                WHERE id=$id";
    } else {
        $sql = "UPDATE kisiler 
                SET username='$username', adsoyad='$adsoyad', rol='$rol' 
                WHERE id=$id";
    }

    if ($baglanti->query($sql)) {
        echo json_encode(["success" => true, "message" => "Kayıt başarıyla güncellendi."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Güncelleme başarısız: " . $baglanti->error]);
    }
    exit;
}

// PUT: Film güncelleme
if ($_SERVER["REQUEST_METHOD"] === "PUT" && isset($_GET["films"])) {
    $girdi = json_decode(file_get_contents("php://input"), true);

    if (!isset($girdi["id"])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "ID eksik."]);
        exit;
    }

    $id = (int)$girdi["id"];
    $film_adi = $baglanti->real_escape_string($girdi["film_adi"]);
    $yil = (int)$girdi["yil"];
    $sure = $baglanti->real_escape_string($girdi["sure"]);
    $imdb_puani = (float)$girdi["imdb_puani"];
    $poster_url = $baglanti->real_escape_string($girdi["poster_url"]);
    $ozet = $baglanti->real_escape_string($girdi["ozet"]);
    $yonetmen = $baglanti->real_escape_string($girdi["yonetmen"]);
    $oyuncular = $baglanti->real_escape_string($girdi["oyuncular"]);
    $tur = $baglanti->real_escape_string($girdi["tur"]);
    $ulke = $baglanti->real_escape_string($girdi["ulke"]);
    $fragman_url = $baglanti->real_escape_string($girdi["fragman_url"]);
    $kategori = $baglanti->real_escape_string($girdi["kategori"]);

    $sql = "UPDATE filmler 
            SET film_adi='$film_adi', yil=$yil, sure='$sure', imdb_puani=$imdb_puani, 
                poster_url='$poster_url', ozet='$ozet', yonetmen='$yonetmen', 
                oyuncular='$oyuncular', tur='$tur', ulke='$ulke', 
                fragman_url='$fragman_url', kategori='$kategori' 
            WHERE id=$id";

    if ($baglanti->query($sql)) {
        echo json_encode(["success" => true, "message" => "Film başarıyla güncellendi."]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Film güncelleme başarısız: " . $baglanti->error]);
    }
    exit;
}

// YORUM ENDPOINT'LERİ
// GET: Tüm yorumları listele (debug için)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["tum_yorumlar"])) {
    header("Content-Type: application/json");
    error_log("Tüm yorumlar listeleme endpoint'i çağrıldı");
    
    // Yorum tablosunun varlığını kontrol et
    $tablo_kontrol = $baglanti->query("SHOW TABLES LIKE 'yorumlar'");
    if ($tablo_kontrol->num_rows == 0) {
        error_log("yorumlar tablosu bulunamadı!");
        echo json_encode([]);
        exit;
    }
    
    $sql = "SELECT * FROM yorumlar ORDER BY created_at DESC";
    
    error_log("Tüm yorumlar SQL sorgusu: " . $sql);
    $sonuc = $baglanti->query($sql);
    $yorumlar = [];
    
    if ($sonuc) {
        while ($satir = $sonuc->fetch_assoc()) {
            $yorumlar[] = $satir;
        }
        error_log("Toplam yorum sayısı: " . count($yorumlar));
    } else {
        error_log("Tüm yorumlar SQL hatası: " . $baglanti->error);
    }
    
    echo json_encode($yorumlar);
    exit;
}

// GET: Son yorumları getir (anasayfa için)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["son_yorumlar"])) {
    header("Content-Type: application/json");
    error_log("Son yorumlar endpoint'i çağrıldı");
    
    // Yorum tablosunun varlığını kontrol et
    $tablo_kontrol = $baglanti->query("SHOW TABLES LIKE 'yorumlar'");
    if ($tablo_kontrol->num_rows == 0) {
        error_log("yorumlar tablosu bulunamadı!");
        echo json_encode([]);
        exit;
    }
    
    $limit = isset($_GET["limit"]) ? (int)$_GET["limit"] : 6;
    $limit = min($limit, 20); // Maksimum 20 yorum
    
    $sql = "SELECT * FROM yorumlar 
            ORDER BY created_at DESC 
            LIMIT $limit";
    
    error_log("Son yorumlar SQL sorgusu: " . $sql);
    $sonuc = $baglanti->query($sql);
    $yorumlar = [];
    
    if ($sonuc) {
        while ($satir = $sonuc->fetch_assoc()) {
            $yorumlar[] = $satir;
        }
        error_log("Bulunan son yorum sayısı: " . count($yorumlar));
    } else {
        error_log("Son yorumlar SQL hatası: " . $baglanti->error);
    }
    
    echo json_encode($yorumlar);
    exit;
}

// GET: Yorumları getir
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["yorum"])) {
    header("Content-Type: application/json");
    error_log("=== YORUM GETİRME BAŞLADI ===");
    error_log("Yorum getirme endpoint'i çağrıldı");
    
    // Yorum tablosunun varlığını kontrol et
    $tablo_kontrol = $baglanti->query("SHOW TABLES LIKE 'yorumlar'");
    if ($tablo_kontrol->num_rows == 0) {
        error_log("❌ yorumlar tablosu bulunamadı!");
        echo json_encode([]);
        exit;
    }
    error_log("✅ yorumlar tablosu bulundu");
    
    // Parametreleri kontrol et
    if (!isset($_GET["tur"]) || !isset($_GET["icerik_id"])) {
        error_log("❌ Eksik parametreler: tur veya icerik_id");
        echo json_encode([]);
        exit;
    }
    
    $tur = $baglanti->real_escape_string($_GET["tur"]);
    $icerik_id = (int)$_GET["icerik_id"];
    error_log("🔍 Tür: '$tur', İçerik ID: $icerik_id");
    
    $sql = "SELECT * FROM yorumlar 
            WHERE tur = '$tur' AND icerik_id = $icerik_id 
            ORDER BY created_at DESC";
    
    error_log("📝 SQL sorgusu: " . $sql);
    $sonuc = $baglanti->query($sql);
    $yorumlar = [];
    
    if ($sonuc) {
        error_log("✅ SQL sorgusu başarılı");
        while ($satir = $sonuc->fetch_assoc()) {
            error_log("📊 Yorum satırı: " . json_encode($satir));
            $yorumlar[] = $satir;
        }
        error_log("📈 Bulunan yorum sayısı: " . count($yorumlar));
    } else {
        error_log("❌ SQL hatası: " . $baglanti->error);
    }
    
    error_log("🎯 Gönderilen JSON: " . json_encode($yorumlar));
    error_log("=== YORUM GETİRME BİTTİ ===");
    
    echo json_encode($yorumlar);
    exit;
}

// DELETE: Yorum sil
if ($_SERVER["REQUEST_METHOD"] === "DELETE" && isset($_GET["yorum"])) {
    header("Content-Type: application/json");
    error_log("=== YORUM SİLME BAŞLADI (DELETE) ===");
    
    $yorum_id = (int)$_GET["id"];
    error_log("Silinecek yorum ID: $yorum_id");
    
    // Yorumun var olup olmadığını kontrol et
    $kontrol_sql = "SELECT id FROM yorumlar WHERE id = $yorum_id";
    error_log("Kontrol SQL: " . $kontrol_sql);
    $kontrol = $baglanti->query($kontrol_sql);
    
    if ($kontrol && $kontrol->num_rows > 0) {
        error_log("✅ Yorum bulundu, silme işlemi başlıyor");
        
        // Basit silme işlemi (transaction olmadan)
        $sql = "DELETE FROM yorumlar WHERE id = $yorum_id";
        error_log("Silme SQL: " . $sql);
        
        $silme_sonuc = $baglanti->query($sql);
        error_log("Silme sonucu: " . ($silme_sonuc ? "true" : "false"));
        error_log("Etkilenen satır sayısı: " . $baglanti->affected_rows);
        
        if ($silme_sonuc && $baglanti->affected_rows > 0) {
            error_log("✅ Yorum silindi");
            
            // Silme sonrası kontrol
            $kontrol2 = $baglanti->query("SELECT id FROM yorumlar WHERE id = $yorum_id");
            if ($kontrol2 && $kontrol2->num_rows == 0) {
                error_log("✅ Yorum gerçekten silindi (kontrol doğrulandı)");
                echo json_encode(["success" => true, "message" => "Yorum başarıyla silindi"]);
            } else {
                error_log("❌ Yorum silinmedi (kontrol başarısız)");
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Yorum silinmedi"]);
            }
        } else {
            error_log("❌ Yorum silinirken hata: " . $baglanti->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Yorum silinirken hata: " . $baglanti->error]);
        }
    } else {
        error_log("❌ Yorum bulunamadı (ID: $yorum_id)");
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Yorum bulunamadı"]);
    }
    error_log("=== YORUM SİLME BİTTİ (DELETE) ===");
    exit;
}

// POST: Yorum sil (alternatif method)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["yorum"]) && isset($_GET["sil"])) {
    header("Content-Type: application/json");
    error_log("=== YORUM SİLME BAŞLADI (POST) ===");
    
    $yorum_id = (int)$_GET["id"];
    error_log("Silinecek yorum ID: $yorum_id");
    
    // Yorumun var olup olmadığını kontrol et
    $kontrol_sql = "SELECT id FROM yorumlar WHERE id = $yorum_id";
    error_log("Kontrol SQL: " . $kontrol_sql);
    $kontrol = $baglanti->query($kontrol_sql);
    
    if ($kontrol && $kontrol->num_rows > 0) {
        error_log("✅ Yorum bulundu, silme işlemi başlıyor");
        
        // Basit silme işlemi (transaction olmadan)
        $sql = "DELETE FROM yorumlar WHERE id = $yorum_id";
        error_log("Silme SQL: " . $sql);
        
        $silme_sonuc = $baglanti->query($sql);
        error_log("Silme sonucu: " . ($silme_sonuc ? "true" : "false"));
        error_log("Etkilenen satır sayısı: " . $baglanti->affected_rows);
        
        if ($silme_sonuc && $baglanti->affected_rows > 0) {
            error_log("✅ Yorum silindi");
            
            // Silme sonrası kontrol
            $kontrol2 = $baglanti->query("SELECT id FROM yorumlar WHERE id = $yorum_id");
            if ($kontrol2 && $kontrol2->num_rows == 0) {
                error_log("✅ Yorum gerçekten silindi (kontrol doğrulandı)");
                echo json_encode(["success" => true, "message" => "Yorum başarıyla silindi"]);
            } else {
                error_log("❌ Yorum silinmedi (kontrol başarısız)");
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Yorum silinmedi"]);
            }
        } else {
            error_log("❌ Yorum silinirken hata: " . $baglanti->error);
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Yorum silinirken hata: " . $baglanti->error]);
        }
    } else {
        error_log("❌ Yorum bulunamadı (ID: $yorum_id)");
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Yorum bulunamadı"]);
    }
    error_log("=== YORUM SİLME BİTTİ (POST) ===");
    exit;
}


// TİYATRO ENDPOINT'LERİ
// GET: Tiyatro eserlerini getir
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["tiyatro"])) {
    header("Content-Type: application/json");
    
    error_log("Tiyatro endpoint çağrıldı");
    
    // Tabloların varlığını kontrol et
    $tablo_kontrol = $baglanti->query("SHOW TABLES LIKE 'tiyatro_eserleri'");
    error_log("Tablo kontrol sonucu: " . $tablo_kontrol->num_rows);
    
    if ($tablo_kontrol->num_rows == 0) {
        error_log("tiyatro_eserleri tablosu bulunamadı");
        echo json_encode([]);
        exit;
    }
    
    $sql = "SELECT * FROM tiyatro_eserleri ORDER BY puan DESC";
    error_log("SQL sorgusu: " . $sql);
    
    $sonuc = $baglanti->query($sql);
    
    if (!$sonuc) {
        error_log("SQL hatası: " . $baglanti->error);
        echo json_encode([]);
        exit;
    }
    
    $tiyatrolar = [];
    while ($satir = $sonuc->fetch_assoc()) {
        $tiyatrolar[] = $satir;
    }
    
    error_log("Bulunan tiyatro sayısı: " . count($tiyatrolar));
    echo json_encode($tiyatrolar);
    exit;
}

?>
