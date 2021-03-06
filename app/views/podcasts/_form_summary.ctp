<div id="FormPodcastSummaryContainer" <?php echo isSet($edit_mode) == false ? 'style="display:none"' : ''; ?> class="float_left two_column">

  <div class="collection_input"><!--General info-->
    <div class="text">
      <label for="PodcastTitle">Title <span class="required">(Required)</span></label>
      <input type="hidden" value="" id="PodcastTitle_" name="data[Podcast][title]">
      <input type="text" size="100"  class="fieldrequired" id="PodcastTitle"  class="fieldrequired" value="<?php echo $this->data['Podcast']['title']; ?>" name="data[Podcast][title]">
      <?php echo $this->Form->error('Podcast.title'); ?>
    </div>
  </div>

  <?php if( isSet( $this->data['Podcast']['id'] ) && (int)$this->data['Podcast']['id'] ) : ?>
    <div class="link">
    <?php if( $this->Object->syndicated( $this->data['Podcast']['syndicated'] ) == false ) : ?>
      
      
      
      <a href="/" id="PodcastFlagLink" class="button white juggle" data-target="data[Podcast][podcast_flag]"><img src="/img/icon-16-open.png" alt="Upgrade this collection" class="icon" /><span>Upgrade this <?php echo PODCAST; ?></span></a>
      
      
      
      
      <p>Upgrading is only needed if you want to generate RSS feeds for your collection, publish it on the Podcast.open.ac.uk site or publish to the OU on iTunes U sites</p>
      
      
      
      
    <?php endif; ?>
      <input type="hidden" id="PodcastPodcastFlag" value="<?php echo trim( $this->data['Podcast']['podcast_flag'] );?>" name="data[Podcast][podcast_flag]">
    </div>
    <div class="clear"></div>

    <div id="PodcastSyndicationContainer" class="podcast_container" style="display:none;"> <!-- start of syndication container -->
      
      
      <div class="textarea collection_input">
        <label for="summary">Summary <span class="required">(Required)</span></label>
        <input type="hidden" value="" id="PodcastSummary_" name="data[Podcast][summary]">
        <textarea id="PodcastSummary" rows="6" cols="100" name="data[Podcast][summary]" class="fieldrequired"><?php echo $this->data['Podcast']['summary']; ?></textarea>
        <span class="tip-text">4000 characters max. No HTML allowed</span>
        <?php echo $this->Form->error('Podcast.summary'); ?>
      </div>
      
      <div class="file">
        <div class="image thumbnail wrapper">
          <div class="float_right text_right">
            <img src="<?php echo $this->Attachment->getMediaImage( $this->data['Podcast']['image'], $this->data['Podcast']['custom_id'], RESIZED_IMAGE_EXTENSION ); ?>" title="thumbnail image" />
          <?php if( !empty( $this->data['Podcast']['image'] ) ) : ?>
         	<a class="button orange" href="/podcasts/delete_image/image/<?php echo $this->data['Podcast']['id']; ?>" title="delete collection image" onclick="return confirm('Are you sure you wish to delete the Collection image?')"><span>Delete</span></a>
          <?php endif; ?>
          </div>
          
          
          <div class="collection_input">
            <label for="PodcastNewImage">Image</label>
            <input type="file" size="100" id="PodcastNewImage" name="data[Podcast][new_image]">
            <input type="hidden" id="PodcastImage" name="data[Podcast][image]" value="<?php echo $this->data['Podcast']['image']; ?>">
            <span class="tip-text">JPG only. PNGs not yet supported! 100 pixel square</span>
          </div>
          <?php echo $this->Form->error('Podcast.image'); ?>
          <div class="clear"></div>
        </div>
      </div>

      <div class="collection_input"><!--additional info-->   
        <div class="select">
          <label for="PodcastLanguage">Language</label>
          <select name="data[Podcast][language]" id="PodcastLanguage">
          <span class="tip-text"></span>
          <?php foreach( $languages as $language_code => $language_description ) : ?>
            <option value="<?php echo $language_code; ?>" <?php echo $this->data['Podcast']['language'] == $language_code ? 'selected="true"' : ''; ?>><?php echo $language_description; ?></option>
          <?php endforeach; ?>
          </select>
          <?php echo $this->Form->error('Podcast.language'); ?>
        </div>

        <div class="text">
          <label for="PodcastKeywords">Keywords</label>
          <input type="text" size="100" id="PodcastKeywords" value="<?php echo $this->data['Podcast']['keywords']; ?>" name="data[Podcast][keywords]">
          <span class="tip-text">Enter a list of words, separated by a comma</span>
          <?php echo $this->Form->error('Podcast.keywords'); ?>
        </div>
        
        <div class="text">
          <label for="PodcastAuthor">Author</label>
          <input type="text" size="100" id="PodcastAuthor" value="<?php echo $this->data['Podcast']['author']; ?>" name="data[Podcast][author]">
          <?php echo $this->Form->error('Podcast.author'); ?>
        </div>
        
        <div class="text">
          <label for="PodcastContactName">Contact Name </label>
          <input type="text" size="100" id="PodcastContactName" value="<?php echo $this->data['Podcast']['contact_name']; ?>" name="data[Podcast][contact_name]">
          
          <span class="tip-text">Enter a contact name</span>
          
          <?php echo $this->Form->error('Podcast.contact_name'); ?>
        </div>
        <div class="text">
          <label for="PodcastContactEmail">Contact Email </label>
          <input type="text" size="100" id="PodcastContactEmail" value="<?php echo $this->data['Podcast']['contact_email']; ?>" name="data[Podcast][contact_email]">
          
          <span class="tip-text">Enter a contact email</span>
          
          <?php echo $this->Form->error('Podcast.contact_email'); ?>
        </div>
        <div class="text">
          <label for="PodcastLink">Web URL <span class="required">(Required)</span></label>
          <input type="text" size="100" id="PodcastLink"  class="fieldrequired" value="<?php echo $this->data['Podcast']['link']; ?>" name="data[Podcast][link]">
          
          <span class="tip-text">URL of a web page you want linked to this particular track</span>
          
          <?php echo $this->Form->error('Podcast.link'); ?>
        </div>
        <div class="text">
          <label for="PodcastLinkText">Web link text</label>
          <input type="text" size="100" id="PodcastLinkText" value="<?php echo $this->data['Podcast']['link_text']; ?>" name="data[Podcast][link_text]">
          
          <span class="tip-text">Optional text for above link</span>
          
          <?php echo $this->Form->error('Podcast.link_text'); ?>
        </div>
        
      </div><!--/End of collection_input: additional info-->
      
      <div class="collection_input"><!--collection_input: copyright and privacy-->
        <div class="text">
          <label for="PodcastCopyright">Copyright</label>
          <input type="text" size="100" id="PodcastCopyright" value="<?php echo $this->data['Podcast']['copyright']; ?>" name="data[Podcast][copyright]">
          <span class="tip-text">Who owns the copyright for this collection?</span>
          <?php echo $this->Form->error('Podcast.copyright'); ?>
        </div>
        <div class="checkbox">
          <input type="hidden" value="N" id="PodcastPrivate_" name="data[Podcast][private]">
          
          <label for="PodcastPrivate">Private</label>
          <input type="checkbox" id="PodcastPrivate" value="Y" <?php echo $this->data['Podcast']['private'] == 'Y' ? 'checked="checked"' : '';?> name="data[Podcast][private]">
          <br />
          <?php echo $this->Form->error('Podcast.private'); ?>
        </div>
        
        <div class="checkbox">
          <input type="hidden" value="N" id="PodcastIntranetOnly_" name="data[Podcast][intranet_only]">
           <label for="PodcastIntranetOnly">Intranet (SAMS) only</label>
          <input type="checkbox" id="PodcastIntranetOnly" value="Y" <?php echo $this->data['Podcast']['intranet_only'] == 'Y' ? 'checked="checked"' : '';?>  name="data[Podcast][intranet_only]">
         <br />
          <?php echo $this->Form->error('Podcast.intranet_only'); ?>
        </div>
        
        <div class="clear"></div>
      </div><!--/end of collection_input: copyright and privacy-->

      <div class="collection_input"><!--collection_input: podcast categories-->
        <div id="nodes_container">
          <div class="float_left_list">
            <div class="select">
              <label for="Nodes">Selected 'Podcast.open.ac.uk' categories <span class="required">(Required)</span></label>
              <input type="hidden" name="data[Nodes]" value="" id="Nodes_"/>
              <select name="data[Nodes][]" class="selected fieldrequired" multiple="multiple" id="Nodes">
              <?php if( isSet( $this->data['Nodes'] ) && is_array( $this->data['Nodes'] ) ) : ?>
                <?php foreach( $this->data['Nodes'] as $node ) : ?>
                <option value="<?php echo $node['id']; ?>"><?php echo $node['title']; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
              </select>
              <?php echo $this->Form->error('Podcast.Nodes'); ?>
              <div class="multiple-button">
                <div class="move float_right" data-source="Nodes" data-target="PodcastAllNodes"><img src="/img/multiple-button-right.png" alt="Move right" class="icon" />
                </div>
              </div>
              <div class="clear"></div>
            </div><!--/end of select -->
          </div><!--/end of float_left_list -->
          <div class="float_left_list">
            <div class="select">
              <label for="PodcastAllNodes">All 'Podcast.open.ac.uk' categories </label>
              <input type="hidden" name="data[Podcast][AllNodes]" value="" id="PodcastAllNodes_" />
              <select name="data[Podcast][AllNodes][]" multiple="multiple" id="PodcastAllNodes"  class="fieldrequired">
              <?php foreach( $nodes as $nodegroup) : ?>
                <?php foreach( $nodegroup as $thisnode) : ?>
                  <?php if(isset($thisnode['title'])){
                  echo "<option value=\"".$thisnode['id']."\">".str_repeat("-",$thisnode['level']).$thisnode['title']."</option>";
                  } else{
                   echo "<option value=\"".$thisnode['nodes']['id']."\">".str_repeat("-",$thisnode['nodes']['level']).$thisnode['nodes']['title']."</option>";
                  }
                  ?>
                <?php endforeach; ?>
              <?php endforeach; ?>
              </select>
              <div class="multiple-button">
                <div class="move float_left_list" data-source="PodcastAllNodes" data-target="Nodes"><img src="/img/multiple-button-left.png" alt="Move left" class="icon" />
                </div>
              </div>
              <div class="clear"></div>
            </div><!--/end of select-->
          </div><!--/end of float_left_list-->
        </div><!--/end of nodes_container-->
      </div><!--/end of collection_input: podcast categories-->

        <?php echo $this->element('../podcasts/_form_admin'); ?>

      <div class="collection_input"><!--collection_input: itunes (podcast) categories-->

        <div id="itune_categories_container">
          <div class="float_left_list">
            <div class="select">
              <label for="Categories">Selected iTunes Categories</label>
              <input type="hidden" name="data[Categories]" value="" id="Categories_" />
