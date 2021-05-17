<?php
/**
 * by stephan scheide
 */

namespace App\Services\Customers;


use Illuminate\Http\Request;

class CustomerCompletedRegisrationRequestAccessor
{

    private $r;

    private $a;

    public function __construct(Request $request)
    {
        $this->r = $request;
        $this->a = json_decode($request->input('rawRequest'), true);
    }

    public function getJSONData()
    {
        return $this->a;
    }

    public function getToken()
    {
        return $this->a['q63_token'];
    }

    public function getVisibleData()
    {
        $h = $this->safe('q14_uhrzeitZweiter.hourSelect');
        if (strlen($h) < 2) $h = "0$h";

        //EMail der Webseiten
        $seitenemails = $this->safe('q40_welcheEmailadressen');
        if ($seitenemails != null) {
            $seitenemails = implode(',', self::linesFromStringNormalized($seitenemails));
        }

        //Benoetige Domains
        $anzahlBenoetigterDomains = 0;
        $tmp = $this->safe('q8_anzahlDer8');
        if (is_array($tmp) && count($tmp) > 0) $tmp = $tmp[0];
        if (is_array($tmp) && count($tmp) > 0) $anzahlBenoetigterDomains = $tmp[0];

        //Webseitentyp
        $webseitentyp = 'k.A.';
        $tmp = $this->safe('q3_websitetypspatere');
        if (is_array($tmp)) {
            if (array_key_exists('1', $tmp))
                $tmp = $tmp['1'];
            else $tmp = $tmp[0];
        }
        if (is_array($tmp) && count($tmp) > 0) $webseitentyp = $tmp[0];

        //SSL
        $ssl = 'k.A.';
        $tmp = $this->safe('q6_sslVerschlusselung6');
        if (is_array($tmp) && count($tmp) > 0) $tmp = $tmp[0];
        if (is_array($tmp) && count($tmp) > 0) $ssl = $tmp[0];

        //Rabatt erstes Jahr
        $rabattErstesJahr = 'k.A.';
        $tmp = $this->safe('q4_rabatteFur');
        if (is_array($tmp) && array_key_exists('1', $tmp)) $tmp = $tmp['1'];
        if (is_array($tmp) && count($tmp) > 0) $tmp = $tmp[0];
        if (is_array($tmp) && count($tmp) > 0) $tmp = $tmp[0];
        if (!is_array($tmp)) $rabattErstesJahr = $tmp;

        return [
            'token' => $this->safe('q63_token'),
            'zweiterTerminTag' => $this->safe('q13_datumZweiter'),
            'zweiterTerminUhrzeit' =>
                $h . ':' .
                $this->safe('q14_uhrzeitZweiter.minuteSelect'),
            'wem' => $this->safe('q16_mitWem'),
            'strasse' => $this->safe('q31_adresse.addr_line1'),
            'hnr' => $this->safe('q31_adresse.addr_line2'),
            'plz' => $this->safe('q31_adresse.postal'),
            'ort' => $this->safe('q31_adresse.city'),
            'staat' => $this->safe('q31_adresse.state'),
            'land' => $this->safe('q31_adresse.country'),
            'firma' => $this->safe('q24_firmenname'),
            'name' => $this->safe('q29_vorUnd'),
            'email' => $this->safe('q34_email'),
            'telefon1' => $this->safe('q32_telefonnummerAuf.area') . $this->safe('q32_telefonnummerAuf.phone'),
            'telefon2' => $this->safe('q33_weitereTelefonnummer.area') . $this->safe('q33_weitereTelefonnummer.phone'),
            'zugangsdaten' => $this->safe('q19_ihreZugangsdaten'),
            'erklaerung_unternehmereigenschaft' => $this->safeFirst('q67_erklarungUnternehmereigenschaft'),
            'name_rechnungsempfaenger' => $this->safe('q61_nameRechnungsempfanger'),
            'kontakt_mittels' => $this->safeImplode('q36_wieMochten36'),
            'seite' => $this->safe('q38_aufWelcher38'),
            'seitenemail' => $seitenemails,
            'anbieter' => $this->safe('q42_beiWelchem'),
            'anzahl_benoetigter_domains' => $anzahlBenoetigterDomains,
            'webseitentyp' => $webseitentyp,
            'ssl_verschluesselung' => $ssl,
            'rabatt_erstes_jahr' => $rabattErstesJahr,
            'monatlicher_preis_komplett' => $this->safe('q2_monatlicherPreis'),
            'rabatt_in_prozent' => $this->safe('q9_rabattIn9'),
            'monatlicher_preis_nach_rabatt_erstes_jahr' => $this->safe('q10_monatlicherPrFeis10'),
            'webseitengestaltung' => $this->safeImplode('q45_websitegestaltung'),
            'logoauffrischung' => $this->safe('q49_input49'),
            'sonstiges' => $this->safe('q47_sonstiges'),
            'datenschutz' => $this->safe('q56_typeA56'),
            'erlaubnis_bildeinkauf' => $this->safe('q65_erlaubnisBildeinkauf'),
            'agbs' => $this->safe('q64_agbs'),
            'unterschrift_roh' => $this->safe('q59_unterschriftin'),
            'eventid' => $this->safe('event_id')
        ];
    }


    private function safe($str)
    {
        $tmp = explode('.', $str);
        $arr = $this->a;
        for ($i = 0; $i < count($tmp); $i++) {
            $key = $tmp[$i];
            if (!array_key_exists($key, $arr)) return null;
            $arr = $arr[$key];
            if (!is_array($arr)) return trim($arr);
        }
        return $arr;
    }

    private function safeImplode($str, $glue = ',')
    {
        $v = $this->safe($str);
        $r = is_array($v) ? implode($glue, $v) : $v;
        return $r;
    }

    private function safeFirst($str)
    {
        $v = $this->safe($str);
        return is_array($v) && count($v) > 0 ? $v[0] : null;
    }

    private static function linesFromStringNormalized($s)
    {
        $const = '@@@LINEBREAK@@@';
        $s = str_replace("\r\n", $const, $s);
        $s = str_replace("\r", $const, $s);
        $s = str_replace("\n", $const, $s);
        return explode($const, $s);
    }

}