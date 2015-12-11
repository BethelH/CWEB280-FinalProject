<?php

/**
 * Description of PasswordChecker 
 * PasswordChecker allows us to check if an entered username and password
 * is valid
 *
 * @author ins226
 */
class PasswordChecker {
    private $password;
    
    /***
     * Purpose: Create a password checker object
     * and fill an assoc array with usernames and passwords
     */    
    public function __construct() {
    }
    
    /**
     * Purpose: Determine whether the supplied username and password 
     *  combination is valid
     * @param string $username the username to check
     * @param string $password the password to check
     * @return boolean True if the username/password combo is valid
     *  and False otherwise
     */
    public function isValid( $username, $password)
    {
        // Verify that the username exits, and that the supplied password
        // matches the real password
        
        // By default, the combination is not valid
        $valid = FALSE;
        
        // Open a database connection
        $db = new DbObject();
        
        // Query for the password for the specified username
        $qryResults = $db->select( "password", "Member",
                "username='" . $db->escape( $username ) . "'" );
        // If there was one row returned, check the password against
        // the supplied password
        if ( $qryResults->num_rows == 1 )
        {
            $passwordRow = $qryResults->fetch_row();
            if ( password_verify( $password, $passwordRow[0] ) )
            {
                $valid = TRUE;
        
            }
        }
        
        // Return whether the username and password combination is valid
        return $valid;
    }
    
    /**
     * Purpose: Add a user into the password list
     * @param string $username The username to add
     * @param string $password The password associated with the username
     * @return boolean TRUE if the user was successfully added,
     *   FALSE otherwise
     */
    public function addUser( $username, $password, $email, $qst1, $qst1Answer, $qst2, $qst2Answer)
    {
        // Open a database connection
        $db = new DbObject();

        // Create the array to use with the insert method
        $memberRecord["username"] = $username;
        $memberRecord["email"] = $email;
        $memberRecord["password"] = password_hash( $password, PASSWORD_DEFAULT );
        
        $qst1Record["question"] = $qst1;
        $qst1Record["answer"] = $qst1Answer;
        $qst1Record["username"] = $username;
        
        $qst2Record["question"] = $qst2;
        $qst2Record["answer"] = $qst2Answer;
        $qst2Record["username"] = $username;
        
        // Insert the user into the Password database
        $memberNumRows = $db->insert( $memberRecord, "Member" );
        $qst1NumRows = $db->insert( $qst1Record, "ChallengeQuestion" );
        $qst2NumRows = $db->insert( $qst2Record, "ChallengeQuestion" );

        return ( $memberNumRows == 1 && $qst1NumRows == 1 && $qst2NumRows == 1);
    }

}
