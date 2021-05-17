<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Utils\StringUtils;

/**
 * Class IncomingCompany
 *
 * ein Unternehmen, welches von außen ins System gelangt und zwischengespeichert wird
 *
 * @package Modules\Companies\Entities
 * @property string name
 * @property string bundesland
 * @property string land
 * @property string branche
 * @property string strasse
 * @property string plz
 * @property string ort
 * @property string oeffnungszeiten
 * @property string webseite
 * @property string telefonnummer
 * @property string email
 * @property string inhaber
 * @property string hash
 * @property int hashversion
 * @property string erzeuger
 * @property int locationId
 * @property int status
 *
 */
class IncomingCompany extends AbstractModel
{

    const STATUS_NEW = 0;

    const STATUS_IMPORTED_AS_SCHOOL = 1;

    const STATUS_ERROR_CREATING_SCHOOL = 2;

    protected $table = 'unternehmensimport';

    /**
     * creates new instance with important fields filled for new one
     * @return IncomingCompany
     */
    public static function createCommonInstance()
    {
        $i = new IncomingCompany();
        $i->locationId = null;
        $i->status = self::STATUS_NEW;
        return $i;
    }

    /**
     * quickly checks if valid
     * @return bool
     */
    public function isValid()
    {
        return !StringUtils::isTooShort($this->name, 3) && !StringUtils::isTooShort($this->plz, 4);
    }

    /**
     * calculcates hash for duplicate checks
     */
    public function calculateHash()
    {
        $this->hashversion = 1;
        $this->hash = sha1($this->name . '@' . $this->strasse . '@' . $this->plz);
    }

    /**
     * saves this entity but performs validation before
     * furthermore, hash is calculated
     * @param array $options
     * @return mixed
     * @throws ValidationException
     */
    public function save(array $options = array())
    {
        if (!$this->isValid()) {
            throw new ValidationException('fields_missing');
        }
        $this->calculateHash();
        return parent::save($options);
    }

    /**
     * saves if this company does not already exissts
     * this is checked by reading the hash's existence in the database
     * @param array $options
     * @return mixed
     * @throws ValidationException
     */
    public function saveNoDuplicate(array $options = array())
    {
        if (!$this->isValid()) {
            throw new ValidationException('fields_missing');
        }
        $this->calculateHash();

        $first = self::query()
            ->where('hash', '=', $this->hash)
            ->where('hashversion', '=', $this->hashversion)
            ->first();

        if ($first !== null) {
            throw new ValidationException('hash ' . $this->hash . ' already present');
        }

        return parent::save($options);
    }

}