<?php
//debug($this->data['Categories']);
?>
              <select name="data[Categories][]" class="selected" multiple="multiple" id="Categories">
              <?php if( isSet( $this->data['Categories'] ) && is_array( $this->data['Categories'] ) ) : ?>
                <?php foreach( $this->data['Categories'] as $category ) : ?>
                  <option value="<?php echo $category['id']; ?>"><?php echo $category['category']; ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
              </select>
              <?php echo $this->Form->error('Podcast.Categories'); ?>
              <div class="multiple-button">
                <div class="move float_right" data-source="Categories" data-target="PodcastAllCategories"><img src="/img/multiple-button-right.png" alt="Move right" class="icon" />
                </div>
              </div>
              <div class="clear"></div>
            </div><!--/select-->
          </div><!--float_left-->

          <div class="float_left_list">
            <div class="select">
              <label for="PodcastAllCategories">All iTunes (Podcast directory) Categories</label>
              <input type="hidden" name="data[Podcast][AllCategories]" value="" id="PodcastAllCategories_" />
              <select name="data[Podcast][AllCategories][]" multiple="multiple" id="PodcastAllCategories">
                  <?php //print_r($categories); ?>
                  <?php foreach( $categories as $id => $value ) : ?>
                      <option value="<?php echo $id; ?>"><?php echo $value; ?></option>
                  <?php endforeach; ?>
              </select>
              <div class="multiple-button">
                <div class="move float_left_list" data-source="PodcastAllCategories" data-target="Categories"><img src="/img/multiple-button-left.png" alt="Move left" class="icon" />
                </div>
              </div>
              <div class="clear"></div>
            </div><!--/end of select-->
          </div><!--/end of float_left_list-->
          <div class="clear"></div>
        </div><!--/end of itune_categories_container-->
      </div><!--/end of collection_input: itunes (podcast) categories-->

    </div><!-- end of syndication container -->

    <?php endif; ?>
</div>