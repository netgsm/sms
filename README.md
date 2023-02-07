


# Netgsm Sms Laravel Paketi

Netgsm Sms paket aboneliği bulunan kullanıcılarımız için laravel paketidir.

# İletişim & Destek

 Netgsm API Servisi ile alakalı tüm sorularınızı ve önerilerinizi teknikdestek@netgsm.com.tr adresine iletebilirsiniz.


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

```
        use Netgsm\Sms\SmsSend;
        $data['message']='test';
        $data['no']=['553xxxxxxx']; //$data['gsm']=['553xxxxxxx','555xxxxxxx']	
        $data['filter']=0;
        // $data['encoding']='tr';
        //$data['startdate']='200120231600';
        //$data['stopdate']='200120231700';
        //$data['bayikodu']=1312;
        //$data['appkey']='A123-F3DASD-XXXXX....';
        
        $sms= new SmsSend;
        $cevap=$sms->smsGonder($data);
        
        echo '<pre>';
          print_r($cevap);
        echo '<pre>';
``` 
#### Başarılı istek örnek 
```
Array
(
    [code] => 00
    [bulkid] => 1311033503
    [durum] => Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.
)
```

#### Başarısız istek örnek 
```
Array
(
    [code] => 30
    [durum] => Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.
)
```

### n:n SMS GÖNDERİMİ

Birden fazla farklı SMS içeriğini farklı numaralara aynı anda tek pakette gönderebilirsiniz. 

```
        use Netgsm\Sms\SmsSend;
        $msGsm[0]['gsm']='553XXXXXXX';
        $msGsm[0]['message']='MESAJ METNİ 1';
        $msGsm[1]['gsm']='553XXXXXXX';
        $msGsm[1]['message']='MESAJ METNİ 2';
        $data['startdate']='230120230900';
        $data['stopdate']='230120231000';
        $data['filter']=0;
        $sms=new SmsSend;
        $cevap=$sms->smsGonderNN($msGsm,$data);
        
        
        echo '<pre>';
           print_r($cevap);
        echo '<pre>';
```
#### Başarılı istek örnek 
```
Array
(
    [code] => 00
    [bulkid] => 1311033503
    [durum] => Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.
)
```
#### Başarısız istek örnek 
```
Array
(
    [code] => 30
    [durum] => Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.
)
```

### TEKLİ SMS GÖNDERİMİ



```
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
        
        echo '<pre>';
            print_r($sonuc);
        echo '<pre>';
```
#### Başarılı istek örnek
```
Array
(
    [code] => 00
    [aciklama] => Görevinizin tarih formatinda bir hata olmadığını gösterir.
    [bulkid] => 1311044635
)
```
#### BaşarıSIZ istek örnek
```
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
    <td><b> type=0</b> </td>
    <td> Tek BulkID sorgular.  </td>
    
  </tr>
  <tr>
    <td><b> type=2</b> </td>
    <td> İki tarih arasında sorgulama yapar.   </td>
  </tr>
  
 
</table>  

```
        use Netgsm\Sms\SmsSend;
        $sms=new SmsSend;
        $data['bulkid']='1297355397'; 
        $data['startdate']='180120231500';
        $data['stopdate']='200120231500';
        $data['status']='100';
        $data['type']='0';
        $sonuc=$sms->smsSorgulama($data);
    
        echo '<pre>';
            print_r($sonuc);
        echo '<pre>';
```  

#### Başarılı istek sonuç
```
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
```
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

```
        use Netgsm\Sms\SmsSend;
      	$sms=new SmsSend;
      	$data['bulkid']='1295384366';
       	$data['startdate']='230120231300';
      	$data['stopdate']='230120231400';
       	$data['type']=0;
      	$sonuc=$sms->smsiptal($data);
       	
      	echo '<pre>';
            	print_r($sonuc);
        echo '<pre>';
```  
### GELEN SMS SORGULAMA

Aboneliğinizde bulunan Paket - Kampanya bilgilerine bu servisten ulaşabilirsiniz.  

```
        use Netgsm\Sms\SmsSend;	
        $islem=new SmsSend;
        $data['startdate']='120120230940';
        $data['stopdate']='230120231400';
        $sonuc=$islem->gelensms($data);
        
        echo '<pre>';
            print_r($sonuc);
        echo '<pre>';
```

### BAŞLIK(GÖNDERİCİ ADI) SORGULAMA

Hesabınızda tanımlı gönderici adlarını(mesaj başlığı)  sorgulama modülüdür. 

```
        use Netgsm\Sms\SmsSend;
        $baslik=new SmsSend;
        $sonuc=$baslik->basliksorgu();
        
        echo '<pre>';
                print_r($sonuc);
        echo '<pre>';
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

```
        use Netgsm\Sms\SmsSend;
      	$karaliste=new SmsSend;
        $data['number']=['553xxxxxxx','553xxxxxxx'];
        $data['tip']=2;
        $sonuc=$karaliste->karaliste($data);
        
        echo '<pre>';
             print_r($sonuc);
        echo '<pre>';
```  

### FLASH SMS

Gönderdiğiniz SMS'lerin kullanıcılarınızın cep telefonu ekranında bildirim olarak gösterilmesidir.  
Abone numaranızın kurumsal olması gereklidir

```
        use Netgsm\Sms\SmsSend;
   	$data['message']='test3';
        $data['gsm']=['553XXXXXXX'];
        // $data['encoding']='tr';//TÜRKÇE METİN
        // $data['startdate']='200120231600';
        // $data['stopdate']='200120231700';
        // $data['filter']=0;//IYS
        // $data['bayikodu']=1312; //TANIMLI BAYİKODUNUZ
        // $data['appkey']='hsfxa-xhytf21-....';
        // $data['header']='HEADERINIZ'; //TANIMILI MESAJ BAŞLIĞINIZ
        $flashsms=new SmsSend;
        $sonuc=$flashsms->flashSms($data);
        
        echo '<pre>';
                print_r($sonuc);
        echo '<pre>';
``` 
