<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
*  social login trait
*/
trait Social_login
{
    protected function social_login_init()
    {
        // social login init
        date_default_timezone_set($this->config->item('timezone'));
        $this->load->helper(array('form', 'url', 'cookie'));
        $this->load->library(array('form_validation', 'session', 'email'));
        $this->load->model('Signupmodel', 'signup');  // Load Model
    }
    
    /*
     *  Function is used to login with facebook, google and twitter.
     */
    protected function social_login()
    {
        if ($this->social_login_facebook() == 'logged_in') {
            redirect('login');
        } elseif ($this->social_login_google() == 'logged_in') {
            redirect('login');
        } elseif ($this->social_login_twitter() == 'logged_in') {
            redirect('login');
        } else {
            return [
                'facebook' => $this->social_login_facebook(),
                'google' => $this->social_login_google(),
                'twitter' => $this->social_login_twitter()
            ];
        }
    }

    /*
     * Function is used to login with Facebook.
     */
    protected function social_login_facebook()
    {
        $fb_config = $this->config->item('facebook');
        $this->load->library('Facebook', $fb_config); // Load Facebook Library

        $social_user = $this->facebook->getUser(); // Get Facebook User
        if ($social_user != 0) {
            $fb_config = $this->config->item('facebook');
            $data['user_profile'] = $this->facebook->api('/me?' . http_build_query(array('access_token' => $social_user, 'fields' => $fb_config['fields'])));
            $this->session->set_userdata('user_session', $data['user_profile']); // Created session of FB user info array

            // Create array to save values to register user with you in db.
            $fblogin_id = $data['user_profile']['id'];
            $data = array(
                'loginId' => $fblogin_id,
                'loginType' => '1', // If Login with Fb then loginType = 1
                'name' => $data['user_profile']['name'],
                'firstName' => $data['user_profile']['first_name'],
                'lastName' => $data['user_profile']['last_name'],
                'email' => $data['user_profile']['email'],
                'gender' => $data['user_profile']['gender'],
                'profilePic' => $data['user_profile']['picture']['data']['url'],
                'location' => '',
                'created_on' => date('Y-m-d H:m:s')
            );

            if ($data['email'] == null) {
                $data['email'] = $data['loginId'] . '@facebook.com';
            }

            // Call facebook login function to login/register user and redirect after login.
            if ($this->facebook_login($data, $fblogin_id)) {
                return 'logged_in';
            }
        } else {
            return $this->facebook->getLoginUrl(); // Return FB login link.
        }
    }

    /*
     * Function is used to login with Google.
     */
    public function social_login_google()
    {
        $this->load->library('Google'); // Load Google Library
        $googleobj = new Google();
        $googleuser = $googleobj->googlelog();

        if (isset($_SESSION['google_access_token']) && $_SESSION['google_access_token']) {
            if ($googleuser) {
                $usersesn['email'] = $googleuser->email;
                $usersesn['googleid'] = $googleuser->id;
                $usersesn['name'] = $googleuser->name;
                $usersesn['gender'] = $googleuser->gender;
                $usersesn['picture'] = $googleuser->picture;
                $usersesn['id'] = $googleuser->id;
                $usersesn['familyName'] = $googleuser->familyName;
                $usersesn['givenName'] = $googleuser->givenName;
                $this->session->set_userdata('user_session', $usersesn); // Created session og Google loged in user

                // Create array to save values to register user with you in db.
                if ($googleuser->gender != '') {
                    $gender = $googleuser->gender;
                } else {
                    $gender = '';
                }
                $data = array(
                    'loginId' => $googleuser->id,
                    'loginType' => '2', // If Login with Google then loginType = 2
                    'name' => $googleuser->name,
                    'firstName' => $googleuser->familyName,
                    'lastName' => $googleuser->givenName,
                    'email' => $googleuser->email,
                    'gender' => $gender,
                    'profilePic' => $googleuser->picture,
                    'location' => '',
                    'created_on' => date('Y-m-d H:m:s')
                );

                // Call google_login function to login/register user and redirect after login.
                if ($this->google_login($data, $googleuser->id)) {
                    return 'logged_in';
                }
            }
        } else {
            return $googleuser; // Return Google Login link.
        }
    }

    /*
     * Function is used to login with twitter.
     */
    protected function social_login_twitter()
    {
        $this->load->library('twitter', $this); // Load Twitter Library
        $twitterobj = new Twitter(); // New twitter object
        return $twitterobj->twitterlogin(); // Call twitterlogin function of twitter library
    }
    
