<?php echo $rss->header(); ?>
<?php $channel = $this->BespokeRss->channel(array(), $channelData, $content_for_layout); ?>
<?php echo $rss->document($documentData,$channel); ?>