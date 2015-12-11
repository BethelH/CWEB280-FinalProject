<?php
/**
 * VERSION: 4.0
 * Description of HtmlForm
 * this class will define HELPER methods to generate HTML forms on a webpage
 * we will continue using this class going forward
 * to speed up coding of examples and assignments
 * IMPORTANT - DO NOT put a PHP closing tag in this file
 * @author cst2##
 */
class HtmlForm {
    
/************************* FORM TEMPLATE CONSTANTS ****************************/
    // define constants that act as templates for the sprintf and printf methods
    // we are using the HEREDOC to define strings that output formatted html
    const FORMSTART = <<<EOT
    <form id="%s" action="%s" method="%s" %s role="form">
        <fieldset>
            <legend>%s</legend>
EOT;
// the above line indicates the end of string - nothing else can be on that line
    
    //string constant wrap the inputs with a div and add label
    const INPUTWRAPPER = <<<EOT

            <div class="form-group">
                <label for="%s">%s</label>
                %s
            </div>
EOT;
// the above line indicates the end of string - nothing else can be on that line
    
    
    //string constant for rendering a submit and reset button on the page
    const FORMSUBMIT = <<<EOT

            <div class="formbuttons" >
                <input type="submit" value="%s" name="%s" />
                <input type="reset" value="%s" />
                </span>
            </div>
EOT;
    
    // the above line indicates the end of string - nothing else can be on that line
    
    
    //string constant for rendering a submit and reset button on the page
    const FORMSUBMITREDIRECT = <<<EOT

            <div class="formbuttons" >
                <input type="submit" value="%s" name="%s"  onclick="change()"  />
                <input type="reset" value="%s" onclick="cancel()" />
                </span>
            </div>
EOT;
// the above line indicates the end of string - nothing else can be on that line
    
    //string constant to close off the fieldset and the form
    const FORMEND = <<<EOT

        </fieldset>
    </form>

EOT;
// the above line indicates the end of string - nothing else can be on that line    
    
 
/********************** RENDER FORM TAGS *************************************/
    
    /**
     * 
     * @param string $name - the id of the form element
     * @param string $labelTextText - is the legend of the fieldset
     * @param bool $allowFile - if true specifies enctype="multipart/form-data" for the form
     * @param string $action - the page to post to (default: #)
     * @param string $method - the way data is sent to the server (default: POST);
     */
    public function renderStart($name, $labelTextText, $allowFile=false, 
            $action="", $method="POST")
    {
        
        $enctype = $allowFile ? 'enctype="multipart/form-data"' : '';
        // to access a constant variable in php class use self::CONSTNAME
        printf(self::FORMSTART,$name,$action,$method,$enctype,$labelTextText);
    }
    
    
    
    /**
     * renderSubmitReset - generates submit and reset buttons
     * @param string $nameSubmit - the name of the submit button sent to the server
     * @param string $labelTextSubmit - the value attribute of the submit button
     *  - displayed to the user
     * @param string $labelTextReset - the value attribute of the reset button
     * - displayed to the user  (default: Reset)
     */
    public function renderSubmitReset($nameSubmit, $labelTextSubmit, 
            $labelTextReset="Clear")
    {
        printf(self::FORMSUBMIT, $labelTextSubmit, $nameSubmit, $labelTextReset);
    }
        
      /**
     * renderSubmitReset - generates submit and reset buttons
     * @param string $nameSubmit - the name of the submit button sent to the server
     * @param string $labelTextSubmit - the value attribute of the submit button
     *  - displayed to the user
     * @param string $labelTextReset - the value attribute of the reset button
     * - displayed to the user  (default: Reset)
     */
    public function renderSubmitResetRED($nameSubmit, $labelTextSubmit, 
            $labelTextReset="Cancel")
    {
        printf(self::FORMSUBMITREDIRECT, $labelTextSubmit, $nameSubmit, $labelTextReset);
    }
    
    /**
     * renderSubmitEnd - generates the closing form tags and buttons
     * @param string $nameSubmit - the name of the submit button sent to the server
     * @param string $labelTextSubmit - the value attribute of the submit button
     *  - displayed to the user
     * @param string $labelTextReset - the value attribute of the reset button
     *  - displayed to the user  (default: Reset)
     */
    public function renderSubmitEnd($nameSubmit, $labelTextSubmit, 
            $labelTextReset="Clear")
    {
        $this->renderSubmitReset($nameSubmit, $labelTextSubmit, $labelTextReset);
        $this->renderEnd();
    }
    
    
    /*
     * renderEnd - - generates the closing fieldset and form tags
     */
    public function renderEnd()
    {
        // to access a constant variable in php class use self::CONSTNAME
        echo self::FORMEND;
    }

    
/********************** CONSTRUCTOR AND HELPERS *******************************/
    
// private variable that stores whether the form was posted or not
    private $isPosted; //set this in the constructor  
    
