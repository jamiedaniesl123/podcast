<?php
class Workflow extends AppModel {

  var $name = 'Workflow';
  var $useTable = 'workflows';
	var $error = null;
	
	var $data = array();
	var $id3_data = array();
	var $params = array();
	var $conditions = array(); // Holds the conditions used on the lookup table.
	
	var $file_format = null;
	var $file_extension = null;
	var $screencast  = false;
	var $video_width = null;
	var $video_height = null;
	var $aspect_ratio = null;
	var $aspect_ratio_float = 0;
	var $watermark_bumper_trailer = null;
	var $media_type = null;
	var $vle = false;
	var $transcode = true;
	
	public $workflow = null; // Holds the determined workflow.

  // BH 20111115 for info .m4a and .m4b are audio book formats and contain chapter track information that would be lost if transcoded
	var $not_for_transcoding = array('pdf','m4a','m4b');
	// BH 20111115 - .mkv and .ogg files disabled as not supported by Episode for input
	var $video_transcoding = array('mp4','m4v','mov','mpg','wmv','avi','flv','swf','3gp','3g2','dv');
	// BH 20120606 - .mp3 files moved from 'not_for_transcoding' to 'audio_transcoding' to ensure that iTunes U (public) version is created
	//							 Note: originally mp3 bypassed transcoding to avoid multiple encoding of audio.
	var $audio_transcoding = array('wav','amr','aif','aiff','mp3');

	/*
	 * @name : get
	 * @description : Determines the appropriate workflow based on the information provided and sets the class attribute.
	 * @updated : 21st June 2011
	 * @by : Charles Jackson
	 */
	function determine() {
		
		$this->error = null;
		$this->setFileFormat( $this->id3_data['fileformat'] );
		$this->setFileExtension( strtolower( $this->getExtension( $this->id3_data['filename'] ) ) );
		
		if( in_array( $this->file_extension, $this->not_for_transcoding ) ) {

			$this->setTranscode( false ); // Does not need to be transcoded
			$this->setWorkflow( 'not-for-transcoding' );
			
			if( strtolower( $this->file_extension ) == 'pdf' ) {

				$this->setMediaType( 'transcript' );					
				
			} else {

				$this->setMediaType( 'audio' );	
			}

		} elseif( in_array( $this->file_extension, $this->video_transcoding ) ) {

			$this->setScreencast( strtoupper( $this->params['url']['ff02v'] ) == 'YES' ? true : false );
			$this->setVideoWidth( isSet( $this->id3_data['video']['resolution_x'] ) ? $this->id3_data['video']['resolution_x'] : 0 );
			$this->setVideoHeight( isSet( $this->id3_data['video']['resolution_y'] ) ? $this->id3_data['video']['resolution_y'] : 0 );
			$this->setWatermarkBumperTrailer( isSet( $this->params['url']['ff03v'] ) ? $this->params['url']['ff03v'] : null );

			$this->setAspectRatio( $this->data['PodcastItem']['aspect_ratio'] );
			$this->setMediaType( 'video' );
			$this->setVle(false);

			$this->setConditions();
			
			$this->setWorkflow( $this->__select() );
			//$this->setWorkflow( 'video-wide-360-watermark-trailers' ); // NOTE : LINE TO BE REMOVED, FORCING A WORKFLOW
			
		} elseif( in_array( $this->file_extension, $this->audio_transcoding ) ) {

			// An audio file cannot have a watermark, if watermark set to true assume it is a null value.
			$this->setWatermarkBumperTrailer();
			$this->setMediaType( 'audio' );
			$this->video_height = null;
			$this->aspect_ratio = null;
			$this->aspect_ratio_float = null;
			$this->setConditions();
			
			$this->setWorkflow( $this->__select() );
			
		} else {

			// If we reached this point the user has uploaded an unsupported file type. Should never happen because validation
			// also exists in the file chucker upload.
			$this->error = 'We cannot recognise this media file. It cannot be transcoded.';
		}
		
		if( !empty( $this->error ) )
			return true;
		
		return false;
	}

	/*
	 * @name : setData
	 * @description : Called from the podcast_items controller, the data array contains Podcast and PodcastItem details.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */	
	function setData( $data = array() ) {
		
		$this->data = $data;
	}
	
	/*
	 * @name : setId3Data
	 * @description : Contain all the information retrieved from the getId3->analyse function called in the podcast_items controller.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */
	function setId3Data( $id3_data = array() ) {
		
		$this->id3_data = $id3_data;
	}
	
