<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assets extends MY_Controller {

	// Caching can be strictly controlled here, or varied dynamically
	public function readBinaryFile($file) {
		if (file_exists($file)) {
			header("Content-Type: {$this->ctype}");

			// time (in seconds) to cache
			$expires = 3600*10; // 24 hours
			header("Pragma: public");
			header("Cache-Control: maxage=".$expires);
			header('Expires: '.gmdate('D, d M Y H:i:s',time()+$expires).' GMT');
			
			// Tells browsers hich support this header not to reload unless file
			// has been changed since last load date.
			if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&  strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($file)) {
				header('HTTP/1.0 304 Not Modified');
			}
			
			readfile($file);
			exit;
		} else {
			var_dump($file);
		} // else silently fail
	}

	public function load()
	{
		// get path segments as interpreted by CI routing
		$segments = $this->uri->segment_array();
		
		// Get rid of "css", "img", "js", etc. from path as we don't need it for
		// anything. Our redirection is based on the file extension. However, we
		// wish to keep this value for determining whether we requested a js 
		// file or anything else
		$file_category = $segments[1];
		unset( $segments[1] );

		// If we are redirecting to javascript files, we wish to properly handle
		// the slug (vendor files are in another folder).
		$vendor = false;
		
		if($file_category=="js")
			$vendor = ( $segments[2] == "vendor" );
		if($vendor) unset($segments[2]);
			
		// get normal string path to file from URI segments
		$path = implode("/",$segments);
			
		// get path info
		$path_parts = pathinfo($path);
		$file_name  = $path_parts['basename'];
		$this->file_ext = $file_ext = $path_parts['extension'];
			
		// set the mime type based on extension
		$content_types = array("ai" => "application/postscript","aif" => "audio/x-aiff","aifc" => "audio/x-aiff","aiff" => "audio/x-aiff","asc" => "text/plain","atom" => "application/atom+xml","au" => "audio/basic","avi" => "video/x-msvideo","bcpio" => "application/x-bcpio","bin" => "application/octet-stream","bmp" => "image/bmp","cdf" => "application/x-netcdf","cgm" => "image/cgm","class" => "application/octet-stream","cpio" => "application/x-cpio","cpt" => "application/mac-compactpro","csh" => "application/x-csh","css" => "text/css","dcr" => "application/x-director","dif" => "video/x-dv","dir" => "application/x-director","djv" => "image/vnd.djvu","djvu" => "image/vnd.djvu","dll" => "application/octet-stream","dmg" => "application/octet-stream","dms" => "application/octet-stream","doc" => "application/msword","dtd" => "application/xml-dtd","dv" => "video/x-dv","dvi" => "application/x-dvi","dxr" => "application/x-director","eps" => "application/postscript","etx" => "text/x-setext","exe" => "application/octet-stream","ez" => "application/andrew-inset","gif" => "image/gif","gram" => "application/srgs","grxml" => "application/srgs+xml","gtar" => "application/x-gtar","hdf" => "application/x-hdf","hqx" => "application/mac-binhex40","htm" => "text/html","html" => "text/html","ice" => "x-conference/x-cooltalk","ico" => "image/x-icon","ics" => "text/calendar","ief" => "image/ief","ifb" => "text/calendar","iges" => "model/iges","igs" => "model/iges","jnlp" => "application/x-java-jnlp-file","jp2" => "image/jp2","jpe" => "image/jpeg","jpeg" => "image/jpeg","jpg" => "image/jpeg","js" => "application/x-javascript","kar" => "audio/midi","latex" => "application/x-latex","lha" => "application/octet-stream","lzh" => "application/octet-stream","m3u" => "audio/x-mpegurl","m4a" => "audio/mp4a-latm","m4b" => "audio/mp4a-latm","m4p" => "audio/mp4a-latm","m4u" => "video/vnd.mpegurl","m4v" => "video/x-m4v","mac" => "image/x-macpaint","man" => "application/x-troff-man","mathml" => "application/mathml+xml","me" => "application/x-troff-me","mesh" => "model/mesh","mid" => "audio/midi","midi" => "audio/midi","mif" => "application/vnd.mif","mov" => "video/quicktime","movie" => "video/x-sgi-movie","mp2" => "audio/mpeg","mp3" => "audio/mpeg","mp4" => "video/mp4","mpe" => "video/mpeg","mpeg" => "video/mpeg","mpg" => "video/mpeg","mpga" => "audio/mpeg","ms" => "application/x-troff-ms","msh" => "model/mesh","mxu" => "video/vnd.mpegurl","nc" => "application/x-netcdf","oda" => "application/oda","ogg" => "application/ogg","pbm" => "image/x-portable-bitmap","pct" => "image/pict","pdb" => "chemical/x-pdb","pdf" => "application/pdf","pgm" => "image/x-portable-graymap","pgn" => "application/x-chess-pgn","pic" => "image/pict","pict" => "image/pict","png" => "image/png","pnm" => "image/x-portable-anymap","pnt" => "image/x-macpaint","pntg" => "image/x-macpaint","ppm" => "image/x-portable-pixmap","ppt" => "application/vnd.ms-powerpoint","ps" => "application/postscript","qt" => "video/quicktime","qti" => "image/x-quicktime","qtif" => "image/x-quicktime","ra" => "audio/x-pn-realaudio","ram" => "audio/x-pn-realaudio","ras" => "image/x-cmu-raster","rdf" => "application/rdf+xml","rgb" => "image/x-rgb","rm" => "application/vnd.rn-realmedia","roff" => "application/x-troff","rtf" => "text/rtf","rtx" => "text/richtext","sgm" => "text/sgml","sgml" => "text/sgml","sh" => "application/x-sh","shar" => "application/x-shar","silo" => "model/mesh","sit" => "application/x-stuffit","skd" => "application/x-koan","skm" => "application/x-koan","skp" => "application/x-koan","skt" => "application/x-koan","smi" => "application/smil","smil" => "application/smil","snd" => "audio/basic","so" => "application/octet-stream","spl" => "application/x-futuresplash","src" => "application/x-wais-source","sv4cpio" => "application/x-sv4cpio","sv4crc" => "application/x-sv4crc","svg" => "image/svg+xml","swf" => "application/x-shockwave-flash","t" => "application/x-troff","tar" => "application/x-tar","tcl" => "application/x-tcl","tex" => "application/x-tex","texi" => "application/x-texinfo","texinfo" => "application/x-texinfo","tif" => "image/tiff","tiff" => "image/tiff","tr" => "application/x-troff","tsv" => "text/tab-separated-values","txt" => "text/plain","ustar" => "application/x-ustar","vcd" => "application/x-cdlink","vrml" => "model/vrml","vxml" => "application/voicexml+xml","wav" => "audio/x-wav","wbmp" => "image/vnd.wap.wbmp","wbmxl" => "application/vnd.wap.wbxml","wml" => "text/vnd.wap.wml","wmlc" => "application/vnd.wap.wmlc","wmls" => "text/vnd.wap.wmlscript","wmlsc" => "application/vnd.wap.wmlscriptc","wrl" => "model/vrml","xbm" => "image/x-xbitmap","xht" => "application/xhtml+xml","xhtml" => "application/xhtml+xml","xls" => "application/vnd.ms-excel","xml" => "application/xml","xpm" => "image/x-xpixmap","xsl" => "application/xml","xslt" => "application/xslt+xml","xul" => "application/vnd.mozilla.xul+xml","xwd" => "image/x-xwindowdump","xyz" => "chemical/x-xyz","zip" => "application/zip");
		
		// set the CI header for js or css views
		$this->ctype = $content_types[$file_ext];
		$this->output->set_header("Content-Type: {$this->ctype}");

		if( $file_ext == "css" ) {
			if( $file_category == "js" ) {
				// Since we're looking for a css file in the js folder, don't do any redirecting of paths
				// Ideally all css would be in the css folder, etc, but with some large libraries (tinymce... mumble mumble) it's just too much effort to separate it all
				header("Content-Type: ".$this->ctype);
				$this->load->view("js/vendor/$path",$this->data);	
			} else {
				// If the file is css, we insert the slug to the path
				$this->load->view("css/{$this->data['slug']}/$path",$this->data);	
				//$this->output->cache(60); // cache css for 1 hour
			}
		} elseif( $file_ext == "js" ) {
			// If the file is js, we insert the slug to the path ONLY if the
			// string "vendor" doesn't exist in the the second segment.
			if($vendor) {
				header("Content-Type: ".$this->ctype);
				echo file_get_contents(FCPATH.APPPATH."views/js/vendor/$path");
			} else {
				$this->load->view("js/{$this->data['slug']}/$path",$this->data);
			}
			//$this->output->cache(60*24); // cache js for 24 hours

		} elseif( 	$file_ext == "png" || 
					$file_ext == "jpg" || 
					$file_ext == "jpeg" || 
					$file_ext == "gif" 
			) {
			if( $file_category == "js" ) {
				// Since we're looking for an image file in the js folder, don't do any redirecting of paths
				// Ideally all images would be in the img folder, etc, but with some large libraries (tinymce... mumble mumble) it's just too much effort to separate it all
				$this->readBinaryFile(FCPATH.APPPATH."views/js/vendor/$path");	
			} else {
				// This is a binary image so read the file directly from the img 
				// folder after sending the header - don't load it as a view
				$this->readBinaryFile(FCPATH.APPPATH."views/img/{$this->data['slug']}/{$path}");
			}
		} elseif( $file_ext == "htm" || $file_ext == "html" ) {
			if( $file_category == "js" ) {
				// Since we're looking for html in the js folder, don't do any redirecting of paths
				// Ideally this would never happen but with some large libraries (tinymce... mumble mumble) it's just too much effort to separate it all
				header("Content-Type: ".$this->ctype);
				$this->load->view("js/vendor/$path",$this->data);
			}
		} else {
			// This isn't js, css or an image based on it's extension so try and
			// load it from a random folder based on it's extension?
			$this->readBinaryFile(FCPATH.APPPATH."views/{$file_ext}/{$this->data['slug']}/{$path}");
		}
	}
}