    /**
     * constructor -  checks if the form was posted and set the isPosted variable
     */
    function __construct() {
        
        $this->isPosted = false;
        if(strtoupper($_SERVER["REQUEST_METHOD"]) == "POST")
        {
            $this->isPosted = true;
        }
        
    }
    
     /**
     * postedValue - will try to get the posted value for a field from
     * the $_POST super global
     * if the posted value is not set then the function with return a 
     * default value specified or null by default
     * @param string $name
     * @param object $defaultValue
     * @return string
     */
    private function postedValue($name, $escaped=true, $defaultValue=null)
    {
        //set the return value to the passed in default value
        $postedValue = $defaultValue;
        
        //check to see if the posted value is set and not empty
        //this will work for all types of form input controls
        if(!empty($_POST[$name]))
        {
            //call htmlentities to filter/escape the posted value this is to 
            //  prevent code injection from the user
            //$escaped is a boolean the programmer passes in because in some 
            //  cases we may NOT want to escape the posted values
            $postedValue = $escaped ? htmlentities($_POST[$name]):$_POST[$name];
        }
        return $postedValue;
    }
    
    
    /*
     * checkRequired - generates an indicator that the field is requires
     * if the form has been posted this method will also check and generate
     * an appropriate error message if required
     */
    private function checkRequired($name, $labelText,
            $msgFormat='<span class="fielderror">* %s is required</span>')
    {
        //set the default return string to "*"
        // this indicates to the user that the field is required.
        $requiredMsg = "*"; 
        
        //check if the form has been posted -if so check for required fields
        if($this->isPosted)
        {
            //check if the form control name is in the $_POST super global
            // if it is not then return an error message to display to the user
            if( !isset($_POST[$name]) ||       /* works for radio and check boxes */
                ( !is_array($_POST[$name])&&   /* if not array then it's a textbox */
                    empty(trim($_POST[$name])) /* additional checks for text box */ )
            )
            {           
                $requiredMsg = sprintf($msgFormat,$labelText);
            }
        }
        return $requiredMsg;
    }

    
     /**
     * renderInput - generates a single form input markup for a input group
     * @param bool $type - the type of form input 
     * @param string $name - the name of the input group (also used to generate a unique id)
     * @param string $labelText - the label of the form input
     * @param string $value - the form input value (also used to generate a unique id)
     * @param bool $checked - whether to mark the current form input as checked
     * @param string $extras - any other attributes that maybe needed (default: empty string)
     * @return string
     */
     protected function renderGroupItem($type, $name, $labelText, $value, 
            $checked, $extras="")
    {
       //determine whether to mark the form control as checked 
       $extras .= $checked ? ' checked' : '';
       
       //create a unique id from the name and cleaned value of the form input
       //the id is used by label adjacent to the form input
       $id = preg_replace("/\W/i", "" , $name.$value);
       
       // set up the format string for the input - the div tag is optional
       // the div tag is so later we can use css to line up the radio buttons 
       $inputHTML = <<<EOT
    <div>
        <input type="%s" name="%s" id="%s" value="%s" %s class="form-control" />
        <label for="%s">%s</label>
    </div>
EOT;
       //call htmlentiies to escape the value just in case the programmer
       // uses special characters for the values
       return sprintf($inputHTML, $type, $name, $id, htmlentities($value),
               $extras, $id, $labelText);
    }    
    
