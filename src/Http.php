<?php

/* ==== LICENCE AGREEMENT =====================================================
 *
 * © Cédric Ducarre (20/05/2010)
 * 
 * wlib is a set of tools aiming to help in PHP web developpement.
 * 
 * This software is governed by the CeCILL license under French law and
 * abiding by the rules of distribution of free software. You can use, 
 * modify and/or redistribute the software under the terms of the CeCILL
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 * 
 * As a counterpart to the access to the source code and rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty and the software's author, the holder of the
 * economic rights, and the successive licensors have only limited
 * liability.
 * 
 * In this respect, the user's attention is drawn to the risks associated
 * with loading, using, modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean that it is complicated to manipulate, and that also
 * therefore means that it is reserved for developers and experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or 
 * data to be ensured and, more generally, to use and operate it in the 
 * same conditions as regards security.
 * 
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 * 
 * ========================================================================== */

namespace wlib\Http\Client;

use UnexpectedValueException;

if (!function_exists('curl_init'))
	trigger_error('wlib\Http\Client : cURL extention not found', E_USER_ERROR);

/**
 * HTTP client.
 *
 * The extention PHP cURL must be loaded to work.
 *
 * The cURL error list is available at the following URL :
 * http://curl.haxx.se/libcurl/c/libcurl-errors.html
 *
 * @author Cédric Ducarre
 * @since 07/12/2010
 * @version	11/02/2021
 */
class Http
{
	/**
	 * Run an HTTP GET request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function get(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'get', $mData, $aMore);
	}

	/**
	 * Run an HTTP POST request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function post(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'post', $mData, $aMore);
	}

	/**
	 * Run an HTTP PUT request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function put(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'put', $mData, $aMore);
	}

	/**
	 * Run an HTTP PATCH request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function patch(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'patch', $mData, $aMore);
	}

	/**
	 * Run an HTTP DELETE request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function delete(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'delete', $mData, $aMore);
	}

	/**
	 * Run an HTTP HEAD request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function head(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'head', $mData, $aMore);
	}

	/**
	 * Run an HTTP OPTIONS request.
	 *
	 * @see request() for complete doc.
	 * @param string $sUrl URL to call.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function options(string $sUrl, array|string $mData = [], array $aMore = []): array
	{
		return static::request($sUrl, 'options', $mData, $aMore);
	}

	/**
	 * Download a file.
	 *
	 * ## Note about HTTP headers
	 *
	 * 
	 * The `CURLOPT_HEADER` option is deliberately disabled so that only the body
	 * of the response is saved in the file. As a result, HTTP headers are not
	 * available in the return table.
	 *
	 * If you need these headers, you can reset the parameter to `true`. In this
	 * case, you will have to open the file once downloaded to extract them manually.
	 *
	 * @param string $sUrl File URL.
	 * @param string|resource $mFile Path of file handle where save the response.
	 * @param array|string $mData Request data.
	 * @param array $aMore Array of additional parameters to set headers and/or cURL options.
	 * @return array Response array.
	 */
	public static function download(string $sUrl, mixed $mFile, array|string $mData = [], array $aMore = []): array
	{
		if (empty($mFile))
			throw new UnexpectedValueException('$mFile can\'t be empty.');

		if (is_string($mFile))
			$mFile = fopen($mFile, 'w');

		if (!is_resource($mFile))
			throw new UnexpectedValueException(
				'$mFile must be a file pointer (fopen) or a writable filename.'
			);

		if (!isset($aMore['curlopts']))
			$aMore['curlopts'] = [];

		$aMore['curlopts'] += [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FILE => $mFile,
			CURLOPT_HEADER => false
		];

		$aResponse = static::get($sUrl, $mData, $aMore);

		fclose($mFile);

		return $aResponse;
	}

