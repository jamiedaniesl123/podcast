<form accept-charset="utf-8" action="/podcasts/add" method="post" id="PodcastAddForm" enctype="multipart/form-data">
    <fieldset>
        <legend><h3>Create collection</h3></legend>
        
        
        <p class="leader">
            Use the form below to create a new collection container
        </p>
        
        <img src="/img/add-collection-large.png" width="45" height="33" />
        
        <?php echo $this->element('../podcasts/_form'); ?>
        <button id="create_podcast" type="submit" class="button blue auto_select_and_submit">Create collection</button>
    </fieldset>
<form>