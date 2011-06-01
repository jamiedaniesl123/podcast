<fieldset class="podcasts index">
    <legend>All Podcasts</legend>
    <p>
        Below is a list of all podcasts on the system.
    </p>
    <?php echo $this->element('../podcasts/_filter'); ?>
    <p>
        <?php
            echo $this->Paginator->counter(array(
            'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
            ));
        ?>
    </p>
    <form method="post" action="/admin/podcasts/delete">
        <a href="/" class="toggler" data-status="unticked">Toggle</a>
        <button type="submit" onclick="return confirm('Are you sure you wish to delete all these podcasts and associated media?')"><span>delete</span></button>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>Select</th>
                <th>Image</th>
                <th><?php echo $this->Paginator->sort('Owner', 'user_id');?></th>
                <th><?php echo $this->Paginator->sort('title');?></th>
                <th><?php echo $this->Paginator->sort('created');?></th>
                <th><?php echo $this->Paginator->sort('Status','deleted');?></th>
                <th><?php echo $this->Paginator->sort('Media',count('PodcastItems') );?></th>
                <th class="actions"><?php __('Actions');?></th>
            </tr>
            <?php
            if( isSet( $this->data['Podcasts'] ) ) :
                $i = 0;
                foreach ($this->data['Podcasts'] as $podcast ) :

                    $class = null;
                    if ($i++ % 2 == 0) :
                        $class = ' class="altrow"';
                    endif;
            ?>
                    <tr<?php echo $class;?>>
                        <td>
                            <input type="checkbox" name="data[Podcast][Checkbox][<?php echo $podcast['Podcast']['id']; ?>]" class="podcast_selection" id="PodcastCheckbox<?php echo $podcast['Podcast']['id']; ?>">
                        </td>
                        <td>
                            <?php echo $this->Attachment->getPodcastThumbnail( $podcast ); ?>
                        </td>
                        <td>
                            <?php echo $podcast['Owner']['full_name']; ?>
                        </td>
                        <td>
                            <?php echo $podcast['Podcast']['title']; ?>
                        </td>
                        <td>
                            <?php echo $this->Time->getPrettyShortDate( $podcast['Podcast']['created'] ); ?>
                         </td>
                        <td>
                            <?php echo $this->Object->isDeleted( $podcast['Podcast'] ) ? 'Deleted' : 'Active'; ?>
                        </td>
                        <td>
                            <?php echo count( $podcast['PodcastItems'] ); ?>
                        </td>
                        <td class="actions">
                            <?php if( $this->Object->isDeleted( $podcast['Podcast'] ) ) : ?>
                                <a href="/admin/podcasts/restore/<?php echo $podcast['Podcast']['id']; ?>" id="restore_podcast_<?php echo $podcast['Podcast']['id']; ?>">restore</a>
                            <?php endif; ?>
                            <a href="/admin/podcasts/view/<?php echo $podcast['Podcast']['id']; ?>" id="view_podcast_<?php echo $podcast['Podcast']['id']; ?>">view</a>
                            <a href="/admin/podcasts/edit/<?php echo $podcast['Podcast']['id']; ?>" id="edit_podcast_<?php echo $podcast['Podcast']['id']; ?>">edit</a>
                            <a href="/admin/podcasts/delete/<?php echo $podcast['Podcast']['id']; ?>" id="delete_podcast_<?php echo $podcast['Podcast']['id']; ?>" onclick="return confirm('Are you sure you with to delete this podcast and all associated media? This action cannot be undone.');">delete</a>
                            <a href="/admin/podcast_items/index/<?php echo $podcast['Podcast']['id']; ?>" id="view_media_<?php echo $podcast['Podcast']['id']; ?>">media</a>
                        </td>
                    </tr>
            <?php
                endforeach;
            endif; ?>
        </table>
    </form>
    <div class="paging">
            <?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
     | 	<?php echo $this->Paginator->numbers();?>
            <?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
    </div>
</fieldset>