
# Laravel & Symfony Netgsm Sms Entegrasyonu

Netgsm Sms paket aboneliği bulunan kullanıcılarımız için composer paketidir.

## Contents

- [Installation](#Kurulum)
   - [Setting up the Netgsm service](#setting-up-the-Netgsm-service)
- [Usage](#usage)
    - [Service Methods](#service-methods)
    - [SMS Sending](#sms-sending)
       - [Available SMS Interfaces](#available-sms-interfaces)
   - [Reporting](#reporting)
       - [Available Reporting Interfaces](#available-report-interfaces)
   - [Account Balance](#account-balance)
        - [Remaining Balance](#remaining-balance)
        - [Remaining Package Credits](#remaining-package-credits)
   - [IYS Integration](#iys-integration)
        - [Add Address](#add-address)
        - [Search Address](#search-address)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


# İletişim & Destek

 Netgsm API Servisi ile alakalı tüm sorularınızı ve önerilerinizi teknikdestek@netgsm.com.tr adresine iletebilirsiniz.
### Supported Laravel Versions

Laravel 6.x, Laravel 7.x, Laravel 8.x, Laravel 9.x, Laravel 10.x

### Supported Lumen Versions

Lumen 6.x, Lumen 7.x, Lumen 8.x, Lumen 9.x, 

### Supported Symfony Versions

Symfony 4.x, Symfony 5.x, Symfony 6.x

### Supported Php Versions

PHP 7.2.5 ve üzeri

# Doküman 
https://www.netgsm.com.tr/dokuman/
 API Servisi için hazırlanmış kapsamlı dokümana ve farklı yazılım dillerinde örnek amaçlı hazırlanmış örnek kodlamalara 
 [https://www.netgsm.com.tr/dokuman](https://www.netgsm.com.tr/dokuman) adresinden ulaşabilirsiniz.  

### Kurulum

<b>composer require netgsm/sms </b>

.env  dosyası içerisinde NETGSM ABONELİK bilgileriniz tanımlanması zorunludur.  

<b>NETGSM_USERCODE=""</b>  
<b>NETGSM_PASSWORD=""</b>  
<b>NETGSM_HEADER=""</b>  

## PARAMETRELER

<table width="300">
  <th>Parametre</th>
  <th>Anlamı</th>
  <tr>
    <td><b> encoding</b> </td>
    <td> Türkçe karakter desteği isteniyorsa bu alana TR girilmeli, istenmiyorsa null olarak gönderilmelidir. SMS boyu hesabı ve ücretlendirme bu parametreye bağlı olarak değişecektir. </td>
    
  </tr>
  <tr>
    <td><b> startdate</b> </td>
    <td> Gönderime başlayacağınız tarih. (ddMMyyyyHHmm) * Boş bırakılırsa mesajınız hemen gider.  </td>
  </tr>
  <tr>
    <td><b> stopdate</b> </td>
    <td>İki tarih arası gönderimlerinizde bitiş tarihi.(ddMMyyyyHHmm)* Boş bırakılırsa sistem başlangıç tarihine 21 saat ekleyerek otomatik gönderir.  </td>
  </tr>
  <tr>
    <td><b> bayikodu </b> </td>
    <td> Bayi üyesi iseniz bayinize ait kod   </td>
  </tr>
  <tr>
    <td><b> filter </b> </td>
    <td> Ticari içerikli SMS gönderimlerinde bu parametreyi kullanabilirsiniz. Ticari içerikli bireysele gönderilecek numaralar için İYS kontrollü gönderimlerde ise "11" değerini, tacire gönderilecek İYS kontrollü gönderimlerde ise "12" değerini almalıdır. null gönderildiği taktirde filtre uygulanmadan gönderilecektir.İstek yapılırken gönderilmesi zorunludur. Ticari içerikli ileti gönderimi yapmıyorsanız 0 gönderilmelidir.    </td>
  </tr>
  <tr>
    <td><b> appkey </b> </td>
    <td> Geliştirici hesabınızdan yayınlanan uygulamanıza ait id bilgisi.    </td>
  </tr>
  <tr>
    <td><b> bulkid </b> </td>
    <td> başarılı mesaj gönderimlerinizde dönen görevid (bulkid) nizdir.    </td>
  </tr>
  
</table> 

### 1:n SMS GÖNDERİMİ

SMS'lerinizi 1:n yöntemiyle birden fazla numaraya aynı anda tek gönderimde iletebilirsiniz.

```php
        use Netgsm\Sms\SmsSend;
        $data=array(
            'message'=>'test mesajı',
            'no'=>['553xxxxxxx','553xxxxxxx'],
            'header'=>'MESAJ_BASLİK',
            'filter'=>0,
            'encoding'=>'tr',
            'startdate'=>'170220231000',
            'stopdate'=>'170220231200',
            'bayikodu'=>'1312...',
            'appkey'=>'A123-F3DASD-XXXXX....'
        );
        $sms= new SmsSend;
        $cevap=$sms->smsGonder($data);
        dd($cevap);
        die;
``` 
#### Başarılı istek örnek 
```php
Array
(
    [code] => 00
    [bulkid] => 1311033503
    [durum] => Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.
)
```

#### Başarısız istek örnek 
```php
Array
(
    [code] => 30
    [durum] => Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.
)
```

### n:n SMS GÖNDERİMİ

Birden fazla farklı SMS içeriğini farklı numaralara aynı anda tek pakette gönderebilirsiniz. 

```php
        use Netgsm\Sms\SmsSend;
        $msGsm=array(
                    array('gsm'=>'553XXXXXX','message'=>'MESAJ METNİ 1'),
                    array('gsm'=>'553XXXXXX','message'=>'MESAJ METNİ 2')
                );
        $data=array('startdate'=>'170220231210','stopdate'=>'170220231300','header'=>'BASLIGINIZ','filter'=>0);
        $sms=new SmsSend;
        $cevap=$sms->smsGonderNN($msGsm,$data);
        dd($cevap);
        die;
```
#### Başarılı istek örnek 
```php
Array
(
    [code] => 00
    [bulkid] => 1311033503
    [durum] => Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.
)
```
#### Başarısız istek örnek 
```php
Array
(
    [code] => 30
    [durum] => Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.
)
```

### TEKLİ SMS GÖNDERİMİ



```php
        use Netgsm\Sms\SmsSend;
        $sms=new SmsSend;
        $data=array(
            'msgheader'=>"",
            'gsm'=>'553XXXXXXX',
            'message'=>'Merhaba',
            'filter'=>'0',
            'startdate'=>'270120230950',
            'stopdate'=>'270120231030',
        );

        $sonuc=$sms->smsgonder1_1($data);
        dd($sonuc);
        die;
```
#### Başarılı istek örnek
```php
Array
(
    [code] => 00
    [aciklama] => Görevinizin tarih formatinda bir hata olmadığını gösterir.
    [bulkid] => 1311044635
)
```
#### Başarısız istek örnek
```php
Array
(
    [code] => 40
    [aciklama] => Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.
)
```
### SMS SORGULAMA

Gönderilen mesajların son 3 aya kadar raporlarını sorguyarak; iletim durumlarını öğrenebilirsiniz.
<table width="300">
  <th>Parametre</th>
  <th>Anlamı</th>
  <tr>
   <td>bulkid</td>
   <td>başarılı mesaj gönderimlerinizde dönen görevid (bulkid) nizdir.</td>
 </tr>
  <tr>
    <td><b> type=0</b> </td>
    <td> Tek BulkID sorgular.  </td>
    
  </tr>
  <tr>
    <td><b> type=2</b> </td>
    <td> İki tarih arasında sorgulama yapar.   </td>
  </tr>
 <tr>
<td>bastar</td>
<td>İki tarih arası sorgulamalarınızda başlangıç tarihidir(ddmmyyyy)</td>
</tr>
 <tr>
<td>bittar</td>
<td>İki tarih arası sorgulamalarınızda bitiş tarihidir(ddmmyyyy) Bütün numaralar birbirlerinden &lt;BR&gt; kodu ile ayrılmiştir.</td>
</tr>
  

</table>  

 <b>status</b>
<table>
<thead>
<tr>
<th>Kod</th>
<th>Anlamı</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>0</code></td>
<td>İletilmeyi bekleyenler</td>
</tr>
<tr>
<td><code>1</code></td>
<td>İletilmiş olanlar</td>
</tr>
<tr>
<td><code>2</code></td>
<td>Zaman aşımına uğramış olanlar</td>
</tr>
<tr>
<td><code>3</code></td>
<td>Hatalı veya kısıtlı numara</td>
</tr>
<tr>
<td><code>4</code></td>
<td>Operatöre gönderilemedi</td>
</tr>
<tr>
<td><code>11</code></td>
<td>Operatör tarafından kabul edilmemiş olanlar</td>
</tr>
<tr>
<td><code>12</code></td>
<td>Gönderim hatası olanlar</td>
</tr>
<tr>
<td><code>13</code></td>
<td>Mükerrer olanlar</td>
</tr>
<tr>
<td><code>100</code></td>
<td>Tüm mesaj durumları</td>
</tr>
<tr>
<td><code>103</code></td>
<td>Başarısız Görev (Bu görevin tamamı başarısız olmuştur.)</td>
</tr>
</tbody>
</table>

```php
        use Netgsm\Sms\SmsSend;
        $sms=new SmsSend;
        $data=array('bulkid'=>'1311042194','bastar'=>'010220231500','bittar'=>'070220231500','status'=>'100','type'=>2);
        //bulkid girildiğinde type 0 gönderilmelidir.type=0 girildiğinde bastar ve bittar girilmesine gerek bulunmamaktadır.
        //bastar ve bittar girildiğinde type 2 gönderilmelidir.
        $sonuc=$sms->smsSorgulama($data);
        dd($sonuc);
        die;
```  

#### Başarılı istek sonuç
```php
Array
(
    [durum] => İletilmiş olanlar
    [durumcode] => 1
    [operator] => Türk Telekom
    [operatorcode] => 20
    [hataaciklama] => Hata Yok.
    [hatakod] => 0
    [cepno] => 905531105200
    [mesajboy] => 1
    [tarih] => 23.01.2023 09:35:00
)
```
#### Başarısız istek sonuç
```php
Array
(
    [code] => 60
    [aciklama] => Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.
)
```
### SMS İPTALİ

İleri tarihe zamanlanmış SMS'lerinizi iptal edebilirsiniz ya da görev zamanını değiştirebilirsiniz.  

<table width="300">
  <th>Parametre</th>
  <th>Anlamı</th>
  <tr>
    <td><b> type=0</b> </td>
    <td> gönderilirse görev iptal edilir. </td>
    
  </tr>
  <tr>
    <td><b> type=1</b> </td>
    <td> gönderilip startdate stopdate girilirse güncelleme işlemi yapılır   </td>
  </tr>
  
 
</table>  

```php
        $sms=new SmsSend;
        $data=array('bulkid'=>'1311176624','startdate'=>'180220230100','stopdate'=>'180220231000','type'=>1);
        //type=0 gönderilirse  startdate ve stopdate gönderilmesine gerek yoktur.
        //type=1 gönderilirse stardate ve stopdate değerleri güncellenebilir.
        $sonuc=$sms->smsiptal($data);
        dd($sonuc);
        die;
```  
#### Başarılı istek sonuç
```php
Array
(
    [aciklama] => İleri zamanlı görevinizin başarılı bir şekilde iptal edilğini ifade eder.
    [code] => 00
)
```
#### Başarısız istek sonuç
```php
Array
(
    [aciklama] => Baslangiç ve bitis tarihleri arasindaki fark en az 1 , en fazla 21 saat olmalidir.
    [code] => 60
)
```
### GELEN SMS SORGULAMA

Aboneliğinizde bulunan Paket - Kampanya bilgilerine bu servisten ulaşabilirsiniz.  

```php
        use Netgsm\Sms\SmsSend;	
        $islem=new SmsSend;
        $data=array('startdate'=>'120120230940','stopdate'=>'230120231400');
        $sonuc=$islem->gelensms($data);
        dd($sonuc);
        die;
```
#### Başarılı istek örnek sonuç
```php
Array
(
    [0] => Array
        (
            [telno] => 553xxxxxxx 
            [mesaj] =>  mesaj_içerigi
            [tarih] =>  12.01.2023 09:43:51
        )

    [1] => Array
        (
            [telno] => 553xxxxxxx 
            [mesaj] =>  mesaj_içerigi
            [tarih] =>  12.01.2023 09:43:04
        )

)
```
#### Başarısız istek örnek sonuç
```php
Array
(
    [code] => 60
    [aciklama] => Arama kiterlerindeki startdate ve stopdate zaman farkının 30 günden fazla olduğunu ifade eder.
)
```
### BAŞLIK(GÖNDERİCİ ADI) SORGULAMA

Hesabınızda tanımlı gönderici adlarını(mesaj başlığı)  sorgulama modülüdür. 

```php
        use Netgsm\Sms\SmsSend;
        $baslik=new SmsSend;
        $sonuc=$baslik->basliksorgu();
        dd($sonuc);
        die;
```
#### Başarılı istek örnek sonuç
```php
Array
(
    [msgheader] => Array
        (
            [0] => 850xxxxxxx
            [1] => HEADER_BILGISI
        )

)
```
#### Başarısız istek örnek sonuç
```php
Array
(
    [code] => 30
    [error] => Kullanici bilgisi bulunamadi
)
```
### Kara Liste

Blacklist olarak da bilinen SMS gönderimi istenmeyen yasaklı numaralar listeniz için, belirlediğiniz numaraları Kara Listeye Ekleme / Kara Listeden Çıkarma modülünü kullanabilirsiniz. Kara Listede bulunan numaralara hesabınızdan SMS gönderilmez.Bu kontrol Netgsm tarafında sağlanır.  

<table width="300">
  <th>Parametre</th>
  <th>Anlamı</th>
  <tr>
    <td><b> type</b> </td>
    <td>1 değeri ile Kara listeye ekleme, 2 değeri ile Kara listeden çıkarma işlemi gerçekleşir. İstek yapılırken gönderilmesi zorunludur. </td>
    
  </tr>
  
  
 
</table>  

```php
        use Netgsm\Sms\SmsSend;
       	$karaliste=new SmsSend;
        $data=array('number'=>['553xxxxxxx','553xxxxxxx'],'tip'=>2);
        $sonuc=$karaliste->karaliste($data);
        dd($sonuc);
        die;
```  
#### Başarılı istek örnek sonuç
```php
Array
(
    [code] => OK
    [aciklama] => Kara Listeye Ekleme / Çıkarma işleminde bir hata olmadığını gösterir.
)
```
#### Başarısız istek örnek sonuç
```php
Array
(
    [code] => 60
    [aciklama] => Geçersiz tip gönderimi
)
```
### FLASH SMS

Gönderdiğiniz SMS'lerin kullanıcılarınızın cep telefonu ekranında bildirim olarak gösterilmesidir.  
Abone numaranızın kurumsal olması gereklidir.

<table>
<thead>
<tr>
<th>Parametre</th>
<th>Anlamı</th>
</tr>
</thead>
<tbody>

<tr>
<td><code>header</code></td>
<td>Sistemde tanımlı olan mesaj başlığınızdır (gönderici adınız). En az 3, en fazla 11 karakterden oluşur.</td>

</tr>
<tr>
<td><code>message</code></td>
<td>SMS metninin yer alacağı alandır.Nn sms gönderimlerinde array olarak gönderilmelidir.</td>

</tr>
<tr>
<td><code>gsm[ ]</code></td>
<td>SMS in gideceği numaraları temsil eder array gönderilmeli</td>

</tr>
 <tr>
<td><code>filter/code></td>
<td>Ticari içerikli SMS gönderimlerinde bu parametreyi kullanabilirsiniz. Ticari içerikli bireysele gönderilecek numaralar için İYS kontrollü gönderimlerde ise "11" değerini, tacire gönderilecek İYS kontrollü gönderimlerde ise "12" değerini almalıdır. null gönderildiği taktirde filtre uygulanmadan gönderilecektir.İstek yapılırken gönderilmesi zorunludur. Ticari içerikli ileti gönderimi yapmıyorsanız 0 gönderilmelidir.</td>

</tr>
 <tr>
<td><code>appkey/code></td>
<td>Geliştirici hesabınızdan yayınlanan uygulamanıza ait id bilgisi.</td>

</tr>
<tr>
<td><code>encoding</code></td>
<td>Türkçe karakter desteği isteniyorsa bu alana TR girilmeli, istenmiyorsa null olarak gönderilmelidir. SMS boyu hesabı ve ücretlendirme bu parametreye bağlı olarak değişecektir.</td>

</tr>
<tr>
<td><code>startdate</code></td>
<td>Gönderime başlayacağınız tarih. (ddMMyyyyHHmm) * Boş bırakılırsa mesajınız hemen gider.</td>

</tr>
<tr>
<td><code>stopdate</code></td>
<td>İki tarih arası gönderimlerinizde bitiş tarihi.(ddMMyyyyHHmm)* Boş bırakılırsa sistem başlangıç tarihine 21 saat ekleyerek otomatik gönderir.</td>

</tr>


</tbody>
</table>

```php
        use Netgsm\Sms\SmsSend;
       	$data=array('message'=>'Test','gsm'=>['553xxxxxxx','553xxxxxxx'],
                    'header'=>'312xxxxxxx',
                    'encoding'=>'tr',
                    'startdate'=>'170220231418',
                    'stopdate'=>'170220231425',
                    'filter'=>0,
                    'bayikodu'=>132,
                    'appkey'=>'hsfxa-xhytf21-....',
        );
        $islem=new SmsSend;
        $sonuc=$islem->flashSms($data);
        dd($sonuc);
        die;
``` 
#### Başarılı istek örnek sonuç
```php
Array
(
    [aciklama] => Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.
    [code] => 00
    [bulkid] => 1311191776
)
```
#### Başarısız istek örnek sonuç
```php
Array
(
    [code] => 30
    [aciklama] => Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.
)
```