    /**
     * renderFormInput - generates a wrapped form input control
     * @param string $type - the type of input - any valid html input type
     * @param string $name - the name sent to the server and the id of the form control
     * @param string $labelText - the label of the field
     * @param bool  $required -  whether the field is required or not
     * @param string $value - the default value for the form input control (default: empty string)
     * @param string $extras - any other attributes that may be needed (default: empty string)
     */
    protected function renderFormInput($type, $name, $labelText, $required,
            $value="", $extras="")
    {
        //generate a string that indicates the field is required
        //and concatenate the string to the label text
        $labelText .= $required ? $this->checkRequired($name, $labelText) : "";

        //get the escaped/filtered posted value to display to the user again
        $userPostedValue=$this->postedValue($name, true, $value);
        
        // set up the format string for the input 
       // add white space as needed to generate human readable html
       $inputHTML = <<<EOT
<input type="$type" name="$name" id="$name" value="$userPostedValue" $extras class="form-control" required="$required"/>
EOT;
       
        //wrap the input tag with our layout (within a div and preceded by a label)
        //use printf to output all the html for the form control
        printf(self::INPUTWRAPPER, $name, $labelText, $inputHTML);
    }    
    
/******************************* TEXTBOX **************************************/
    
    /**
     * renderTextbox - generates a wrapped text input
     * @param string $name - the name sent to the server and the id of the form input control
     * @param string $labelText - the label of the field
     * @param bool  $required -  whether the field is required or not
     * @param int $max - maxlength of the  text box (default: 50)
     * @param string $defaultValue - the default value populated in the text box (default: empty string)
     * @param string $extras - any other attributes that may be needed (default: empty string)
     */
    public function renderTextbox($name, $labelText, $required=false, $max=50,
            $defaultValue="", $extras="")
    {
              
        //append the maxlength attribute to the textbox useing the $max param
        $extras .= ' maxlength="' . $max . '"';

        // call the renderFormInput helper function which will output the
        // wrapped form input control with the approprate attributes
        $this->renderFormInput("text", $name, $labelText, $required,
                $defaultValue, $extras);
    }
    
    /**
     * renderPassword - generates a wrapped password input control
     * @param string $name - the name sent to the server and the id of the form input control
     * @param string $labelText - the label of the field
     * @param bool  $required -  whether the field is required or not
     * @param int $max - maxlength of the  text box (default: 50)
     * @param string $defaultValue - the default value populated in the text box (default: empty string)
     * @param string $extras - any other attributes that may be needed (default: empty string)
     */
    public function renderPassword($name, $labelText, $required=false, $max=50,
            $defaultValue="", $extras="")
    {
              
        //append the maxlength attribute to the textbox useing the $max param
        $extras .= ' maxlength="' . $max . '"';

        // call the renderFormInput helper function which will output the
        // wrapped form input control with the approprate attributes
        $this->renderFormInput("password", $name, $labelText, $required,
                $defaultValue, $extras);
    }    
/**************************** SELECT BOX **************************************/    
    /**
     * renderSelect - generates a form select box
     * @param string $name - the name and id of the select box
     * @param string $labelText -  the label of the select box
     * @param array $options - array of option values and text
     * @param string $selectedValue - value of the default option to be selected (default: empty string)
     * @param string $extras - any other attributes that maybe needed (default: empty string)
     */
    public function renderSelect($name, $labelText, $options, 
            $selectedValue="", $extras="")
    {
        //select box is more comples than simple input tag
        //set the select format string 
        $selectboxHTML = <<<EOT
<select name="%s" id="%s" %s class="form-control">%s
                </select>
EOT;
        
        //get the posted value for the form control
        //we are going to compare the user posted string to the 
        //  strings the programmer passed in so we should NOT escape the 
        //  the user posted string otherwise it will not match up with any of
        //  the strings the programmer passed in (the false means do not escape)            
        $userPostedValue=$this->postedValue($name,false,$selectedValue);
        
        //loop through the options array
        $optionsHTML = "";//create variable to contain all generated option html  
        foreach($options as $optionValue=>$labelTextText)
        {
            //check if the current option value is the same as 
            //  the value the user selected/posted - if so flag it as selected
            $selected = $optionValue == $userPostedValue;
            
            //append the html for the current option to 
            //  the string that has all the options' html
            //(.= shorthand for append to string)
            $optionsHTML .= $this->renderOption($optionValue, 
                    $labelTextText, $selected);
        }        

        //wrap options html with the opening and closing select tags
        $inputHTML = sprintf($selectboxHTML,$name, $name, $extras, $optionsHTML);        
        
        //wrap the select tag with our layout(within a div and preceded by a label)
        //use printf to output all the html for the form control        
        printf(self::INPUTWRAPPER, $name, $labelText, $inputHTML);
        
    }
 
