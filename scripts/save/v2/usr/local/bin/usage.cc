#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <errno.h>
#include <time.h>
#include <syslog.h>

#include <map>
#include <string>
#include <iostream>

using namespace std;

map<string, int> userUsage;
map<string, time_t> firstTab; // First tabulated time for this user
map<string, time_t> lastTab;  // Last tabulated time for this user
map<string, string> lastIp;  // Last tabulated IP for this user
map<string, string> ipUsers;
time_t startTime;
time_t reportTime=0;

void tabulate(char *ip, char *uid, int bytes, time_t logTime) {
	time_t age=startTime-logTime; 
	string uidStr="(unknown)";
	string ipStr=string(ip);
	if(strcmp(uid,"-") != 0) { uidStr=string(uid); ipUsers[ipStr]=uidStr; }
	else { uidStr=ipUsers[ipStr]; }

	if(uidStr == "") { uidStr=ipStr; }

	lastIp[uidStr]=ipStr;

	// Only count usage for items that came after startTime (11am)
	if(age<0) {
		if(userUsage[uidStr] == 0) { firstTab[uidStr]=logTime; }
		userUsage[uidStr]+=bytes;
		lastTab[uidStr]=logTime;
	}
}

void report(time_t curTime, time_t tabTime, int count) {
//	printf("\x1b[H\x1b[2J"); // Escape sequence to clear terminal
	FILE *fpout=fopen("/tmp/topusers.csv.tmp","wb");
	if(!fpout) { syslog(LOG_ERR,"fopen(/tmp/topusers.csv.tmp): %s",strerror(errno)); exit(1); }

	typedef map<string, int>::iterator ItUsers;
	int userCount=0;
	fprintf(fpout,"#version=%s_%s,tabTime=%ld,logLines=%d\n",__DATE__,__TIME__,tabTime,count);
	fprintf(fpout,"   MB,   STARTED,     ENDED,LASTIP,USERID\n");
	for(ItUsers iterator=userUsage.begin(); iterator != userUsage.end(); ++iterator) {
	    // iterator->first = key (user id)
	    // iterator->second = value (bytes)
		int bytes=iterator->second;
		string uid=iterator->first;
		fprintf(fpout,"%5.2f,%ld,%ld,%s,%s\n",bytes/1024.0/1024.0, 
			firstTab[uid], lastTab[uid], lastIp[uid].c_str(),uid.c_str());
		userCount++;
	}
	fclose(fpout);
	rename("/tmp/topusers.csv.tmp","/tmp/topusers.csv");
	reportTime=curTime;
	syslog(LOG_INFO,"Found %d users in %d log lines.",userCount,count);
}

/*** NOTE: LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\" \"%{forensic-id}n\" \"%{Cookie}i\"" combined-cookie
  1=remote host
  2=remote log name
  3=remote user
  4=time request rec'd
  5=first line of req
  6=status of last req (after redirects)
  7=response bytes excluding headers (- if no bytes sent)

e.g 192.168.2.123 - - [16/Dec/2013:03:26:58 -0900] "GET /xmls/samsung/sgs2/gen.xml HTTP/1.1" 302 277 
    HOST              TIME                         REQ                                       STS BYT
 "-" "Dalvik/1.4.0 (Linux; U; Android 2.3.4; GT-I9100 Build/GINGERBREAD)" "-" "-"
 RFR USERAGENT                                                            FID COOKIE
****/

