<?php

App::import('Sanitize');

class AppController extends Controller {

	// NOTE: We are loading every component for every class. We should split this down further and only load each component as
	// needed. However, currently simples...
    var $components = array( 'Auth', 'Session', 'Permission', 'RequestHandler', 'Folder', 'Api', 'Getid3', 'emailTemplates', 'Object', 'Cookie' => array( 'name' => 'OpenUniversity' ) );

    var $helpers = array('Html', 'Javascript','Form', 'Session', 'Attachment', 'Time', 'Permission', 'Text', 'Object', 'Breadcrumb', 'Miscellaneous' );

    var $alert = false; // A flag used to determine if an alter has been set that overrides any defacto flash message.
    /*
     * @name : beforeFilter
     * @description : Called before the controller is executed. Currently using this to ensure only administrators 
	 * access the admin routing and for building the breadcrumbs
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function beforeFilter() {
    	
    	$this->alert = false;
    	
		if( $this->RequestHandler->isAjax() == false ) {
			
			// If the current user is attempting to view an admin method ensure the "administrator" flag on their
			// profile is set to TRUE.
			if( $this->Permission->isAdminRouting() && $this->Permission->isAdministrator() == false ) {
	
				$this->Session->setFlash('You do not have permission to access this page.', 'default', array(), 'error');
				$this->redirect( array( 'admin' => false, 'controller' => 'users', 'action' => 'dashboard' ) );
				
			} 
			
			if( $this->Permission->isItunesRouting() && $this->Permission->isItunesUser() == false ) {
				
				$this->Session->setFlash('You do not have permission to access this page.', 'default', array(), 'error');
				$this->redirect( array( 'admin' => false, 'controller' => 'users', 'action' => 'dashboard' ) );
			
			} elseif( $this->Permission->isYoutubeRouting() && $this->Permission->isYoutubeUser() == false ) {
	
				$this->Session->setFlash('You do not have permission to access this page.', 'default', array(), 'error');
				$this->redirect( array( 'admin' => false, 'controller' => 'users', 'action' => 'dashboard' ) );
			}
			
			

			// Set the page title in the browser
			$this->set('title_for_layout', ucwords( $this->params['controller'].' &rarr; '.$this->params['action'] ) );
		}
		
		$this->set('params', $this->params );
    }
    
    function beforeRender() {
    	
    	if( $this->RequestHandler->isAjax() == false ) {
    		
			$Breadcrumb = ClassRegistry::init('Breadcrumb');
			$this->set('breadcrumbs', $Breadcrumb->build( $this->params, $this->data  ) );			
    	}
    }

	/*
	 * @name : getExtension
	 * @description : Return the extension of parameter passed	
	 * @updated : 28th June 2011
	 * @by : Charles Jackson
	 */
    function getExtension( $filename = null ) {

        return substr( $filename, ( strpos( $filename, '.' ) + 1 ), strlen( $filename ) );
    }

    /*
     * @name : cookie
     * @description :
     * @updated : 4th August 2011
     * @by : Charles Jackson
     */
    function cookie() {
    	
    	$this->autoRender = false;
    	$active_columns = array();
    	
    	foreach( $this->data['Podcast'] as $key => $value ) {
    		
    		$active_columns[] = $key;
    	}
    	
    	$this->Cookie->write('Podcasts',$active_columns, false );
    	return true;
    }
    
    /*
     * @name : cookieStanding
     * @description :
     * @updated : 5th August 2011 
     * @by : Charles Jackson
     */
    function cookieStanding( $cookie_name = null ) {
    	
    	$active_columns = array();
    	
		if( $this->Cookie->read($cookie_name) ) {
        
        	$active_columns = $this->Cookie->read($cookie_name);
        
        } else {

	        $active_columns = array('title','owner','created','thumbnail', 'media', 'publish_itunes_u','publish_youtube','podcast_flag');
			$this->Cookie->write($cookie_name,$active_columns, false );
       }
       
       return $active_columns;
    }    

}
