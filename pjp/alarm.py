#!/usr/env python
# coding: utf-8
#
#  by Luís Alves @ CSC 05.11.2014
#  Contact: FirstName.LastName@csc.fi
#


import socket, subprocess, MySQLdb
import smtplib

from email.MIMEMultipart import MIMEMultipart

# Connect to DB
connection=MySQLdb.connect(host="HOSTNAME", user="USER", passwd="PASSWORD", db="DBNAME")
cur=connection.cursor()

# Get Jobs on PENDING state for 7 or more days
cur.execute('SELECT jobid,clusterName,submitTime,DATEDIFF(NOW(),submitTime) FROM pendingTime WHERE DATEDIFF(NOW(),submitTime) >= 7 ORDER BY jobid ASC')
table = cur.fetchall()

# Close connection to DB
cur.close()
connection.close()

text = ['']
text[0] = 'Jobs on PENDING state for 7 or more days:\n'

for row in table:
	jobid = row[0]
	clusterName = row[1]
	submitTime = str(row[2])
	pendingDays = row[3]
	text.append('job ID: %s | System: %s | Submission Time: %s | Days on PENDING State: %d' % (jobid,clusterName,submitTime,pendingDays))

if len(text) <= 1:
	text.append("\nThere are no long pending jobs at the moment.\n")

text.append("\nMore Info: https://WEBSERVER_HOSTNAME/pjpView/index.php\n")
body = 'Subject: %s\n\n%s' % ('FGI Jobs on PENDING state for 7 or more days','\n'.join(text))

# DEBUG: #print body

# Sending notification e-Mail

From = "EMAIL_ADDR"
To = "EMAIL_ADDR"
s = smtplib.SMTP('localhost')
s.sendmail(From, To, body)
s.quit()