	/**
	 * Run an HTTP request.
	 *
	 * ## Usage of '$mData'
	 *
	 * ### GET case
	 *
	 * Data are concatenated to the URL, with automatic add of "?". If `$mData`
	 * is an array, it is then passed to `http_build_query()`.
	 *
	 * ### POST, PUT, PATCH, DELETE cases
	 *
	 * Data are added to `CURLOPT_POSTFIELDS` after processing ; the function
	 * handle the convertion of '@' prefixed parameters for joined documents to
	 * `CURLFile` instance.
	 *
	 * The "Content-Type" HTTP header is automatically defined to
	 * "application/x-www-form-urlencoded", if it's not defined by caller. In the
	 * case of a file submit, "Content-Type" is forced to "multipart/form-data".
	 *
	 * To make files submissions with custom "Content-Type", use the
	 * `multipartBuildQuery()` method which will allows you to build manually
	 * a "multipart/form-data" request.
	 *
	 * ## Usage of `$aMore`
	 *
	 * `$aMore` supports the following entries :
	 *
	 * - 'headers', array : array of HTTP headers to join to the request,
	 * - 'curlopts', array : array of `CURLOPT_*` options.
	 *
	 * ### Default options
	 *
	 * - CURLOPT_RETURNTRANSFER = true
	 * - CURLOPT_FRESH_CONNECT	= true
	 * - CURLOPT_SSL_VERIFYPEER	= false
	 * - CURLOPT_FOLLOWLOCATION	= true
	 * - CURLOPT_MAXREDIRS		= 5
	 * - CURLOPT_CONNECTTIMEOUT	= 0
	 * - CURLOPT_USERAGENT		= 'Oasis cURL Client'
	 * - CURLOPT_HEADER			= true
	 *
	 * Use `$aMore['curlopts']` to overwrite default values.
	 *
	 * ## Returning array
	 *
	 * - 'body' : self-explanatory (empty for **HEAD** requests),
	 * - 'status_code' : self-explanatory,
	 * - 'content_type' : self-explanatory,
	 * - 'content_lenght' : self-explanatory,
	 * - 'effective_url' : final URL called, when `CURLOPT_FOLLOWLOCATION` is set to `true`,
	 * - 'redirect_url' : redirect URL to follow, when `CURLOPT_FOLLOWLOCATION` is set to `false`,
	 * - 'headers' : response headers,
	 * - 'duration' : running execution time.
	 *
	 * The following keys are only available in case of error :
	 *
	 * - 'errorno' & 'error' : code and error message ('body' is then generaly set to `false`).
	 *
	 * @param string $sUrl URL à appeler.
	 * @param string $sMethod Méthode HTTP à exécuter, GET par défaut.
	 * @param array|string $mData Données de la requête.
	 * @param array $aMore Tableau de paramètres supplémentaires pour joindres des entêtes HTTP et/ou des options cURL.
	 * @return array Tableau de la réponse.
	 */
	public static function request(
		string $sUrl,
		string $sMethod = 'get',
		array|string $mData = [],
		array $aMore = []
	): array
	{
		$sUrl = trim($sUrl);
		$sMethod = strtolower(trim($sMethod));

		if ($sUrl == '')
			throw new UnexpectedValueException('URL can\'t be empty.');

		if (!in_array($sMethod, array('get', 'post', 'put', 'patch', 'head', 'delete', 'options')))
			throw new UnexpectedValueException(
				'Unsupported "'.$sMethod.'" method.'
			);

		/**
		 * Parse an HTTP header string.
		 *
		 * @param string $sHeadersString HTTP header string.
		 * @return array
		 */
		$cParseHeaders = function($sHeadersString)
		{
			$aResponseHeaders	= preg_split(
				"`(\r|\n)+`", $sHeadersString, -1, \PREG_SPLIT_NO_EMPTY
			);
			$aParsedHeaders		= array();
			$iCountHeaders		= sizeof($aResponseHeaders);

			for ($i = 0; $i < $iCountHeaders; $i++)
			{
				$aParts = explode(':', $aResponseHeaders[$i], 2);

				if (count($aParts) < 2)
				{
					$aParsedHeaders[] = $aParts[0];
					continue;
				}

				$sHeaderName	= trim($aParts[0]);
				$sHeaderValue	= trim($aParts[1]);

				// HTTP RFC Sec 4.2 Paragraph 5 : http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
				if (array_key_exists($sHeaderName, $aParsedHeaders))
					$aParsedHeaders[$sHeaderName] .= ',' . $sHeaderValue;
				else
					$aParsedHeaders[$sHeaderName] = $sHeaderValue;
			}

			return $aParsedHeaders;
		};

		if (stripos($sUrl, 'http://') === false && stripos($sUrl, 'https://') === false)
			$sUrl = 'http://'. $sUrl;

		$aDefaultsOpts = array(
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FRESH_CONNECT	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_MAXREDIRS		=> 5,
			CURLOPT_CONNECTTIMEOUT	=> 0,
			CURLOPT_USERAGENT		=>
				'wlib-http-cli php/'.PHP_VERSION.' curl/'.curl_version()['version'],
			CURLOPT_HEADER			=> true
		);

		// Init. URL
		if ($sMethod == 'get' && !empty($mData))
			$sUrl .= '?'.(is_array($mData) ? http_build_query($mData, '', '&') : $mData);

		$aMore['curlopts'][CURLOPT_URL] = $sUrl;

		// Init. by method
		if ($sMethod == 'post')
			$aMore['curlopts'][CURLOPT_POST] = true;

		if ($sMethod == 'head')
			$aMore['curlopts'][CURLOPT_NOBODY] = true;

		if (in_array($sMethod, array('put', 'patch', 'head', 'delete', 'options')))
			$aMore['curlopts'][CURLOPT_CUSTOMREQUEST] = strtoupper($sMethod);

		// == POST and PUT specific proccessing
		if ($sMethod == 'post' || $sMethod == 'put' || $sMethod == 'patch' || $sMethod == 'delete')
		{
			if (is_array($mData))
			{
				$bBinaryData = false;

				foreach ($mData as $sKey => $mValue)
				{
					if (is_array($mValue) && empty($mValue))
						$mData[$sKey] = '';

					elseif (is_string($mValue) && strpos($mValue, '@') === 0)
					{
						$bBinaryData = true;

						if (version_compare(PHP_VERSION, '5.5.0') >= 0)
						{
							// Extraction @.../.../file.ext[;type=...]
							$aFile = explode(';', substr($mValue, 1));

							$mData[$sKey] = (sizeof($aFile) > 1
								? new \CURLFile($aFile[0], $aFile[1])
								: new \CURLFile($aFile[0])
							);
						}
					}

					if (version_compare(PHP_VERSION, '5.5.0') >= 0)
					{
						if ($mValue instanceof \CURLFile)
							$bBinaryData = true;
					}
				}

				if (!$bBinaryData)
					$mData = http_build_query($mData, '', '&');
				else
					// boundary managed by cURL
					$aMore['headers']['Content-Type'] = 'multipart/form-data';
			}

			$aDefaultsOpts[CURLOPT_POSTFIELDS] = ($mData === '' ? [] : $mData);
		}

		$aMore['curlopts'] = $aMore['curlopts'] + $aDefaultsOpts;
		$bHasHeaders = array_key_exists('headers', $aMore);

		// Add default "Content-Type" for data submissions
		if (
			array_key_exists(CURLOPT_POSTFIELDS, $aMore['curlopts'])
			&& $aMore['curlopts'][CURLOPT_POSTFIELDS]
			&& (!$bHasHeaders || ($bHasHeaders && !array_key_exists('Content-Type', $aMore['headers'])))
		) {
			$aMore['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
		}

		// "Expect" header is forced to avoid receiving "100 Continue" response in the same time of "200 OK"
		if (!$bHasHeaders || ($bHasHeaders && !array_key_exists('Expect', $aMore['headers'])))
		{
			$aMore['headers']['Expect'] = '';
		}

		// Init. HTTP headers
		if ($bHasHeaders && !empty($aMore['headers']))
		{
			$aHeaders = array();

			foreach ($aMore['headers'] as $sHeaderName => $sHeaderValue)
				$aHeaders[] = $sHeaderName.': '.$sHeaderValue;

			$aMore['curlopts'][CURLOPT_HTTPHEADER] = $aHeaders;
		}

		// == Run
		$aResponse = [];
		$rCurlSession = curl_init();

		curl_setopt_array($rCurlSession, $aMore['curlopts']);

		$iStart			= microtime(true);
		$sResponse		= curl_exec($rCurlSession);
		$iEnd			= microtime(true);
		$iCurlErrno		= curl_errno($rCurlSession);
		$iHeaderSize	= curl_getinfo($rCurlSession, CURLINFO_HEADER_SIZE);

		if ($iCurlErrno)
		{
			$aResponse['errorno']	= $iCurlErrno;
			$aResponse['error']		= curl_error($rCurlSession);
		}

		$aResponse['status_code']		= curl_getinfo($rCurlSession, CURLINFO_HTTP_CODE);
		$aResponse['duration']			= $iEnd - $iStart;
		$aResponse['content_type']		= curl_getinfo($rCurlSession, CURLINFO_CONTENT_TYPE);
		$aResponse['content_length']	= curl_getinfo($rCurlSession, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		if (isset($aMore['curlopts'][CURLOPT_FOLLOWLOCATION]) && $aMore['curlopts'][CURLOPT_FOLLOWLOCATION] == true)
			$aResponse['effective_url']	= curl_getinfo($rCurlSession, CURLINFO_EFFECTIVE_URL);
		else
			$aResponse['redirect_url'] = curl_getinfo($rCurlSession, CURLINFO_REDIRECT_URL);

		$aResponse['headers']	= $cParseHeaders(substr($sResponse, 0, $iHeaderSize));
		$aResponse['body']		= substr($sResponse, $iHeaderSize);

		if ($aResponse['content_length'] <= 0)
			$aResponse['content_length'] = strlen($aResponse['body']);

		curl_close($rCurlSession);

		return $aResponse;
	}

	/**
	 * Generate a request string for manual "multipart/form-data" sending.
	 *
	 * ## Example of entry array
	 *
	 * ```php
	 * $aMultipart = multipartBuildQuery([
	 * 	[
	 * 	    'name' => 'username',
	 * 	    'contents' => 'Anonymous'
	 * 	],
	 * 	[
	 * 	    'name' => 'avatar',
	 * 	    'contents' => file_get_contents($sAvatarPath),
	 * 	    'filename' => 'avatar.jpg',
	 * 	    'headers' => ['Content-Type' => 'image/jpeg'],
	 * 	    // 'base64' => true
	 * 	]
	 * ]);
	 * ```
	 *
	 * ## Returning array
	 *
	 * - 'boundary' represents the generated bound to be replaced in the "Content-Type" header of the request,
	 * - 'data' represents data to be posted (= to put in `CURLOPT_POSTFIELDS`).
	 *
	 * @see https://tools.ietf.org/html/rfc7578 to learn specification.
	 * @param array $aFields Array of associative arrays, each of which contains the following elements :
	 * 						 - 'name', mandatory, string : field name, as of defined in html form,
	 * 						 - 'contents', mandatory, string/resource : field value, file handle,
	 * 						 - 'headers', optional, array : additional HTTP headers,
	 * 						 - 'filename', optional, string : filename if it is,
	 * 						 - 'base64', optional, boolean : use to set "contents" in base 64.
	 * @param string $sBoundary Field separator, generated if empty.
	 * @return array Associative array which contains 'boundary' and 'data'.
	 * @throws InvalidArgumentException if `$aFields` is badly shaped.
	 */
	public static function multipartBuildQuery(array $aFields, string $sBoundary = ''): array
	{
		$sData = '';
		$sBoundary = '------------------------'.($sBoundary ?: uniqid());
		$sEol = "\r\n";

		foreach ($aFields as $aElt)
		{
			$aElt = array_merge(
				['name' => '', 'contents' => '', 'headers' => [], 'filename' => '', 'base64' => false],
				$aElt
			);

			if ($aElt['name'] == '')
				throw new \InvalidArgumentException(
					'Keys \'name\' is required to build a multipart/form-data request.'
				);

			if (!is_array($aElt['headers']))
				throw new \InvalidArgumentException(
					'Keys \'headers\' must be an array to build a multipart/form-data request.'
				);

			if (!isset($aElt['headers']['Content-Disposition']))
			{
				$aElt['headers']['Content-Disposition'] = 'form-data; name="'.$aElt['name'].'"';

				if ($aElt['filename'] != '')
					$aElt['headers']['Content-Disposition'] .= '; filename="'.basename($aElt['filename']).'"';
			}

			if (!isset($aElt['headers']['Content-length']))
			{
				$aElt['headers']['Content-Length'] = strlen((string) $aElt['contents']);
			}

			if ($aElt['filename'] != '' && !isset($aElt['headers']['Content-Type']))
			{
				$aElt['headers']['Content-Type'] = 'application/octet-stream';
			}

			if ($aElt['base64'])
			{
				$aElt['headers']['Content-Transfert-Encoding'] = 'base64';
				$aElt['contents'] = chunk_split(base64_encode($aElt['contents']));
			}

			$sData .= '--'.$sBoundary.$sEol;

			foreach ($aElt['headers'] as $sHeaderName => $sHeaderValue)
				$sData .= $sHeaderName.': '.$sHeaderValue.$sEol;

			$sData .= $sEol.$aElt['contents'].$sEol;
		}

		if ($sData)
			$sData .= '--'.$sBoundary.'--'.$sEol.$sEol; // Warning : 2 ends of line !

		return ['boundary' => $sBoundary, 'data' => $sData];
	}
}