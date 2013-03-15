<?php
class CustomAutoloader{

    public function __construct(){
        spl_autoload_register(array($this, 'loader'));
    }

    public function loader($className){
		
		$a = explode("\\", $className);

		// Are we working in the DataTables namespace
		if ( $a[0] !== "DataTables" ) {
			return;
		}

		if ( count( $a ) === 2 ) {
			// If just a single top level namespace is given, then we just need to
			// include the class from its own Directory
			require( dirname(__FILE__).'/'.$a[1].'/'.$a[1].'.php' );
		}
		else if ( count( $a ) === 3 ) {
			// If a sub-namespace is used, then we can use A-Z to separate classes in
			// that namespace
			preg_match_all( "/[A-Z]+[^A-Z]*/", $a[2], $matches );
			$location = implode( '/', $matches[0] );

			require( dirname(__FILE__).'/'.$a[1].'/'.$location.'.php' );
		}
    }
}
?>