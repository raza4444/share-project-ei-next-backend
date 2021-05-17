<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Entities\Customers\CustomerInfoData;
use App\Entities\States\FederalState;
use App\ValueObjects\LocationSslInfo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Location
 * @package App\Entities\Branches
 *
 * @property int company_register_id
 * @property int company_register_event_id
 * @property string result
 * @property int agentId1
 * @property int agentId2
 * @property int agentId3
 * @property string title
 * @property string street
 * @property string zip
 * @property string city
 * @property string location_lat
 * @property string location_lng
 * @property string country
 * @property string state
 * @property string phoneNumber
 * @property string mobilePhoneNumber
 * @property string email
 * @property string fax
 * @property bool callOnlyForenoon
 * @property LocationCategory $locationCategory
 * @property int manuellErstellt
 * @property int locationCategoryId
 * @property int said_whatsapp
 * @property int ist_alte_homepage
 * @property string homepage_info
 * @property int customerstate
 *
 * @property string username
 * @property string password
 * @property string registerlink
 * @property int canlogin
 *
 * @property string domain
 * @property string ftphost
 * @property string ftpusername
 * @property string ftppassword
 * @property string ftpdirectoryhtml
 * 
 * @property string ext_host_domain
 * @property string ext_host_ftphost
 * @property string ext_host_ftpusername
 * @property string ext_host_ftppassword
 * @property string ext_host_ftpdirectoryhtml
 *
 * @property string ansprechpartner_anrede
 * @property string ansprechpartner_vorname
 * @property string ansprechpartner_nachname
 * @property string werbeaktion
 * 
 * @property string canceled
 * @property string revoked
 * @property string date_of_cancellation
 * @property string effective_date_of_cancellation
 *
 * @property int ssl_origin
 * @property int ftp_credentials_checked
 * @property int ssl_active
 * @property int status_cert_gen
 * @property int status_cert_import
 * @property string last_cert_gen
 * @property string last_cert_import
 * @property string last_cert_gen_touched
 * @property string last_cert_import_touched
 * @property string last_ssl_error_message
 * @property string last_ssl_error
 * @property string ssl_options
 * @property int ssl_count_processed_gen
 * @property int ssl_count_processed_import
 * @property LocationSslInfo ssl_info
 * @property array ssl_check_bucket
 *
 */
class Location extends AbstractModel
{
  use SoftDeletes;

  public $table = "campaign_locations";
  protected $fillable = [
    'company_register_id',
    'company_register_event_id',
    'result',
    'title',
    'street',
    'zip',
    'country',
    'phoneNumber',
    'email',
    'homepage',
    'city',
    'location_lat',
    'location_lng',
    'werbeaktion',
    'canLogin',
    'said_whatsapp',
    'sub_category'
  ];

  public function locationCategory()
  {
    return $this->belongsTo(LocationCategory::class, "locationCategoryId");
  }

  public function federalState()
  {
    return $this->belongsTo(FederalState::class, 'bundeslandid');
  }

  public function events()
  {
    return $this->hasMany(LocationEvent::class, 'schoolId', 'id');
  }

  public function notes()
  {
    return $this->hasMany(LocationNote::class, 'locationId', 'id');
  }

  public function numbers()
  {
    return $this->hasMany(LocationPhoneNumber::class, 'locationid', 'id');
  }

  public function emails()
  {
    return $this->hasMany(LocationEmail::class, 'locationid', 'id');
  }

  public function infoData()
  {
    return $this->hasMany(CustomerInfoData::class, 'customerid', 'id');
  }

  public function hashOfFtpData() {
      $tmp = [$this->ftpusername,$this->ftppassword,$this->ftphost,$this->ftpdirectoryhtml];
      return implode(';',$tmp);
  }
}
