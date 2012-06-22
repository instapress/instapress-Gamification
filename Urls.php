<?php

/**
 * Description of Urls
 *
 * @author ashok
 */
class Gamification_Urls {

    // holds single instance
    private static $hinst = null;
    private static $_urlFileUri = "error_404";
    private static $_splittedDomainURI = "";

    /**
     *
     * @_permission <int> holds permission set required for page
     */
    private static $_pagePermission = 0;
    public static $self = '';
    public static $pageUrl = '';
    public static $pageSlug = '';
    public static $pageVars = array();

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!self::$hinst) {
            self::$hinst = new self();
        }
        return self::$hinst;
    }

    private function makePageVars($varPart) {
        self::$pageVars['GETPOST'] = array();
        self::$pageVars['GET'] = array();
        self::$pageVars['POST'] = array();
        self::$pageVars['QUERY'] = array();
        self::$pageVars['COOKIE'] = $_COOKIE;
        self::$pageVars['FILES'] = $_FILES;
        //self::$pageVars[ 'SERVER' ] = $_SERVER;
        if (trim($varPart) != '') {
            $allVars = explode('/', $varPart);

            if (( count($allVars) % 2 ) !== 0) {
                self::$pageVars['GETPOST']['ORPHAN'] = trim(array_pop($allVars));
            }

            for ($varCount = 0; $varCount < count($allVars); $varCount += 2) {
                self::$pageVars['GET'][trim($allVars[$varCount])] = stripslashes(stripslashes(urldecode(trim($allVars[$varCount + 1]))));
                self::$pageVars['QUERY'][trim($allVars[$varCount])] = stripslashes(stripslashes(urldecode(trim($allVars[$varCount + 1]))));
                self::$pageVars['GETPOST'][trim($allVars[$varCount])] = stripslashes(stripslashes(urldecode(trim($allVars[$varCount + 1]))));
            }
        }

        foreach ($_GET as $key => $value) {
            self::$pageVars['GETPOST'][$key] = ( is_array($value) ) ? $value : stripslashes(stripslashes(trim($value)));
            self::$pageVars['GET'][$key] = ( is_array($value) ) ? $value : stripslashes(stripslashes(trim($value)));
        }

        foreach ($_POST as $key => $value) {
            self::$pageVars['GETPOST'][$key] = ( is_array($value) ) ? $value : stripslashes(stripslashes(trim($value)));
            self::$pageVars['POST'][$key] = ( is_array($value) ) ? $value : stripslashes(stripslashes(trim($value)));
        }
    }

    public function processUrl() {
        //self::$_urlFileUri = ADMIN_PUBLIC . "error_404.php";
        $_domainHostName = $_SERVER['HTTP_HOST'];
        $_domainURIwQ = $_SERVER['REQUEST_URI'];

        $_urlSlug = "";

        self::$self = explode('?', $_domainURIwQ);
        self::$self = self::$self[0];
        self::$pageUrl = explode('nv/', self::$self);
        self::$pageUrl = self::$pageUrl[0];

        $_domainURIwQ = self::$self;
        $_arrDomainURIwQ = explode('/nv/', $_domainURIwQ);
        $_urlSlug = trim($_arrDomainURIwQ[0], '/');
        $_arrDomainURIwQ[1] = count($_arrDomainURIwQ) === 2 ? explode('?', $_arrDomainURIwQ[1]) : array('');
        $_arrDomainURIwQ[1] = $_arrDomainURIwQ[1][0];
        self::makePageVars(trim($_arrDomainURIwQ[1], '/'));
        $_tempurlFileUri = self::getPageInfo($_urlSlug);
        if ($_tempurlFileUri)
            self::$_urlFileUri = $_tempurlFileUri;
    }

    public function getUrlFileUri() {
        return self::$_urlFileUri;
    }

    private function getPageInfo($_urlSlug) {

        $urlData = new Gamification_Db_Url();
        //echo 'urlslug'.$_urlSlug;
        $urlData->set("urlSlug||$_urlSlug");
        $urlCount = $urlData->getResultCount();

        if ($urlCount > 0) {
//				try{
//
//					self::$_Permission = $urlData->get("urlPermission");
//				}
//				catch(Exception $ex){}
            $urlData = new Gamification_Db_Url();

            $urlData->set("urlSlug||$_urlSlug");
            $urld = $urlData->get("phpFile", 0);


            if ('' == $urlData->get("phpFilePath", 0))
                return $urlData->get("phpFile", 0);
            else
                return $urlData->get("phpFilePath", 0) . "/" . $urlData->get("phpFile", 0);
        }
        else {

            return false;
        }
    }

}

?>
