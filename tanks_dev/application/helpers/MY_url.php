<?php defined('SYSPATH') or die('No direct script access.');
 
class url extends url_Core {
	/**
	* Returns URL that has been converted to full-path URL 
	*
	* @param string $relative_url URL to be converted
	* @param string $protocol Site protocol to use eg. http, https
	* @return object Full-path URL containing site protocol.
	*/
	public static function fullpath($path, $append_arr=NULL, $protocol=NULL) {
	$pos = strpos($path, 'tex-ui');
	if ($pos !== false) {
		return('https://waste.web.env.nm.gov' . $path . '?sessionId=' . Kohana::instance()->input->get('sessionId') );
	}
	else if(strtolower($_SERVER['SERVER_NAME']) == 'waste.web-ghuang.nmenv.state.nm.us') {
		if ($append_arr)
                        $path = url::append_url($path, $append_arr);
		if (strpos($path, '/') === 0) // remove redundant slash
			$path = substr($path, 1);
		return(url::base(FALSE, $protocol) . $path);	
	}
	else {
		if ($append_arr)
			$path = url::append_url($path, $append_arr);
		if (strpos($path, '/') === 0) // remove redundant slash
			$path = substr($path, 1);
		return(url::base(FALSE, $protocol) . $path);
	     }
	}

	/**
        * Returns URL that has been converted to UDAPI URL
        *
        * @param string $relative_url URL to be converted
        * @param string $protocol Site protocol to use eg. http, https
        * @return object Full-path URL containing site protocol.
        */
	public static function udapi_url($path, $append_arr=NULL, $protocol=NULL) {
        $pos = strpos($path, 'tex-ui');
        if ($pos !== false) {
                return('https://waste.web.env.nm.gov' . $path . '?sessionId=' . Kohana::instance()->input->get('sessionId') );
        }
        else if(strtolower($_SERVER['SERVER_NAME']) == 'waste.web-ghuang.nmenv.state.nm.us') {
		echo '123';exit;
                if (strpos($path, '/') === 0) // remove redundant slash
                        $path = substr($path, 1);
                return $path;
        }
        else {
                if ($append_arr)
                        $path = url::append_url($path, $append_arr);
                if (strpos($path, '/') === 0) // remove redundant slash
                        $path = substr($path, 1);
                return(url::base(FALSE, $protocol) . $path);
             }
        }

	/**
         * Supports multiple keyed ID if $append_url is array
         */
	public static function external_file_path($path) {
		$pos = strpos($path, 'tex-ui');
		if ($pos !== false) {
	                return('https://waste.web.env.nm.gov' . $path . '?sessionId=' . Kohana::instance()->input->get('sessionId') );
        	} else {
			// if it's in local machine, just use web-t
			if(strtolower($_SERVER['SERVER_NAME']) == 'waste.web-ghuang.nmenv.state.nm.us') {
				return 'https://tanks.waste.web-t.env.nm.gov/' . $path;
			} else {
				if (strpos($path, '/') === 0) // remove redundant slash
		                        $path = substr($path, 1);
                		return(url::base(FALSE, $protocol) . $path);
			}
		}

	}

	/**
	 * Supports multiple keyed ID if $append_url is array
	 */
	public static function append_url($preset_url, $append_arr) {
		if (! is_array($append_arr)) $append_arr = array($append_arr);

		return($preset_url . implode('/', array_map('url::kohana_encode', $append_arr)));
	}

	/**
	 * Overridden to fullpath URL conversion
	 */
	public static function redirect($url, $redirect_code=302, $do_fullpath=FALSE) {
		if ($do_fullpath)
			parent::redirect(url::fullpath($url), $redirect_code);
		else
			parent::redirect($url, $redirect_code);
	}

	/**
	 * Urlencodes and converts chars to symbols for problematic chars in kohana
	 */
	public static function kohana_encode($param) {
		return(urlencode(strtr($param, array(
			'[' => '-LB-',	// kohana doesn't like brackets
			']' => '-RB-'	// kohana doesn't like brackets
		))));
	}

	/**
	 * Urlencoded characters are not enough to get around kohana's URL rewrite rules
	 * Should not be used for all cases since '_' might really have been an underscore
	 */
	public static function kohana_decode($param) {
		return(strtr($param, array(
			'_' => '.',	// kohana automatically converts '.' to '_'
			'-LB-' => '[',
			'-RB-' => ']'
		)));
	}

	public static function invoice_filename($owner_id, $invoice_date) {
		$invoice_date = strtoupper($invoice_date);
		return($owner_id ? "inv_{$owner_id}_{$invoice_date}.pdf" : 'all_invoices.pdf');
	}

	public static function cert_filename($status) {
		return($status['FACILITY_ID'] ? "cert_{$status['FACILITY_ID']}.pdf" : "all_certs_{$status['COUNT']}.pdf");
	}

	public static function convert_to_pdf($url, $output_file) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://v2.api2pdf.com/libreoffice/any-to-pdf');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "{
			\"url\":\"" . $url
			. "\",\"inline\":true,
			\"fileName\":\"" . $output_file
			. "\",\"extraHTTPHeaders\":{},
			\"useCustomStorage\":false,
			\"storage\":{
				\"method\":\"PUT\",
				\"url\":\"https://presignedurl\",
				\"extraHTTPHeaders\":{}
			}
		}");

		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Authorization: ' . Kohana::config('constants.api2pdf_api_key');
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = json_decode(curl_exec($ch), true);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		
		return $result;
	}
}
?>
