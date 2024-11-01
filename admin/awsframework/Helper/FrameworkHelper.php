<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

/*
 * Framework for Wordpress
 */
namespace WSF\Helper;

/*AWS_PHP_HEADER*/

use WSF\Controller;

class FrameworkHelper {
        var $plugin_label;
        var $plugin;
        var $pluginDirPath;
        
        var $view;
        
        var $controllerName;
        var $actionName;
        
        var $controller;
        
        var $executions;
        
        var $targetPlatform;
        
        var $translatorFiles;
        
        var $version;
        
        public function __construct($plugin, $pluginDirPath, $targetPlatform, $translatorFiles = array()){
            
            $this->plugin               = $plugin;
            $this->pluginDirPath        = $pluginDirPath;
            $this->targetPlatform       = $targetPlatform;
            $this->translatorFiles      = $translatorFiles;
            
            if($this->getTargetPlatform() == "joomla"){
                //Faccio in modo che la libreria jquery.js sia caricata come prima
                \JHtml::_('jquery.framework', true, true);
            }
  
        }
        
        public function getPluginDirPath(){
            return $this->pluginDirPath;
        }
        
        public function setVersion($version){
            $this->version  = $version;
        }
        
        public function getVersion(){
            return $this->version;
        }
        
        public function execute($path = null, $admin = null, $namespace = null, $pcontroller = null, $paction = null){
            if(empty($pcontroller)){
                $controllerRequest     = $this->requestValue('controller');
            }else{
                $controllerRequest     = $pcontroller;
            }

            if(empty($paction)){
                /*
                 * Forzo da richiesta da GET, in modo tale che altri componenti, 
                 * possano usare il GET, con all'interno delle variabili nascoste di tipo
                 * action
                 */
                
                $actionRequest         = $this->requestValue('action', 'GET');
            }else{
                $actionRequest         = $paction;
            }

            if(empty($actionRequest)){
                $actionRequest = "index";
            }

            $this->actionName = $actionRequest;
            
            $actionRequest      .= 'Action';
            
            $controllerName = $this->getControllerName($controllerRequest);
            $controllerClass = "{$namespace}\\{$controllerName}";
            
            $controllerPath = $this->getPluginPath("{$path}/Controller/{$controllerName}.php", $admin);
            
            $this->controllerName = $controllerName;
                        
            require_once($controllerPath);
            
            $controller = new $controllerClass($this);
            $this->controller = array(
                'instance'  => $controller,
                'path'      => $path,
                'admin'     => $admin,
            );

            if(empty($this->executions)){
                $this->executions = array();
            }
            
            /* Evita i loop nell'esecuzione delle azioni */
            if($this->checkLoop($controllerName, $actionRequest, $this->executions) == true){
                return;
            }
            
            $this->executions[] = array(
                'controller'    => $controllerName,
                'action'        => $actionRequest,
            );
            
            /* DEBUG */
            if(count($this->executions) >= 1){
                //print_r($this->executions);
               // exit(-1);
            }

            $controller->{$actionRequest}();
        }
        
        public function getFirstExecution(){
            return $this->executions[0];
        }
        
        private function checkLoop($controller, $action, $executions){
            foreach($executions as $execution){
                if($execution['controller'] == $controller &&
                   $execution['action']     == $action){
                    return true;
                }
            }
            
            return false;
        }
        
        public function requestValue($name = null, $type = "REQUEST", $default = null){
            if($type == null){
                $type   = "REQUEST";
            }
            
            if($this->getTargetPlatform() == "wordpress"):
                if(empty($name)){
                    if($type == "REQUEST"){
                        return $_REQUEST;
                    }else if($type == "GET"){
                        return $_GET;
                    }else if($type == "POST"){
                        return $_POST;
                    }
                }

                if($type == "REQUEST"){
                    if(isset($_REQUEST[$name])){
                        return is_string($_REQUEST[$name])?stripslashes($_REQUEST[$name]):$_REQUEST[$name];
                    }
                }else if($type == "GET"){
                    if(isset($_GET[$name])){
                        return is_string($_GET[$name])?stripslashes($_GET[$name]):$_GET[$name];
                    }
                }else if($type == "POST"){
                    if(isset($_POST[$name])){
                        return is_string($_POST[$name])?stripslashes($_POST[$name]):$_POST[$name];
                    }
                }

                return $default;
            elseif($this->getTargetPlatform() == "joomla"):
                
                $jinput             = \JFactory::getApplication()->input;
            
                $values['GET']       = $jinput->get->get($name, $default, 'STR');
                $values['POST']      = $jinput->post->get($name, $default, 'STR');
                $values['REQUEST']   = $jinput->request->get($name, $default, 'STR');
                
                if(empty($name)){
                    if($type == "REQUEST"){
                        return \JFactory::getApplication()->input->request->getArray();
                    }else if($type == "GET"){
                        return \JFactory::getApplication()->input->get->getArray();
                    }else if($type == "POST"){
                        return \JFactory::getApplication()->input->post->getArray();
                    }
                }
                
                return $values[$type];
                
            endif;
        }
        
