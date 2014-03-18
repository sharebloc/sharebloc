<?php
require_once(DOCUMENT_ROOT . "/includes/hybridauth/Hybrid/Auth.php");

class ExtAuth {
    static $adapter = null;
    static $profile = null;
    static $hauth = null;
    static $user_data = array();
    static $provider = '';

    public static function connect($provider) {
        self::$provider = $provider;

        try {
            self::$hauth = new Hybrid_Auth(Settings::$HYBRYD_AUTH_CONFIG);
            self::$adapter = self::$hauth->authenticate(self::$provider);
            self::$profile = self::$adapter->getUserProfile();
            self::initUserData();
        } catch (Exception $e) {
            $err_code = $e->getCode();
            Log::$logger->error("Some problem when doing oAuth auth. Exception message is:\n ".$e->getMessage()
                                ."\n, exception code is : \n".$err_code);
            if ($err_code==6 || $err_code==7) {
                if (self::$adapter) {
                    self::$adapter->logout();
                } else {
                    Log::$logger->error("Adapter is empty on oauth logout try.");
                }
            }

            return false;
        }
        return true;
    }

    public static function getAdditionalData() {
        $data = array();
        if (self::$provider==='linkedin') {
            $data = self::getAdditionalLinkedINData();
        } elseif (self::$provider==='twitter') {
            $data = self::getAdditionalTwitterData();
        }
        return $data;
    }

    public static function logout() {
        if (!self::$hauth) {
            self::$hauth = new Hybrid_Auth(Settings::$HYBRYD_AUTH_CONFIG);
        }
        self::$hauth->logoutAllProviders();
    }

    static function initUserData() {
        self::$user_data = self::getUserDataFromHybridAuthObject();

        self::$user_data['provider'] = self::$provider;
        self::$user_data['hauth_info'] = self::$user_data;
        self::$user_data['provider_info'] = array();
        if (self::$provider == 'twitter') {
            self::$user_data['user_contacts'] = array();
        } else {
            self::$user_data['user_contacts'] = self::getContacts();
        }
        self::getAdditionalData();
    }

    static function getUserDataFromHybridAuthObject() {
        $user_data = array();
        $user_data['provider_uid'] = self::$profile->identifier;
        $user_data['profile_url'] = self::$profile->profileURL;
        $user_data['image_url'] = self::$profile->photoURL;
        $user_data['website_url'] = self::$profile->webSiteURL;
        $user_data['display_name'] = self::$profile->displayName;
        $user_data['about'] = self::$profile->description;

        if (self::$provider === 'twitter') {
            $user_data['first_name'] = self::parseFullName(self::$profile->displayName, 'first_name');
            $user_data['last_name'] = self::parseFullName(self::$profile->displayName, 'last_name');
        } else {
            $user_data['first_name'] = self::$profile->firstName;
            $user_data['last_name'] = self::$profile->lastName;
        }

        // the display name is the name we get from HybridAuth, and the full_name is the name concatenated from fname+lname.
        // this is done because FE LI uses lname+fname for display name.
        $user_data['full_name'] = Utils::getFullNameByFNameLName($user_data['first_name'], $user_data['last_name']);

        $user_data['gender'] = self::$profile->gender;
        $user_data['language'] = self::$profile->language;
        $user_data['age'] = self::$profile->age;
        $user_data['birthDay'] = self::$profile->birthDay;
        $user_data['birthMonth'] = self::$profile->birthMonth;
        $user_data['birthYear'] = self::$profile->birthYear;
        $user_data['email'] = self::$profile->email;
        $user_data['emailVerified'] = self::$profile->emailVerified;
        $user_data['phone'] = self::$profile->phone;
        $user_data['address'] = self::$profile->address;
        $user_data['country'] = self::$profile->country;
        $user_data['region'] = self::$profile->region;
        $user_data['city'] = self::$profile->city;
        $user_data['zip'] = self::$profile->zip;
        $user_data['company'] = self::$profile->li_company;
        $user_data['position'] = self::$profile->li_position;

        // is fetched for Twitter but is not currently for LI
        $user_data['location'] = '';

        return $user_data;
    }

