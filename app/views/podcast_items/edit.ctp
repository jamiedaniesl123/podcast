<form accept-charset="utf-8" action="/podcast_items/edit/<?php echo $this->data['PodcastItem']['id']; ?>" method="post" id="PodcastItemEditForm" enctype="multipart/form-data">
    <input type="hidden" id="PodcastItemId" value="<?php echo $this->data['PodcastItem']['id']; ?>" name="data[PodcastItem][id]">
    <input type="hidden" id="PodcastItemPodcastId" value="<?php echo $this->data['PodcastItem']['podcast_id']; ?>" name="data[PodcastItem][podcast_id]">
	<fieldset>
        <legend>Media</legend>
		<?php echo $this->element('../podcast_items/_form'); ?>
        <button id="update_podcast_item" type="submit"  class="auto_select_and_submit">update podcast media</button>
    </fieldset>
</form>