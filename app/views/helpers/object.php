<?php
class ObjectHelper extends AppHelper {

    var $helpers = array('Permission');

	var $processed_state = array(
	
		'-2' => array('message' => 'failed to copy file back', 'icon' => ERROR_IMAGE ),
		'-1' => array('message' => 'error in transcoding', 'icon' => ERROR_IMAGE ),
		'0' => array('message' => 'no media file at all', 'icon' => INCORRECT_IMAGE ),
		'1' => array('message' => 'awaiting transcoding choice', 'icon' => INCORRECT_IMAGE ),
		'2' => array('message' => '', 'icon' => AJAX_IMAGE ),
		'3' => array('message' => 'transcoded but awaiting approval', 'icon' => INCORRECT_IMAGE ),
		'9' => array('message' => '', 'icon' => CORRECT_IMAGE ),
	);
	
    /*
     * @name : isDeleted
     * @description : Accepts an array and returns a bool on whether soft deleted.
     * We return false if it has already been scheduled for deletion 
     * @updated : 1st June 2011
     * @by : Charles Jackson
     */
    function isDeleted( $object = array() ) {

        if( (int)$object['deleted'] )
            return true;

        return false;
    }

    /*
     * @name : isHardDeleted
     * @description : Accepts an array and returns a bool on whether hard deleted.
     * We return false if it has already been scheduled for deletion 
     * @updated : 1st June 2011
     * @by : Charles Jackson
     */
    function isHardDeleted( $object = array() ) {

        if( (int)$object['deleted'] == 2 )
            return true;

        return false;
    }


    /*
     * @name : scheduledForDeletion
     * @description :  
     * @updated : 18th July 2011
     * @by : Charles Jackson
     */
    function scheduledForDeletion( $object = array() ) {

        if( $object['deleted'] == 2 )
            return true;

        return false;
    }

    /*
     * @name : syndicated
     * @description : Retruns a bool depending up the value of the flag.
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function syndicated( $flag = null ) {

    	// Podcast level
       	return $flag == true ? true : false;
    }
	    
    /*
     * @name : considerForItunes
     * @description : Retruns a bool depending up the value of the flag.
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function considerForItunes( $object = array() ) {

    	// Podcast level
    	if( isSet( $object['consider_for_itunesu'] ) )
        	return $object['consider_for_itunesu'] == true ? true : false;
    	
		return false;
    }

    /*
     * @name : considerForYoutube
     * @description : Retruns a bool depending up the value of the flag set (Y or N)
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function considerForYoutube( $object = array() ) {

    	// Podcast level
    	if( isSet( $object['consider_for_youtube'] ) )
        	return $object['consider_for_youtube'] == true;
        	
        return false;
    }
    
    /*
     * @name : intendedForYoutube
     * @description : Retruns a bool depending up the value of the flag set (Y or N)
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function intendedForYoutube( $object = array() ) {

    	// Podcast level
    	if( isSet( $object['intended_youtube_flag'] ) && $object['intended_youtube_flag'] == strtoupper( YES ) )
        	return true;
        	
        return false;
    }
    
    /*
     * @name : isOpenLearn
     * @description : Returns a bool
     * @updated : 8th August 2011
     * @by : Charles Jackson
     */
    function isOpenLearn( $object = array() ) {

    	// Podcast level
    	if( isSet( $object['openlearn_epub'] ) )
        	return $object['openlearn_epub'] == strtoupper( YES );
        	
        return false;
    }
    
    /*
     * @name : intendedForItunes
     * @description : Retruns a bool depending up the value of the flag set (Y or N)
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function intendedForItunes( $object = array() ) {

    	// Podcast level
    	if( isSet( $object['intended_itunesu_flag'] ) )
        	return $object['intended_itunesu_flag'] == strtoupper( YES );
        	
        return false;
    }


	function intendedForPublication( $podcast = array() ) {
		
		if( $this->intendedForItunes( $podcast ) )
			return true;
			
		return $this->intendedForYoutube( $podcast );
	}
	
    /*
     * @name : itunesPublished
     * @description : Retruns a bool depending up the value of the flag set (Y or N)
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function itunesPublished( $object = array() ) {
    	
    	// Podcast level
    	if( isSet( $object['publish_itunes_u'] ) )
        	return $object['publish_itunes_u'] == strtoupper( YES );

        // PodcastItem level
    	if( isSet( $object['itunes_flag'] ) )
        	return $object['itunes_flag'] == strtoupper( YES );
        	
    }

    
	function hardDeleted( $podcast_item = array() ) {
		
		if( $podcast_item['deleted'] == 2 )
			return true;
			
		return false;
	}
	
    /*
     * @name : youtubePublished
     * @description : Retruns a bool depending up the value of the flag set (Y or N)
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function youtubePublished( $object = array() ) {


    	// Podcast level
    	if( isSet( $object['publish_youtube'] ) )
        	return $object['publish_youtube'] == strtoupper( YES );
    	
		// PodcastItem level
    	if( isSet( $object['youtube_flag'] ) )
        	return $object['youtube_flag'] == strtoupper( YES );
    }
    
    function isPublished( $object = array() ) {
    	
    	if( $this->youtubePublished( $object ) )
    		return true;
    		
    	if( $this->itunesPublished( $object ) )
    		return true;
    		
    	return false;
    		
    }


	function editing( $object = array() ) {
	
		if( !isSet( $object['id'] ) )
			return false;
			
		if( (int)$object['id'] )
			return true;	
	}
	
    /*
     * @name : changeOfOwnership
     * @description : Checks to see if a podcast is in a statement of 'unconfirmed change of ownership' and returns a bool
     * @updated : 28th June 2011
     * @by : Charles Jackson
     */
	function changeOfOwnership( $podcast = array() ) {
		
		return isSet( $podcast['current_owner_id'] );
	}
	
