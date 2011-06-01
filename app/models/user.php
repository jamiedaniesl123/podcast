<?php
class User extends AppModel {

    var $name = 'User';
    var $actsAs = array('Containable');

    var $validate = array(

        'firstname' => array(
            'Rule1' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter the users first name.'
            )
        ),
        'lastname' => array(
            'Rule1' => array(
                'rule' => 'notEmpty',
                'message' => 'Please enter the users last name.'
            )
        ),
        'email' => array(
            'Rule1' => array(
                'rule' => 'email',
                'allowEmpty' => true,
                'message' => 'Please enter a valid email address.'
            )
        ),
        'oucu' => array(
            'Rule1' => array(
                'rule' => 'alphaNumeric',
                'allowEmpty' => false,
                'message' => 'Please enter your oucu.'
            )
        )
    );

    var $hasAndBelongsToMany = array(

        'UserGroups' => array(
            'className' => 'UserGroup',
            'joinTable' => 'user_user_groups',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'user_group_id',
            'unique' => true
        ),
        'UserPodcasts' => array(
            'className' => 'Podcast',
            'joinTable' => 'user_podcasts',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'podcast_id',
            'unique' => true,
            'fields' => 'UserPodcasts.id, UserPodcasts.owner_id, UserPodcasts.title, UserPodcasts.summary, UserPodcasts.image, UserPodcasts.created'
        )
    );

    var $hasMany = array(

        'Podcasts' => array(
            'className' => 'Podcast',
            'foreignKey' => 'owner_id',
            'fields' => array('Podcasts.id', 'Podcasts.title')
        )
    );

    /*
     * @name : __construct
     * @description : Standard model constructor, we are currently using it to define a virtual field of full_name.
     * We define in the constructor so we are able to use an alias such as "Member" as well as "User" as defined in related
     * model associations/joins.
     * @updated : 11th May 2011
     * @by : Charles Jackson
     */
    function __construct($id = false, $table = null, $ds = null) {

            parent::__construct($id, $table, $ds);

            $this->virtualFields['full_name'] = sprintf('CONCAT(%s.firstname, " ", %s.lastname)', $this->alias, $this->alias);
    }

    /*
     * @name : buildUserFromSamsData
     * @description : Called from the users/registration action when a new user agrees to the
     * terms and conditions. This method will build an array based on SAMS data as defined
     * in the bootstrap.php file
     * @updated : 10th May 2011
     * @by : Charles Jackson
     */
    function buildUserFromSamsData( $data = array() ) {

		$name = array();
		$name = explode(" ", SAMS_NAME );

        $data['User']['firstname'] = $name[0];
        $data['User']['lastname'] = $name[1];
        $data['User']['email'] = SAMS_EMAIL;
        $data['User']['oucu'] = SAMS_OUCU_ID;

        return $data;
    }


    /*
     * @name : getDashboardData
     * @description : Return data for an individual user.
     * @updated : 20th March 2011
     * @by : Charles Jackson
     */
    function getDashboardData( $user_id ) {

        return $this->find('first', array(
            'conditions' => array(
                'User.id' => $user_id
            ),
            'fields'=>array(
                'User.*'
            ),
            'contain' =>array(
                'UserGroups' => array(
                    'fields' => array(
                        'UserGroups.id'
                        )
                    ),
                'Podcasts' => array(
                    'fields' => array(
                        'Podcasts.id'
                        )
                    )
                )
            )
        );
    }
}
?>