    public static function getContacts() {
        $SECONDS_FOR_WARNING = 1;
        $SECONDS_FOR_ERROR = 5;

        $contacts = array();

        $start_ts = microtime(true);

        $data = self::$adapter->getUserContacts();

        foreach ($data as $contact_obj) {
            $contact = array();
            $contact['id'] = $contact_obj->identifier;
            $contact['profile_url'] = $contact_obj->profileURL;
            $contact['website_url'] = $contact_obj->webSiteURL;
            $contact['image_url'] = $contact_obj->photoURL;
            $contact['display_name'] = $contact_obj->displayName;
            $contact['about'] = $contact_obj->description;
            $contact['email'] = $contact_obj->email;

            if ($contact_obj->first_name) {
                $contact['first_name'] = $contact_obj->first_name;
            } else {
                $contact['first_name'] = self::parseFullName($contact_obj->displayName, 'first_name');
            }

            if ($contact_obj->last_name) {
                $contact['last_name'] = $contact_obj->last_name;
            } else {
                $contact['last_name'] = self::parseFullName($contact_obj->displayName, 'last_name');
            }

            if (!$contact['first_name']) {
                continue;
            }

            // the display name is the name we get from HybridAuth, and the full_name is the name concatenated from fname+lname.
            // this is done because FE LI uses lname+fname for display name.
            $contact['full_name'] = Utils::getFullNameByFNameLName($contact['first_name'], $contact['last_name']);

            $contact['local_id'] = User::generateRandomKey();

            $contacts[] = $contact;
        }

        $diff = microtime(true) - $start_ts;
        if ($diff > $SECONDS_FOR_WARNING) {
            $msg = "Too long contact fetching from oAuth, time = " . $diff;
            if ($diff > $SECONDS_FOR_ERROR) {
                Log::$logger->error($msg);
            } else {
                Log::$logger->warn($msg);
            }
        }
        return $contacts;
    }

    /* Getting additional twitter data */
    public static function getTwitterAccountObject() {
        $account = self::$adapter->api()->get('users/show.json',
                                            array('user_id' => self::$profile->identifier));
        return $account;
    }

    public static function getTwitterFollowersObject() {
        $followers = self::$adapter->api()->get('followers/list.json',
                                            array('user_id' => self::$profile->identifier));
        return $followers;
    }

    public static function getTwitterFriendsObject() {
        $friends = self::$adapter->api()->get('friends/list.json',
                                            array('user_id' => self::$profile->identifier));
        return $friends;
    }

    public static function getTwitterFollowers() {
        $followers = array();
        $followers_obj = self::getTwitterFollowersObject();
        foreach ($followers_obj->users as $user_obj) {
            $follower = array();
            $follower['id'] = $user_obj->id;
            $follower['followers_count'] = $user_obj->followers_count;
            $follower['description'] = $user_obj->description;
            $follower['friends_count'] = $user_obj->friends_count;
            $follower['screen_name'] = $user_obj->screen_name;
            $follower['name'] = $user_obj->name;
            $follower['image_url'] = $user_obj->profile_image_url;
            $followers[] = $follower;
        }
        return $followers;
    }

    // friends == following
    public static function getTwitterFriends() {
        $friends = array();
        $friends_obj = self::getTwitterFollowersObject();
        foreach ($friends_obj->users as $user_obj) {
            $friend = array();
            $friend['id'] = $user_obj->id;
            $friend['followers_count'] = $user_obj->followers_count;
            $friend['description'] = $user_obj->description;
            $friend['friends_count'] = $user_obj->friends_count;
            $friend['screen_name'] = $user_obj->screen_name;
            $friend['name'] = $user_obj->name;
            $friend['image_url'] = $user_obj->profile_image_url;
            $friends[] = $friend;
        }
        return $friends;
    }


    public static function getAdditionalTwitterData() {
        $account = self::getTwitterAccountObject();
        self::$user_data['location'] = $account->location;

        // $data['followers'] = self::getTwitterFollowers();
        // $data['friends'] = self::getTwitterFriends();
    }

    /* END OF Getting additional linkedin data */

    public static function getAdditionalLinkedINData() {
        $data = array();
        return $data;
    }

    // parsing: first name - first word; last name - last word. Others words drop.
    private static function parseFullName($string, $what_to_parse) {
        if (!$string) {
            return '';
        }
        $words = explode(' ', $string);

        if (!$words) {
            return '';
        }

        $words_numb = count($words);

        if ($what_to_parse == 'first_name') {
            return $words[0];
        } else {
            if ($words_numb > 1) {
                return $words[$words_numb-1];
            } else {
                return '';
            }
        }

    }
}

?>
