<div id="PodcastSummaryContainer" <?php echo isSet($edit_mode) ? 'style="display:none"' : ''; ?>>

	<?php if( $this->Object->isPodcast( $this->data['Podcast']['podcast_flag'] ) ) : ?>
        <div class="float_right images_container">
            <h2 style="display:block;"><?php echo ucfirst( PODCAST ); ?> Image</h2>
            <?php echo !empty( $this->data['Podcast']['image_copyright'] ) ? $this->data['Podcast']['image_copyright'] : 'Copyright Unknown'; ?>
            <div class="clear"></div>
            <div>
                <img width="100" src="<?php echo $this->Attachment->getMediaImage( $this->data['Podcast']['image'], $this->data['Podcast']['custom_id'], RESIZED_IMAGE_EXTENSION); ?>" title="podcast image" />
            </div>
        </div>
    <?php endif; ?>
    <div class="float_left two_column">
        <dl>
            <dt>Title: </dt>
            <dd><?php echo $this->data['Podcast']['title']; ?>&nbsp;</dd>
            <?php if( $this->Object->isPodcast( $this->data['Podcast']['podcast_flag'] ) ) : ?>
                <dt>Summary: </dt>
                <dd><?php echo nl2br( $this->data['Podcast']['summary'] ); ?>&nbsp;</dd>
                <dt>Created: </dt>
                <dd><?php echo $this->Time->getPrettyShortDate( $this->data['Podcast']['created'] ); ?>&nbsp;</dd>
                <dt>Copyright: </dt>
                <dd><?php echo $this->data['Podcast']['copyright']; ?>&nbsp;</dd>
                <dt>Language: </dt>
                <dd><?php echo $this->data['Podcast']['language']; ?>&nbsp;</dd>
                <dt>Explicit: </dt>
                <dd><?php echo ucfirst( $this->data['Podcast']['explicit'] ); ?>&nbsp;</dd>
                <dt>Keywords: </dt>
                <dd><?php echo $this->data['Podcast']['keywords']; ?>&nbsp;</dd>
                <dt>Contact Name (RSS): </dt>
                <dd><?php echo $this->data['Podcast']['contact_name']; ?>&nbsp;</dd>
                <dt>Contact Email (RSS): </dt>
                <dd><?php echo $this->data['Podcast']['contact_email']; ?>&nbsp;</dd>
                <dt>Web URL: </dt>
                <dd><?php echo $this->data['Podcast']['link']; ?>&nbsp;</dd>
                <dt>Web URL Text: </dt>
                <dd><?php echo $this->data['Podcast']['link_text']; ?>&nbsp;</dd>
                <dt>Private: </dt>
                <dd><img src="/img<?php echo ( $this->data['Podcast']['private'] == YES ) ? CORRECT_IMAGE : INCORRECT_IMAGE; ?>" title="private status" /></dd>
                <dt>Intranet Only: </dt>            
                <dd><img src="/img<?php echo ( $this->data['Podcast']['intranet_only'] == YES ) ? CORRECT_IMAGE : INCORRECT_IMAGE; ?>" title="private status" /></dd>
                <dt>Preferred Node</dt>
                <dd><?php echo $this->data['PreferredNode']['title']; ?>&nbsp;</dd>
                <dt>Nodes</dt>
                <dd>
                    <?php 
                    foreach( $this->data['Nodes'] as $node ) :
                        echo $node['title'].'. ';
                    endforeach; 
                    ?>
                    &nbsp;
                </dd>
			<?php endif; ?>
        </dl>
    </div>

	<?php if( $this->Permission->toUpdate( $this->data ) || $this->Permission->isAdminRouting( $this->params ) ) : ?>


        <div class="action_buttons track_save_cancel">
            <ul>
                    <li><a href="/" class="jquery_display button edit"  data-source="PodcastSummaryContainer" data-target="FormPodcastSummaryContainer" id="PodcastSummaryButton"><span>edit</span></a></li>
                    <li><a class="button rss" href="/feeds/add/<?php echo $podcast['id']; ?>" onclick="return confirm('Are you sure?');"><span>RSS Refresh</span></a></li>
            </ul>
        </div>
	<?php endif; ?>
</div>