<div id="FormPodcastYoutubeContainer" <?php echo isSet($edit_mode) == false ? 'style="display:none"' : ''; ?>>
    <div class="input text">
        <label for="PodcastYoutubeSeriesPlaylistText">Series Playlist</label>
        <input type="text" size="60" id="PodcastYoutubeSeriesPlaylistText" value="<?php echo $this->data['Podcast']['youtube_series_playlist_text']; ?>" name="data[Podcast][youtube_series_playlist_text]">
        <?php echo $this->Form->error('Podcast.youtube_series_playlist_text'); ?>
    </div>
    <div class="input text">
        <label for="PodcastYoutubeSeriesPlaylistLink">Series Playlist Link</label>
        <input type="text" size="60" id="PodcastYoutubeSeriesPlaylistLink" value="<?php echo $this->data['Podcast']['youtube_series_playlist_link']; ?>" name="data[Podcast][youtube_series_playlist_link]">
        <?php echo $this->Form->error('Podcast.youtube_series_playlist_link'); ?>
    </div>
    <div class="input select">
        <label for="PodcastYoutubeChannel">Youtube Channel</label>
        <select name="data[Podcast][youtube_channel]" id="PodcastYoutubeChannel">
            <option value="">Please select</option>
            <?php foreach( $youtube_channels as $youtube_channel ) : ?>
                <option value="<?php echo $youtube_channel; ?>"><?php echo $youtube_channel; ?></option>
            <?php endforeach; ?>
        </select>
        <?php echo $this->Form->error('Podcast.youtube_channel'); ?>
    </div>
    <div class="input text">
        <label for="PodcastCourseCode">Course Code</label>
        <input type="text" size="60" id="PodcastCourseCode" value="<?php echo $this->data['Podcast']['course_code']; ?>" name="data[Podcast][course_code]">
        <?php echo $this->Form->error('Podcast.course_code'); ?>
    </div>
</div>
