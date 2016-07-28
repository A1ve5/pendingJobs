#!/usr/env python
# coding: utf-8
#
#  by Luís Alves @ CSC 18.11.2013
#  Contact: FirstName.LastName@csc.fi
#

import socket, subprocess, MySQLdb, time, re

# Debug Flag
DEBUG = 0

# Connect to DB
connection=MySQLdb.connect(host="HOSTNAME", user="USER", passwd="PASSWORD", db="DBNAME")
cur=connection.cursor()

# Get Hostname
clusterName = socket.getfqdn()

# Get Number of Running Jobs
proc = subprocess.Popen("squeue -o '%t' | grep R", shell = True, stdout=subprocess.PIPE)
running = proc.communicate()
runJobsList = running[0].split()

if DEBUG == 1: print 'runJobsList: [%s]' % ', '.join(map(str, runJobsList))

# Get Number of Pending Jobs
proc = subprocess.Popen("squeue -o '%i %t' | grep PD", shell = True, stdout=subprocess.PIPE)
pending = proc.communicate()
pdjList = pending[0].split()

# Flush this host pending time tables
cur.execute('DELETE from pendingTime WHERE clusterName="%s"'%(clusterName))

# Clean list from PD values
for i in pdjList:
	if "PD" in pdjList: pdjList.remove("PD")

# Insert values into pending time table
for i in pdjList:
	if DEBUG == 1: print "JobId: %s\n"%(i)
	proc = subprocess.Popen('scontrol show job "%s"'%(i), shell = True, stdout=subprocess.PIPE)
	jobAllData = proc.communicate()
	jobAllData = jobAllData[0].split()
	
	# Jump if no data for this Job. i.e. Array Jobs.
	if jobAllData[0] == "Job":
		continue
	else:
		# FIX ME:
		# Insert values into a dictionary variable
		# There must be a better way to do this... oh well...
		jobData = {}
		for item in jobAllData:
			# Ensure that you just have pairs
			if len(item.split("=",1)) == 2: 
				k,v = item.split("=",1)
				jobData[k] = v

		cur.execute('INSERT INTO pendingTime (jobid, submitTime, clusterName) VALUES ("%s", "%s", "%s")'%(jobData['JobId'],re.sub('T',' ',jobData['SubmitTime']), clusterName))
		if DEBUG == 1: print '("%s", "%s", "%s"\n)'%(jobData['JobId'],re.sub('T',' ',jobData['SubmitTime']), clusterName)
		
if DEBUG == 1: print 'pdjList: [%s]' % ', '.join(map(str, pdjList))

# Update #Running and #Pending Jobs
cur.execute('update pendingJobs set nrPDjobs=%d,totalRunJobs=%d where clusterName="%s"'%(len(pdjList),len(runJobsList),clusterName))

if DEBUG == 1: print 'nrPDjobs=%d\ntotalRunJobs=%d\nclusterName="%s"\n'%(len(pdjList),len(runJobsList),clusterName)

# Close connection to DB
cur.close()
connection.close()