        function getPluginPath($relpath, $admin = false){
            if($this->getTargetPlatform() == "joomla"){
                if($admin == true){
                    return JPATH_ROOT . "/administrator/components/com_hikapricecalculator/{$relpath}";
                }else{
                    return JPATH_ROOT . "/components/com_hikapricecalculator/{$relpath}";
                }
                
                
            }else if($this->getTargetPlatform() == "wordpress"){
                return "{$this->pluginDirPath}{$this->plugin}/admin/{$relpath}";                
            }
            
        }
        
        function getUploadUrl($relPath){
            if($this->getTargetPlatform() == "wordpress"){
                $uploadDirArray     = wp_upload_dir();

                return "{$uploadDirArray['baseurl']}/{$this->plugin}/{$relPath}";
            }else if($this->getTargetPlatform() == "joomla"){
                return "{$this->getSiteUrl()}/media/com_hikapricecalculator/{$relPath}";
            }
        }
        
        function getUploadPath($relPath){
            if($this->getTargetPlatform() == "wordpress"){
                $uploadDirArray     = wp_upload_dir();
                return "{$uploadDirArray['basedir']}/{$this->plugin}/{$relPath}";
            }else if($this->getTargetPlatform() == "joomla"){
                return JPATH_ROOT . "/media/com_hikapricecalculator/{$relPath}";
            }
            
        }
        
        function getSiteUrl(){
            if($this->targetPlatform == "wordpress"){
                $siteUrl    = site_url();
            }else if($this->targetPlatform == "joomla"){
                $siteUrl    = \JURI::root();
            }
            
            /* Tolgo se presente l'ultimo carattere / */
            return rtrim($siteUrl, '/');
        }
        
        function getPluginUrl($relpath = ''){
            return $this->getSiteUrl() . '/wp-content/plugins/' . $this->plugin . '/' . $relpath;
        }
        
        /*
         * Ritorna l'indirizzo URL alla cartella resources
         */
        function getResourcesUrl($readpath = ''){
            $siteUrl    = $this->getSiteUrl();

            if($this->targetPlatform == "wordpress"){
                return "{$siteUrl}/wp-content/plugins/{$this->plugin}/admin/resources/{$readpath}";
            }else if($this->targetPlatform == "joomla"){
                return "{$siteUrl}/administrator/components/com_hikapricecalculator/resources/{$readpath}";
            }
        }
        
        
        function getControllerName($controllerName = null){
            if(empty($controllerName)){
                $retControllerName = 'Index';
            }else{
                $retControllerName = $controllerName;
                $retControllerName = ucfirst($retControllerName);
            }

            $retControllerName .= 'Controller';
            
            return $retControllerName;
        }
        
        function renderView($view, $params = array(), $absolutePath = false){
            
            foreach($params as $param_name => $param_value){
                $this->view[$param_name] = $param_value;
            }

            if($absolutePath == false){
                if(!isset($this->controller['admin'])){
                    $admin      = false;
                }else{
                    $admin      = $this->controller['admin'];
                }
                
                require($this->getPluginPath("{$this->controller['path']}/View/{$view}", $admin));
            }else{
                require($view);
            }
        }
        
        function getView($module, $view, $admin, $params = array()){
            $this->controller['path']   = $module;
            
            foreach($params as $param_name => $param_value){
                $this->view[$param_name] = $param_value;
            }

            ob_start();
            require($this->getPluginPath("{$module}/View/{$view}", $admin));
            $view   = ob_get_contents();
            ob_end_clean();
            
            return $view;
        }
        
        function requireFile($path, $params = array()){
            foreach($params as $param_name => $param_value){
                $this->view[$param_name] = $param_value;
            }
            
            ob_start();
            require($path);
            $view   = ob_get_contents();
            ob_end_clean();
            
            return $view;
        }
        