void parselog1(int lLines) {
	char buf[65536];
	char buf2[65536];
	FILE *fp=popen("tailogcp","r");
	if(!fp) { syslog(LOG_ERR,"tailogcp: %s",strerror(errno)); exit(1); }
	int count=0;
	while(!feof(fp) && fgets(buf,sizeof(buf),fp)) {
		int blen=strlen(buf);
		for(int xa=blen; xa<blen+32; xa++) { buf[xa]=0; }

		strncpy(buf2,buf,sizeof(buf2));

		char *ip=buf;

		char *pp=strchr(buf,' ');
		if(pp) { *pp=0; pp++; } // log name '-'
		else { syslog(LOG_ERR,"parse error2: %s",buf); exit(1); }
		char *logname=pp;

		pp=strchr(pp,' ');
		if(pp) { *pp=0; pp++; } // user or '-'
		else { syslog(LOG_ERR,"parse error3: %s",logname); exit(1); }
		char *uid=pp;

		pp=strchr(pp,'[');
		if(pp) { pp--; *pp=0; pp+=2; } // time req rec'd
		else { syslog(LOG_ERR,"parse error4: %s",uid); exit(1); }
		char *reqTime=pp;

		pp=strchr(pp,']'); // end of time
		if(pp) { *pp=0; pp+=3; } // first line of req
		else { syslog(LOG_ERR,"parse error5: %s",reqTime); exit(1); }
		char *req=pp;

		pp=strchr(pp,'"'); // end of req
		if(pp) { *pp=0; pp+=2; } // req status
		else { syslog(LOG_ERR,"parse error6: %s",req); exit(1); }
		char *reqstsLine=pp;
		int reqsts=atoi(reqstsLine);

		pp=strchr(pp,' '); // end of status
		if(pp) { *pp=0; pp++; } // response bytes
		else { syslog(LOG_ERR,"parse error7: %s",reqstsLine); exit(1); }
		char *bytesLine=pp;
		int bytes=atoi(bytesLine);
		
		pp=strchr(pp,'"'); // start referer
		if(pp) { pp++; } // referer
		else { syslog(LOG_ERR,"parse error8: %s",bytesLine); exit(1); }
		char *referer=pp;

/***
 "-" "Dalvik/1.4.0 (Linux; U; Android 2.3.4; GT-I9100 Build/GINGERBREAD)" "-" "-"
 RFR USERAGENT                                                            FID COOKIE
****/
		pp=strchr(pp,'"'); // end referer
		if(pp) { *pp=0; pp+=3; } // user agent
		else { syslog(LOG_ERR,"parse error9: %s",referer); exit(1); }
		char *agent=pp;

		pp=strchr(pp,'"'); // end agent
		if(pp) { *pp=0; pp+=3; } // fid
		else { syslog(LOG_ERR,"parse error10: %s",agent); exit(1); }
		char *fid=pp;

		pp=strchr(pp,'"'); // end fid
		if(pp) { *pp=0; pp+=3; } // cookie
		else { syslog(LOG_ERR,"parse error11: %s",fid); exit(1); }
		char *cookie=pp;

		if(*cookie != 0) {
			pp=strchr(pp,'"'); // end cookie
			if(pp) { *pp=0; }
			else { syslog(LOG_ERR,"parse error12: %s fid=%s cookie=%s",req,fid,cookie); exit(1); }
		}

/****************/
		struct tm tms;
		memset(&tms, 0, sizeof(struct tm));
		//char *result=strptime(reqTime, "%d/%s/%4d:%02d:%02d:%02d %04d", &tms);
		char *result=strptime(reqTime, "%d/%b/%Y:%H:%M:%S %z", &tms);
		if (result == NULL) { syslog(LOG_ERR,"strptime(%s) failed.",reqTime); exit(1); }
		else if(result[0]) { syslog(LOG_ERR,"strptime(%s) result=%s",reqTime,result); }
		time_t logTime=mktime(&tms);

/****************/

		tabulate(ip, uid, bytes, logTime);

		time_t curTime=time(NULL);
		time_t dTime=curTime-reportTime;

		count++;
		time_t tabTime=curTime-startTime; // Time included in tabulation (only keep 24 hours)
		bool moreThanOneDay=(tabTime > 86400);
		if(dTime>30/*seconds between report*/ || count==lLines || moreThanOneDay) { 
	syslog(LOG_INFO,"dTime=%ld,count=%d,lLines=%d,moreThanOneDay=%d",dTime,count,lLines,moreThanOneDay);
report(curTime,tabTime,count); }
//		printf("count=%06d,lLines=%06d\r",count,lLines);
		if(moreThanOneDay) { break; } // Only keep stats for one day at a time
	}
	fclose(fp);
	syslog(LOG_INFO,"Terminating after >24 hrs stats collected");
}

int logLines() {
	FILE *fp=popen("loglines","r");
	if(!fp) { syslog(LOG_ERR,"loglines: %s",strerror(errno)); exit(1); }
	char buf[10]={0};
	if(!feof(fp) && fgets(buf,sizeof(buf),fp));
	return atoi(buf);
}

int main(int argc, char **argv) {
	reportTime=startTime=time(NULL);
	int adjust=(startTime-(20*3600))%86400; // Number of seconds past 11 am
	startTime-=adjust; // Appear to start at 11 am (in case we actually started after it by some amount)
	int lLines=logLines();
	syslog(LOG_INFO,"startTime=%ld,logLines=%d",startTime,lLines);
	parselog1(lLines);
}