    /*
     * Function is used to login with LinkedIn.
     */
    protected function social_login_linkedin($data)
    {
        $this->load->library('Linkedinlib'); //  Load Linkedin Library
        $linkedinobj = new Linkedinlib(); // New twitter object
        $new_linkedinobj = $linkedinobj->linkedinlogin($data); // call to linkedinlogin() function and pass data to the function
        if ($new_linkedinobj) { // If $new_linkedinobj is executed

            /*  Create array of linkedin Response */
            $linkedinId = (string) $new_linkedinobj->{'id'};
            $firstname = (string) $new_linkedinobj->{'first-name'};
            $last_name = (string) $new_linkedinobj->{'last-name'};
            $email = (string) $new_linkedinobj->{'email-address'};
            $pic = (string) $new_linkedinobj->{'picture-url'};
            $loc = (string) $new_linkedinobj->{'location'}->{name};

            $usersesn['fname'] = $firstname;
            $usersesn['lname'] = $last_name;
            $usersesn['email'] = $email;
            $usersesn['profile_pic'] = $pic;
            $usersesn['location'] = $loc;
            $usersesn['linkedinId'] = $linkedinId;
            $this->session->set_userdata('user_session', $usersesn); // Created a session of linkedin response.

            // Create array to save values to register user with you in db.
            $data = array(
                'loginId' => $linkedinId,
                'loginType' => '4', // If Login with Google then loginType = 4
                'name' => $firstname . ' ' . $last_name,
                'firstName' => $firstname,
                'lastName' => $last_name,
                'email' => $email,
                'gender' => '',
                'profilePic' => $pic,
                'location' => $loc,
                'created_on' => date('Y-m-d H:m:s')
            );
            // Call linkedin_login function to login/register user and redirect after login
            if ($this->linkedin_login($data, $linkedinId)) {
                redirect('login');
            }
        }
    }

    /**
     * Function is used for the insert the new user loggdin with Facebook
     */
    protected function facebook_login($data, $fbId)
    {
        $getfbuser = $this->signup->userAlreadyExists($fbId, 1); // Call your model to check if user already register with you.
        if (count($getfbuser) == 0) {
            $user_id = $this->signup->addUser($data); // Call your model to save user info if new user
            if ($this->ion_auth_signup($data)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            if ($this->ion_auth_signin($data)) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Function is used for the insert the new user loggdin with Google
     */
    protected function google_login($data, $googleId)
    {
        $getuser = $this->signup->userAlreadyExists($googleId, 2);  // Call your model to check if user already register with you.
        if (count($getuser) == 0) {
            $user_id = $this->signup->addUser($data); // Call your model to Insert new user
            if ($this->ion_auth_signup($data)) {
                return 2;
            } else {
                return 0;
            }
        } else {
            if ($this->ion_auth_signin($data)) {
                return 2;
            } else {
                return 0;
            }
        }
    }

    /**
     * Function is used for the insert the new user loggdin with LinkedIn
     */
    protected function linkedin_login($data, $linkedinId)
    {
        $getuser = $this->signup->userAlreadyExists($linkedinId, 4); // Call your model to check if user already register with you.
        if (count($getuser) == 0) {
            $user_id = $this->signup->addUser($data); // Call your model to save user info if new user
            if ($this->ion_auth_signup($data)) {
                return 4;
            } else {
                return 0;
            }
        } else {
            if ($this->ion_auth_signin($data)) {
                return 4;
            } else {
                return 0;
            }
        }
    }

     // do signup in ion_auth then do login session
    private function ion_auth_signup($data)
    {
        try {
            $username = $data['firstName'].'-'.$data['lastName'];
            $password = sha1(sha1(sha1(md5($data['email']))));
            $email = $data['email'];
            $additional_data = [
                'full_name' => $data['firstName'].' '.$data['lastName'],
                'additional' => '"on"',
                'active' => 1,
                'photo' => $data['profilePic']
            ];

            $this->ion_auth->register($username, $password, $email, $additional_data);
            $user = $this->db->get_where('users', ['email'=>$email])->row();
            $this->ion_auth->activate($user->id);
            $this->ion_auth->login($email, $password, 0);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function ion_auth_signin($data)
    {
        try {
            $identity = $data['email'];
            $password = sha1(sha1(sha1(md5($data['email']))));
            $remember = 0;

            $userdata = new stdClass();
            $userdata->password = $this->ion_auth->hash_password($password);
            $this->db->where('email', $identity);
            $this->db->update('users', $userdata);
            $this->ion_auth->login($identity, $password, $remember);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