        function get($namespace, $admin, $path, $class, $params = array()){
            $className = $namespace . '\\' . $class;
            
            if(!class_exists($className)){
                require_once($this->getPluginPath($path . '/' . $class . '.php', $admin));
            }
            
            
            $reflection = new \ReflectionClass($className); 
            return $reflection->newInstanceArgs($params); 
        }
        
        function getPluginLabel(){
            return $this->plugin_label;
        }
                
        function getPluginDir(){
            return $this->plugin;
        }
        
        function adminUrl($params = null){
            if($this->targetPlatform == "wordpress"){
                $url = "admin.php?page={$this->plugin}";

                foreach($params as $key => $value){
                    if(!empty($value)){
                        $url .= '&' . $key . '=' . $value;
                    }
                }
                return admin_url($url);
            }else if($this->targetPlatform == "joomla"){
                $url = "administrator/index.php?option=com_hikapricecalculator";
                
                foreach($params as $key => $value){
                    if(!empty($value)){
                        $url .= '&' . $key . '=' . $value;
                    }
                }
                return "{$this->getSiteUrl()}/{$url}";
            }
        }
        
        function getCurrentControllerName(){
            return $this->controllerName;
        }
        
        function getCurrentActionName(){
            return $this->actionName;
        }
        
        
        function getUserCurrentLocaleFilePath(){
            $defaultLocale      = "en_US";
            $locale             = $this->getLocale();
            $langFilePath       = $this->getUploadPath("translations/{$locale}.php");

            if(empty($locale) || file_exists($langFilePath) == false){
                $locale         = $defaultLocale;
                
                $langFilePath   = $this->getUploadPath("translations/{$locale}.php");
                
                if(file_exists($langFilePath) == false){
                    return false;
                }
            }
            
            return $langFilePath;
        }
        
        
        /*
         * Effettua la traduzione utilizzando i file lingua dell'utente
         */
        function userTrans($string, $tokens = array()){

            $langFilePath       = $this->getUserCurrentLocaleFilePath();
            $translations       = array();
            
            if($langFilePath !== false){
                $translations   = include $langFilePath;
            }

            if(isset($this->translatorFiles['user'])){
                foreach($this->translatorFiles['user'] as $translatorFile){
                    if(!empty($translatorFile)){
                        $translations   = array_merge($translations, include $translatorFile);
                    }
                }
            }

            if(!isset($translations[$string])){
                return $string;
            }
            
            $translation    = $translations[$string];
            
            foreach($tokens as $key => $value){
                $translation     = str_replace("%{$key}%", $value, $translation);
            }
            
            if(empty($translation)){
                return $string;
            }
            
            return $translation;
        }
        
        /*
         * Ritorna il locale in formato xx_XX
         */
        function getLocale(){
            if($this->targetPlatform == "wordpress"){
                return get_locale();
            }else if($this->targetPlatform == "joomla"){
                $locale = \JFactory::getLanguage()->getLocale();
                return $locale[2];
            }
        }
        
        function getControllerPath(){
            if(empty($this->controller['path'])){
                return ($this->plugin == "woo-price-calculator")?"awspricecalculator":$this->plugin; /* Default */
            }
            
            return $this->controller['path'];
        }

        function getSystemCurrentLocaleFilePath(){
            $defaultLocale      = "en_US";   
            $locale             = $this->getLocale();
            
            $langFilePath       = $this->getPluginPath("{$this->getControllerPath()}/Language/{$locale}.php", true);
            
            if(empty($locale) || file_exists($langFilePath) == false){
                $locale         = $defaultLocale;
                $langFilePath   = $this->getPluginPath("{$this->getControllerPath()}/Language/{$locale}.php", true);
            }
            
            return $langFilePath;
            
        }

        function trans($string, $tokens = array()){
                    
            $langFilePath   = $this->getSystemCurrentLocaleFilePath();
            $translations   = include $langFilePath;

            if(isset($this->translatorFiles['system'])){
                foreach($this->translatorFiles['system'] as $translatorFile){
                    if(!empty($translatorFile)){
                        $translations   = array_merge($translations, include $translatorFile);
                    }
                }
            }
            
            if(isset($translations[$string])){
                $translation    = $translations[$string];

                foreach($tokens as $key => $value){
                    $translation     = str_replace("%{$key}%", $value, $translation);
                }
            }else{
                return $string;
            }
            
            if(empty($translation)){
                return $string;
            }
            
            return $translation;
        }
        
