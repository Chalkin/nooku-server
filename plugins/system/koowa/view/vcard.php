<?php
/**
 * @version		$Id$
 * @category	Koowa
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2009 Johan Janssens and Mathias Verraes. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

/**
 * Vcard Class
 * 
 * Complies to version 2.1 of the vCard specification
 *
 * @author		Johan Janssens <johan@koowa.org>
 * @category	Koowa
 * @package     Koowa_View
 * @see 		http://www.imc.org/pdi/
 * @see 		http://en.wikipedia.org/wiki/VCard
 */
class KViewVcard extends KViewFile
{
	/**
	 * The Vcard properties
	 *
	 * @var	array
	 */
	protected $_properties;
		
	/**
	 * Execute and echo's the views output
 	 *
	 * @return KViewVCard
	 */
	public function display()
	{
		//Set the filename
		$filename = KFactory::tmp('lib.koowa.filter.filename')->sanitize($this->_properties['FN']);
		$this->assign('filename', $filename.'.vcard');
		
		//Set the mimetype
		$this->assign('mimetype', 'text/x-vcard');
	
		//Render the vcard	
		$data 	= 'BEGIN:VCARD';
		$data	.= "\r\n";
		$data 	.= 'VERSION:2.1';
		$data	.= "\r\n";

		foreach( $this->_properties as $key => $value ) 
		{
			$data	.= "$key:$value";
			$data	.= "\r\n";
		}
		
		$data	.= 'REV:'. date( 'Y-m-d' ) .'T'. date( 'H:i:s' ). 'Z';
		$data	.= "\r\n";
		$data	.= 'END:VCARD';
		$data	.= "\r\n";
		
		echo $data;
		
		parent::display();
	}
	
	/**
	 * A structured representation of the name of the person, place or thing 
	 *
	 * @param 	string	Family name
	 * @param 	string	First name
	 * @param 	string	Additional name
	 * @param 	string	Prefix
	 * @param 	string 	Suffix
	 * @return  KViewVCard
	 */
	public function setName( $family = '', $first = '', $additional = '', $prefix = '', $suffix = '' ) 
	{
		$this->_properties["N"] 	= "$family;$first;$additional;$prefix;$suffix";
		$this->setFormattedName( trim( "$prefix $first $additional $family $suffix" ) );
		return $this;
	}
	
	/** 
	 * The formatted name string 
	 *
	 * @param	string	Name
	 * @return  KViewVCard
	 */
	public function setFormattedName($name) 
	{
		$this->_properties['FN'] = $this->quoted_printable_encode($name);
		return $this;
	}
	
	/**
	 * The name and optionally the unit(s) of the organization
	 * 
	 * This property is based on the X.520 Organization Name attribute and the X.520 Organization Unit attribute
	 *
	 * @param 	string	Organisation
	 * @return  KViewVCard
	 */
	public function setOrg( $org ) 
	{
		$this->_properties['ORG'] =  trim( $org );
		return $this;
	}
	
	/**
	 * Specifies the job title, functional position or function of the individual 
	 * within an organization (V. P. Research and Development)
	 *
	 * @param 	string	Title
	 * @return  KViewVCard
	 */
	public function setTitle( $title ) 
	{
		$this->_properties['TITLE'] = trim( $title );
		return $this;
	}
	
	/**
	 * The role, occupation, or business category within an organization (eg. Executive)
	 *
	 * @param 	string	Role
	 * @return  KViewVCard
	 */
	public function setRole( $role ) 
	{
		$this->_properties['ROLE'] = trim( $role );
		return $this;
	}
	

	/**
	 * The canonical number string for a telephone number for telephony communication
	 *
	 * @param	string	Phone number
	 * @param 	string	Type [PREF|WORK|HOME|VOICE|FAX|MSG|CELL|PAGER|BBS|CAR|MODEM|ISDN|VIDEO] or a combination, e.g. "PREF;WORK;VOICE"
	 * @return	 KViewVCard
	 */
	public function setPhoneNumber($number, $type = 'PREF;WORK;VOICE') 
	{
		$this->_properties['TEL;'.$type] = $number;
		return $this;
	}
	
	/**
	 * A structured representation of the physical delivery address
	 *
	 * @param 	string Postoffice
	 * @param	string Extended
	 * @param 	string Street
	 * @param 	string City
	 * @param 	string Region
	 * @param 	string Zip
	 * @param 	string Country
	 * @param 	string Type [DOM|INTL|POSTAL|PARCEL|HOME|WORK] or a combination e.g. "WORK;PARCEL;POSTAL"
	 * @return  KViewVCard
	 */
	public function setAddress( $postoffice = '', $extended = '', $street = '', $city = '', $region = '', $zip = '', $country = '', $type = 'WORK;POSTAL' ) 
	{
		$data = $this->encode( $postoffice );
		$data .= ';' . $this->encode( $extended );
		$data .= ';' . $this->encode( $street );
		$data .= ';' . $this->encode( $city );
		$data .= ';' . $this->encode( $region);
		$data .= ';' . $this->encode( $zip );
		$data .= ';' . $this->encode( $country );

		$this->_properties['ADR;'.$type] = $data;
		return $this;
	}
	
