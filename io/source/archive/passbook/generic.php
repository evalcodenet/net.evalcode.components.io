<?php


namespace Components;


  /**
   * Io_Archive_Passbook_Generic
   *
   * @package net.evalcode.components
   * @subpackage io.archive.passbook
   *
   * @author evalcode.net
   */
  class Io_Archive_Passbook_Generic extends Io_Archive_Zip
  {
    // PREDEFINED PROPERTIES
    const STYLE='generic';

    const FORMAT_VERSION=1;

    const TYPE_BARCODE_2DMATRIX_QR='PKBarcodeFormatQR';
    const TYPE_BARCODE_AZTEC='PKBarcodeFormatAztec';
    const TYPE_BARCODE_STACKED_PDF417='PKBarcodeFormatPDF417';

    const TYPE_FIELD_HEADER='headerFields';
    const TYPE_FIELD_PRIMARY='primaryFields';
    const TYPE_FIELD_SECONDARY='secondaryFields';
    const TYPE_FIELD_AUXILIARY='auxiliaryFields';
    const TYPE_FIELD_BACK='backFields';

    const FILE_PASS='pass.json';
    const FILE_MANIFEST='manifest.json';
    const FILE_SIGNATURE='signature';
    const FILE_ICON='icon.png';
    const FILE_LOGO='logo.png';
    const FILE_BACKGROUND='background.png';
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var Properties
     */
    public $properties;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($path_, $passTypeIdentifier_, $description_, $organizationName_, $teamIdentifier_)
    {
      parent::__construct($path_, Io_File::CREATE|Io_File::TRUNCATE);

      $this->properties=new Properties();

      $this->properties->description=$description_;
      $this->properties->formatVersion=self::FORMAT_VERSION;
      $this->properties->organizationName=$organizationName_;
      $this->properties->passTypeIdentifier=$passTypeIdentifier_;
      $this->properties->teamIdentifier=$teamIdentifier_;
      $this->properties->serialNumber=static::generateSerialNumber();
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function generateSerialNumber()
    {
      $serial=md5(uniqid(null, true));
      $chunks=str_split($serial, 5);

      return implode('-', array_slice($chunks, 0, 5));
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function getStyle()
    {
      return self::STYLE;
    }

    public function getFields()
    {
      return $this->m_fields;
    }

    public function addField($type_, $name_, $value_, $title_)
    {
      $this->m_fields[$type_][]=array(
        'key'=>$name_,
        'value'=>$value_,
        'label'=>$title_
      );
    }

    public function addHeaderField($name_, $value_, $title_)
    {
      $this->addField(self::TYPE_FIELD_HEADER, $name_, $value_, $title_);
    }

    public function addPrimaryField($name_, $value_, $title_)
    {
      $this->addField(self::TYPE_FIELD_PRIMARY, $name_, $value_, $title_);
    }

    public function addSecondaryField($name_, $value_, $title_)
    {
      $this->addField(self::TYPE_FIELD_SECONDARY, $name_, $value_, $title_);
    }

    public function addAuxiliaryField($name_, $value_, $title_)
    {
      $this->addField(self::TYPE_FIELD_AUXILIARY, $name_, $value_, $title_);
    }

    public function addBackField($name_, $value_, $title_)
    {
      $this->addField(self::TYPE_FIELD_BACK, $name_, $value_, $title_);
    }

    public function setIcon(Io_Image $icon_)
    {
      if(!$icon_->exists())
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Given icon does not exist [icon: %s].', Io_MimeType::IMAGE_PNG(), $icon_)
        );
      }

      if(!Io_MimeType::IMAGE_PNG()->equals($icon_->getMimeType()))
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Icon must be of type %s [icon: %s].', Io_MimeType::IMAGE_PNG(), $icon_)
        );
      }

      $this->add($icon_, self::FILE_ICON);
    }

    public function setLogo(Io_Image $logo_)
    {
      if(!$logo_->exists())
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Given logo does not exist [icon: %s].', Io_MimeType::IMAGE_PNG(), $logo_)
        );
      }

      if(!Io_MimeType::IMAGE_PNG()->equals($logo_->getMimeType()))
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Logo must be of type %s [logo: %s].', Io_MimeType::IMAGE_PNG(), $logo_)
        );
      }

      $this->add($logo_, self::FILE_LOGO);
    }

    public function setLogoText($text_)
    {
      $this->properties->logoText=$text_;
    }

    public function setBarcode($text_, $alternativeText_=null,
      Io_Charset $charset_=null, $type_=self::TYPE_BARCODE_2DMATRIX_QR)
    {
      if(null===$charset_)
        $charset_=Io_Charset::ISO_8859_1();

      $barcode=array(
        'format'=>$type_,
        'message'=>$text_,
        'messageEncoding'=>strtolower($charset_->name())
      );

      if(null!==$alternativeText_)
        $barcode['altText']=$alternativeText_;

      $this->properties->barcode=$barcode;
    }

    public function setLabelColor(Color $color_)
    {
      $this->properties->labelColor=(string)$color_;
    }

    public function setForegroundColor(Color $color_)
    {
      $this->properties->foregroundColor=(string)$color_;
    }

    public function setBackgroundColor(Color $color_)
    {
      $this->properties->backgroundColor=(string)$color_;
    }

    public function setBackgroundImage(Io_Image $image_)
    {
      if(!$image_->exists())
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Given background image does not exist [icon: %s].', Io_MimeType::IMAGE_PNG(), $image_)
        );
      }

      if(!Io_MimeType::IMAGE_PNG()->equals($image_->getMimeType()))
      {
        throw new Io_Exception('io/archive/passbook/generic', sprintf(
          'Background image must be of type %s [logo: %s].', Io_MimeType::IMAGE_PNG(), $image_)
        );
      }

      $this->add($image_, self::FILE_BACKGROUND);
    }

    public function addAssociatedAppKey($appKey_)
    {
      $associatedStoreIdentifiers=array();
      if(isset($this->properties->associatedStoreIdentifiers))
        $associatedStoreIdentifiers=$this->properties->associatedStoreIdentifiers;

      $associatedStoreIdentifiers[]=$appKey_;

      $this->properties->associatedStoreIdentifiers=$associatedStoreIdentifiers;
    }

    public function addRelevantLocation($text_, $altitude_, $latitude_, $longitude_)
    {
      $locations=array();
      if(isset($this->properties->locations))
        $locations=$this->properties->locations;

      $locations[]=array(
        'relevantText'=>$text_,
        'altitude'=>$altitude_,
        'latitude'=>$latitude_,
        'longitude'=>$longitude_
      );

      $this->properties->locations=$locations;
    }

    public function setDateTimeStart(\DateTime $date_)
    {
      $this->properties->relevantDate=$date_->format('c');
    }

    public function setWebserviceUrl($url_)
    {
      $this->properties->webServiceURL=$url_;
    }

    public function setWebserviceAuthenticationToken($authenticationToken_)
    {
      $this->properties->authenticationToken=$authenticationToken_;
    }

    public function enableStripShine()
    {
      $this->properties->suppressStripShine=false;
    }

    public function disableStripShine()
    {
      $this->properties->suppressStripShine=true;
    }

    public function closeAndSign($certificateAppleIntermediate_, $certificate_, $privateKey_, $privateKeyPassphrase_)
    {
      $properties=$this->properties->toArray();
      $properties[$this->getStyle()]=$this->getFields();

      $pass=Io::tmpFile();
      $pass->setContent(json_encode($properties));
      $this->add($pass, self::FILE_PASS);

      $manifest=Io::tmpFile();
      $manifest->setContent(json_encode($this->m_files));

      parent::add($manifest, self::FILE_MANIFEST);

      $signature=Io::tmpFile();

      @openssl_pkcs7_sign(
        (string)$manifest,
        (string)$signature,
        "file://$certificate_",
        array("file://$privateKey_", $privateKeyPassphrase_),
        array(),
        PKCS7_BINARY|PKCS7_NOATTR|PKCS7_DETACHED,
        $certificateAppleIntermediate_
      );

      if(1>$signature->getSize()->bytes())
        throw new Io_Exception('io/archive/passbook/generic', 'Failed to sign passs.');

      $content=$signature->getContent();
      $start=strpos($content, 'filename="smime.p7s"')+strlen('filename="smime.p7s"');
      $content=trim(substr($content, $start, strrpos($content, '------')-$start));
      $signature->setContent(base64_decode($content));

      parent::add($signature, self::FILE_SIGNATURE);

      parent::close();

      $signature->delete();
      $manifest->delete();
      $pass->delete();

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function add(Io_File $file_, $withName_=null)
    {
      parent::add($file_, $withName_);

      if(null===$withName_)
        $withName_=$file_->getName();

      $this->m_files[$withName_]=$file_->getHashSHA1();
    }

    public function close()
    {
      $properties=$this->properties->toArray();
      $properties[$this->getStyle()]=$this->getFields();

      $pass=Io::tmpFile();
      $pass->setContent(json_encode($properties));
      $this->add($pass, self::FILE_PASS);

      $manifest=Io::tmpFile();
      $manifest->setContent(json_encode($this->m_files));

      parent::add($manifest, self::FILE_MANIFEST);

      parent::close();

      $manifest->delete();
      $pass->delete();

      return $this;
    }

    public function getMimeType()
    {
      return Io_MimeType::APPLICATION_VND_APPLE_PKPASS();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_fields=array();
    private $m_files=array();
    //--------------------------------------------------------------------------
  }
?>
