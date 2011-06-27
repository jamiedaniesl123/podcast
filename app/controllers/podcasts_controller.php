<?php

class PodcastsController extends AppController {

    var $name = 'Podcasts';
    var $components = array( 'Image' );
    private $errors = array();
    var $html = null; // Used to store errors created by the images component.
    
    const YES = 'Y';
    const NO = 'N';
    const ITUNES = 'ITUNES';
    const YOUTUBE = 'YOUTUBE';

    var $paginate = array( 'limit' => 20, 'page' => 1, 'order' => 'Podcast.id DESC' );

    /*
     * @name : beforeRender
     * @description : The beforeRender action is automatically called after the controller action has been executed and before the screen
     * is rendered. 
     * @updated : 5th May 2011
     * @by : Charles Jackson
     */
    function beforeRender() {

        // If there are any errors assign them to the view
        if( count( $this->errors ) )
            $this->set('errors', $this->errors );
    }

    /*
     * @name : index
     * @desscription : Displays a paginated list of all podcasts that are owned by the current user.
     * @name : Charles Jackson
     * @by : 16th May 2011
     */
    function index() {

        $id_numbers = array();
        
        // Unset this join else we will get duplicate rows on the various joins.
        unset( $this->Podcast->hasOne['UserPodcast'] );
        

        // Have they posted the filter form?
        if( isSet( $this->data['Podcast']['filter'] ) == false )
            $this->data['Podcast']['filter'] = null;

        $id_numbers = $this->Podcast->getUserPodcasts( $this->Session->read('Auth.User.id'), $this->data['Podcast']['filter'] );

        $this->Podcast->recursive = 2;
        $this->data['Podcasts'] = $this->paginate('Podcast', array('Podcast.id' => $id_numbers ) );

    }

    /*
     * @name : add
     * @desscription : Displays a form that enables peeps to add a row to the podcasts table. If the form is populated
     * it will validate and save the data.
     * @name : Charles Jackson
     * @by : 4th May 2011
     */
    function add() {

        if ( !empty( $this->data ) ) {

            // Assign the podcast to the current user.
            $this->data['Podcast']['owner_id'] = $this->Session->read('Auth.User.id');
            $this->data['Podcast']['private'] = self::YES;
			
            $this->Podcast->set( $this->data );

            if( $this->Podcast->saveAll() ) {

				$this->data['Podcast']['custom_id'] = $this->Podcast->getLastInsertId().'_'.$this->Podcast->buildSafeFilename( $this->data['Podcast']['title'] );
	            $this->Podcast->save( $this->data );
				
                $this->redirect( array( 'action' => 'view', $this->Podcast->getLastInsertId() ) );

            } else {

                // Rebuild the dynamic select boxes according to the users current selections else they will merely display ID numbers.
                $this->data = $this->Podcast->rebuild( $this->data );

                $this->errors = $this->Podcast->invalidFields( $this->data );
                $this->Session->setFlash('Could not create your collection. Please see issues listed below.', 'default', array( 'class' => 'error' ) );
            }
        }
    }

