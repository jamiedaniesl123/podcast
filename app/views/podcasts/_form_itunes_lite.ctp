<fieldset class="itunes">
	<legend><h3>iTunes U</h3></legend>
    
    <img src="/img/collection-itunes-large.png" width="45" height="33" alt="iTunes" />
    
	<div class="link" style="margin-top: 20px;">
	    <a href="/" id="PodcastItemItunesToggle" class="juggle button white" data-target="FormPodcastItunesContainer"><img src="/img/icon-16-open.png" alt="sharing - ownership" class="icon" />View details</a>
    </div>	
	<input type="hidden" id="PodcastCustomId" name="data[Podcast][custom_id]" value="<?php echo $this->data['Podcast']['custom_id']; ?>">
    <div class="itunes_container itunes" id="FormPodcastItunesContainer" style="display:none">
        <div class="input file">
            <label for="PodcastNewImageLogoless">Logoless image</label>
            <input type="file" id="PodcastNewImageLogoless" name="data[Podcast][new_image_logoless]">
            <input type="hidden" id="PodcastImageLogoless" name="data[Podcast][image_logoless]" value="<?php echo $this->data['Podcast']['image_logoless']; ?>">            
            <?php echo $this->Form->error('Podcast.image_logoless'); ?>
        </div>
        <div class="image thumbnail">
           <img src="<?php echo $this->Attachment->getMediaImage( $this->data['Podcast']['image_logoless'], $this->data['Podcast']['custom_id'], THUMBNAIL_EXTENSION); ?>" title="thumbnail image" />
            <a href="/podcasts/delete_image/image_logoless/<?php echo $this->data['Podcast']['id']; ?>" title="delete logoless image" onclick="return confirm('Are you sure you wish to delete the logoless screen image?')">delete</a>
        </div>
        <div class="input text">
            <label for="PodcastImageLlCopyright">Copyright/credit</label>
            <input type="text" size="60" id="PodcastImageLlCopyright" name="data[Podcast][image_ll_copyright]" value="<?php echo $this->data['Podcast']['image_ll_copyright']; ?>">
            <?php echo $this->Form->error('Podcast.image_ll_copyright'); ?>
        </div>
        <div class="input file">
            <label for="PodcastNewImageWide">Widescreen Image</label>
            <input type="file" id="PodcastNewImageWide" name="data[Podcast][new_image_wide]">
            <input type="hidden" id="PodcastImageWide" name="data[Podcast][image_wide]" value="<?php echo $this->data['Podcast']['image_wide']; ?>">            
            <?php echo $this->Form->error('Podcast.image_wide'); ?>
        </div>
        <div class="image thumbnail">
           <img src="<?php echo $this->Attachment->getMediaImage( $this->data['Podcast']['image_wide'], $this->data['Podcast']['custom_id'], THUMBNAIL_EXTENSION); ?>" title="thumbnail image" />
            <a href="/podcasts/delete_image/image_wide/<?php echo $this->data['Podcast']['id']; ?>" title="delete widescreen image" onclick="return confirm('Are you sure you wish to delete the wide screen image?')">delete</a>
        </div>
        <div class="input text">
            <label for="PodcastImageWideCopyright">Copyright/credit</label>
            <input type="text" size="60" id="PodcastImageWideCopyright" name="data[Podcast][image_wide_copyright]" value="<?php echo $this->data['Podcast']['image_wide_copyright']; ?>">
            <?php echo $this->Form->error('Podcast.image_wide_copyright'); ?>
        </div>
        <div class="input file">
            <label for="PodcastNewArtworkFile">Artwork File</label>
            <input type="file" id="PodcastNewArtworkFile" name="data[Podcast][new_artwork_file]">
            <input type="hidden" id="PodcastArtworkFile" name="data[Podcast][artwork_file]" value="<?php echo $this->data['Podcast']['artwork_file']; ?>">            
            <?php echo $this->Form->error('Podcast.artwork_file'); ?>
        </div>
        <div class="input text">
            <label for="PodcastAuthor">Author</label>
            <input type="text" size="60" id="PodcastAuthor" value="<?php echo $this->data['Podcast']['author']; ?>" name="data[Podcast][author]">
            <?php echo $this->Form->error('Podcast.author'); ?>
        </div>
        <div class="input select">
            <label for="PodcastExplicit">Explicit</label>
            <select name="data[Podcast][explicit]" id="PodcastExplicit">
                <option value="clean">Clean</option>
            </select>
            <?php echo $this->Form->error('Podcast.explicit'); ?>
        </div>
        <div class="input text">
            <label for="PodcastCourseCode">Course Code</label>
            <input type="text" size="60" id="PodcastCourseCode" value="<?php echo $this->data['Podcast']['course_code']; ?>" name="data[Podcast][course_code]">
            <?php echo $this->Form->error('Podcast.course_code'); ?>
        </div>
        <div class="input select">
            <label for="PodcastCourseType">Course Type</label>
            <select name="data[Podcast][course_type]" id="PodcastCourseType">
            	<?php foreach( $course_types as $course_type ) : ?>
	                <option value="<?php echo $course_type; ?>" <?php echo strtoupper( $this->data['Podcast']['course_type'] ) == $course_type ? 'selected="selected"' : ''; ?>><?php echo $course_type; ?></option>
                <?php endforeach; ?>
            </select>
            <?php echo $this->Form->error('Podcast.course_type'); ?>
        </div>
        <div class="input checkbox">
            <label for="PodcastOpenlearnEpub">Open Learn ePub</label>
            <input type="hidden" value="N" id="PodcastOpenlearnEpub_" name="data[Podcast][openlearn_epub]">
            <input type="checkbox" id="PodcastOpenlearnEpub" value="Y" <?php echo $this->data['Podcast']['openlearn_epub'] == 'Y' ? 'checked="checked"' : ''; ?>" name="data[Podcast][openlearn_epub]">
            <?php echo $this->Form->error('Podcast.openlearn_epub'); ?>
        </div>        
        <div class="input select">
            <label for="PodcastItunesuSite">iTunes Site</label>
            <select name="data[Podcast][itunesu_site]" id="PodcastItunesuSite">
                <option value="Public" <?php echo strtoupper( $this->data['Podcast']['itunesu_site'] ) == 'PUBLIC' ? 'selected="selected"' : ''; ?>>Public</option>
                <option value="Private" <?php echo strtoupper( $this->data['Podcast']['itunesu_site'] ) == 'PRIVATE' ? 'selected="selected"' : ''; ?>>Private</option>            
            </select>
            <?php echo $this->Form->error('Podcast.itunes_site'); ?>
        </div>
        <div class="wrapper" id="itunesCategoriesContainer">
            <div class="float_left">
                <div class="input select">
                    <label for="Categories">Podcast iTune Categories</label>
                    <input type="hidden" name="data[Categories]" value="" id="Categories_" />
                    <select name="data[Categories][]" class="selected" multiple="multiple" id="Categories">
                        <?php if( isSet( $this->data['Categories'] ) && is_array( $this->data['Categories'] ) ) : ?>
                            <?php foreach( $this->data['Categories'] as $category ) : ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['category']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php echo $this->Form->error('Podcast.Categories'); ?>
					<div class="multiple-button">
                    	<div class="move float_right" data-source="Categories" data-target="PodcastAllCategories"><img src="/img/multiple-button-right.png" alt="Move right" class="icon" /></div>
                    </div>
                    <div class="clear"></div>                    
                </div>
            </div>
            <div class="float_left">
                <div class="input select">
                    <label for="PodcastAllCategories">All iTune Categories</label>
                    <input type="hidden" name="data[Podcast][AllCategories]" value="" id="PodcastAllCategories_" />
                    <select name="data[Podcast][AllCategories][]" multiple="multiple" id="PodcastAllCategories">
                        <?php foreach( $categories as $id => $value ) : ?>
                            <option value="<?php echo $id; ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
					<div class="multiple-button">
                    	<div class="move float_left" data-source="PodcastAllCategories" data-target="Categories"><img src="/img/multiple-button-left.png" alt="Move left" class="icon" /></div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="wrapper" id="ituneu_categories_container">
            <div class="float_left">
                <div class="input select">
                    <label for="iTuneCategories">Podcast iTune U Categories</label>
                    <input type="hidden" name="data[iTuneCategories]" value="" id="iTuneCategories_" />
                    <select name="data[iTuneCategories][]" class="selected" multiple="multiple" id="iTuneCategories">
                        <?php if( isSet( $this->data['iTuneCategories'] ) && is_array( $this->data['iTuneCategories'] ) ) : ?>
                            <?php foreach( $this->data['iTuneCategories'] as $category ) : ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['code_title']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php echo $this->Form->error('Podcast.iTuneCategories'); ?>
					<div class="multiple-button">
                    	<div class="move float_right" data-source="iTuneCategories" data-target="PodcastAlliTuneCategories"><img src="/img/multiple-button-right.png" alt="Move right" class="icon" /></div>
                    </div>
                    <div class="clear"></div>                    
                </div>
            </div>
            <div class="float_left">
                <div class="input select">
                    <label for="PodcastAlliTuneCategories">All iTune U Categories</label>
                    <input type="hidden" name="data[Podcast][PodcastAlliTuneCategories]" value="" id="PodcastAlliTuneCategories_" />
                    <select name="data[Podcast][PodcastAlliTuneCategories][]" multiple="multiple" id="PodcastAlliTuneCategories">
                        <?php foreach( $itunes_categories as $id => $value ) : ?>
                            <option value="<?php echo $id; ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
					<div class="multiple-button">
                    	<div class="move float_left" data-source="PodcastAlliTuneCategories" data-target="iTuneCategories"><img src="/img/multiple-button-left.png" alt="Move right" class="icon" /></div>
                    </div>
                    <div class="clear"></div>                    
                    
                </div>
            </div>
        </div>
        <div class="clear"></div>
        
    </div>
</fieldset>