	/*
	 * @name : getProcessedState
	 * @description : Translate the processed state of any media into 1 of 3 images to give an indication of status.
	 * @updated : 7th July 2011
	 * @by : Charles Jackson
	 */	
	function getProcessedState( $processed_state ) {
		
		if( isSet( $this->processed_state[$processed_state] ) ) {
			$html = '<img src="/img/'.$this->processed_state[$processed_state]['icon'].'" class="icon" alt="'.$this->processed_state[$processed_state]['message'].'" />';
			$html .= '<span class="tagline">'.ucwords( $this->processed_state[$processed_state]['message'] ).'</span>';
			
		} else { 
		
			$html = '<img src="/img/'.ERROR_IMAGE.'" alt="State unknown - '.$processed_state.' Investigate" class="icon" />';
			$html .= '<span class="tagline">State unknown : '.$processed_state.' Investigate!</span>';
			
		}
		
		return $html;
		
	}
	
	function getApprovalStatus( $object, $media ) {


		if( strtolower( $media ) == 'itunes' ) {
			
			// Podcast level
			if( isSet( $object['intended_itunesu_flag'] ) && $object['intended_itunesu_flag'] == 'Y' )
				return CORRECT_IMAGE;
				
			// Podcast level
			if( isSet( $object['publish_itunes_u'] ) && $object['publish_itunes_u'] == 'Y' )
				return CORRECT_IMAGE;

			// Podcast level
			if( isSet( $object['consider_for_itunesu'] ) && $object['consider_for_itunesu'] == true )
				return QUESTION_MARK;
        				
			// Podcast Item Level
			if( isSet( $object['itunes_flag'] ) && $object['itunes_flag'] == 'Y' )
				return CORRECT_IMAGE;
			
			return INCORRECT_IMAGE;				
			
		} elseif( strtolower( $media ) == 'youtube' ) {	

			// Podcast level
			if( isSet( $object['intended_youtube_flag'] ) && $object['intended_youtube_flag'] == 'Y' )
				return CORRECT_IMAGE;

			// Podcast level
			if( isSet( $object['publish_youtube'] ) && $object['publish_youtube'] == 'Y' )
				return CORRECT_IMAGE;

			// Podcast Item level
			if( isSet( $object['youtube_flag'] ) && $object['youtube_flag'] == 'Y' )
				return CORRECT_IMAGE;

			// Podcast & PodcastItem level
			if( isSet( $object['consider_for_youtube'] ) && $object['consider_for_youtube'] == true )
				return QUESTION_MARK;

			return INCORRECT_IMAGE;				
		}
	}
	
	/*
	 * @name : isPodcast
	 * @description : Checks to ensure the "podcast_flag column" is set to true. Used to ensure
	 * a collection is syndicated before it can be submitted to itunes or youtube.
	 * @updated : 22nd July 29011
	 * @by : Charles Jackson
	 */
	function isPodcast( $flag = null ) {
		
		return ( $flag == true );
		
	}
	
	/*
	 * @name : getMediaFlavours
	 * @description : Checks to see if flavour of given media exists and returns the processState
	 * 								
	 * @updated : 19th June 2012
	 * @by : Ben Hawkridge
	 */
	function getMediaFlavours( $object = array()) {

		$mediaFlavours = array();
		
		foreach ($object as $row) {
			$mediaFlavours[$row['media_type']] = array('filename' => $row['filename'], 'processed_state' => $row['processed_state']);
		}

		return $mediaFlavours;
	}
	
	/*
	 * @name : hasYoutubeFlavour
	 * @description : Checks to see if a youtube flavour of media exists
	 * @updated : 11th August 2011
	 * @by : Charles Jackson
	 */
	function hasYoutubeFlavour( $object = array() ) {

		if( isSet( $object['YoutubeVideo']['filename'] ) && !empty( $object['YoutubeVideo']['filename'] ) )
			return true;
			
		return false;
	}
	
}