    /*
     * @name : view
     * @desscription : Enables a moderator or member to view details of an individual podcast.
     * as a moderator.
     * @name : Charles Jackson
     * @by : 20th May 2011
     */
    function view( $id = null ) {

        $this->Podcast->recursive = 2;
        // They are loading the page, get the data using the $id passed as a parameter.
        $this->data = $this->Podcast->findById( $id );

        // We did not find the podcast, error and redirect.
        if( empty( $this->data ) || $this->Permission->toView( $this->data ) == false ) {

            $this->Session->setFlash( 'Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
            $this->redirect( $this->referer() );
        }
    }

    /*
     * @name : justification
     * @desscription : Enables an itunes or youtube user to view details of a collection and the associated justifiication
     * for inclusion on youtube or itunesu.
     * @name : Charles Jackson
     * @by : 22nd June 2011
     */
    function justification( $id = null ) {

        $this->Podcast->recursive = 2;
        // They are loading the page, get the data using the $id passed as a parameter.
        $this->data = $this->Podcast->findById( $id );

        // We did not find the podcast, error and redirect.
        if( empty( $this->data ) || $this->Permission->isItunesUser() == false || $this->Permission->isYoutubeUser() == false ) {

            $this->Session->setFlash( 'Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
            $this->redirect( $this->referer() );
        }
    }

    /*
     * @name : edit
     * @desscription : Displays a form that enables peeps to edit an existing row on the podcasts table.
     * @name : Charles Jackson
     * @by : 4th May 2011
     */
    function edit( $id = null ) {

        $this->Podcast->recursive = 2;
        
        if ( !empty( $this->data ) ) {

            $this->Podcast->begin(); // begin a transaction so we may rollbaack if anything fails.
            
            // Save this->data into a local array so we can rebuild the form if any of the validation fails and
            // we are required to rollback the database.
            $data = array();
            $data = $this->data;

            $this->data = $this->Podcast->unsetAttachments( $this->data );

            // Delete any existing hasMany moderators.
            $this->Podcast->deleteExistingModerators( $this->data['Podcast']['id'] );
            // Create the PodcastModerators that are saved using a hasMany relationship
            $this->data = $this->Podcast->createPodcastModerators( $this->data );
            // Create the ModeratorUserGroups that are saved using a hasMany relationship
            $this->data = $this->Podcast->createModeratorUserGroups( $this->data );
            // Set the preferred node to equal the first node chosen
            $this->data = $this->Podcast->setPreferredNode( $this->data );

            $this->Podcast->set( $this->data );

            if( $this->Podcast->saveAll() ) {

                // OK, it validates but have they changed/confirmed ownership.
                if( $this->Podcast->unconfirmedChangeOfOwnership( $this->data ) ) {

                    $this->Podcast->rollback(); // We rollback the data because they still need to confirm this update
                    $this->data = $this->Podcast->rebuild( $data );
                    $this->data['Podcast']['confirmed'] = true;
                    $this->Session->setFlash('You are changing ownership of this podcast. Submit again to confirm the change.', 'default', array( 'class' => 'alert' ) );

                } else {

                    // Now copy back the original including array elements and
                    // save again with attachment elements.
                    $this->data = $data;
                    if ( $this->__updateImages() == false ) {

                        $this->Session->setFlash('We were unable to upload all your images.', 'default', array( 'class' => 'error' ) );
                        $this->data = $this->Podcast->rebuild( $data );
                        $this->Podcast->rollback();

                    } else {

                        // Generate the RSS Feeds.
                        if( $this->__generateRSSFeeds( $this->data['Podcast']['id'] )  == false ) {
                        
                            $this->Session->setFlash('We were unable to generate the RSS feeds. If the problem continues please alert an administrator', 'default', array( 'class' => 'error' ) );
                            $this->data = $this->Podcast->rebuild( $data );
                            $this->Podcast->rollback();

                        } else {

                            $this->Podcast->commit(); // Everything hunky dory, commit the changes.
                            $this->Session->setFlash('Your collection has been successfully updated.', 'default', array( 'class' => 'success' ) );

							$this->Podcast->recursive = 2; // Increase the recursive level so we retrieve enough information to check permissions.
                            $this->data = $this->Podcast->findById( $this->data['Podcast']['id'] );

                            // They may no longer have permision to view this podcast if they have changed ownership, therefore double-check.
                            if( $this->Permission->toView( $this->data ) ) {

                                $this->redirect( array( 'action' => 'view', $this->data['Podcast']['id'] ) );
								exit;

                            } else {

                                $this->redirect( array( 'action' => 'index') );
								exit;
                            }
                        }
                    }
                }

            } else {

                $this->Podcast->rollback();
                
                // Rebuild the dynamic select boxes according to the users current selections else they will merely display ID numbers.
                $this->data = $this->Podcast->rebuild( $data );
                $this->errors = $this->Podcast->invalidFields( $this->data );

                // We explicitly set confirmed to false incase they have confirmed/failed validation in a single post (silly billy).
                $this->data['Podcast']['confirmed'] == false;

                $this->Session->setFlash('Could not update your collection. Please see issues listed below.', 'default', array( 'class' => 'error' ) );
            }

        } else {

            $this->data = $this->Podcast->findById( $id );

            // We did not find the podcast, redirect.
            if( empty( $this->data ) || $this->Permission->toUpdate( $this->data ) == false ) {

                $this->Session->setFlash('Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
                $this->redirect( $this->referer() );

            } else {
                
                // We need to track is the ownership changes so make a note here and the original owner with be passed as a
                // hidden form element.
                $this->data['Podcast']['current_owner_id'] = $this->data['Podcast']['owner_id'];
            }
        }
		
        // Need to retrieve form options such as additional users and catagories etc... on the system.
        $this->__setPodcastDefaults();
    }

    /*
     * @name : itunes
     * @desscription : Displays a form that enables peeps to edit itunes details of a collection
     * @name : Charles Jackson
     * @by : 22nd June 2011
     */
    function itunes( $id = null ) {

        $this->Podcast->recursive = 2;

        if ( !empty( $this->data ) ) {

            $this->Podcast->begin();
            $this->Podcast->set( $this->data );

            if( $this->Podcast->saveAll() && $this->__generateRSSFeeds( $this->data['Podcast']['id'] ) ) {

                $this->Podcast->commit();
                $this->Session->setFlash('Itunes details have been successfully updated and RSS feeds refreshed.', 'default', array( 'class' => 'success' ) );
                $this->redirect( array( 'action' => 'justification', $this->data['Podcast']['id'] ) );

            } else {

                $this->Podcast->rollback();
                $this->data = $this->Podcast->rebuild( $this->data );
                $this->errors = $this->Podcast->invalidFields( $this->data );
                $this->Session->setFlash('Could not update your collection. If the problem persists please contact an administrator.', 'default', array( 'class' => 'error' ) );
            }

        } else {

            $this->data = $this->Podcast->findById( $id );

            // We did not find the podcast, redirect.
            if( empty( $this->data ) || $this->Permission->isItunesUser() == false ) {

                $this->Session->setFlash('Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
                $this->redirect( $this->referer() );
            }
        }

        // Need to retrieve form options such as additional users and catagories etc... on the system.
        $this->__setPodcastDefaults();
    }

    /*
     * @name : delete
     * @desscription : Enables a user to perform a soft delete on a podcast and the associated media if they are the current owner.
     * @name : Charles Jackson
     * @by : 19th May 2011
     */
    function delete( $id = null ) {

        $this->autoRender = false;
        $this->recursive = -1;

        // This method is used for individual deletes and deletions via the form posted checkbox selection. Hence
        // when somebody is deleting an individual podcast we pass into an array and loop through as is the data
        // was posted.
        if( $id )
            $this->data['Podcast']['Checkbox'][$id] = true;

        foreach( $this->data['Podcast']['Checkbox'] as $key => $value ) {

            $this->podcast = $this->Podcast->find('first', array( 'conditions' => array( 'Podcast.id' => $key, 'Podcast.owner_id' => $this->Session->read('Auth.User.id' ) ) ) );

            $this->Podcast->begin();

            // Delete the podcast
            $this->podcast['Podcast']['deleted'] = true;
            $this->Podcast->set( $this->podcast );
            $this->Podcast->save( $this->podcast );

            // We only perform a soft delete hence we write a .htaccess file that will produce a "404 - Not Found" and transfer to media server.
            if( $this->Folder->createHtaccess( $this->podcast ) && $this->Api->transferFileMediaServer( 
				array( 
					'source_path' => $data['Podcast']['custom_id'].'/',
					'target_path' => $data['Podcast']['custom_id'].'/', 
					'filename' => '.htaccess' 
					)
				) ) {						
				
                $this->Podcast->commit();
                $this->Session->setFlash('We successfully deleted the podcast and all associated media.', 'default', array( 'class' => 'success' ) );

            } else {

                $this->Podcast->rollback();
                $this->Session->setFlash('We could not delete all associated media. If the problem persists please alert an administrator.', 'default', array( 'class' => 'error' ) );
                break; // Break out of the loop
            }
        }
        
        $this->redirect( $this->referer() );
    }

   /*
     * @name : approve
     * @desscription : Displays a paginated list of all podcasts currently on the system that are waiting to be approved
     * for either itunes or youtube
     * @name : Charles Jackson
     * @by : 20th June 2011
     */
    function approve() {

        unset( $this->Podcast->hasOne['UserPodcast'] );
        
        $conditions = $this->Podcast->waitingApproval();
        $this->data['Podcasts'] = $this->paginate('Podcast', $conditions );

    }

    /*
     * @name : approval
     * @description : Enables an approver to update the status flags for itunes and youtube to 'Y', in essence
     * approving them.
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function approval( $media_channel, $id ) {

        $this->Podcast->recursive = -1;
        $this->data = $this->Podcast->findById( $id );

        if( !empty( $this->data ) ) {

            if( strtoupper( $media_channel ) == self::ITUNES )
                $this->data['Podcast']['intended_itunesu_flag'] = self::YES;
            
            if( strtoupper( $media_channel ) == self::YOUTUBE )
                $this->data['Podcast']['intended_youtube_flag'] = self::YES;

            $this->data['Podcast']['owner_id'] = $this->Session->read('Auth.User.id');
            
            $this->Podcast->save( $this->data );
            $this->Session->setFlash('The collection has been approved.', 'default', array( 'class' => 'success' ) );

        } else {

            $this->Session->setFlash('We could not find the collection.', 'default', array( 'class' => 'error' ) );

        }

        $this->redirect( '/podcasts/approve' );
    }

    /*
     * @name : rejection
     * @description : Enables a user to update the 'intended' status flags for itunes and youtube to 'N', in essence
     * rejecting them.
     * @updated : 20th June 2011
     * @by : Charles Jackson
     */
    function rejection( $media_channel, $id ) {

        $this->Podcast->recursive = -1;
        $this->data = $this->Podcast->findById( $id );

        if( !empty( $this->data ) ) {

            if( strtoupper( $media_channel ) == self::ITUNES ) {
                $this->data['Podcast']['intended_itunesu_flag'] = self::NO;
                $this->data['Podcast']['consider_for_itunesu'] = false;
            }
            
            if( strtoupper( $media_channel ) == self::YOUTUBE ) {
                $this->data['Podcast']['intended_youtube_flag'] = self::NO;
                $this->data['Podcast']['consider_for_youtube'] = false;
            }

            $this->Podcast->save( $this->data );
            $this->Session->setFlash('The collection has been rejected.', 'default', array( 'class' => 'success' ) );

        } else {

            $this->Session->setFlash('We could not find the collection.', 'default', array( 'class' => 'error' ) );
        }

        $this->redirect( '/podcasts/approve' );
    }

    /*
     * ADMIN FUNCTIONALITY
     * Below this line are the administration functionality that can only be reach if the flag 'administrator' is set to true on the
     * users profile. The URL for all admin routes is "admin/:controller:/:action:/*
     */
    
    /*
     * @name : admin_index
     * @desscription : Displays a paginated list of all podcasts currently on the system.
     * @name : Charles Jackson
     * @by : 4th May 2011
     */
    function admin_index() {

        unset( $this->Podcast->hasOne['UserPodcast'] );
        
        // Have they posted the filter form?
        if( isSet( $this->data['Podcast']['filter'] ) ) {

            $conditions = $this->Podcast->buildFilters( $this->data['Podcast']['filter'] );

            $this->data['Podcasts'] = $this->paginate('Podcast', $conditions );

        } else {

            // Create a null PodcastFilter to prevent an unwanted notice in the view
            $this->data['Podcast']['filter'] = null;
            $this->data['Podcasts'] = $this->paginate('Podcast');
        }
    }


    /*
     * @name : admin_add
     * @description : Displays a form that enables administrators to add a row to the podcasts table. If the form is
     * populated it will validate the data and save if possible.
     * @name : Charles Jackson
     * @by : 5th May 2011
     */
    function admin_add() {

        if ( !empty( $this->data ) ) {

            // Assign the podcast to the current user.
            $this->data['Podcast']['owner_id'] = $this->Session->read('Auth.User.id');
            $this->data['Podcast']['private'] = self::YES;

            $this->Podcast->set( $this->data );

            if( $this->Podcast->saveAll() ) {

				$this->data['Podcast']['custom_id'] = $this->Podcast->getLastInsertId().'_'.$this->Podcast->buildSafeFilename( $this->data['Podcast']['title'] );
	            $this->Podcast->save( $this->data );
                
                $this->redirect( array( 'action' => 'admin_view', $this->Podcast->getLastInsertId() ) );

            } else {

                // Rebuild the dynamic select boxes according to the users current selections else they will merely display ID numbers.
                $this->data = $this->Podcast->rebuild( $this->data );

                $this->errors = $this->Podcast->invalidFields( $this->data );
                $this->Session->setFlash('Could not create your collection. Please see issues listed below.', 'default', array( 'class' => 'error' ) );
            }
        }
    }

    /*
     * @name : admin_view
     * @desscription : Enables an adminitrator to view details of an individual podcast.
     * @name : Charles Jackson
     * @by : 20th May 2011
     */
    function admin_view( $id = null ) {

        // They are loading the page, get the data using the $id passed as a parameter.
        $this->data = $this->Podcast->findById( $id );

        // We did not find the podcast, error and redirect.
        if( empty( $this->data ) ) {

            $this->Session->setFlash( 'Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
            $this->redirect( $this->referer() );
        }

    }

    /*
     * @name : admin_edit
     * @desscription : Displays a form that enables administrator to edit an existing row on the podcasts table.
     * @name : Charles Jackson
     * @by : 4th May 2011
     */
    function admin_edit( $id = null ) {

        if ( !empty( $this->data ) ) {

            $this->Podcast->begin();
            
            // Save this->data into a local array called data so we may unset the attachment array elements before
            // validating else it will fail because they contain arrays.
            $data = array();
            $data = $this->data;

            $this->data = $this->Podcast->unsetAttachments( $this->data );

            // Delete any existing hasMany moderators.
            $this->Podcast->deleteExistingModerators( $this->data['Podcast']['id'] );

            // Create the PodcastModerators that are saved using a hasMany relationship
            $this->data = $this->Podcast->createPodcastModerators( $this->data );
            // Create the ModeratorUserGroups that are saved using a hasMany relationship
            $this->data = $this->Podcast->createModeratorUserGroups( $this->data );
            // Set the preferred node to equal the first node chosen
            $this->data = $this->Podcast->setPreferredNode( $this->data );

            $this->Podcast->set( $this->data );

            if(  $this->Podcast->saveAll()  ) {

                // Now copy back the original including array elements and
                // save again with attachment elements.
                $this->data = $data;
                if ( $this->__updateImages() == false ) {

                    $this->Session->setFlash('We were unable to upload all your images.', 'default', array( 'class' => 'error' ) );
                    $this->data = $this->Podcast->rebuild( $data );
                    $this->Podcast->rollback();

                } else {

                    // Generate the RSS Feeds.
                    if( $this->__generateRSSFeeds( $this->data['Podcast']['id'] )  == false ) {

                        $this->Session->setFlash('We were unable to generate the RSS feeds. If the problem continues please alert an administrator', 'default', array( 'class' => 'error' ) );
                        $this->data = $this->Podcast->rebuild( $data );
                        $this->Podcast->rollback();

                    } else {

                        $this->Podcast->commit(); // Everything hunky dory, commit the changes.
                        $this->Session->setFlash('Your collection has been successfully updated.', 'default', array( 'class' => 'success' ) );
                    }
                }

            } else {

                $this->Podcast->rollback();
                
                // Rebuild the dynamic select boxes according to the users current selections else they will merely display ID numbers.
                $this->data = $this->Podcast->rebuild( $this->data );

                $this->errors = $this->Podcast->invalidFields( $this->data );
                $this->Session->setFlash('Could not update your collection. Please see issues listed below.', 'default', array( 'class' => 'error' ) );
            }

        } else {

            $this->data = $this->Podcast->findById( $id );
            
            // We did not find the podcast, redirect.
            if( empty( $this->data ) ) {

                $this->Session->setFlash('Could not find your collection. Please try again.', 'default', array( 'class' => 'error' ) );
                $this->redirect( $this->referer() );
            }
        }

        // Need to retrieve form options such as additional users and catagories etc... on the system.
        $this->__setPodcastDefaults();
    }
 
    /*
     * @name : admin_delete
     * @desscription : Enables an administrator to performa hard delete on a podcast and the associated media.
     * @name : Charles Jackson
     * @by : 5th May 2011
     */
    function admin_delete( $id = null ) {

        $this->autoRender = false;
        $this->recursive = -1;

        // This method is used for individual deletes and deletions via the form posted checkbox selection. Hence
        // when somebody is deleting an individual podcast we pass into an array and loop through as if the data
        // was posted via a form.
        if( $id )
            $this->data['Podcast']['Checkbox'][$id] = true;

        foreach( $this->data['Podcast']['Checkbox'] as $key => $value ) {

            $this->podcast = $this->Podcast->findById( $key );
        
            // If we did no find the podcast that redirect to the referer.
            if( empty( $this->podcast ) == false ) {


				// Hard delete this podcast by deleting the whole folder structure.
				if( $this->Api->deleteFolderOnMediaServer( array( 
					array( 
						'source_path' => $podcast['Podcast']['custom_id'].'/',
						)
					)
				) ) {

                    // Delete the podcast
                    $this->Podcast->delete( $this->podcast['Podcast']['id'] );
                    $this->Session->setFlash('We successfully deleted the collection and all associated media.', 'default', array( 'class' => 'success' ) );

                } else {

                     $this->Session->setFlash('We were unable to delete the collection from the media server. Please try again.', 'default', array( 'class' => 'error' ) );
                }
            }
        }
        
        $this->redirect( $this->referer() );
    }

    /*
     * @name : admin_restore
     * @description : Will update the value of the 'deleted' column on any podcast to zero therefore restoring the
     * collection.
     * @updated : 1st June 2011
     * @by : Charles Jackson
     */
    function admin_restore( $id = null ) {

        $this->autoRender = false;
        $this->data = $this->Podcast->findById( $id );

        if( empty( $this->data ) ) {

            $this->Session->setFlash('We could not identify the collection you are trying to restore.', 'default', array( 'class' => 'error' ) );

        } else {

			// The file has only been 'soft' deleted by writing a .htaccess file. To retrore the file we merely delete the .htaccess
            if( $this->Api->deleteFileOnMediaServer( array( 
				array( 
					'source_path' => $this->data['Podcast']['custom_id'].'/',
					'filename' => '.htaccess'
					)
				)
			) ) {
                
                $this->data['Podcast']['deleted'] = false;
                $this->Podcast->set( $this->data );
                $this->Podcast->save();
                
                $this->Session->setFlash('We successfully restored the collection and all associated media.', 'default', array( 'class' => 'success' ) );

            } else {

                $this->Session->setFlash('We were unable to restore the collection. Please try again.', 'default', array( 'class' => 'error' ) );
            }

        }

        $this->redirect( $this->referer() );
    }

    // PRIVATE METHODS
    // Below this line are methods that can only be called by another controller method. In traditional MVC these
    // methods should exist as functions in the model however, they exploit various components that are more elegantly
    // accessed via the controller hence I have left them here. Not a perfect world!

    /*
     * @name : __updateImages
     * @description : Internal method called by the add and edit methods, both user and administrator.
     * @updated : 9th May 2011
     * @by : Charles Jackson
     */
    function __updateImages() {

        // Try to upload the associated images and transfer to the media server. If successful the upload component will return the name
        // of the uploaded file else it will return false.
		if( isSet($this->data['Podcast']['image'] ) )
	        $this->data['Podcast']['image'] = $this->Image->uploadPodcastImage( $this->data, 'image' );
			
		if( isSet($this->data['Podcast']['image_logoless'] ) )			
	        $this->data['Podcast']['image_logoless'] = $this->Image->uploadLogolessPodcastImage( $this->data, 'image_logoless' );
			
		if( isSet($this->data['Podcast']['image_wide'] ) )			
	        $this->data['Podcast']['image_wide'] = $this->Image->uploadWidePodcastImage( $this->data, 'image_wide' );

        // Check to see if the upload component created any errors.
        if( $this->Image->hasErrors() ) {
			
            $this->errors = $this->Image->getErrors();
            return false;

        } else {

            // Resave the object so we capture the names of the uploaded images.
            $this->Podcast->save( $this->data['Podcast'] );
            return true;
        }
    }

    /*
     * @name : __generateRSSFeeds
     * @description : Will retrieve the podcast passed as an ID and try to generate RSS feeds if needed. Returns a bool.
     * @updated : 23rd June 2011
     * @by : Charles Jackson
     */
    function __generateRSSFeeds( $id = null ) {

        $podcast = null;

        $this->Podcast->recursive = -1; // Minimise the amount of data we retrieve.
        $podcast = $this->Podcast->findById( $id );

        if( empty( $podcast ) )
            return false;

        if( $podcast['Podcast']['podcast_flag'] == true ) {

            // Generate the RSS Feeds by calling the "/feeds/add/*ID*" URL.
            return $this->requestAction( array('controller' => 'feeds', 'action' => 'add'), array('id' => $podcast['Podcast']['id'] ) );
        }

        return true; // No RSS Feed needed so return a default of true to signify everything OK.
    }

    /*
     * @name : setDefaults
     * @description : Called prior to various methods when the user can update a podcast. This method will sanatize data prior to being
     * shown on a form such as retrieving all users on the system as a list for possible membership of a podcast then removing any people
     * that are already members of the aforementioned podcast.
     * @updated : 23rd June 2011
     * @by : Charles Jackson
     */
    function __setPodcastDefaults() {

        // Get all the nodes
        $this->Node = ClassRegistry::init('Node');
        $this->nodes = $this->Node->find('list', array( 'order' => 'Node.title' ) );
        $this->nodes = $this->Node->removeDuplicates( $this->nodes, $this->data, 'Nodes' );
        $this->set('nodes', $this->nodes );

        // Get all the categories
        $this->Category = ClassRegistry::init('Category');
        $this->categories = $this->Category->find('list', array( 'fields' => array('Category.id', 'Category.category'), 'order' => array('Category.category') ) );
        $this->categories = $this->Category->removeDuplicates( $this->categories, $this->data, 'Categories' );
        $this->set('categories', $this->categories );

        // Get all the itunes categories
        $this->ItunesuCategory = ClassRegistry::init('ItunesuCategory');
        $this->itunesu_categories = $this->ItunesuCategory->find('list', array( 'fields' => array('ItunesuCategory.id', 'ItunesuCategory.code_title'), 'order' => array('ItunesuCategory.code_title') ) );
        $this->itunesu_categories = $this->ItunesuCategory->removeDuplicates( $this->itunesu_categories, $this->data, 'iTuneCategories' );
        $this->set('itunes_categories', $this->itunesu_categories );

        // Get all the languages
        $this->Language = ClassRegistry::init('Language');
        $this->set('languages', $this->Language->find('list', array( 'fields' => array('Language.lang_code', 'Language.language'), 'order' => 'Language.language' ) ) );

        // Get all the user groups
        $this->UserGroup = ClassRegistry::init('UserGroup');
        $this->user_groups = $this->UserGroup->find('list', array( 'fields' => array('UserGroup.id', 'UserGroup.group_title'), 'order' => array('UserGroup.group_title') ) );
        $this->user_groups = $this->UserGroup->removeDuplicates( $this->user_groups, $this->data, 'MemberGroups' );
        $this->user_groups = $this->UserGroup->removeDuplicates( $this->user_groups, $this->data, 'ModeratorGroups' );
        $this->set('user_groups', $this->user_groups );

        // Get all the user groups
        $this->User = ClassRegistry::init( 'User' );
        $this->users = $this->User->find( 'list', array( 'fields' => array( 'User.id', 'User.full_name' ), 'order' => 'User.full_name ASC' ) );
        $this->users = $this->User->removeDuplicates( $this->users, $this->data, 'Members' );
        $this->users = $this->User->removeDuplicates( $this->users, $this->data, 'Moderators' );
        $this->set('users', $this->users );

        $this->set('all_users', $this->User->find('list', array( 'fields' => array('User.id', 'User.full_name' ), 'order' => 'User.full_name ASC' ) ) );

        // Set the possible values of explicit
        $this->set( 'explicit', array( 'clean' => 'clean', 'no' => 'no', 'yes' => 'yes' ) );
    }
}
