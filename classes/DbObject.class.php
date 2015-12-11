<?php

/**
 * The dbObject class represents a connection to a MySQL server.
 * With objects of this class, we'll be able to create and execute
 * query statements. This is a convenience class, wrapped around
 * the mysqli object.
 *
 * @author ins214
 */
class DbObject
{
    /**
     * @var mysqli The database connection
     */
    private $dbConnect;
    
    /**
     * Purpose: To create a connection to a MySQL server and
     *   open a database on that server.
     * @param string $server - name of the MySQL server
     * @param string $user - name of the MySQL user
     * @param string $password - user's password
     * @param string $schema - name of the schema to use
     */
    public function __construct( $server = "kelcstu06",
            $user = "CST228", $password = "CBLUFN", $schema = "CST228" )
    {
        // Create a mysqli object, and assign it to the internal attribute
        $this->dbConnect = @new mysqli( $server, $user, $password, $schema );
        
        // IF the connection failed
        if ( $this->dbConnect->connect_error )
        {
            // Display an error message
            // Exit
            die( "<p>Failed to connect to database: " .
                    $this->dbConnect->connect_error . "</p>\n" );
        }
    }
    
    /**
     * Purpose: Close the database connection.
     * The destructor gets called when the object goes out of scope
     * (function terminates, program ends) or is destroyed.  Rather than
     * having a separate close method (which might be a good idea anyways,
     * because then we can close early if we want), we'll just close the
     * connection here.
     */
    public function __destruct()
    {
        $this->dbConnect->close();
    }
    
    /**
     * Purpose: This routine will run the query that is provided by the query
     *   string that is passed in.  If the query fails, we will stop processing
     *   and exit the PHP interpreter immediately.
     * @param string $qryString The SQL query string that is to be run
     * @return mysqli_result The result of the query if the query is SELECT,
     *   DESCRIBE, EXPLAIN, or SHOW, or true otherwise
     */
    public function runQuery( $qryString )
    {
        // Execute the query
        $qryResult = $this->dbConnect->query( $qryString );
        // Return the result of the query
        return $qryResult;
    }
    
    /**
     * Purpose: Perform a SELECT query on the database
     * @param string $columnList List of columns to be selected
     * @param string $tableList List of tables to select from
     * @param string $condition Optional SQL condition to select with
     * @param string $sort Optional SQL sort statement to apply
     * @param string $other Optional any other SQL clauses to apply
     * @return mysqli_result The result of the SELECT query
     */
    public function select( $columnList, $tableList,
            $condition="", $sort="", $other="" )
    {
        // Create the basic SELECT statement
        $qryStmt = "SELECT $columnList FROM $tableList";
        
        // If a condition is specified, add it to the query
        if ( $condition != "" )
        {
            $qryStmt .= " WHERE $condition";
        }
        
        // If a sort order is specified, add it to the query
        if ( $sort != "" )
        {
            $qryStmt .= " ORDER BY $sort";
        }
        
        // Add any other SQL clauses if they've been specified
        if ( $other != "" )
        {
            $qryStmt .= " $other";
        }
        // Execute the query, and return the result
        return $this->runQuery( $qryStmt );
    }
    
    /**
     * Purpose: This method will add a new record to the specified table
     * @param array $newRecord An associative array of the field names
     *   (the array index) and the values to be inserted (the array values)
     * @param string $tableName The name of the table to add the record to
     * @return int The number of rows inserted
     */
    public function insert( $newRecord, $tableName )
    {
        // Construct the field name and value lists
        $fieldList = "( ";
        $valueList = "( ";
        
        foreach ( $newRecord as $field=>$value)
        {
            $fieldList .= $field . ", ";
            $valueList .= "'" .
                    $this->dbConnect->real_escape_string( $value ) . "', ";
        }
        
        // We've finished adding all the field names and values to their
        // respective lists, so delete the final comma and space.
        $fieldList = rtrim( $fieldList, ", " );
        $valueList = rtrim( $valueList, ", " );
        
        $fieldList .= " )";
        $valueList .= " )";
        
        // Perform the insertion
        $insStatement = "INSERT INTO " . $tableName . " " . $fieldList .
                " VALUES " . $valueList;
       
        $this->runQuery( $insStatement );
        
        // Return the number of affected rows
        return $this->dbConnect->affected_rows;
    }
    
