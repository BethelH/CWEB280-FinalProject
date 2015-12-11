<?php
require_once 'DbObject.class.php';

/**
 * Description of xmlDbObject
 *XmlDbObject extends the DBObject class to query the db and return XML
 * @author CST205
 */
class XmlDbObject extends DbObject 
{
   
    /**
     * Purpose: to Select data from the db and return an XML string
     * @param type $rootName
     * @param type $columnList 
     * @param type $tableList
     * @param type $condition
     * @param type $sort
     * @param type $other
     * @param type $childName
     * @param type $xsltPath
     * @return string
     */
   public function selectToXml($rootName, $columnList, $tableList,
           $condition="", $sort="", $other="", $childName="row", $xsltPath="")
   {
       //get data from specified tables - call parent's select method
       $queryResult = $this->select($columnList, $tableList, 
               $condition, $sort, $other);
       
       $xmlDeclare = "<?xml version='1.0' encoding='UTF-8'?>";
       
       $rootNode = new SimpleXMLElement($xmlDeclare."<$rootName />");
       
       //loop through the query results and set the xml elements and attributes
       while($rowData  = $queryResult->fetch_assoc())
        {
            //create new child node to store the row's data
            $childNode = $rootNode->addChild($childName);
            
            //for each column in the select we create a new child node
            foreach($rowData as $node=>$nodeValue)
            {
                //need to excape sml special chars using htmlentities
                $childNode->addChild($nodeName, htmlentities($nodeValue));
            }
        }
       //free up resources
        $queryResult->free();
        
        return $rootNode->asXML();
   }
}
