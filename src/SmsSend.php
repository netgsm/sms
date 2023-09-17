<?php

namespace Netgsm\Sms;

use Exception;
use Ramsey\Uuid\Type\Integer;
use SimpleXMLElement;

class SmsSend
{   
    private $username;
    private $password;
    private $header;
    public function __construct()
    {
     if(isset($_ENV['NETGSM_USERCODE']))
      {
          $this->username=$_ENV['NETGSM_USERCODE'];
      }
      else{
          $this->username='x';
      }
      if(isset($_ENV['NETGSM_PASSWORD']))
      {
          $this->password=$_ENV['NETGSM_PASSWORD'];
      }
      else{
          $this->password='x';
      }
      if(isset($_ENV['NETGSM_HEADER']))
      {
          $this->header=$_ENV['NETGSM_HEADER'];
      }
      else{
          $this->password='x';
      }
    }

    // SETTERS : ilgili property'leri .env dosyasini kullanmadan set amaciyla.
    public function setUsername(mixed $username): void
    {
        $this->username = $username;
    }

    public function setPassword(mixed $password): void
    {
        $this->password = $password;
    }

    public function setHeader(mixed $header): void
    {
        $this->header = $header;
    }


    public function smsSorgulama($data):array
    {
        
      
       if(!isset($data['bastar'])){
        $data['bastar']=null;
       }
       if(!isset($data['bittar'])){
        $data['bittar']=null;
       }
       if(!isset($data['mbaslik'])){
        $data['mbaslik']=null;
       }
       if(!isset($data['telno'])){
        $data['telno']=null;
       }
       if(!isset($data['status'])){
        $data['status']=100;
       }
       if( isset($data['type'])   ){
        $type=$data['type'];
       }
       else{
        $type=0;
       }
       
       if(!isset($data['bulkid']) || $data['bulkid']==null){
        $data['bulkid']='0';
       }
       
       $hata=array(
        30=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir. Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
        60=>"Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",
        70=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
        80=>"sınır aşımı, dakikada 10 kez sorgulanabilir.",
        
     );
     $hatakod=array(
        0=>"Hata Yok.",
        101=>"Mesaj Kutusu Dolu",
        102=>"Kapalı yada Kapsama Dışında",
        103=>"Meşgul",
        104=>"Hat Aktif Değil",
        105=>"Hatalı Numara",
        106=>"SMS red, Karaliste",
        111=>"Zaman Aşımı",
        112=>"Mobil Cihaz Sms Gönderimine Kapalı",
        113=>"Mobil Cihaz Desteklemiyor",
        114=>"Yönlendirme Başarısız",
        115=>"Çağrı Yasaklandı",
        116=>"Tanımlanamayan Abone",
        117=>"Yasadışı Abone",
        119=>"Sistemsel Hata",
        
     );
     $state=array(
        0=>"İletilmeyi bekleyenler",
        1=>"İletilmiş olanlar",
        2=>"Zaman aşımına uğramış olanlar",
        3=>"Hatalı veya kısıtlı numara",
        4=>"Operatöre gönderilemedi",
        11=>"Operatör tarafından kabul edilmemiş olanlar",
        12=>"Gönderim hatası olanlar",
        13=>"Mükerrer olanlar",
        100=>"Tüm mesaj durumları",
        103=>"Başarısız Görev (Bu görevin tamamı başarısız olmuştur.)",

     );
     $operator=array(
        10=>"Vodafone",
        20=>"Türk Telekom",
        30=>"Turkcell",
        40=>"Netgsm STH",
        41=>"Netgsm Mobil",
        160=>"KKTC Vodafone",
        212=>"Yurtdışı",
        213=>"Yurtdışı",
        214=>"Yurtdışı",
        215=>"Yurtdışı",
        880=>"KKTC Turkcell",

     );
      
        
         $url= "https://api.netgsm.com.tr/sms/report/?usercode=".$this->username."&password=".$this->password."&bulkid=".$data['bulkid']."&type=".$type."&status=".$data['status']."&bastar=".$data['bastar']."&bittar=".$data['bittar']."&version=2&telno=".$data['telno'];     
        
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         $http_response = curl_exec($ch);
         $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
         if($http_code != 200){
           echo "$http_code $http_response\n";
           return false;
         }
         $balanceInfo = $http_response;
         $data=explode('<br>',$balanceInfo);
         $data=array_filter($data);
         
            $dizi = explode (" ",$balanceInfo);
            if(($dizi[0]==30 || $dizi[0]==60 || $dizi[0]==70 || $dizi[0]==80)){
                
                $res["code"]=$dizi[0];
                $res['aciklama']=$hata[$dizi[0]];
                return $res;
                
             }
             elseif($dizi[0]<=110 && $dizi[0]>=100)
             {
                $res["code"]=$dizi[0];
                $res['aciklama']='Sistem Hatası';
                return $res;
             }
             else{
               
                foreach($data as $k=>$v){
                
                
                    $donen=[];
                    $dizi=explode(' ',$v);
                    if(!isset($dizi[7]))
                    {
                        $dzEk=array(0=>null);
                        $dizi=array_merge($dzEk,$dizi);
                    }
                    $res[$k]['bulkid']=$dizi[0];
                    $res[$k]['cepno']=$dizi[1];
                    $res[$k]['durum']=$state[$dizi[2]];
                    $res[$k]['durumcode']=$dizi[2];
                    
                    if(isset($operator[$dizi[3]]))
                    {
                        $res[$k]['operator']=$operator[$dizi[3]];
                        $res[$k]['operatorcode']=$dizi[3];
                    }
                    else{
                        $res[$k]['operator']='-';
                        $res[$k]['operatorcode']=$dizi[3];
                    }
                   
                    $res[$k]['mesajboy']=$dizi[4];
                    $res[$k]['tarih']=$dizi[5].' '.$dizi[6];
                    $res[$k]['hataaciklama']=$hatakod[$dizi[7]];
                    $res[$k]['hatakod']=$dizi[7];
      
                }
    
                return $res;
                
            }
            
            
    
       
         
         
        
         

    }
    public function smsGonder($data):array
    {
        
         
        if(!isset($data['message'])){
            $data['message']=null;
         }
        if(!isset($data['no'])){
            $data['no']=null;
        }
        if(!isset($data['encoding'])){
            $data['encoding']=null;
           }
        if(!isset($data['startdate'])){
            $data['startdate']=null;
        }
        if(!isset($data['stopdate'])){
            $data['stopdate']=null;
        }
        if(!isset($data['filter'])){
            $data['filter']=null;
        }
        if(!isset($data['appkey'])){
            $data['appkey']=null;
        }
        if(!isset($data['header'])){
            $header=$this->header;
        }
        else{
            $header=$data['header'];
        }
        if(!isset($data['bayikodu'])){
            $data['bayikodu']=null;
        }
        $curl = curl_init();

        
         $nolar='';
         if(!empty($data['no'])){
            foreach($data['no'] as $g){
                $nolar.= '<no>'.$g.'</no>';
             }
         }
         else{
            $cevap['success']='Gsm göndermediniz';
            return  $cevap;
         }
         
         $xmlData='<?xml version="1.0" encoding="UTF-8"?>
         <mainbody>
         <header>
         <company dil="TR">Netgsm</company>        
         <usercode>'.$this->username.'</usercode>
         <password>'.$this->password.'</password>
         <type>1:n</type>
         <msgheader>'.$header.'</msgheader>
         <startdate>'.$data['startdate'].'</startdate>
         <stopdate>'.$data['stopdate'].'</stopdate>
         <bayikodu>'.$data['bayikodu'].'</bayikodu>              
         <filter>'.$data['filter'].'</filter>
         <encoding>'.$data['encoding'].'</encoding>
         <appkey>'.$data['appkey'].'</appkey>
         </header>
         <body>
         <msg>
         <![CDATA['.$data['message'].']]>
         </msg>
         '.$nolar.'
         </body>
         </mainbody>';
        
         
         $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/sms/send/xml');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
		
        $result=explode(" ",$result);
       

        $sonuc=array(
            "20"=>"Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder.",
            "30"=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            "40"=>"Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",
            "50"=>"Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.",
            "51"=>"Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.",
            "70"=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            "80"=>"Gönderim sınır aşımı.",
            "85"=>"Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20'den fazla görev oluşturulamaz.",
            "00"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "01"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 01 : Mesajınızın başlangıç tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "02"=>"	Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 02 : Mesajınızın sonlandırma tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",

        );
      
        if($result[0]==20 || $result[0]==30|| $result[0]==40|| $result[0]==50|| $result[0]==51|| $result[0]==70|| $result[0]==80|| $result[0]==85||$result[0]==20)
        {
            $res['code']=$result[0];
            $res['durum']=$sonuc[$result[0]];
        }
        elseif($result[0]==00 || $result[0]==01 || $result[0]==02 )
        {
            $res['code']=$result[0];
            $res['bulkid']=$result[1];
            $res['durum']=$sonuc[$result[0]];
        }
        
            
       
        return $res;
        
    }
    public function flashSms($data):array
    {

        $sonuc=array(
            "20"=>"Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder.",
            "30"=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            "40"=>"Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",
            "50"=>"Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.",
            "51"=>"Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.",
            "70"=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            "80"=>"Gönderim sınır aşımı.",
            "85"=>"Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20'den fazla görev oluşturulamaz.",
            "00"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "01"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 01 : Mesajınızın başlangıç tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "02"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 02 : Mesajınızın sonlandırma tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",

        );
        
        
         
        if(!isset($data['message'])){
            $data['message']=null;
         }
        if(!isset($data['gsm'])){
            $data['gsm']=null;
        }
        if(!isset($data['encoding'])){
            $data['encoding']=null;
           }
        if(!isset($data['startdate'])){
            $data['startdate']=null;
        }
        if(!isset($data['stopdate'])){
            $data['stopdate']=null;
        }
        if(!isset($data['filter'])){
            $data['filter']=null;
        }
        if(!isset($data['appkey'])){
            $data['appkey']=null;
        }
        if(!isset($data['header'])){
            $header=$this->header;
        }
        else{
            $header=$data['header'];
        }
        if(!isset($data['bayikodu'])){
            $data['bayikodu']=null;
        }
        $nolar='';
         if(!empty($data['gsm'])){
            foreach($data['gsm'] as $g){
                $nolar.= '<no>'.$g.'</no>';
             }
         }
        $xmlData='<?xml version="1.0" encoding="UTF-8"?>
        <mainbody>
        <header>
        <company dil="TR">Netgsm</company>        
        <usercode>'.$this->username.'</usercode>
        <password>'.$this->password.'</password>
        <type>1:n</type>
        <flash>1</flash>
        <msgheader>'.$header.'</msgheader>
        </header>
        <body>
        <msg>
        <![CDATA['.$data['message'].']]>
        </msg>
        '.$nolar.'
       
        </body>
        </mainbody>';
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/sms/send/xml');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
        $dz=explode(" ",$result);
        if($dz[0]==20||$dz[0]==30||$dz[0]==40||$dz[0]==50||$dz[0]==51||$dz[0]==70||$dz[0]==80||$dz[0]==85){
            $res['code']=$dz[0];
            $res['aciklama']=$sonuc[$dz[0]];
        }
        else{
            $res['aciklama']=$sonuc[$dz[0]];
            $res['code']=$dz[0];
            $res['bulkid']=$dz[1];
        }
        
        return $res;
    }
    public function smsGonderNN($msGsm,$data):array
    {
       
        $sonuc=array(
            "20"=>"Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder.",
            "30"=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            "40"=>"Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",
            "50"=>"Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.",
            "51"=>"Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.",
            "70"=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            "80"=>"Gönderim sınır aşımı.",
            "85"=>"Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20'den fazla görev oluşturulamaz.",
            "00"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 00 : Mesajınızın tarih formatına ilişkin bir hata olmadığı anlamına gelir. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "01"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 01 : Mesajınızın başlangıç tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",
            "02"=>"Gönderdiğiniz SMS'inizin başarıyla sistemimize ulaştığını gösterir. 02 : Mesajınızın sonlandırma tarihine ilişkin bir hata olduğunu gösterir, sistem tarihi ile değiştirilip işleme alınmıştır. 123xxxxxx : Gönderilen SMSe ait ID bilgisi, Bu görevid (bulkid) niz ile mesajınızın iletim raporunu sorguyabilirsiniz.",

        );
         
        if(!isset($data['encoding'])){
            $data['encoding']=null;
           }
        if(!isset($data['startdate'])){
            $data['startdate']=null;
        }
        if(!isset($data['stopdate'])){
            $data['stopdate']=null;
        }
        if(!isset($data['filter'])){
            $data['filter']=null;
        }
        if(!isset($data['header'])){
            $header=$this->header;
        }
        else{
            $header=$data['header'];
        }
        if(!isset($data['appkey'])){
            $data['appkey']=null;
        }
        $sg='';
        
        foreach($msGsm as $d){
            if(!isset($d['message'])){
                $d['message']=null;
             }
            if(!isset($d['gsm'])){
                $d['gsm']=null;
            }
            $sg.='<mp><msg><![CDATA['.$d['message'].']]></msg><no>'.$d['gsm'].'</no></mp>';
           
        }
        if(!isset($data['header'])){
            $header=$this->header;
        }
        else{
            $header=$data['header'];
        }
        $xmlData='<?xml version="1.0" encoding="UTF-8"?>
            <mainbody>
            <header>
            <company dil="TR">Netgsm</company>
            <usercode>'.$this->username.'</usercode>
            <msgheader>'.$header.'</msgheader>
            <startdate>'.$data['startdate'].'</startdate>
            <stopdate>'.$data['stopdate'].'</stopdate>
            <password>'.$this->password.'</password>
            <filter>'.$data['filter'].'</filter>
            <type>n:n</type>
            <msgheader>'.$header.'</msgheader>
            <appkey>'.$data['appkey'].'</appkey>
            </header>
            <body>
            '.$sg.'
            
            </body>
            </mainbody>';
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/sms/send/xml');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);

        $dizi=explode(' ',$result);
        if(count($dizi)==2)
        {
            $res["durum"]=$sonuc[$dizi[0]];
            $res["code"]=$dizi[0];
            $res["bulkid"]=$dizi[1];;
            
        }
        else
        {
            $res["durum"]=$sonuc[$dizi[0]];
            $res["code"]=$dizi[0];
            
        }
        return $res;
		
    }
    public function smsIptal($data):array
    {
     
        if(!isset($data['startdate'])){
            $data['startdate']=null;
        }
        if(!isset($data['stopdate'])){
            $data['stopdate']=null;
        }
        
        if(!isset($data['type'])){
            $data['type']=null;
        }
        if(!isset($data['bulkid'])){
            $data['bulkid']=null;
        }
       
        
         $xmlData='<?xml version="1.0" encoding="UTF-8"?>
            <mainbody>
            <header>
                <usercode>'.$this->username.'</usercode>
                <password>'.$this->password.'</password>
                <gorevid>'.$data['bulkid'].'</gorevid>
                <startdate>'.$data['startdate'].'</startdate>
                <stopdate>'.$data['stopdate'].'</stopdate>
                <type>'.$data['type'].'</type>
            </header>
            </mainbody>';
        
            
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/sms/edit');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
        
		$data=[];

        $sonuc=array(
            "00"=>"İleri zamanlı görevinizin başarılı bir şekilde iptal/guncelleme işleminin yapıldığı ifade eder.",
            "30"=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.",
            "40"=>"API ile hesap erişim izninin olmadığını veya IP sınırlamanız olduğunu ifade eder.",
            "41"=>"Gönderdiğiniz gorevid parametresinde hata olduğunu ifade eder.",
            "42"=>"Gönderdiğiniz type parametresinde hata olduğunu ifade eder.",
            "43"=>"Gönderdiğiniz başlangıç tarihinin boş geldiğini ifade eder.",
            "44"=>"Gönderdiğiniz bitiş tarihinin boş geldiğini ifade eder.",
            "45"=>"Gönderdiğiniz başlangıç tarihinde (startdate parametresi) format hatası olduğunu ifade eder.",
            "46"=>"Gönderdiğiniz bitiş tarihinde (stopdate parametresi) format hatası olduğunu ifade eder.",
            "47"=>"Gönderdiğiniz başlangıç tarihinin bugünün tarihinden küçük olduğunu ifade eder.",
            "48"=>"Gönderdiğiniz bitiş tarihinin bugünün tarihinden küçük olduğunu ifade eder.",
            "49"=>"Baslangiç ve bitis tarihleri arasindaki fark en az 1 , en fazla 21 saat olmalidir.",
            "50"=>"-",
            "60"=>"Gönderdiğiniz görevid'ye ait kayıt olmadığını ifade eder.",
            "70"=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            "100"=>'Hatalı sorgu'

        );
        
        $response["aciklama"]=$sonuc[$result];
        $response["code"]=$result;

        return $response;
    }