	/*
	 * @name : setParams
	 * @description : Contains all the data passed in the URL by the file chucker script.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */
	function setParams( $params = array() ) {
		
		$this->params = $params;
	}

	/* 
	 * @name : setFileFormat
	 * @description : Standard setter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */
	function setFileFormat( $file_format = null ) {
		
		$this->file_format = $file_format;
	}
	
	/* 
	 * @name : setTranscode
	 * @description : Standard setter
	 * @updated : 22nd August 2011
	 * @by : Charles Jackson
	 */	
	function setTranscode( $status = false ) {
		
		$this->transcode = $status;	
	}
	
	
	/* 
	 * @name : setFileExtension
	 * @description : Standard setter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */
	function setFileExtension( $file_extension = null ) {
		
		$this->file_extension = $file_extension;
	}

	/* 
	 * @name : setScreenCast
	 * @description : Standard setter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */		
	function setScreenCast( $screencast = false ) {
	
		$this->screencast = $screencast;
	}

	/* 
	 * @name : setVideoWidth
	 * @description : Standard setter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */		
	function setVideoWidth( $video_width = 0 ) {
		
		$this->video_width = $video_width;
	}

	/* 
	 * @name : setVideoHeight
	 * @description : Standard setter
	 * @updated : 13th April 2012
	 * @by : ben Hawkridge
	 */		
	function setVideoHeight( $video_height = 0 ) {
		
		if( (int)$video_height > 720 ) {
			
			$this->video_height	= 1080;
			return true;
		// BH 20120413 - added support for height 576 which is in DB but was missing from here
		//               and was causing 576p (PAL) 4:3 uploads to fail as it upgraded them to 720p
		
		} elseif( (int)$video_height > 576 ) {
			
			$this->video_height	= 720;
			return true;
			
		} elseif( (int)$video_height > 480 ) {
			
			$this->video_height	= 576;
			return true;
			
		} elseif( (int)$video_height > 360 ) {
			
			$this->video_height	= 480;
			return true;
			
		} elseif( (int)$video_height > 270 ) {
			
			$this->video_height	= 360;
			return true;
			
		} else {
			
			$this->video_height	= 270;
			return true;
		}
	}
	
	/* 
	 * @name : setAspectRatio
	 * @description : Unusual solution, we pass the aspect_ratio as a floating_point number because that is what we 
	 * store in the PodcastItem table. We convert that value into a string as stored in the workflows database table hence
	 * we have two related class properties called "aspect_ratio" the second having "_float" appended to the variable name.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */		
	function setAspectRatio( $aspect_ratio_float = null ) {

		// If the user chose an aspect ratio on upload, use it.
		if( $aspect_ratio_float ) {

			$this->aspect_ratio_float = $aspect_ratio_float;
			
			if( $this->aspect_ratio_float == STANDARD_SCREEN_FLOAT ) {
				
				$this->aspect_ratio = STANDARD_SCREEN;
				
			} else {
				
				$this->aspect_ratio = WIDE_SCREEN;
			}
		
		// The user did not specify an aspect ratio on upload, figure it out.
		} else {

			if( $this->video_width == false ) {
				
				$this->aspect_ratio = STANDARD_SCREEN;
				$this->aspect_ratio_float = STANDARD_SCREEN_FLOAT;
				
			} else {
				
				$this->aspect_ratio_float = ( $this->video_height / $this->video_width );
				
				if( $this->aspect_ratio_float < 0.70 ) {
					
					$this->aspect_ratio = WIDE_SCREEN;
					$this->aspect_ratio_float = WIDE_SCREEN_FLOAT;
					
				} else {
					
					$this->aspect_ratio = STANDARD_SCREEN;
					$this->aspect_ratio_float = STANDARD_SCREEN_FLOAT;
				}
			}
		}
		
	}

	/* 
	 * @name : setMediaType
	 * @description : Standard setter
	 * @updated : 15th August 2011
	 * @by : Charles Jackson
	 */		
	function setMediaType( $media_type = null ) {
		
		$this->media_type = $media_type;	
	}

	/* 
	 * @name : setWorkflow
	 * @description : Standard setter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */		
	function setWorkflow( $workflow = null ) {
		
		$this->workflow = $workflow;
	}
	