	/**
	 * Addressing label for physical delivery to the person/object
	 *
	 * @param 	string Postoffice
	 * @param	string Extended
	 * @param 	string Street
	 * @param 	string City
	 * @param 	string Region
	 * @param 	string Zip
	 * @param 	string Country
	 * @param 	string Type [DOM|INTL|POSTAL|PARCEL|HOME|WORK] or a combination e.g. "WORK;PARCEL;POSTAL"
	 * @return  KViewVCard
	 */
	public function setLabel($postoffice = '', $extended = '', $street = '', $city = '', $region = '', $zip = '', $country = '', $type = 'WORK;POSTAL') 
	{
		$label = '';
		if ($postoffice != '') {
			$label.= $postoffice;
			$label.= "\r\n";
		}

		if ($extended != '') {
			$label.= $extended;
			$label.= "\r\n";
		}

		if ($street != '') {
			$label.= $street;
			$label.= "\r\n";
		}

		if ($zip != '') {
			$label.= $zip .' ';
		}

		if ($city != '') {
			$label.= $city;
			$label.= "\r\n";
		}

		if ($region != '') {
			$label.= $region;
			$label.= "\r\n";
		}

		if ($country != '') {
			$country.= $country;
			$label.= "\r\n";
		}

		$this->_properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = $this->quoted_printable_encode($label);
		return $this;
	}
	
	/**
	 * The address for electronic mail communication
	 *
	 * @param 	string Email
	 * @return  KViewVCard
	 */
	public function setEmail($address) 
	{
		$this->_properties['EMAIL;PREF;INTERNET'] = $address;
		return $this;
	}
	
	/**
	 * An URL is a representation of an Internet location that can be used to obtain real-time information
	 *
	 * @param 	string	Url
	 * @param 	string	Type [WORK|HOME]
	 * @return  KViewVCard
	 */
	public function setURL($url, $type = 'WORK') 
	{
		$this->_properties['URL;'.$type] = $url;
		return $this;
	}

	/**
	 * An image or photograph of the individual associated with the vCard
	 *
	 * @param	string 	Type [GIF|JPEG]
	 * @param 	string	Photo
	 * @return  KViewVCard
	 */
	public function setPhoto($photo, $type = 'JPEG') 
	{ 
		$this->_properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
		return $this;
	}

	/**
	 * Date of birth of the individual
	 *
	 * @param 	string	Date YYYY-MM-DD
	 * @return  KViewVCard
	 */
	public function setBirthday($date) 
	{ 
		$this->_properties['BDAY'] = $date;
		return $this;
	}

	
	/**
	 * Specifies supplemental information or a comment that is associated with the vCard
	 *	
	 * @param 	string	Note
	 * @return  KViewVCard
	 */
	public function setNote($note) 
	{
		$this->_properties['NOTE;ENCODING=QUOTED-PRINTABLE'] = $this->quoted_printable_encode($note);
		return $this;
	}

	/**
	 * Encode
	 *
	 * @param 	string	String to encode
	 * @return 	string	Encoded string
	 */
	public function encode($string) 
	{
		return $this->escape($this->quoted_printable_encode($string));
	}

	/**
	 * Escape
	 *
	 * @param 	string	String to escape
	 * @return 	string	Escaped string
	 */
	public function escape($string) 
	{
		return str_replace(';',"\;",$string);
	}

	/**
	 * Quote for printable output
	 *
	 * @param 	string 	Input
	 * @param 	int		Max line length
	 * @return 	string
	 */
	public function quoted_printable_encode($input, $line_max = 76) 
	{
		$hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines 		= preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol 		= "\r\n";
		$linebreak 	= '=0D=0A';
		$escape 	= '=';
		$output 	= '';

		for ($j=0;$j<count($lines);$j++) 
		{
			$line 		= $lines[$j];
			$linlen 	= strlen($line);
			$newline 	= '';

			for($i = 0; $i < $linlen; $i++) 
			{
				$c 		= substr($line, $i, 1);
				$dec 	= ord($c);

				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = '=20';
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16);
					$h1 = floor($dec%16);
					$c 	= $escape.$hex["$h2"] . $hex["$h1"];
				}
				if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
					$newline = "    ";
				}
				$newline .= $c;
			} // end of for
			$output .= $newline;
			if ($j<count($lines)-1) {
				$output .= $linebreak;
			}
		}

		return trim($output);
	}
}