    public function kredisorgu():array
    {
        $xmlData='<?xml version="1.0"?>
        <mainbody>
            <header>		
                <usercode>'.$this->username.'</usercode>
                <password>'.$this->password.'</password>
                <stip>2</stip>      
                </header>		
        </mainbody>';
         $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/balance/list/xml');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
        
        $dizi=explode(" ",$result);
        $sonuc=[];
       
        if($dizi[0]=="00"){
            $sonuc["durum"]="Başarılı sorgulama";
            $sonuc["cüzdan"]=$dizi[1];
            $sonuc["code"]=$dizi[0];

        }
        else if($dizi[0]==30){
            $sonuc["durum"]="Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzümüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.";
            $sonuc["code"]=$dizi[0];
        }
        else if($dizi[0]==70){
            $sonuc['durum']="Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.";
            $sonuc["code"]=$dizi[0];
        }
        return $sonuc;

    }
    public function paketsorgu():array
    {
        
        $xmlData='<?xml version="1.0"?>
        <mainbody>
            <header>		
            <usercode>'.$this->username.'</usercode>
            <password>'.$this->password.'</password>
                <stip>1</stip>      
                </header>		
        </mainbody>';
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://api.netgsm.com.tr/balance/list/xml");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        
		$result = curl_exec($ch);
        $result=explode("<BR>",$result);
        $res=array();
       
		foreach($result as $r=>$v)
        {
          $res[$r]=$v;
          
        }
        
        $res=array_filter($res);
        $pkRes=array();
       
       
        foreach($res as $k=>$v){
            $d=explode('|',$v);
            $pkRes[$k]['miktar']=rtrim(ltrim($d[0]));
            $pkRes[$k]['birim']=rtrim(ltrim($d[1]));
            $pkRes[$k]['paketismi']=rtrim(ltrim($d[2]));
        }
        
         if($res[0]==30)
         {
            $response['message']="Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzümüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.";
            $response['code']=$res[0];
         }
         else if($res[0]==40)
         {
            $response['message']="Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.";
            $response['code']=$res[0];
         }
         else if($res[0]==70)
         {
            $response['message']="Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.";
            $response['code']=$res[0];
         }
         else{
            $response=$pkRes;
         }
         return $response;
    }
    public function gelensms($data):array
    {
        if(!isset($data['startdate'])){
            $data['startdate']=null;
        }
        if(!isset($data['stopdate'])){
            $data['stopdate']=null;
        }
        
        $xmlData='<?xml version="1.0" encoding="UTF-8"?>
        <mainbody>
        <header>
            <usercode>'.$this->username.'</usercode>
            <password>'.$this->password.'</password>
            <startdate>'.$data['startdate'].'</startdate>
            <stopdate>'.$data['stopdate'].'</stopdate>
            <type>0</type>
        </header>
        </mainbody>';
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,'https://api.netgsm.com.tr/sms/receive');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
        
