#!/usr/bin/env python

############################################################
#
# Python client to query the GOCDB programmatic interface, 
# retrieve info about downtimes for a top entity region 
# using curl, parse xml output with the xml.dom.minidom mudule 
# and store data into a MySQl db.
# 
# Giuseppe Misurelli
# giuseppe.misurelli@cnaf.infn.it
# Last changes: July 16th, 2009 
#
############################################################

import sys, pycurl, time, StringIO, MySQLdb
from xml.dom import minidom

# Main function that execute all the functions defined
def main():

    # Functions that will be invoked and executed
    curlDownload()
    xmlParsing()
    mysqlSaving()

# Performs curl download and store output into the IO string variable
def curlDownload():

    global output  

    # GOCDB-PI url and method settings
    #
    # Set the GOCDB URL
    gocdbpi_url = "https://goc.egi.eu/gocdbpi/public/?"
    # Set your method
    gocdbpi_method = "get_downtime"
    # Set your topentity
    gocdbpi_topentity = "Finland"
    # Set the desidered windowstart
    #gocdbpi_winstart = "2014-04-01"
    gocdbpi_winstart = sys.argv[1]
    #gocdbpi_winend = "2019-12-31"
    gocdbpi_winend = sys.argv[2]
 
    # GOCDB-PI to query
    gocdb_ep = gocdbpi_url + "method=" + gocdbpi_method + "&topentity=" + gocdbpi_topentity + "&windowstart=" + gocdbpi_winstart + "&windowend=" + gocdbpi_winend

    output = StringIO.StringIO()
  
    c = pycurl.Curl()
    c.setopt(c.URL, gocdb_ep)
    c.setopt(c.VERBOSE, 1) # Need verbosity?
    # For public methods as the get_downtime
    c.setopt(c.SSL_VERIFYPEER, 0)
    # For protected and private method
    #c.setopt(c.SSLCERT, "/path/to/your/usercert")
    #c.setopt(c.SSLKEY, "/path/to/your/userkey")
    c.setopt(c.CAPATH, "/etc/grid-security/certificates")
    c.setopt(c.WRITEFUNCTION, output.write)
    c.perform()
    c.close()
    
    return output
    
# Performs xml parsing from the xml_doc string and save result into a dictionary handler
def xmlParsing():

    xml_doc = output.getvalue()
    
    try:
        doc = minidom.parseString(xml_doc)
        
    except minidom.DOMException, e:
        print e
    
    downtimes = doc.getElementsByTagName("DOWNTIME") 
    
    # Initiating the output handler list 
    handler_list  = []
    
    # Iterating over the main topic element and creating the mysql handler dictionary
    for dt in downtimes:
        # Instatiating the result dictionary handler 
        handler = {}
        # ID and CLASSIFICATION as DOWNTIME tag attributes
        if dt.getAttributeNode("ID"):
            attrs_id = dt.attributes["ID"]
            #handler[str(attrs_id.name)] = int(attrs_id.value)
            handler["Downtime-ID"] = int(attrs_id.value)
        if dt.getAttributeNode("CLASSIFICATION"):
            attrs_class = dt.attributes["CLASSIFICATION"]
            #handler[str(attrs_class.name)] = str(attrs_class.value)
            handler["Classification"] = str(attrs_class.value)
        
        # List containing all the DOM elements
        dom_elements = dt.childNodes
        
        # Iterating through all the DOM elements
        for elements in dom_elements:
            #Iterating for the SITENAME value
            if elements.nodeName == "SITENAME":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE:
                        site = str(element.nodeValue)
                        #handler[str(elements.nodeName)] = site
                        handler["Sitename"] = site
            # Iterating for the HOSTNAME value            
            elif elements.nodeName == "HOSTNAME":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE:
                        host = str(element.nodeValue)
                        #handler[str(elements.nodeName)] = host
                        handler["Hostname"] = host
            # Iterating for the SEVERITY value
            elif elements.nodeName == "SEVERITY":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE: 
                        severity = str(element.nodeValue)
                        #handler[str(elements.nodeName)] = severity
                        handler["Severity"] = severity
            # Iterating for the DESCRIPTION value
            elif elements.nodeName == "DESCRIPTION":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE:
                        description = str(element.nodeValue)
                        #handler[str(elements.nodeName)] = description
                        handler["Description"] = description
            # Iterating for the START_DATE value
            elif elements.nodeName == "START_DATE":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE:
                        sdate = float(element.nodeValue)
                        sdate = time.gmtime(sdate)[0:3]
                        start_date = "%s%2s%2s"%(sdate[0:3])
                        start_date = start_date.replace(' ','0')
                        #handler[str(elements.nodeName)] = start_date
                        handler["Startdate"] = start_date
            # Iterating for the END_DATE value
            elif elements.nodeName == "END_DATE":
                for element in elements.childNodes:
                    if element.nodeType == element.TEXT_NODE:
                        edate = float(element.nodeValue)
                        edate = time.gmtime(edate)[0:3]
                        end_date = "%s%2s%2s"%(edate[0:3])
                        end_date = end_date.replace(' ','0')
                        #handler[str(elements.nodeName)] = end_date
                        handler["Enddate"] = end_date
        
        handler_list.append(handler)
    return handler_list
        
# Insert and updates handler entries into a downtime database table
def mysqlSaving():

# In order to fit your database please provide the following 
# parameters into the conn and sql variables:
# host = "your_db_host" 
# user = "your_user" 
# passwd = "your_passwd" 
# db = "your_db_name"
# `table_name` (your_table_name)
    
    callback = xmlParsing()
    
    # Open connection to your database 
    try:
        conn = MySQLdb.connect(host = "HOSTNAME", user = "USER", passwd = "PASSWORD", db = "DBNAME")
        # Required cursor to deal with the slq statements to insert values into the fullscale table
        cursor = conn.cursor()
	cursor.execute('DELETE FROM downtimes;')
        # Generating all the SQL queries for the INSERT operations
        for i in callback:
            # Creating the tuple for the SQL string    
            k = (i.keys())
            v = (i.values())
            K = tuple(k)
            V = tuple(v)
            sql = "INSERT into `downtimes`" + str(K).replace("'","`")
            sql += " VALUES" + str(V)
            print sql
            cursor.execute(sql)
        cursor.close()
        conn.close()
    except MySQLdb.Error, e:
        print "Error %d: %s" % (e.args[0], e.args[1])
        sys.exit(1)
         
if __name__=="__main__":
    main()