	/*
	 * @name : setWatermarkBumperTrailer
	 * @description : Takes the string passed as a parameter and uses it as the key on this class property array.
	 * Not a robust solution, if somebody tweaks the filechucker upload form it will break. I have documented the cgi
	 * script sccordingly.
	 * @updated : 15th August 2011
	 * @by : Charles Jackson
	 */
	function setWatermarkBumperTrailer( $bumper_and_trailer = null ) {
		
		$this->watermark_bumper_trailer = $bumper_and_trailer;
	}
	
	/*
	 * @name : setVle
	 * @description : Standard setter
	 * @updated : 16th August 2011
	 * @by : Charles Jackson
	 */
	function setVle( $vle = false ) {
		
		$this->vle = $vle;
	}
	
	/* 
	 * @name : getWorkflow
	 * @description : Standard getter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */			
	function getWorkflow() {
		
		return $this->workflow;	
	}

	/* 
	 * @name : getAspectRatio
	 * @description : Standard getter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */			
	function getAspectRatio() {
		
		return $this->aspect_ratio;
	}

	/* 
	 * @name : getTranscode
	 * @description : Standard getter
	 * @updated : 22nd August 2011
	 * @by : Charles Jackson
	 */	
	function getTranscode() {
		
		return $this->transcode;	
	}
	
	/* 
	 * @name : getAspectRatioFloat
	 * @description : Standard getter
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */			
	function getAspectRatioFloat() {
		
		return $this->aspect_ratio_float;
	}

	/*
	 * @name : getWatermarkBumperTrailer
	 * @description : Standard getter
	 * @updated : 15th August 2011
	 * @by : Charles Jackson
	 */
	function getWatermarkBumperTrailer( $bumper_and_trailer = null ) {
		
		return $this->watermark_bumper_trailer;
	}
	
	/*
	 * @name : exists
	 * @description : Called from the controller to determine if the current media file has a workflow
	 * else it will be transferred direct to the media box.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */
	function exists() {
		
		if( empty( $this->workflow ) )
			return false;
			
		return true;	
	}

	function getVle() {
		
		return $this->vle;	
	}

	/*
	 * @name : __select
	 * @description : Selects a workflow from the lookup table "workflows";
	 * @updated : 15th August 2011
	 * @by : Charles Jackson
	 */
	function __select() {
							
		$this->recursive = -1;
		$workflow = $this->find('first', array( 'conditions' => $this->conditions ) );
		if( empty( $workflow ) ) {
			//die('cant find a workflow');
			$this->error = 'We cannot determine a workflow for this media.';
			return false;
		}
	
		if( $workflow['Workflow']['active'] == false ) {
			$this->error = 'The workflow you have chosen <i>'.$workflow['Workflow']['workflow'].'</i> has not yet been enabled.';
			return false;
		}	
		return $workflow['Workflow']['workflow'];
	}
	
	/*
	 * @name : hasErrors
	 * @description : Returns a count of the elements in class attribute "errors"
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */	
	function hasErrors() {
	
		if( empty( $this->error ) )	
			return false;
		
		return true;
	}

	/*
	 * @name : getErrors
	 * @description : Returns the "error" string.
	 * @updated : 29th June 2011
	 * @by : Charles Jackson
	 */		
	function getErrors() {
		
		return $this->error;	
	}
	
	/*
	 * @name : setConditions
	 * @description : Builds the ORM conditions that are used on the lookup table from the class properties 
	 * with have populated. The properties are :
	 * 1. type (eg: video)
	 * 2. aspect_ratio (eg: 16:9)
	 * 3. height (eg: <=240)
	 * 4. bumber_and_trailer (eg: set "Y" flag on one of 3 fields accordingly. Fields are 'watermark_bumper_trailer',
	 * 'watermark_only','nothing_added'.
	 * 5. VLE flag
	 * @updated : 15th August 2011
	 * @by : Charles Jackson
	 */
	function setConditions() {

		// error_log("models/workflow > setConditions | media_type=".$this->media_type." type=".$this->aspect_ratio." height=".$this->video_height." vle=".$this->vle);

		$this->conditions = array(
			'Workflow.media_type' => $this->media_type,
			'Workflow.aspect_ratio' => $this->aspect_ratio,
			'Workflow.height' => $this->video_height,
			//'Workflow.watermark_bumpers_trailers' => $this->watermark_bumper_trailer,
			'Workflow.watermark_bumpers_trailers' => 'None', // Now that we are making all flavours this choice is no longer needed so hardcoded to 'none'
			'Workflow.vle' => $this->vle
		);
	}
}
