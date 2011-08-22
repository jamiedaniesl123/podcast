<?php
class PodcastItemMedia extends AppModel {

    var $name = 'PodcastItemMedia';
    var $useTable = 'podcast_item_media';
    var $validate = array();
	var $order = 'PodcastItemMedia.id ASC';
	var $common_meta_injection = array('default' => null ); 
	var $itunes_meta_injection = array('ipod-all' => 'ipod-all/','desktop-all' => 'desktop-all/','hd' => 'hd/','hd-1080' => 'hd-1080/' );

    var $belongsTo = array(

        'PodcastItem' => array(
            'className' => 'PodcastItem',
            'foreignKey' => 'podcast_item',
            'dependent' => true
         )
    );
	
	/*
	 * @name : saveFlavours
	 * @description : Called from the callbacks controller, will save a flavour of media
	 * and update the status accordingly.
	 * @updated : 3rd August 2011
	 * @by : Charles Jackson
	 */
	function saveFlavour( $row = array() ) {
		
		$data = array();
		$this->create();
		
		if( strtoupper( $row['status'] ) == YES ) {
						
			$data['PodcastItemMedia']['processed_state'] = MEDIA_AVAILABLE; // Media available

			if( $row['flavour'] == 'default' || $row['flavour'] == 'transcript' ) {
				
				$data['PodcastItem']['id'] = $row['podcast_item_id'];
				$data['PodcastItem']['processed_state'] = MEDIA_AVAILABLE;
			}
			
		} else {
			
			$data['PodcastItemMedia']['processed_state'] = -1; // Error in transcoding
			
			if( $row['flavour'] == 'default' || $row['flavour'] == 'transcript' ) {
				
				$data['PodcastItem']['id'] = $row['podcast_item_id'];
				$data['PodcastItem']['processed_state'] = -1; // Error in transcoding
			}

		}
			
		$data['PodcastItemMedia']['filename'] = str_replace('//','/',$row['destination_filename'] ); // Quick fudge to fix minor API issue;
		
		// When media has been trancoded the original filename will exist within
		// the array element "original_filename" else it will exist within "destination_filename" 
		// for non transcoded media such as MP3 files.
		if( isSet( $row['original_filename'] ) ) {
			
			$data['PodcastItemMedia']['original_filename'] = $row['original_filename'];
			
		} else {
			
			$data['PodcastItemMedia']['original_filename'] = $row['destination_filename'];
		}

		$data['PodcastItemMedia']['media_type'] = $row['flavour'];
		$data['PodcastItemMedia']['podcast_item'] = $row['podcast_item_id'];
		
		$data['PodcastItemMedia']['duration'] = $row['duration'];
		$data['PodcastItemMedia']['uploaded_when'] = date("Y-m-d H:i:s");

		$this->set( $data );
		return $this->saveAll();
	}	

	/*
	 * @name : metaInject
	 * @description : Reads through every flavour of item media and build an array on meta data for injection.
	 * @updated : 18th August 2011
	 * @by : Charles Jackson
	 */	
	function buildMetaData( $conditions ) {
		
		$data = array();
		$meta_injection = array();
		
		$data = $this->find('all', array('conditions' => $conditions ) );
		
		if( empty( $data ) )
			return false;
			
		foreach( $data['PodcastItemMedia'] as $media ) {
			
			if( isSet( $this->common_meta_injection[$media['media_type']] ) ) {
				
				$inject['podcast_item_id'] = $id;
				$inject['destination_path'] = $data['PodcastItem']['custom_id'].'/'.$common_meta_injection[$media['media_type']];
				$inject['destination_filename'] = $data['PodcastItem']['filename'];
				  $meta_injection[] = $this->PodcastItem->commonMetaInjection( $inject );
				
			} elseif( isSet( $this->itunes_meta_injection[$media['media_type']] ) ) {
				
				$inject['podcast_item_id'] = $id;
				$inject['destination_path'] = $data['PodcastItem']['custom_id'].'/'.$itunes_meta_injection[$media['media_type']];
				$inject['destination_filename'] = $media['filename'];
				$meta_injection[] = $this->PodcastItem->itunesMetaInjection( $inject );
			}
		}
		
		return $meta_injection;
	}	
	
}