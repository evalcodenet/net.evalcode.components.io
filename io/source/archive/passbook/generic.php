<?php


  /**
   * Io_Archive_Passbook_Generic
   *
   * @package net.evalcode.components
   * @subpackage io.archive.passbook
   *
   * @author evalcode.net
   */
  abstract class Io_Archive_Passbook_Generic extends Io_Archive_Zip
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
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var Properties
     */
    public $properties;

    public $certificatePassType;
    public $certificatePassTypePassphrase;
    public $certificateAppleIntermediate;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($path_, $passTypeIdentifier_, $description_, $organizationName_, $teamIdentifier_)
    {
      parent::__construct($path_, Io_File::CREATE);

      $this->properties=new Properties();

      $this->properties->formatVersion=self::FORMAT_VERSION;
      $this->properties->style=$this->getStyle();

      $this->properties->serialNumber=static::generateSerialNumber();

      $this->properties->description=$description_;
      $this->properties->organizationName=$organizationName_;
      $this->properties->teamIdentifier=$teamIdentifier_;
      $this->properties->passTypeIdentifier=$passTypeIdentifier_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function generateSerialNumber()
    {
      $serial=md5(uniqid(null, true));
      $chunks=str_split($serial, 4);

      return implode('-', array_slice($chunks, 0, 5));
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function close()
    {
      parent::close();

      $properties=$this->properties->toArray();
      $properties[$this->getStyle()]=$this->getFields();

      var_dump(json_encode($properties));
    }

    public function getStyle()
    {
      return self::STYLE;
    }

    public function setIcon(Io_Image $icon_)
    {
      $this->m_icon=$icon_;
    }

    public function setLogo(Io_Image $logo_)
    {
      $this->m_logo=$logo_;
    }

    public function setLogoText($text_)
    {
      $this->properties->logoText=$text_;
    }

    public function setBarcode($text_, $alternativeText_,
      Io_Charset $charset_=null, $type_=self::TYPE_BARCODE_2DMATRIX_QR)
    {
      if(null===$charset_)
        $charset_=Io_Charset::ISO_8859_1();

      $this->properties->barcode=array(
        'format'=>$type_,
        'message'=>$text_,
        'altText'=>$alternativeText_,
        'messageEncoding'=>$charset_->name()
      );
    }

    public function getFields()
    {
      return $this->m_fields;
    }

    public function addField($type_, $name_, $value_, $title_)
    {
      $this->m_fields[$type_][$name_]=array(
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

    public function setDateTimeStart(DateTime $date_)
    {
      $this->properties->relevantDate=$date_->format('c');
    }

    public function setColorLabel(Color $color_)
    {
      $this->properties->labelColor=(string)$color_;
    }

    public function setColorForeground(Color $color_)
    {
      $this->properties->foregroundColor=(string)$color_;
    }

    public function setColorBackground(Color $color_)
    {
      $this->properties->backgroundColor=(string)$color_;
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
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_fields=array();
    /**
     * @var Io_Image
     */
    private $m_logo;
    /**
     * @var Io_Image
     */
    private $m_icon;
    //--------------------------------------------------------------------------
  }
?>
