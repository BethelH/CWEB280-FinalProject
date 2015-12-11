<?php
/**
 * Description of XMlObject
 * Uses SimpleXML library and DOMDocument to simplify xml file handling
 * @author ins226
 */
class XmlObject {
    
    private $rootNode;
    private $xmlFilePath;
    
    /**
     * Purpose: create SimpleXML object from a file or from scratch
     * @param string $path - file path to an xml file
     * @param string $rootName - the name of the root element in the xml file
     */
    public function __construct($path=null, $rootName="root")
    {
        if(!empty($path))
        {
            $this->xmlFilePath = $path;
            
            //if the file path is to an existing file -load the file
            if(file_exists($path))
            {
                $this->rootNode = simplexml_load_file($path);
            } else {
                //if the file does not exist create a SimpleXml object
                // from scratch
                $this->rootNode = SimpleXMLElement(
                        '<?xml version="1.0" encoding="UTF-8"?><'.
                        $rootName.' />');
            }
        }
    }
    
    /**
     * Uses DOMDocument to save a formatted xml file to the harddisk
     * @param string $path
     * @param SimpleXmlElement $rootNode
     * @return bool - whether or not the file saved
     */
    public function saveFormattedXml($path=null,$rootNode=null)
    {
        $path =  empty($path) ? $this->xmlFilePath : $path;
        $rootNode = $rootNode==null ? $this->rootNode : $rootNode;
        
        
       //to format the xml file we can not use SimpleXML
       // we need to use another XML library and the DOMDocument object
       // to nicely format the xml file
        $xmlDOM = new DOMDocument();
        $xmlDOM->preserveWhiteSpace = false;
        $xmlDOM->formatOutput = true;
        $xmlDOM->loadXML($rootNode->asXML());
        return $xmlDOM->save($path);
      
    }
    
    /**
     * Purpose: remove the nodes specified by the xpath from the rootnode
     * @param string $xPath
     */
    public function removeNodes($xPath)
    {
        foreach ($this->rootNode->xpath($xPath) as $gettinDeletedChild) {
            unset($gettinDeletedChild[0]);
        }        
    }
    
    /**
     * Purpose: add a child to the nodes specified by the xpath
     * @param string $xpath -the xpath for the parent node/elements
     * @param string $childName - name of the child element
     * @param string $childValue - the value of the child element
     * @param Associative Array $attributes - key value pairs for the 
     * child attributes
     * @return SimpleXml object Array
     */
    public function addNodes($xpath,$childName,
            $childValue=null,$attributes=array())
    {
        //xpath method returns array of SimpleXML nodes
        $nodes = $this->rootNode->xpath($xpath);
        
        foreach($nodes as $parentNode)
        {
            $childNode = $parentNode->addChild($childName);
            
            // if childValue is passed in then set the element value
            if(!empty($childValue))
            {
                $childNode[0] = $childValue;
            }
            //loop through and add attributes to the child element
            foreach ($attributes as $name=>$value)
            {
                $childNode->addAttribute($name,$value);
            }            
        }
        
        //in case the programmer wants to do further actions
        //return the affected nodes
        return $nodes;
    }
    
    /**
     * Purpose: to get an assoc array from the specifed xml nodes
     * @param type $xpath - xpath to the nodes we want to convert to an array
     * @param type $valueName - name of the element/attribute that contains
     *  the value for an item in the returned array
     * @param type $keyName - name of the element/attribute that contains the
     *  key/index for the array item.
     * @return array - an assoc array with the specified key value pairs
     */
    public function toArray($xPath, $valueName, $keyName="" )
    {
        $returnArray = array();
        // loop through the nodes specified by the xpath
        foreach($this->rootNode->xpath($xPath) as $node)
        {
            //check if the attribute exists - if so return the attribute value
            // else return the element value
            $value = !empty($node[$valueName]) ?
                    (string)$node[$valueName]:
                    (string)$node->{$valueName};
                    
            if(!empty($keyName))
            {
                // check if the attributes exists for the keyName      
                $key = !empty($node[$keyName]) ?
                        (string)$node[$keyName]:
                        (string)$node->{$keyName};
            }
            
            //add the key an value to the return array
            $returnArray[$key] = $value;

        }
        return $returnArray;        
    }
    
     /**
     * Purpose: to import an SimpleXml element and its children into the xml
     * @param string $xpath - the xpath to the specifed parent node(s)
     * @param SimpleXMLElement $childNode - SimpleXML element to import
     * @return assoc array - the affected nodes
     */
    public function importSimpleXml($xpath, SimpleXMLElement $childNode)
    {
        $nodes = $this->rootNode->xpath($xpath);
        foreach ($nodes as $parentNode)
        {
            // Create new DOMElements from the two SimpleXMLElements
            $domParent = dom_import_simplexml($parentNode);
            $domChild  = dom_import_simplexml($childNode);

            // Import the child node into the parent node
            $domChild  = $domParent->ownerDocument->importNode($domChild, TRUE);

            // Append the child to the parent in the xml document
            $domParent->appendChild($domChild);
            
            // no need to convert back to SimpleXML - DOM document makes changes
            // to the underlying SimpleXML object
        }
        return $nodes;
    }
    
    
    
}
