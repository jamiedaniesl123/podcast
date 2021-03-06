<?php echo $this->element('tabs'); ?>
<div class="content">
<div class="wrapper" style="margin-top: 40px;">
<fieldset id="podcast_media">
    <legend><h3>Collection <?php echo MEDIA; ?>s</h3></legend>
    
    <img src="/img/collection-large.png" />
    
	<form method="post" action="">
	    <table>
	    	<thead>
	            <tr>
					<?php if( $this->Permission->toUpdate( $this->data ) ) : ?>
	            		<th class="checkbox">Select</th>
            		<?php endif; ?>
	            	<th class="thumbnail">Image</th>
	            	<th class="collection-title">Name</th>
	                <th class="">Uploaded</th>
	                <th class="icon-col">Available</th>
	                <th class="icon-col">Published</th>                                        
	                <th class="icon-col">Podcast.open.ac.uk</th>                                                            
	            	<th class="icon-col">iTunes</th>                
	            	<th class="icon-col">YouTube</th>
	            	<th class="actions">Actions</th>
	            </tr>
	        </thead>
	        <?php foreach( $this->data['PodcastItems'] as $podcast_item ) : ?>
	        
	        	<?php if( $this->Object->isDeleted( $podcast_item ) == false ) : ?>
	        	
		        	<tr>
						<?php if( $this->Permission->toUpdate( $this->data ) && $this->Permission->isAdminRouting( $this->params ) == false ) : ?>
		                    <td width="15px" align="center">
	                            <input type="checkbox" name="data[PodcastItem][Checkbox][<?php echo $podcast_item['id']; ?>]" class="podcast_item_selection" id="PodcastItemCheckbox<?php echo $podcast_item['id']; ?>">
		                    </td>
	                    <?php endif; ?>
			            <td  class="thumbnail">
			            	<img src="<?php echo $this->Attachment->getMediaImage( $podcast_item['image_filename'].'.jpg',$this->data['Podcast']['custom_id'] ,THUMBNAIL_EXTENSION ); ?>" class="thumbnail" />
			            </td>
		                <td  class="collection-title"><a href="/podcast_items/view/<?php echo $podcast_item['id']; ?>"><?php echo strlen( $podcast_item['title'] ) ? $podcast_item['title'] : 'Untitled '.MEDIA; ?></a></td>
		            	<td><?php echo $this->Time->getPrettyLongDate( $podcast_item['created'] ); ?></td>
		                <td class="icon-col available"><?php echo $this->Object->getProcessedState( $podcast_item['processed_state'] ); ?></td>	
                        <td class="icon-col available"><img src="/img<?php echo $this->Object->isPublished( $podcast_item['published_flag'] ) ? CORRECT_IMAGE : INCORRECT_IMAGE; ?>" class="icon" /></td>
                        <td class="icon-col available"><img src="/img<?php echo $this->Object->isPublished( $podcast_item['published_flag'] ) ? CORRECT_IMAGE : INCORRECT_IMAGE; ?>" class="icon" /></td>
		            	<td  class="icon-col"><img src="/img/<?php echo $this->Object->getApprovalStatus( $podcast_item, 'itunes' ); ?>" class="icon"></td>
		            	<td  class="icon-col">
                        
                        <?php if( $this->Object->intendedForYoutube( $this->data['Podcast'] ) && $this->Object->hasYoutubeFlavour( $podcast_item ) ) : ?>
	                        <img src="/img/<?php echo $this->Object->getApprovalStatus( $podcast_item, 'youtube' ); ?>" class="icon">
                            
						<?php else : ?>
                            
                            <img src="/img/icon-16-youtube-unavailable.png" alt="Not available" />
                            
                        <?php endif; ?>
                        </td>
						<td class="actions">
						
								<?php if( $this->Permission->toUpdate( $this->data ) ) : ?>
								
									<a class="button edit" href="/podcast_items/edit/<?php echo $podcast_item['id']; ?>" title="edit media details">edit</a>
									
									<?php if( ( ( $podcast_item['processed_state'] == MEDIA_AVAILABLE ) && ( $this->Object->isPublished( $podcast_item ) == false ) ) ): ?>
									
										<a class="button delete" href="/podcast_items/delete/<?php echo $podcast_item['id']; ?>" title="delete media" onclick="return confirm('Are you sure you wish to delete this media?');">delete</a>

									<?php endif; ?>
                                    
								<?php endif; ?>
						</td>	
						                
		            </tr>
		            
		    	<?php endif; ?>
		    	
	        <?php endforeach; ?>
	        
	    </table>

	    <?php if( $this->Permission->toUpdate( $this->data ) ) : ?>
	    
	        <a href="/" class="toggler button setting" data-status="unticked">Select/deselect all</a>
			<a class="button delete multiple_action_button" href="/podcast_items/delete" id="delete_multiple_podcast_items">Delete</a>
            
			<?php if( $this->Object->isPodcast( $this->data['Podcast']['podcast_flag'] ) ) : ?>

				<a class="button publish multiple_action_button" href="/podcast_items/publish" id="publish_multiple_podcast_items">Publish</a>

				<a class="button publish multiple_action_button" href="/podcast_items/unpublish" id="publish_multiple_podcast_items">Unpublish</a>
                
			<?php endif; ?>
            		
			<?php if( $this->Permission->isItunesUser() && $this->Object->isPodcast( $this->data['Podcast']['podcast_flag'] ) ) : ?>
				        
		        <a class="button itunes-icon multiple_action_button" href="/itunes/podcast_items/approve" id="PodcastItemItunesApprove">iTunes include</a>
		        <a class="button itunes-icon multiple_action_button" href="/itunes/podcast_items/reject" id="PodcastItemItunesReject">iTunes exclude</a>
		        
			<?php endif; ?>
			
			<?php if( $this->Permission->isYoutubeUser() ) : ?>
				        
				<?php if( $this->Object->intendedForYoutube( $this->data['Podcast'] ) ) : ?>
                
                    <a class="button youtube-icon multiple_action_button" href="/youtube/podcast_items/upload" id="PodcastItemYoutubeUpload">YouTube upload</a>
                    
                    <a class="button youtube-icon multiple_action_button" href="/youtube/podcast_items/refresh" id="PodcastItemYoutubeRefresh">YouTube refresh</a>
                    
				<?php endif; ?>
	        
        	<?php endif; ?>
	        
		<?php endif; ?>
		
    </form>
    
</fieldset>
</div>
</div>