    /**
     * Purpose: Updates a record in the specified table with the values
     *   passed in.
     * Assumption: There is a value for the primary key in the values array,
     *   and we don't want to update that.
     * @param array $values An associative array of field names and the values
     *   that those fields are to be updated to
     * @param string $tableName The name of the table to update
     * @param string $primaryKey The name of the primary key
     * @return int The number of rows updated
     */
    public function update( $values, $tableName, $primaryKey )
    {
        // Specify which table to update
        $updateStatement = "UPDATE $tableName SET ";
        
        // LOOP for each of the passed in fields
        foreach ( $values as $fieldName=>$fieldValue )
        {
            // If this isn't the primary key
            if ( $fieldName != $primaryKey )
            {
                // Add the column and value to be changed to the query
                $updateStatement .= $fieldName . "='" .
                        $this->dbConnect->real_escape_string( $fieldValue ) .
                        "', ";
            }
        }
        
        // Oops we added in one too many commands -- remove it
        $updateStatement = rtrim( $updateStatement, ", ");
        
        // Add in the condition to specify which record to update
        $updateStatement .= " WHERE " . $primaryKey . "='" .
                $this->dbConnect->real_escape_string( $values[$primaryKey] ) .
                "'";
        
        // Run the query
        $this->runQuery( $updateStatement );
        
        // Return the number of rows affected by the last query
        return $this->dbConnect->affected_rows;
    }
    
    /**
     * Purpose: Escapes special characters in a string for use in an
     *   SQL statement
     * @param string $strToEscape The string to be escaped
     * @return string The escaped string
     */
    public function escape( $strToEscape )
    {
        return $this->dbConnect->escape_string( $strToEscape );
    }
    
    /**
     * Purpose: Prepare an SQL statement for execution by our database
     *   connection
     * @param string $query The query to be prepared
     * @return mysqli_stmt The prepared statement, or FALSE if an error
     *   occurred.
     */
    public function prepare( $query )
    {
        return $this->dbConnect->prepare( $query );
    }

    /**
     * Purpose: Display the results of a database query in an HTML table
     * @param mysqli_result $qryResults Results of a previous database query
     */
    static public function displayRecords( $qryResults )
    {
        // Display the opening table tag
        echo "<table>\n";
        
        // Display a table row opening tag
        echo "    <tr>";
        
        // LOOP for all query result columns
        foreach ( $qryResults->fetch_fields() as $fieldInfo )
        {
            // Display the column name within a table header tag
            echo "<th>{$fieldInfo->name}</th>";
        }
            
        // Display a table row closing tag
        echo "</tr>\n";
        
        // LOOP for all the query rows returned
        while ( $row = $qryResults->fetch_row() )
        {
            // Display a table row opening tag
            echo "    <tr>";
            
            // LOOP for all the query result columns
            for ( $i = 0; $i < $qryResults->field_count; $i++ )
            {
                // Display the value of this query result row and column
                echo "<td>" . htmlspecialchars( $row[$i] ) . "</td>";
//                $row[$i] = htmlspecialchars( $row[$i] );
//                echo "<td>{$row[$i]}</td>";
            }
            // Display a table row closing tag
            echo "    </tr>\n";
        }

        // Display the closing table tag
        echo "</table>\n";
    }
    
    /**
     * Purpose: Creates an associative array to be used with the HtmlForm
     *   class' methods that populate lists.
     * @param mysqli_result $qryResults The query result record set.  The
     *   result should consist of two columns: the first column will contain
     *   an ID, and second will contain corresponding text.
     * @return array Associative array, where the array index comes from
     *   the qryResults' first column, and the array value comes from the
     *   qryResults' second column.
     */
    public static function createArray( $qryResults )
    {
        // Create an empty result array
        $result = array();
        
        // Loop through all rows in the result set
        while ( $row = $qryResults->fetch_row() )
        {
            // Set an entry in the result set with the index as the first
            // column in the result set, and the value as the second column
            $result[$row[0]] = $row[1];
        }
        
        // Return the result array
        return $result;
    }
}