        /*
         * If no user translations are defined, the system translation will be used
         */
        function mixTrans($string, $tokens = array()){

            $trans  = $this->userTrans($string, $tokens);

            /* No user translations defined, getting system translation */
            if(empty($trans) || $string == $trans){
                $trans  = $this->trans($string, $tokens);
            }
            
            return $trans;
        }
        
        public function requestForm($formClass, $setValues = array()){
            $ret = array();
            
            $fields = $formClass->getForm();
            
            foreach($fields as $field){
                $default = $this->isset_or($field['default']);
                
                $ret[$field['name']] = $this->requestValue($field['name'], null, $default);
            }
            
            $ret = array_merge($ret, $setValues);
            
            return $ret;
        }
        
        public function setFormField($formClass, $field_name, $field_value){
            $fields = $formClass->getForm();
            
            $fields[$field_name] = $field_value;
            
            $formClass->setForm($fields);
        }
                
        public function isPost(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                return true;
            }
            
            return false;
        }
        
        function isset_or(&$check, $alternate = NULL){
            return (isset($check)) ? $check : $alternate;
        } 
        
        /*
         * Effettua la decodifica di codice JSON inserito nel database
         */
        public function decode($string){
            $ret = $string;
            
            $ret = str_replace("\\\"", '"', $ret);
            $ret = str_replace("\\'", "'", $ret);
            
            return $ret;
        }
        
        public function getLicense(){
            $license   = file_get_contents($this->getPluginPath("resources/data/license.bin", true));
            
            return $license;
        }
        
        public function getImageUrl($imagePath){
            return $this->getResourcesUrl("assets/images/{$imagePath}");
        }
      
        
    /*
     * Ritorna la piattaforma in uso
     */
    public function getTargetPlatform(){
        return $this->targetPlatform;
    }
    
    /*
     * Inserisce gli script JS
     */
    public function enqueueScript($name, $url, $deps = array(), $version = false){
        if($this->getTargetPlatform() == "wordpress"){
            wp_enqueue_script($name, $this->getResourcesUrl($url), $deps, $version); 
        }else if($this->getTargetPlatform() == "joomla"){
            
            /* Librerie già caricate */
            if(!in_array($url, array(
                'lib/wsf-bootstrap-3.3.7/js/bootstrap.js',
            ))){
                if($version !== false){
                    $url    = "$url?ver={$version}";
                }

                $document = \JFactory::getDocument();
                $document->addScript($this->getResourcesUrl($url));
            }
        }
    }
     
    /*
     * Inserisce i fogli di stile CSS
     */
    /*
     * $version if null = No version
     */
    public function enqueueStyle($name, $url, $absolute = false, $version = false, $deps = array()){
        
        if($absolute == false){
            $url    = $this->getResourcesUrl($url);
        }
        
        if($this->getTargetPlatform() == "wordpress"){
            wp_enqueue_style($name, $url, $deps, $version); 
        }else if($this->getTargetPlatform() == "joomla"){
            $document = \JFactory::getDocument();
            $document->addStyleSheet($url);
        }
    }
    
    /*
     * Crea delle variabili Javascript
     */
    public function localizeScript($handle, $name, $data){
        if($this->getTargetPlatform() == "wordpress"){
            wp_localize_script($handle, $name, $data);
        }else if($this->getTargetPlatform() == "joomla"){
            $document   = \JFactory::getDocument();
            $jsonData   = json_encode($data);
            
            $document->addScriptDeclaration("
                /* <![CDATA[ */
                var {$name} = {$jsonData};
                /* ]]> */
            ");
        }
    }
    
    /*
     * Ritorna l'URL base per l'AJAX
     */
    public function getAjaxBaseUrl(){
        $siteUrl        = $this->getSiteUrl();
        
        if($this->getTargetPlatform() == "wordpress"){
            return "{$siteUrl}/wp-admin/admin-ajax.php";
        }else if($this->getTargetPlatform() == "joomla"){
            return "{$siteUrl}/index.php?option=com_ajax&format=raw";
        }
    }
    
    /*
     * Ritorna l'URL per effettuare l'AJAX
     */
    public function getAjaxUrl($params = array()){
        $stringParams   = http_build_query($params);
        $baseAjaxUrl    = $this->getAjaxBaseUrl();
        
        if(count($params) == 0){
            return $baseAjaxUrl;
        }
        
        if($this->getTargetPlatform() == "wordpress"){
            
            //url: WPC_HANDLE_SCRIPT.siteurl + "/wp-admin/admin-ajax.php?action=ajax_callback&id=" + productId + "&simulatorid=" + simulatorId,
            return "{$baseAjaxUrl}?{$stringParams}";
        }else if($this->getTargetPlatform() == "joomla"){
            return "{$baseAjaxUrl}&{$stringParams}";
        }
    }
    
    /*
     * Ritorna l'intera richiesta POST
     */
    public function getPost(){
        if($this->getTargetPlatform() == "wordpress"){
            return $_POST;
        }else if($this->getTargetPlatform() == "joomla"){
            return \JFactory::getApplication()->input->post->getArray();
        }
    }

    /*
     * Crea una cartella ed eventualmente crea tutte le cartelle ricorsivamente
     */
    public function createFolder($path){
        if($this->getTargetPlatform() == "wordpress"){
            wp_mkdir_p($path);
        }else if($this->getTargetPlatform() == "joomla"){
            \JFolder::create($path);
        }
    }
    
    
    
    public function getCmsActiveTemplateName(){
        if($this->getTargetPlatform() == "wordpress"){
            throw new Exception("getCmsActiveTemplateName not implemented");
        }else if($this->getTargetPlatform() == "joomla"){
            /*
             * Non ho trovato altro modo, tranne che usare una query per prendere
             * il nome del template attivo
             */
            $databaseHelper   = $this->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this));
            $res              = $databaseHelper->getRow("SELECT template FROM [prefix]template_styles WHERE client_id = 0 AND home = 1");

            return $res->template;
        }

    }
    
    public function getCmsActiveTemplatePath($relativePath = ''){
        if($this->getTargetPlatform() == "wordpress"){
            throw new \Exception("getCmsActiveTemplatePath not implemented");
        }else if($this->getTargetPlatform() == "joomla"){
            $root           = JPATH_ROOT;
            $templateName   = $this->getCmsActiveTemplateName();
            
            return "{$root}/templates/{$templateName}/{$relativePath}";
        }

    }
    
    public function redirect($url){
        if($this->getTargetPlatform() == "wordpress"){
            wp_redirect($url);
        }else if($this->getTargetPlatform() == "joomla"){
            header("Location: {$url}");
        }
        
        die();
    }


    /*
     * Check if all the required extensions are installed, if not return the name of the extensions
     */
    public function checkRequiredExtensions($required_extensions){
        $extension_message=null;

        foreach ($required_extensions as $extension => $value){
            if (!in_array($extension , get_loaded_extensions())){
                $extension_message .= "'".$value."' "." ";

            }

        }

        if (empty($extension_message)){
            return null;
        }else return $extension_message;

    }

    /*
     * Check if the required directories are created, if not return the name of the folder to be created
     */
    public function checkRequiredDirectories(){
        $requiredDirectories    = array("docs" , "style" , "themes" , "translations");
        $missingDirectories     = array();

        foreach($requiredDirectories as $requiredDirectory){
            $path   = $this->getUploadPath($requiredDirectory);
            if(!file_exists($path)){
                array_push($missingDirectories , $path);
            }
        }

        return $missingDirectories;

    }

    public function e($text, $htmlEntities = false, $return = false){
        if($htmlEntities == true){
            $text   = htmlentities($text, ENT_COMPAT | ENT_HTML401, "UTF-8");
        }else{
            $text   = utf8_encode($text);
        }
        
        if($return == true){
            return $text;
        }
        
        echo $text;
    }
    
    /*
     * Get an image Thumbnail from the src url
     */
    public function getImageThumbnail($imageSrc, $size = 'thumbnail', $icon = false, $attr = ''){
        if($this->getTargetPlatform() == "wordpress"){
            $attachmentId		= attachment_url_to_postid($imageSrc);

            return wp_get_attachment_image($attachmentId, $size, $icon, $attr);
            
        }else{
            throw "getImageThumbnail not implemented for Joomla!";
        }
    }
    
    function getHomePath(){
        $home    = set_url_scheme( get_option( 'home' ), 'http' );
        $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
        if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
                $wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
                $pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
                $home_path           = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
                $home_path           = trailingslashit( $home_path );
        } else {
                $home_path = ABSPATH;
        }

        return str_replace( '\\', '/', $home_path );
    }
}