        $sonuc=array(
            30=>'Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
            40=>'Gösterilecek mesajınızın olmadığını ifade eder. Api ile mesajlarınızı eğer startdate ve stopdate parametlerini kullanmıyorsanız sadece bir kere listeyebilirsiniz. Listelenen mesajlar diğer sorgulamalarınızda gelmez.',  
            50=>'Tarih formatı hatalıdır. (Tarih formatı: ddmmyyyyhhmm şeklinde olmalıdır.)',
            60=>'Arama kiterlerindeki startdate ve stopdate zaman farkının 30 günden fazla olduğunu ifade eder.',
            70=>'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.',

        );
        
        if($result==30|| $result==40|| $result==50|| $result==60|| $result==70  )
        {
            $response['code']=$result;
            $response['aciklama']=$sonuc[$result];
        }
        else{
            $dizi=explode('<br>',$result);
                foreach($dizi as $d=>$v){
                    $dn=explode('|',$v);
                    if(count($dn)>2){
                        $response[$d]['telno']=$dn[0];
                        $response[$d]['mesaj']=$dn[1];
                        $response[$d]['tarih']=$dn[2];
                    }
                    
                }
        }

        return $response;
        
    }
    public function basliksorgu():array
    {
        try {
            $arr_acc = array(
                "usercode" => $this->username,
                "password" => $this->password
            );
            
            $content = json_encode($arr_acc);
        
            $curl = curl_init("https://api.netgsm.com.tr/sms/header");
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Content-type: application/json"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            $json_response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
        } catch (Exception $exc)
        {
            echo $exc->getMessage();
        } 

       return (array)json_decode($json_response);
    }
    
    public function karaliste($data):array
    {
        $no='';
                
        if(!isset($data['number']))
        {
            $data['number']=null;
        }
        if(!isset($data['tip']))
        {
            $data['number']=1;
        }
        foreach($data['number'] as $d)
        {
            $no.="<number>".$d."</number>";   
        }
        $xmlData='<?xml version="1.0"?>
        <mainbody>
        <header>  
            <usercode>'.$this->username.'</usercode>
            <password>'.$this->password.'</password>
            <tip>'.$data['tip'].'</tip>
        </header>
        <body>'.$no.'</body>
        </mainbody>';
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://api.netgsm.com.tr/sms/blacklist");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
		$result = curl_exec($ch);
        
        $sonuc=array(
            "OK"=>"Kara Listeye Ekleme / Çıkarma işleminde bir hata olmadığını gösterir.",
            30=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            40=>"1 dakika içerisinde 120 numaradan fazla istek yapıldığını ifade eder.",
            60=>"Geçersiz tip gönderimi",
            70=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder."

        );
       
        $response['code']=$result;
        $response['aciklama']=$sonuc[$result];

        return (array)$response;
    }
    public function smsgonder1_1($data):array
    {

       
        if(!isset($data['msgheader']))
        {
            $data['msgheader']=$this->header;
        }
        else{
            $data['msgheader']=$data['msgheader'];
        }
        if(!isset($data['gsm']))
        {
            $data['gsm']=null;
        }
        if(!isset($data['message']))
        {
            $data['gsm']=null;
        }
        if(!isset($data['filter']))
        {
            $data['filter']=0;
        }
        if(!isset($data['startdate']))
        {
            $data['startdate']=null;
        }
        if(!isset($data['stopdate']))
        {
            $data['stopdate']=null;
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.netgsm.com.tr/sms/send/get',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('usercode' => $this->username,'password' => $this->password,'gsmno' => $data['gsm'],'message' => $data['message'],'msgheader' => $data['msgheader'],'filter' => $data['filter'],'startdate' => $data['startdate'],'stopdate' => $data['stopdate']),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        $sonuc=array(
            "00"=>"Görevinizin tarih formatinda bir hata olmadığını gösterir.",
            "01"=>"Mesaj gönderim baslangıç tarihinde hata var. Sistem tarihi ile değiştirilip işleme alındı.",
            "02"=>"Mesaj gönderim sonlandırılma tarihinde hata var.Sistem tarihi ile değiştirilip işleme alındı.Bitiş tarihi başlangıç tarihinden küçük girilmiş ise, sistem bitiş tarihine içinde bulunduğu tarihe 24 saat ekler.",
            "20"=>"Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir. Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter sayılarının hesaplanış şeklini görebilirsiniz.)",
            "30"=>"Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
            "40"=>"Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",
            "50"=>"Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.",
            "51"=>"Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.",
            "70"=>"Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
            "85"=>"Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20'den fazla görev oluşturulamaz."

        );
        
        $dz=explode(" ",$response);

      

        if($dz[0]==20|| $dz[0]==30|| $dz[0]==40|| $dz[0]==50|| $dz[0]==51|| $dz[0]==70|| $dz[0]==85 ) 
        {
            $res['code']=$dz[0];
            $res['aciklama']=$sonuc[$dz[0]];
        }
        elseif($dz[0]=="00"|| $dz[0]=="01"|| $dz[0]=="02")
        {
           
            $res['code']=$dz[0];
            $res['aciklama']=$sonuc[$dz[0]];
            $res['bulkid']=$dz[1];
        }
        else{
            $res['durum']='Sistem Hatası';
        }
        return $res;


        

   }
    
    
}