    /**
     * renderOption - renders a single option tag for use within a select tag
     * @param string $labelTextText - the option displayed text
     * @param string $optionValue - the option value
     * @param bool $selected - boolean specify the option as selected
     * @return type
     */
    private function renderOption($optionValue, $labelTextText, $selected)
    {
        //if the option is selected then convert the boolean to 
        //  the string "selected" which can be concatenated to the option's html
        $selected = $selected ? 'selected' : '';
        
        // create the HTML string we use as a format for the option html
        // add spacing as needed to generate human readable html
        $optionHTML = <<<EOT

                    <option value="%s" %s >%s</option>
EOT;
        
        //generate the option tag
        //call htmlentities to escape characters that may break option tag
        //concatenate the "selected" attribute to the option's html
        return sprintf($optionHTML, htmlentities($optionValue), $selected, 
                $labelTextText);
    }
    
    /**
     * renderMultiSelect - generates a form select box
     * @param string $name - the name and id of the select box
     * @param string $labelText -  the label of the select box
     * @param array $options - array of option values and text
     * @param array $selectedValues - array of values of the options to be selected (default: null)
     * @param string $extras - any other attributes that maybe needed (default: empty string)
     */
    public function renderMultiSelect($name, $labelText, $options, 
            $selectedValues=null, $extras="")
    {
        // create the HTML string we use as a format for the select box html
        // add spacing as needed to generate human readable html
        $selectboxHTML = <<<EOT
<select name="%s[]" id="%s" multiple %s >%s
                </select>
EOT;
        
        // the programmer can specify which options should be selected by default
        // the programmer must pass in a array but we need to make sure
        //  that selectedValues is an array type object
        // if not an array set to an empty array
        $selectedValues = !is_array($selectedValues) ? array() : $selectedValues;
        
        //get the posted value the user posted for the form control
        //do not escape the value (denoted by the false) 
        //  because we are expecting and array and an array object can not be escaped
        $userPostedValues=$this->postedValue($name,false,$selectedValues);
        
        //loop through the options array
        $optionsHTML = "";//create variable to contain all generated options' html  
        foreach($options as $optionValue=>$labelTextText)
        {
            //check if the current option value is in the array of values the 
            //  user selected/posted - if so flag the current option as selected
            $selected = in_array($optionValue, $userPostedValues);
            
            //append the html for the current option to 
            //  the string that has all the options' html
            //(.= shorthand for append to string)
            $optionsHTML .= $this->renderOption($optionValue, $labelTextText, $selected);
        }        
        
        //wrap options html with the opening and closing select tags
        $inputHTML = sprintf($selectboxHTML,$name, $name, $extras, $optionsHTML);
        
        //wrap the select tag with our layout(within a div and preceded by a label)
        //use printf to output all the html for the form control        
        printf(self::INPUTWRAPPER, $name, $labelText, $inputHTML);
        
    }    
       
/************************* RADIO BUTTON GROUP *********************************/
    
    /**
     * renderRadioGroup - generates a wrapped group of radio buttons
     * @param type $name - the name of the radio group
     * @param type $labelText - the label for the radio Group
     * @param type $options - associative array of radio values and labels
     * @param bool  $required -  whether the field is required or not
     * @param type $selectedValue - the value of the default radio to be checked (default: empty string)
     * @param type $extras - any other attributes that maybe needed (default: empty string)
     */
    public function renderRadioGroup($name, $labelText, $options,
            $required = false, $selectedValue="", $extras="")
    {
        //generate a string that indicates the field is required
        //later (in the printf) we will concatenate the string to the html
        $requiredString = $required ? $this->checkRequired($name, $labelText) : "";
        
        //get the posted value for the field
        //do not escape the value because we are going to use it in a comparison
        $userPostedValue=$this->postedValue($name,false,$selectedValue);
        
        // this is optional - surround the radio groups with a span tag
        // so later we can use css to line up the radiobuttons next to the label
        $radioButtonsHTML = "<span>";
        
        //loop through the options array and generate the html for each radio button
        foreach($options as $valueRadio=>$labelTextRadio)
        {
            //check if the current option value is the same as the
            //posted value / selected value
            $selected = $userPostedValue == $valueRadio;
            
            //append the html for the current radio button to 
            //  the string that has all the radio buttons' html
            //(.= shorthand for append to string)
            $radioButtonsHTML .= $this->renderGroupItem("radio", $name,
                    $labelTextRadio, $valueRadio, 
                    $selected, $extras);
        }
        
        $radioButtonsHTML .= "</span>";
        
        // no need to specify the name this time since each radio button 
        //  will have its own label
        // concatenate the required field string after the label
        printf(self::INPUTWRAPPER, "", $labelText . $requiredString,$radioButtonsHTML);

    }
	
/************************** CHECK BOX GROUP ***********************************/	

    /**
     * renderCheckboxGroup - generates a wrapped group of checkboxes
     * @param string $name - the name of the checkbox group
     * @param string $labelText - the label for the checkbox Group
     * @param string $options - associative array of checkbox values and labels
     * @param bool  $required -  whether the field is required or not
     * @param array $selectedValues - array of values of the options to be selected (default: null)
     * @param string $extras - any other attributes that maybe needed (default: empty string)
     */
    public function renderCheckboxGroup($name, $labelText, $options, 
            $required = false, $selectedValues=null, $extras="")
    {
        //generate a string that indicates the field is required
        //later (in the printf) we will concatenate the string to the html
        $requiredString = $required ? $this->checkRequired($name, $labelText) : "";
        
        // the programmer can specify which check boxes should be checked by default
        // the programmer must pass in a array but we need to make sure
        //  that selectedValues is an array type object
        // if not an array set to an empty array
        $selectedValues = !is_array($selectedValues) ? array() : $selectedValues;
        
        //get the posted value the user posted for the form control
        //do not escape the value (denoted by the false) 
        //  because we are expecting and array and an array object can not be escaped
        $userPostedValues=$this->postedValue($name,false,$selectedValues);
        
        // this is optional - surround the check boxes with a span tag
        // so later we can use css to line up the boxes next to the label
        $checkboxesHTML = "<span>";
        
        //adding '[]' to the name will indicate to php to save 
        //  the multiple posted values into an array
        $name .='[]';
        
        //loop through the options array and generate the html for each checkbox
        foreach($options as $valueCheckbox=>$labelTextCheckbox)
        {
            //check if the current option value is the same as the
            //posted values / selected values
            $selected = in_array($valueCheckbox, $userPostedValues);
            
            //append the html for the current checkbox to 
            //  the string that has all the checkboxes' html
            //(.= shorthand for append to string)
            $checkboxesHTML .= $this->renderGroupItem("checkbox", $name,
                    $labelTextCheckbox, $valueCheckbox, $selected, $extras);
        }
        
        $checkboxesHTML .= "</span>";
        
        // no need to specify the name this time since each check box 
        //  will have its own label
        // concatenate the required field string after the label
        printf(self::INPUTWRAPPER, "", $labelText . $requiredString, $checkboxesHTML);

    }
 
    /**
     * renderCheckbox - generates a wrapped checkbox input
     * @param string $name - the name of the checkbox
     * @param type $labelText - the label of the checkbox
     * @param bool $required -  whether the field is required or not
     * @param type $defaultValue - the default value of the checkbox (default: 1)
     * @param type $checked - whether to mark the checkbox as checked
     *   (default: false)
     * @param type $extras - any other attributes that may be required
     *   (default: empty string)
     */
    public function renderCheckbox( $name, $labelText, $required=false,
            $defaultValue="1", $checked=false, $extras="" )
    {
        
        //for checkboxes we have to perform 2 checks

        // if the form is posted then use the posted value to determine
        // whether the checkbox should be checked   
        if($this->isPosted)
        {
            // if the posted value is not null then the checkbox is checked
            $checked =  $this->postedValue($name,false) != null;
        }
        // else if the form is not posted then use the $checked param 
        // that the programmer passed in to determine whether the checkbox 
        // should be checked   
        $extras .= $checked? " checked" : "";        
       
        // call the renderFormInput helper function which will output the
        // wrapped form input control with the approprate attributes
        $this->renderFormInput("checkbox", $name, $labelText, $required, 
                $defaultValue, $extras); 
    }
    
    /**
     * renderHidden - generates a hidden form input
     * @param string $name - the name send to the server and the id of the element
     * @param string $value - the default value populated in the file input (default: empty string)
     * @param string $extras - any other attributes that may be required (default: empty string)
     */
    public function renderHidden($name, $defaultValue="", $extras="")
    {
        $value = $this->postedValue($name,$defaultValue);
        //hidden inputs are not visible to the user so no need for the Input wrapper
        printf('<input type="hidden" name="%s" id="%s" value="%s" %s />',
                $name, $name,  htmlentities($value), $extras);        
    }    
 
    
}
